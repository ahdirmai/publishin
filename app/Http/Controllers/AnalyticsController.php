<?php

namespace App\Http\Controllers;

use App\Models\PostVersion;
use App\Services\AnalyticsService;
use App\Services\AnalyticsSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analytics,
        private readonly AnalyticsSyncService $sync,
    ) {}

    public function overview(Request $request): Response
    {
        $days     = (int) $request->input('days', 30);
        $platform = $request->input('platform') ?: null;

        $data = $this->analytics->getOverview($request->user(), $days, $platform);

        return Inertia::render('Analytics/Overview', [
            'data'           => $data,
            'filters'        => ['days' => $days, 'platform' => $platform],
            'available_days' => [7, 30, 90],
        ]);
    }

    public function perKonten(Request $request): Response
    {
        $filters = $request->only(['sort', 'type', 'platform', 'page']);
        $data    = $this->analytics->getPostList($request->user(), $filters);

        return Inertia::render('Analytics/PerKonten', [
            'data'    => $data,
            'filters' => $filters,
        ]);
    }

    public function contentDetail(Request $request, int $postVersionId): Response
    {
        $data = $this->analytics->getPostDetail($request->user(), $postVersionId);

        return Inertia::render('Analytics/ContentDetail', ['data' => $data]);
    }

    public function overviewData(Request $request): JsonResponse
    {
        $days     = (int) $request->input('days', 30);
        $platform = $request->input('platform');
        $data     = $this->analytics->getOverview($request->user(), $days, $platform);

        return response()->json($data);
    }

    public function postListData(Request $request): JsonResponse
    {
        $filters = $request->only(['sort', 'type', 'platform', 'page']);
        $data    = $this->analytics->getPostList($request->user(), $filters);

        return response()->json($data);
    }

    public function syncAll(Request $request): JsonResponse
    {
        $result = $this->sync->syncForUser($request->user());

        $msg = "Diimpor: {$result['imported']} post, diperbarui: {$result['synced']} analytics";
        if ($result['failed'] > 0) {
            $msg .= ", gagal: {$result['failed']}";
        }

        return response()->json([
            'message'  => $msg . '.',
            'imported' => $result['imported'],
            'synced'   => $result['synced'],
            'failed'   => $result['failed'],
            'errors'   => $result['errors'],
        ]);
    }

    public function syncOne(Request $request, int $postVersionId): JsonResponse
    {
        $pv = PostVersion::whereHas('post', fn ($q) => $q->where('user_id', $request->user()->id))
            ->findOrFail($postVersionId);

        try {
            $this->sync->syncPostVersion($pv);
            return response()->json(['message' => 'Berhasil disinkronkan.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
