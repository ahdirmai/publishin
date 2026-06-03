<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialAccountController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public marketing routes
Route::get('/', [MarketingController::class, 'index'])->name('home');
Route::get('/waitlist', [MarketingController::class, 'waitlist'])->name('waitlist');
Route::post('/waitlist', [MarketingController::class, 'storeWaitlist'])->name('waitlist.store');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('terms');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/forgot-password',  [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Phase 2 — Compose
    Route::get('/compose', [PostController::class, 'create'])->name('compose');
    Route::get('/compose/{post}/edit', [PostController::class, 'edit'])->name('compose.edit');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Phase 2 — Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/api/v1/calendar', [CalendarController::class, 'data'])->name('calendar.data');

    // Phase 2 — Media upload
    Route::post('/api/v1/media/upload', [MediaController::class, 'store'])->name('media.upload');
    Route::delete('/api/v1/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Phase 2 — Social Accounts (platform connections)
    Route::get('/auth/{platform}/redirect', [SocialAccountController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{platform}/callback', [SocialAccountController::class, 'callback'])->name('social.callback');
    Route::delete('/social-accounts/{account}', [SocialAccountController::class, 'destroy'])
        ->name('social.destroy')
        ->can('delete', 'account');

    // Phase 3 — Analytics
    Route::get('/analytics', [AnalyticsController::class, 'overview'])->name('analytics');
    Route::get('/analytics/per-konten', [AnalyticsController::class, 'perKonten'])->name('analytics.per-konten');
    Route::get('/analytics/posts/{postVersionId}', [AnalyticsController::class, 'contentDetail'])->name('analytics.content-detail');

    // Phase 3 — Analytics JSON (AJAX)
    Route::get('/api/v1/analytics/overview', [AnalyticsController::class, 'overviewData'])->name('api.analytics.overview');
    Route::get('/api/v1/analytics/posts', [AnalyticsController::class, 'postListData'])->name('api.analytics.posts');
    Route::post('/api/v1/analytics/sync', [AnalyticsController::class, 'syncAll'])->name('api.analytics.sync');
    Route::post('/api/v1/analytics/sync/{postVersionId}', [AnalyticsController::class, 'syncOne'])->name('api.analytics.sync-one');

    // Phase 3 — AI
    Route::post('/api/v1/ai/caption', [AIController::class, 'generateCaption'])->name('api.ai.caption');
    Route::post('/api/v1/ai/hashtags', [AIController::class, 'suggestHashtags'])->name('api.ai.hashtags');
    Route::get('/api/v1/ai/best-time/{platform}', [AIController::class, 'bestTime'])->name('api.ai.best-time');

    // Phase 4 — Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::post('/api/v1/reports', [ReportController::class, 'store'])->name('api.reports.store');
    Route::post('/api/v1/reports/preview', [ReportController::class, 'preview'])->name('api.reports.preview');
    Route::get('/api/v1/reports/{report}/download', [ReportController::class, 'download'])->name('api.reports.download');

    // Phase 4 — Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/api/v1/settings/profile', [SettingsController::class, 'updateProfile'])->name('api.settings.profile');
    Route::patch('/api/v1/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('api.settings.notifications');

    // Phase 4 — Notifications
    Route::get('/api/v1/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/api/v1/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('api.notifications.read-all');
    Route::patch('/api/v1/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('api.notifications.read');

    // Email verification
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->middleware('throttle:6,1')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
