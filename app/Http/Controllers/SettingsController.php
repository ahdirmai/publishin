<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $user      = $request->user();
        $accounts  = $user->socialAccounts()->orderBy('platform')->get([
            'id', 'platform', 'username', 'display_name', 'follower_count', 'is_active', 'token_expires_at',
        ]);

        $notifSettings = NotificationSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_weekly_summary'   => true,
                'push_post_published'    => true,
                'push_mentions'          => false,
                'email_monthly_report'   => true,
                'push_schedule_reminder' => true,
            ]
        );

        $subscription = $user->subscription()->with('plan')->first();
        $plan = $subscription?->plan;

        return Inertia::render('Settings/Index', [
            'user' => [
                'name'     => $user->name,
                'email'    => $user->email,
                'timezone' => $user->timezone ?? 'Asia/Jakarta',
                'initials' => collect(explode(' ', $user->name))->map(fn ($w) => mb_strtoupper($w[0] ?? ''))->take(2)->implode(''),
            ],
            'accounts' => $accounts,
            'notifications' => [
                'email_weekly_summary'   => $notifSettings->email_weekly_summary,
                'push_post_published'    => $notifSettings->push_post_published,
                'push_mentions'          => $notifSettings->push_mentions,
                'email_monthly_report'   => $notifSettings->email_monthly_report,
                'push_schedule_reminder' => $notifSettings->push_schedule_reminder,
            ],
            'subscription' => $plan ? [
                'plan_name'    => $plan->name,
                'price'        => 'Rp ' . number_format($plan->price_monthly, 0, ',', '.') . ' / bln',
                'valid_until'  => $subscription?->current_period_end?->format('d M Y') ?? '—',
                'features'     => $this->planFeatures($plan),
            ] : null,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $request->user()->id,
            'timezone' => 'required|string|max:50',
        ]);

        $request->user()->update($data);

        return response()->json(['message' => 'Profil berhasil disimpan.']);
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email_weekly_summary'   => 'required|boolean',
            'push_post_published'    => 'required|boolean',
            'push_mentions'          => 'required|boolean',
            'email_monthly_report'   => 'required|boolean',
            'push_schedule_reminder' => 'required|boolean',
        ]);

        NotificationSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json(['message' => 'Pengaturan notifikasi disimpan.']);
    }

    private function planFeatures($plan): array
    {
        $features = [
            $plan->max_social_accounts . ' akun sosial media',
            'Unlimited post scheduling',
        ];

        if ($plan->has_ai_features) {
            $features[] = 'AI caption generator';
        }
        if ($plan->has_white_label) {
            $features[] = 'Custom report + white-label';
        }
        if ($plan->has_api_access) {
            $features[] = 'API access';
        }

        $features[] = 'Priority support';

        return $features;
    }
}
