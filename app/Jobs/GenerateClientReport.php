<?php

namespace App\Jobs;

use App\Models\AccountAnalytic;
use App\Models\ClientReport;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateClientReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public readonly int $reportId) {}

    public function handle(): void
    {
        $report = ClientReport::find($this->reportId);
        if (! $report) return;

        $report->update(['status' => 'generating']);

        try {
            $content = $this->buildReportContent($report);
            $path    = "reports/{$report->user_id}/report_{$report->id}.txt";

            Storage::disk('local')->put($path, $content);

            $report->update([
                'status'       => 'done',
                'file_path'    => $path,
                'generated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("GenerateClientReport failed for #{$this->reportId}", ['error' => $e->getMessage()]);
            $report->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function buildReportContent(ClientReport $report): string
    {
        $accountIds = SocialAccount::where('user_id', $report->user_id)
            ->where('is_active', true)
            ->when(count($report->platforms), fn ($q) => $q->whereIn('platform', $report->platforms))
            ->pluck('id');

        $agg = AccountAnalytic::whereIn('social_account_id', $accountIds)
            ->whereBetween('date', [$report->period_start, $report->period_end])
            ->selectRaw('SUM(reach) as reach, SUM(impressions) as impressions, AVG(engagement_rate) as er, SUM(follower_change) as fc')
            ->first();

        $topPosts = PostVersion::query()
            ->join('posts', 'post_versions.post_id', '=', 'posts.id')
            ->where('posts.user_id', $report->user_id)
            ->where('post_versions.status', 'published')
            ->whereBetween('post_versions.published_at', [$report->period_start, $report->period_end])
            ->withSum('analytics', 'reach')
            ->withAvg('analytics', 'engagement_rate')
            ->orderByDesc('analytics_sum_reach')
            ->limit(10)
            ->select('post_versions.*')
            ->get();

        $lines = [];
        $lines[] = str_repeat('=', 60);
        $lines[] = $report->name;
        $lines[] = 'Periode: ' . $report->period_start->format('d M Y') . ' – ' . $report->period_end->format('d M Y');
        $lines[] = 'Dibuat: ' . now()->format('d M Y H:i');
        if (! $report->white_label) {
            $lines[] = 'Dibuat dengan Publishin';
        }
        $lines[] = str_repeat('=', 60);

        if ($report->include_kpi) {
            $lines[] = '';
            $lines[] = 'KPI SUMMARY';
            $lines[] = str_repeat('-', 30);
            $lines[] = 'Total Reach     : ' . number_format((int) ($agg->reach ?? 0));
            $lines[] = 'Impressions     : ' . number_format((int) ($agg->impressions ?? 0));
            $lines[] = 'Avg Eng. Rate   : ' . round((float) ($agg->er ?? 0), 2) . '%';
            $lines[] = 'Follower Change : ' . ((int) ($agg->fc ?? 0) >= 0 ? '+' : '') . number_format((int) ($agg->fc ?? 0));
        }

        if ($report->include_top_posts && $topPosts->count()) {
            $lines[] = '';
            $lines[] = 'TOP POSTS';
            $lines[] = str_repeat('-', 30);
            foreach ($topPosts as $i => $pv) {
                $caption = mb_strimwidth($pv->caption ?? '(tanpa judul)', 0, 60, '…');
                $er      = round((float) ($pv->analytics_avg_engagement_rate ?? 0), 1);
                $reach   = number_format((int) ($pv->analytics_sum_reach ?? 0));
                $lines[] = ($i + 1) . ". {$caption}";
                $lines[] = "   Reach: {$reach} | Eng. Rate: {$er}%";
            }
        }

        $lines[] = '';

        return implode("\n", $lines);
    }

    public function backoff(): array
    {
        return [60, 300];
    }
}
