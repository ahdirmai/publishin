<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(private readonly AIService $ai) {}

    public function generateCaption(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|in:instagram,tiktok,facebook,twitter',
            'topic'    => 'required|string|max:500',
            'tone'     => 'sometimes|in:formal,santai,gen-z,profesional',
            'keywords' => 'sometimes|array|max:10',
            'keywords.*' => 'string|max:50',
        ]);

        try {
            $result = $this->ai->generateCaption($request->user(), $request->only([
                'platform', 'topic', 'tone', 'keywords',
            ]));

            return response()->json($result);
        } catch (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            return response()->json(['error' => $e->getMessage()], 429);
        }
    }

    public function suggestHashtags(Request $request): JsonResponse
    {
        $request->validate([
            'caption'  => 'required|string|max:2200',
            'platform' => 'sometimes|string',
            'niche'    => 'sometimes|string|max:100',
        ]);

        try {
            $result = $this->ai->suggestHashtags($request->user(), $request->only([
                'caption', 'platform', 'niche',
            ]));

            return response()->json($result);
        } catch (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            return response()->json(['error' => $e->getMessage()], 429);
        }
    }

    public function bestTime(Request $request, string $platform): JsonResponse
    {
        $times = $this->ai->getBestTime($request->user(), $platform);

        return response()->json(['platform' => $platform, 'times' => $times]);
    }
}
