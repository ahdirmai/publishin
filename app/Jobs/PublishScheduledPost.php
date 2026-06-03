<?php

// Register in routes/console.php: Schedule::command('publishin:process-scheduled')->everyMinute();

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use App\Services\InstagramService;
use App\Services\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Post $post)
    {
        $this->onQueue('publishing');
    }

    public function handle(): void
    {
        if ($this->post->fresh()->status !== 'scheduled') {
            return;
        }

        $this->post->update(['status' => 'publishing']);

        $versions = $this->post->versions()
            ->where('status', 'pending')
            ->with('socialAccount')
            ->get();

        foreach ($versions as $version) {
            $version->update(['status' => 'publishing']);

            try {
                $media = $version->getFirstMedia('post_media');
                $imageUrl = $media?->getUrl('preview');

                $result = match ($version->socialAccount->platform) {
                    'instagram' => (new InstagramService($version->socialAccount))
                        ->publishImagePost($imageUrl ?? '', $version->caption ?? ''),
                    'facebook'  => (new FacebookService($version->socialAccount))
                        ->publishPost($version->caption ?? '', $imageUrl),
                    default     => ['error' => 'Platform tidak didukung'],
                };

                if (isset($result['error'])) {
                    throw new \Exception($result['error']);
                }

                $version->update([
                    'status'           => 'published',
                    'platform_post_id' => $result['id'] ?? null,
                    'published_at'     => now(),
                ]);
            } catch (\Throwable $e) {
                $version->increment('retry_count');
                $version->refresh();

                if ($version->retry_count >= 3) {
                    $version->update(['status' => 'failed']);
                    Log::error("PublishScheduledPost version {$version->id} failed permanently: {$e->getMessage()}");
                } else {
                    $version->update(['status' => 'pending']);
                }

                Log::error("PublishScheduledPost version {$version->id} failed: {$e->getMessage()}");
            }
        }

        $post = $this->post;

        $allFailed = $post->versions()->where('status', 'failed')->count() === $post->versions()->count();
        $anyPublished = $post->versions()->where('status', 'published')->exists();

        if ($allFailed) {
            $post->update(['status' => 'failed']);
        } elseif ($anyPublished) {
            $post->update(['status' => 'published', 'published_at' => now()]);
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->post->update(['status' => 'failed']);
        Log::error("PublishScheduledPost job failed for post {$this->post->id}: {$e->getMessage()}");
    }
}
