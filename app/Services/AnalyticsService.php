<?php

namespace App\Services;

use App\Models\AccountAnalytic;
use App\Models\Post;
use App\Models\PostAnalytic;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboard(User $user): array
    {
        $cacheKey = "dashboard:{$user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user) {
            $accountIds = SocialAccount::where('user_id', $user->id)
                ->where('is_active', true)
                ->pluck('id');

            $totalFollowers = (int) SocialAccount::where('user_id', $user->id)
                ->where('is_active', true)
                ->sum('follower_count');

            $scheduledThisWeek = (int) Post::where('user_id', $user->id)
                ->where('status', 'scheduled')
                ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            $current30 = AccountAnalytic::whereIn('social_account_id', $accountIds)
                ->where('date', '>=', now()->subDays(30))
                ->selectRaw('SUM(reach) as reach, AVG(engagement_rate) as er, SUM(follower_change) as fc')
                ->first();

            $prev30 = AccountAnalytic::whereIn('social_account_id', $accountIds)
                ->whereBetween('date', [now()->subDays(60), now()->subDays(30)])
                ->selectRaw('SUM(reach) as reach, AVG(engagement_rate) as er, SUM(follower_change) as fc')
                ->first();

            $reach30d       = (int) ($current30->reach ?? 0);
            $avgEngagement  = round((float) ($current30->er ?? 0), 2);
            $followerChange = (int) ($current30->fc ?? 0);

            $reachDelta      = (int) ($reach30d - ($prev30->reach ?? 0));
            $engagementDelta = round((float) ($avgEngagement - ($prev30->er ?? 0)), 2);

            $engagementTrend  = $this->getEngagementTrend($accountIds, 30);
            $postsPerPlatform = $this->getPostsPerPlatform($user);
            $recentPosts      = $this->getRecentPosts($user, 5);
            $bestTimes        = $this->getBestTimes($user);
            $notifications    = $this->getNotifications($user);

            return [
                'kpi' => [
                    'total_followers'   => $totalFollowers,
                    'scheduled_posts'   => $scheduledThisWeek,
                    'engagement_rate'   => $avgEngagement,
                    'reach_30d'         => $reach30d,
                    'followers_delta'   => $followerChange,
                    'engagement_delta'  => $engagementDelta,
                    'reach_delta'       => $reachDelta,
                ],
                'engagementTrend'   => $engagementTrend,
                'postsPerPlatform'  => $postsPerPlatform,
                'recentPosts'       => $recentPosts,
                'bestTimes'         => $bestTimes,
                'notifications'     => $notifications,
            ];
        });
    }

    public function getOverview(User $user, int $days = 30, ?string $platform = null): array
    {
        $cacheKey = "analytics:overview:{$user->id}:{$days}:{$platform}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($user, $days, $platform) {
            $query = SocialAccount::where('user_id', $user->id)->where('is_active', true);
            if ($platform) {
                $query->where('platform', $platform);
            }
            $accountIds = $query->pluck('id');

            $current = AccountAnalytic::whereIn('social_account_id', $accountIds)
                ->where('date', '>=', now()->subDays($days))
                ->selectRaw('SUM(reach) as reach, SUM(impressions) as impressions, AVG(engagement_rate) as engagement_rate, SUM(follower_change) as follower_growth')
                ->first();

            $previous = AccountAnalytic::whereIn('social_account_id', $accountIds)
                ->whereBetween('date', [now()->subDays($days * 2), now()->subDays($days)])
                ->selectRaw('SUM(reach) as reach, SUM(impressions) as impressions, AVG(engagement_rate) as engagement_rate, SUM(follower_change) as follower_growth')
                ->first();

            $reach       = (int) ($current->reach ?? 0);
            $impressions = (int) ($current->impressions ?? 0);
            $er          = round((float) ($current->engagement_rate ?? 0), 2);
            $fgrowth     = (int) ($current->follower_growth ?? 0);

            $kpis = [
                [
                    'label'          => 'Total Reach',
                    'value'          => $this->fmtNum($reach),
                    'delta'          => $this->fmtDelta($this->calcDelta($current->reach, $previous->reach)) . '% vs period lalu',
                    'delta_positive' => ($current->reach ?? 0) >= ($previous->reach ?? 0),
                    'subtext'        => null,
                ],
                [
                    'label'          => 'Impressions',
                    'value'          => $this->fmtNum($impressions),
                    'delta'          => $this->fmtDelta($this->calcDelta($current->impressions, $previous->impressions)) . '% vs period lalu',
                    'delta_positive' => ($current->impressions ?? 0) >= ($previous->impressions ?? 0),
                    'subtext'        => null,
                ],
                [
                    'label'          => 'Engagement Rate',
                    'value'          => $er . '%',
                    'delta'          => '',
                    'delta_positive' => null,
                    'subtext'        => 'industri avg: 2.3%',
                ],
                [
                    'label'          => 'Follower Growth',
                    'value'          => ($fgrowth >= 0 ? '+' : '') . $this->fmtNum($fgrowth),
                    'delta'          => 'net new followers',
                    'delta_positive' => $fgrowth >= 0,
                    'subtext'        => null,
                ],
            ];

            $reachByPlatform   = $this->getReachByPlatform($user, $days);
            $followerGrowthChart = $this->getFollowerGrowthChart($accountIds, $days);
            $topPosts          = $this->getTopPosts($user, $days, 5);
            $audience          = $this->getAudienceDemographics($user);

            return [
                'kpis'               => $kpis,
                'reachByPlatform'    => $reachByPlatform,
                'followerGrowthChart' => $followerGrowthChart,
                'topPosts'           => $topPosts,
                'audience'           => $audience,
            ];
        });
    }

    public function getPostList(User $user, array $filters = []): array
    {
        $sort     = $filters['sort'] ?? 'reach';
        $type     = $filters['type'] ?? null;
        $platform = $filters['platform'] ?? null;
        $page     = (int) ($filters['page'] ?? 1);
        $perPage  = 20;

        $query = PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->where('post_versions.status', 'published')
            ->with(['post', 'socialAccount'])
            ->select('post_versions.*');

        if ($type) {
            $query->where('post_versions.content_type', $type);
        }

        if ($platform) {
            $query->join('social_accounts as sa_filter', 'post_versions.social_account_id', '=', 'sa_filter.id')
                ->where('sa_filter.platform', $platform);
        }

        $sortMap = [
            'reach'           => 'post_versions.analytics_sum_reach',
            'engagement_rate' => 'post_versions.analytics_avg_engagement_rate',
            'impressions'     => 'post_versions.analytics_sum_impressions',
            'published_at'    => 'post_versions.published_at',
        ];
        $query->orderByDesc($sortMap[$sort] ?? 'post_versions.analytics_sum_reach');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $items = $paginator->map(function ($pv) {
            $er = round((float) ($pv->analytics_avg_engagement_rate ?? 0), 2);
            $caption = $pv->caption ?? '';
            $title   = mb_strlen($caption) > 60 ? mb_substr($caption, 0, 60) . '…' : ($caption ?: '(tanpa judul)');

            return [
                'id'              => $pv->id,
                'post_version_id' => $pv->id,
                'title'           => $title,
                'content_type'    => $pv->content_type ?? 'photo',
                'platform'        => $pv->socialAccount?->platform ?? 'instagram',
                'published_at'    => $pv->published_at?->format('d M Y') ?? '—',
                'thumbnail_url'   => $pv->getFirstMediaUrl('post_media', 'thumb') ?: null,
                'reach'           => (int) ($pv->analytics_sum_reach ?? 0),
                'impressions'     => (int) ($pv->analytics_sum_impressions ?? 0),
                'likes'           => (int) ($pv->analytics_sum_likes ?? 0),
                'comments'        => (int) ($pv->analytics_sum_comments ?? 0),
                'shares'          => (int) ($pv->analytics_sum_shares ?? 0),
                'saves'           => (int) ($pv->analytics_sum_saves ?? 0),
                'engagement_rate' => $er,
                'status'          => $pv->status,
            ];
        });

        return [
            'data'         => $items->values(),
            'total'        => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $perPage,
        ];
    }

    public function getPostDetail(User $user, int $postVersionId): array
    {
        $pv = PostVersion::with(['post', 'socialAccount'])
            ->whereHas('post', fn ($q) => $q->where('user_id', $user->id))
            ->findOrFail($postVersionId);

        // Read aggregated metrics from post_versions columns (synced from platform API)
        $reach       = (int) ($pv->analytics_sum_reach ?? 0);
        $impressions = (int) ($pv->analytics_sum_impressions ?? 0);
        $likes       = (int) ($pv->analytics_sum_likes ?? 0);
        $comments    = (int) ($pv->analytics_sum_comments ?? 0);
        $shares      = (int) ($pv->analytics_sum_shares ?? 0);
        $saves       = (int) ($pv->analytics_sum_saves ?? 0);
        $er          = round((float) ($pv->analytics_avg_engagement_rate ?? 0), 2);

        // Account average from other post_versions on same account
        $accountId = $pv->social_account_id;
        $accountAvg = PostVersion::where('social_account_id', $accountId)
            ->where('status', 'published')
            ->whereNotNull('analytics_sum_reach')
            ->selectRaw('AVG(analytics_sum_reach) as avg_reach, AVG(analytics_avg_engagement_rate) as avg_er')
            ->first();
        $avgReach = (int) ($accountAvg->avg_reach ?? 0);
        $avgEr    = round((float) ($accountAvg->avg_er ?? 0), 2);

        // Daily breakdown from PostAnalytic (may be empty for imported posts)
        $daily7 = PostAnalytic::where('post_version_id', $pv->id)
            ->orderBy('date')
            ->limit(7)
            ->get();

        $insights = $this->generateInsights($reach, $er, $avgReach, $avgEr, $pv->content_type, $daily7);

        $caption = $pv->caption ?? '';
        $title   = mb_strlen($caption) > 80 ? mb_substr($caption, 0, 80) . '…' : ($caption ?: '(tanpa judul)');

        $kpis = [
            ['label' => 'Reach',      'value' => $this->fmtNum($reach),       'delta' => $this->fmtDelta($this->calcDelta($reach, $avgReach)) . '% vs avg',      'delta_positive' => $reach >= $avgReach],
            ['label' => 'Impresi',    'value' => $this->fmtNum($impressions),  'delta' => null, 'delta_positive' => null],
            ['label' => 'Like',       'value' => $this->fmtNum($likes),        'delta' => null, 'delta_positive' => null],
            ['label' => 'Komentar',   'value' => $this->fmtNum($comments),     'delta' => null, 'delta_positive' => null],
            ['label' => 'Share',      'value' => $this->fmtNum($shares),       'delta' => null, 'delta_positive' => null],
            ['label' => 'Simpan',     'value' => $this->fmtNum($saves),        'delta' => null, 'delta_positive' => null],
            ['label' => 'Eng. Rate',  'value' => $er . '%',                    'delta' => $this->fmtDelta($this->calcDelta($er, $avgEr)) . '% vs avg',            'delta_positive' => $er >= $avgEr],
        ];

        $vsAvg = [
            ['metric' => 'Reach',    'post_value' => $reach, 'avg_value' => $avgReach, 'unit' => ''],
            ['metric' => 'Eng. Rate', 'post_value' => $er,   'avg_value' => $avgEr,    'unit' => '%'],
        ];

        $engDist = [
            ['label' => 'Like',     'value' => $likes,    'color' => '#C96442'],
            ['label' => 'Komentar', 'value' => $comments, 'color' => '#3B6DB5'],
            ['label' => 'Share',    'value' => $shares,   'color' => '#2A7A4B'],
            ['label' => 'Simpan',   'value' => $saves,    'color' => '#928E89'],
        ];

        return [
            'id'                => $pv->id,
            'title'             => $title,
            'platform'          => $pv->socialAccount?->platform ?? 'instagram',
            'platform_username' => $pv->socialAccount?->username ?? null,
            'platform_post_id'  => $pv->platform_post_id,
            'content_type'      => $pv->content_type ?? 'photo',
            'published_at'      => $pv->published_at?->format('d M Y') ?? '—',
            'status'            => $pv->status,
            'thumbnail_url'     => $pv->getFirstMediaUrl('post_media', 'thumb') ?: null,
            'preview_url'       => $pv->getFirstMediaUrl('post_media', 'preview') ?: $pv->getFirstMediaUrl('post_media') ?: null,
            'post_url'          => $pv->post_url,
            'caption'           => $caption,
            'kpis'              => $kpis,
            'daily_7'           => $daily7->values()->map(fn ($d, $i) => [
                'day'   => $i + 1,
                'date'  => $d->date->format('d M'),
                'reach' => (int) $d->reach,
            ])->values(),
            'vs_avg'                  => $vsAvg,
            'engagement_distribution' => $engDist,
            'insights'                => $insights,
        ];
    }

    // ── Private helpers ──────────────────────────────────────────

    private function getEngagementTrend($accountIds, int $days): array
    {
        return AccountAnalytic::whereIn('social_account_id', $accountIds)
            ->where('date', '>=', now()->subDays($days))
            ->selectRaw('date, AVG(engagement_rate) as er')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => ['date' => $r->date->format('d/M'), 'rate' => round((float) $r->er, 2)])
            ->values()
            ->toArray();
    }

    private function getPostsPerPlatform(User $user): array
    {
        return PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->join('social_accounts', 'post_versions.social_account_id', '=', 'social_accounts.id')
            ->where('posts.user_id', $user->id)
            ->where('post_versions.status', 'published')
            ->selectRaw('social_accounts.platform, COUNT(*) as count')
            ->groupBy('social_accounts.platform')
            ->get()
            ->map(fn ($r) => ['platform' => $r->platform, 'count' => (int) $r->count])
            ->values()
            ->toArray();
    }

    private function getRecentPosts(User $user, int $limit): array
    {
        return PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->whereIn('post_versions.status', ['published', 'scheduled', 'draft'])
            ->with(['socialAccount'])
            ->orderByDesc('posts.scheduled_at')
            ->limit($limit)
            ->select('post_versions.*')
            ->get()
            ->map(fn ($pv) => [
                'id'              => $pv->id,
                'title'           => mb_strimwidth($pv->caption ?? '(tanpa judul)', 0, 60, '…'),
                'platform'        => $pv->socialAccount?->platform ?? 'instagram',
                'status'          => $pv->status,
                'reach'           => (int) ($pv->analytics_sum_reach ?? 0),
                'engagement_rate' => $pv->analytics_sum_reach ? round((float) ($pv->analytics_avg_engagement_rate ?? 0), 2) : null,
            ])
            ->values()
            ->toArray();
    }

    private function getBestTimes(User $user): array
    {
        $platforms = SocialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('platform')
            ->unique();

        $defaults = [
            'instagram' => ['platform' => 'instagram', 'days' => 'Sen–Rab', 'time' => '07:00', 'label' => 'WIB'],
            'tiktok'    => ['platform' => 'tiktok',    'days' => 'Kam–Jum', 'time' => '20:00', 'label' => 'WIB'],
            'youtube'   => ['platform' => 'youtube',   'days' => 'Weekend', 'time' => '10:00', 'label' => 'WIB'],
            'facebook'  => ['platform' => 'facebook',  'days' => 'Sel–Rab', 'time' => '13:00', 'label' => 'WIB'],
        ];

        return $platforms
            ->filter(fn ($p) => isset($defaults[$p]))
            ->map(fn ($p) => $defaults[$p])
            ->values()
            ->toArray();
    }

    private function getNotifications(User $user): array
    {
        $failed = PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->where('post_versions.status', 'failed')
            ->where('post_versions.updated_at', '>=', now()->subDays(7))
            ->count();

        $scheduled = Post::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now()->addHours(24))
            ->count();

        $notifications = [];
        $i = 1;

        if ($failed > 0) {
            $notifications[] = ['id' => $i++, 'color' => 'red',  'message' => "{$failed} post gagal dipublikasikan minggu ini", 'time' => ''];
        }
        if ($scheduled > 0) {
            $notifications[] = ['id' => $i++, 'color' => 'blue', 'message' => "{$scheduled} konten terjadwal 24 jam ke depan", 'time' => ''];
        }

        return $notifications;
    }

    private function getReachByPlatform(User $user, int $days): array
    {
        return SocialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['accountAnalytics' => fn ($q) => $q->where('date', '>=', now()->subDays($days))])
            ->get()
            ->map(fn ($a) => [
                'platform' => $a->platform,
                'reach'    => (int) $a->accountAnalytics->sum('reach'),
            ])
            ->sortByDesc('reach')
            ->values()
            ->toArray();
    }

    private function getFollowerGrowthChart($accountIds, int $days): array
    {
        $igIds = SocialAccount::whereIn('id', $accountIds)->where('platform', 'instagram')->pluck('id');
        $ttIds = SocialAccount::whereIn('id', $accountIds)->where('platform', 'tiktok')->pluck('id');

        $igData = AccountAnalytic::whereIn('social_account_id', $igIds)
            ->where('date', '>=', now()->subDays($days))
            ->selectRaw('date, SUM(follower_change) as fc')
            ->groupBy('date')->orderBy('date')
            ->pluck('fc', 'date');

        $ttData = AccountAnalytic::whereIn('social_account_id', $ttIds)
            ->where('date', '>=', now()->subDays($days))
            ->selectRaw('date, SUM(follower_change) as fc')
            ->groupBy('date')->orderBy('date')
            ->pluck('fc', 'date');

        $dates = $igData->keys()->merge($ttData->keys())->unique()->sort();

        return $dates->map(fn ($d) => [
            'date'      => \Carbon\Carbon::parse($d)->format('d/M'),
            'instagram' => (int) ($igData[$d] ?? 0),
            'tiktok'    => (int) ($ttData[$d] ?? 0),
        ])->values()->toArray();
    }

    private function getTopPosts(User $user, int $days, int $limit): array
    {
        return PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->where('post_versions.status', 'published')
            ->where('post_versions.published_at', '>=', now()->subDays($days))
            ->with('socialAccount')
            ->orderByDesc('post_versions.analytics_sum_reach')
            ->limit($limit)
            ->select('post_versions.*')
            ->get()
            ->map(fn ($pv) => [
                'id'              => $pv->id,
                'title'           => mb_strimwidth($pv->caption ?? '(tanpa judul)', 0, 50, '…'),
                'platform'        => $pv->socialAccount?->platform ?? 'instagram',
                'reach'           => (int) ($pv->analytics_sum_reach ?? 0),
                'engagement_rate' => round((float) ($pv->analytics_avg_engagement_rate ?? 0), 2),
            ])
            ->values()
            ->toArray();
    }

    private function getAudienceDemographics(User $user): ?array
    {
        return null;
    }

    private function generateInsights(int $reach, float $er, int $avgReach, float $avgEr, ?string $type, $daily): array
    {
        $insights = [];

        if ($er >= 7) {
            $insights[] = ['icon' => '★', 'title' => 'Performa Tinggi', 'body' => "Engagement rate {$er}% jauh di atas rata-rata industri (2.3%)."];
        }

        if ($avgReach > 0 && $reach > $avgReach * 1.5) {
            $insights[] = ['icon' => '↗', 'title' => 'Viral Potential', 'body' => 'Reach konten ini 1.5× di atas rata-rata akunmu.'];
        }

        $day7Reach = $daily->last()?->reach ?? 0;
        $day1Reach = $daily->first()?->reach ?? 0;
        if ($day7Reach > 0 && $day1Reach > 0 && ($day7Reach / $day1Reach) > 0.4) {
            $insights[] = ['icon' => '◈', 'title' => 'Evergreen Content', 'body' => 'Konten ini masih mendapat traffic signifikan setelah 7 hari.'];
        }

        if (in_array($type, ['reels', 'video'])) {
            $insights[] = ['icon' => '▶', 'title' => 'Format Video', 'body' => 'Video dan Reels rata-rata mendapat 2× reach dibanding foto.'];
        }

        $totalComments = $daily->sum('comments');
        if ($totalComments > 20) {
            $insights[] = ['icon' => '◎', 'title' => 'Diskusi Aktif', 'body' => "Konten ini memancing {$totalComments} komentar — tinggi untuk engagement organik."];
        }

        return $insights;
    }

    private function fmtNum(int|float $n): string
    {
        if ($n >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
        if ($n >= 1_000)     return number_format($n / 1_000, 1) . 'K';
        return (string) (int) $n;
    }

    private function fmtDelta(float $delta): string
    {
        return $delta >= 0 ? "↑ +{$delta}" : "↓ {$delta}";
    }

    private function calcDelta($current, $previous): float
    {
        $current  = (float) ($current ?? 0);
        $previous = (float) ($previous ?? 0);
        if ($previous == 0) {
            return 0.0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
