<?php

namespace App\Jobs;

use App\Models\PostAnalytic;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use App\Services\InstagramService;
use App\Services\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchPostAnalytics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $postVersionId) {}

    public function handle(InstagramService $instagram, FacebookService $facebook): void
    {
        $pv = PostVersion::with('socialAccount')->find($this->postVersionId);

        if (! $pv || ! $pv->platform_post_id || ! $pv->socialAccount) {
            return;
        }

        $account = $pv->socialAccount;

        try {
            $metrics = match ($account->platform) {
                'instagram' => $instagram->getMediaInsights($account, $pv->platform_post_id),
                'facebook'  => $facebook->getPostInsights($account, $pv->platform_post_id),
                default     => null,
            };

            if (! $metrics) {
                return;
            }

            PostAnalytic::updateOrCreate(
                ['post_version_id' => $pv->id, 'date' => today()],
                [
                    'reach'           => $metrics['reach'] ?? 0,
                    'impressions'     => $metrics['impressions'] ?? 0,
                    'likes'           => $metrics['likes'] ?? 0,
                    'comments'        => $metrics['comments'] ?? 0,
                    'shares'          => $metrics['shares'] ?? 0,
                    'saves'           => $metrics['saves'] ?? 0,
                    'engagement_rate' => $metrics['engagement_rate'] ?? 0,
                ]
            );

            // Bust dashboard cache for this user
            $userId = $pv->socialAccount->user_id;
            Cache::forget("dashboard:{$userId}");
            Cache::forget("analytics:overview:{$userId}:30:");
        } catch (\Throwable $e) {
            Log::error("FetchPostAnalytics failed for postVersion {$this->postVersionId}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function backoff(): array
    {
        return [300, 900, 3600]; // 5m, 15m, 1h
    }
}
