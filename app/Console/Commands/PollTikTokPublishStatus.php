<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostVersion;
use App\Services\TikTokService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollTikTokPublishStatus extends Command
{
    protected $signature = 'publishin:poll-tiktok-status';
    protected $description = 'Poll TikTok async publish status for versions stuck in publishing state';

    public function handle(): int
    {
        $versions = PostVersion::query()
            ->where('status', 'publishing')
            ->whereNotNull('platform_post_id')
            ->whereHas('socialAccount', fn ($q) => $q->where('platform', 'tiktok'))
            ->with(['socialAccount', 'post'])
            ->get();

        if ($versions->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($versions as $version) {
            $service    = new TikTokService($version->socialAccount);
            $statusData = $service->getPublishStatus($version->platform_post_id);

            $status    = $statusData['data']['status'] ?? null;
            $postId    = $statusData['data']['publicaly_available_post_id'][0] ?? null;

            if ($status === 'PUBLISH_COMPLETE') {
                $version->update([
                    'status'           => 'published',
                    'platform_post_id' => $postId ?? $version->platform_post_id,
                    'published_at'     => now(),
                ]);

                $post = $version->post;
                if ($post->versions()->where('status', '!=', 'published')->where('status', '!=', 'failed')->doesntExist()) {
                    $post->update(['status' => 'published', 'published_at' => now()]);
                }

                $this->info("Version {$version->id} published (TikTok ID: {$postId}).");
            } elseif (in_array($status, ['FAILED', 'SPAM_ACCOUNT_BLOCKED'], true)) {
                $version->update(['status' => 'failed']);
                Log::error("TikTok publish failed for version {$version->id}: status={$status}");
                $this->warn("Version {$version->id} failed: {$status}");

                $post = $version->post;
                if ($post->versions()->where('status', 'failed')->count() === $post->versions()->count()) {
                    $post->update(['status' => 'failed']);
                }
            } else {
                $this->line("Version {$version->id} still processing (status={$status}).");
            }
        }

        return Command::SUCCESS;
    }
}
