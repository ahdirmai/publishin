<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'notifications' => $this->notifications->getForUser($request->user()),
            'unread_count'  => $this->notifications->getUnreadCount($request->user()),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->notifications->markAllRead($request->user());
        return response()->json(['message' => 'Semua notifikasi ditandai dibaca.']);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->notifications->markRead($request->user(), $id);
        return response()->json(['ok' => true]);
    }
}
