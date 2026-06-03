<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $year = (int) ($request->query('year', now()->year));
        $month = (int) ($request->query('month', now()->month));

        return Inertia::render('Calendar/Index', [
            'year' => $year,
            'month' => $month,
            'posts' => $this->getMonthPosts($request->user()->id, $year, $month),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $posts = $this->getMonthPosts(
            $request->user()->id,
            (int) $validated['year'],
            (int) $validated['month'],
        );

        return response()->json(['posts' => $posts]);
    }

    private function getMonthPosts(int $userId, int $year, int $month): array
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $posts = Post::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['draft', 'scheduled', 'publishing', 'published'])
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->orWhere(function ($q) use ($userId, $startOfMonth, $endOfMonth): void {
                $q->where('user_id', $userId)
                    ->where('status', 'published')
                    ->whereBetween('published_at', [$startOfMonth, $endOfMonth]);
            })
            ->with(['versions.socialAccount:id,platform,username'])
            ->orderBy('scheduled_at')
            ->get();

        return $posts->map(function (Post $post): array {
            $dateSource = $post->scheduled_at ?? $post->published_at;
            $firstVersion = $post->versions->first();

            return [
                'id' => $post->id,
                'title' => $post->title,
                'status' => $post->status,
                'scheduled_at' => $post->scheduled_at?->toIso8601String(),
                'published_at' => $post->published_at?->toIso8601String(),
                'date' => $dateSource?->format('Y-m-d'),
                'time' => $dateSource?->format('H:i'),
                'platforms' => $post->versions->pluck('socialAccount.platform')->unique()->values(),
                'caption_preview' => $firstVersion?->caption
                    ? Str::limit($firstVersion->caption, 60)
                    : null,
            ];
        })->all();
    }
}
