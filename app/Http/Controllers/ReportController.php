<?php

namespace App\Http\Controllers;

use App\Models\ClientReport;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function index(Request $request): Response
    {
        $history = $this->reports->getHistory($request->user());
        $accounts = $request->user()->socialAccounts()
            ->where('is_active', true)
            ->get(['platform', 'username'])
            ->unique('platform')
            ->values();

        $currentMonth = now()->startOfMonth()->toDateString();
        $currentEnd   = now()->endOfMonth()->toDateString();

        return Inertia::render('Reports/Index', [
            'history'  => $history,
            'accounts' => $accounts,
            'defaults' => [
                'period_start' => $currentMonth,
                'period_end'   => $currentEnd,
                'platforms'    => $accounts->pluck('platform')->toArray(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'period_start'         => 'required|date',
            'period_end'           => 'required|date|after_or_equal:period_start',
            'platforms'            => 'sometimes|array',
            'platforms.*'          => 'string',
            'include_kpi'          => 'sometimes|boolean',
            'include_chart'        => 'sometimes|boolean',
            'include_top_posts'    => 'sometimes|boolean',
            'include_demographics' => 'sometimes|boolean',
            'white_label'          => 'sometimes|boolean',
        ]);

        $report = $this->reports->queue($request->user(), $data);

        return response()->json([
            'id'      => $report->id,
            'name'    => $report->name,
            'status'  => $report->status,
            'message' => 'Laporan sedang dibuat, akan tersedia dalam beberapa menit.',
        ], 201);
    }

    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'period_start' => 'required|date',
            'period_end'   => 'required|date',
            'platforms'    => 'sometimes|array',
        ]);

        $preview = $this->reports->getPreviewData(
            $request->user(),
            $data['period_start'],
            $data['period_end'],
            $data['platforms'] ?? [],
        );

        return response()->json($preview);
    }

    public function download(Request $request, int $reportId): StreamedResponse
    {
        $report = ClientReport::where('user_id', $request->user()->id)
            ->where('status', 'done')
            ->findOrFail($reportId);

        if (! $report->file_path || ! Storage::disk('local')->exists($report->file_path)) {
            abort(404, 'File laporan tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $report->file_path,
            str($report->name)->slug() . '.txt'
        );
    }
}
