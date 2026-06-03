<?php

namespace App\Services;

use App\Models\AccountAnalytic;
use App\Models\ClientReport;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportService
{
    public function getHistory(User $user): array
    {
        return ClientReport::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'platforms'   => $r->platforms,
                'period'      => $r->period_start->format('d M Y') . ' – ' . $r->period_end->format('d M Y'),
                'created_at'  => $r->created_at->format('d M Y'),
                'status'      => $r->status,
                'can_download' => $r->status === 'done' && $r->file_path,
            ])
            ->values()
            ->toArray();
    }

    public function getPreviewData(User $user, string $periodStart, string $periodEnd, array $platforms): array
    {
        $start      = Carbon::parse($periodStart);
        $end        = Carbon::parse($periodEnd);
        $monthLabel = $start->format('M Y') . ($start->month !== $end->month ? ' – ' . $end->format('M Y') : '');

        $accountIds = SocialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->when(count($platforms), fn ($q) => $q->whereIn('platform', $platforms))
            ->pluck('id');

        $agg = AccountAnalytic::whereIn('social_account_id', $accountIds)
            ->whereBetween('date', [$start, $end])
            ->selectRaw('SUM(reach) as reach, AVG(engagement_rate) as er, SUM(posts_published) as posts')
            ->first();

        $reach = (int) ($agg->reach ?? 0);
        $er    = round((float) ($agg->engagement_rate ?? 0), 1);
        $posts = (int) ($agg->posts ?? PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->where('post_versions.status', 'published')
            ->whereBetween('post_versions.published_at', [$start, $end])
            ->count());

        return [
            'month_label' => $monthLabel,
            'reach'       => $this->fmtNum($reach),
            'er'          => $er . '%',
            'posts'       => (string) $posts,
        ];
    }

    public function queue(User $user, array $data): ClientReport
    {
        $start = Carbon::parse($data['period_start'] ?? now()->startOfMonth());
        $end   = Carbon::parse($data['period_end'] ?? now()->endOfMonth());
        $name  = 'Laporan ' . $start->format('M Y') . ($start->month !== $end->month ? ' – ' . $end->format('M Y') : '');

        $report = ClientReport::create([
            'user_id'              => $user->id,
            'name'                 => $name,
            'period_start'         => $start->toDateString(),
            'period_end'           => $end->toDateString(),
            'platforms'            => $data['platforms'] ?? [],
            'include_kpi'          => $data['include_kpi'] ?? true,
            'include_chart'        => $data['include_chart'] ?? true,
            'include_top_posts'    => $data['include_top_posts'] ?? true,
            'include_demographics' => $data['include_demographics'] ?? false,
            'white_label'          => $data['white_label'] ?? false,
            'status'               => 'pending',
        ]);

        \App\Jobs\GenerateClientReport::dispatch($report->id);

        return $report;
    }

    private function fmtNum(int|float $n): string
    {
        if ($n >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
        if ($n >= 1_000)     return number_format($n / 1_000, 1) . 'K';
        return (string) (int) $n;
    }
}
