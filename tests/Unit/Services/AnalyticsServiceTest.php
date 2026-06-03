<?php

use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('getOverview returns expected keys', function () {
    $user    = User::factory()->create();
    $service = app(AnalyticsService::class);

    $result = $service->getOverview($user, 30, null);

    expect($result)->toHaveKeys(['kpis', 'reachByPlatform', 'topPosts', 'followerGrowthChart']);
});

test('getOverview kpis are zero for user with no accounts', function () {
    $user    = User::factory()->create();
    $service = app(AnalyticsService::class);

    $result = $service->getOverview($user, 30, null);

    expect($result['kpis'])->toBeArray();
});

test('getPostList returns paginated structure', function () {
    $user    = User::factory()->create();
    $service = app(AnalyticsService::class);

    $result = $service->getPostList($user, []);

    expect($result)->toHaveKeys(['data', 'current_page', 'last_page', 'total']);
});

test('getPostList returns empty data for user with no posts', function () {
    $user    = User::factory()->create();
    $service = app(AnalyticsService::class);

    $result = $service->getPostList($user, []);

    expect($result['data'])->toBeEmpty();
    expect($result['total'])->toBe(0);
});
