<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    public function getForUser(User $user, int $limit = 10): array
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($n) => [
                'id'      => $n->id,
                'type'    => $n->data['type'] ?? 'info',
                'message' => $n->data['message'] ?? '',
                'color'   => $n->data['color'] ?? 'neutral',
                'url'     => $n->data['url'] ?? null,
                'read'    => ! is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function markAllRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function markRead(User $user, string $notificationId): void
    {
        $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
    }

    public function send(User $user, string $type, string $message, string $color = 'neutral', ?string $url = null): void
    {
        $settings = NotificationSetting::where('user_id', $user->id)->first();

        if ($settings && ! $this->isEnabled($settings, $type)) {
            return;
        }

        $user->notifications()->create([
            'id'               => \Illuminate\Support\Str::uuid(),
            'type'             => 'App\\Notifications\\AppNotification',
            'notifiable_type'  => User::class,
            'notifiable_id'    => $user->id,
            'data'             => json_encode(compact('type', 'message', 'color', 'url')),
            'read_at'          => null,
        ]);
    }

    private function isEnabled(NotificationSetting $settings, string $type): bool
    {
        return match ($type) {
            'post_published'     => $settings->push_post_published,
            'schedule_reminder'  => $settings->push_schedule_reminder,
            'mention'            => $settings->push_mentions,
            default              => true,
        };
    }
}
