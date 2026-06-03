<?php

namespace App\Console\Commands;

use App\Models\AccountAnalytic;
use App\Models\SocialAccount;
use App\Services\InstagramService;
use App\Services\FacebookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchAccountAnalytics extends Command
{
    protected $signature   = 'publishin:fetch-account-analytics {--account=}';
    protected $description = 'Fetch daily account metrics from all connected platforms';

    public function handle(InstagramService $instagram, FacebookService $facebook): int
    {
        $query = SocialAccount::where('is_active', true)
            ->whereNotNull('access_token');

        if ($accountId = $this->option('account')) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();
        $this->info("Fetching analytics for {$accounts->count()} accounts...");

        foreach ($accounts as $account) {
            try {
                $metrics = match ($account->platform) {
                    'instagram' => $instagram->getAccountInsights($account),
                    'facebook'  => $facebook->getPageInsights($account),
                    default     => null,
                };

                if (! $metrics) {
                    $this->warn("No service for platform: {$account->platform}");
                    continue;
                }

                AccountAnalytic::updateOrCreate(
                    ['social_account_id' => $account->id, 'date' => today()],
                    [
                        'followers'        => $metrics['followers'] ?? $account->follower_count,
                        'follower_change'  => $metrics['follower_change'] ?? 0,
                        'reach'            => $metrics['reach'] ?? 0,
                        'impressions'      => $metrics['impressions'] ?? 0,
                        'engagement_rate'  => $metrics['engagement_rate'] ?? 0,
                        'posts_published'  => $metrics['posts_published'] ?? 0,
                    ]
                );

                // Update follower_count on account
                $account->update(['follower_count' => $metrics['followers'] ?? $account->follower_count]);

                $this->line("  ✓ {$account->platform}:{$account->username}");
            } catch (\Throwable $e) {
                Log::error("FetchAccountAnalytics failed for account {$account->id}", ['error' => $e->getMessage()]);
                $this->error("  ✗ {$account->platform}:{$account->username} — {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
