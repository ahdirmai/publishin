<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('analytics overview requires auth', function () {
    $this->get(route('analytics'))->assertRedirect(route('login'));
});

test('analytics overview page renders for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('analytics'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Analytics/Overview')
            ->has('data')
            ->has('filters')
            ->has('available_days')
        );
});

test('analytics per-konten page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('analytics.per-konten'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Analytics/PerKonten')
            ->has('data')
        );
});

test('analytics overview accepts days filter', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('analytics') . '?days=7')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->where('filters.days', 7)
        );
});

test('sync all returns json response', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('api.analytics.sync'))
        ->assertStatus(200)
        ->assertJsonStructure(['message', 'imported', 'synced', 'failed']);
});

test('sync all requires auth', function () {
    $this->postJson(route('api.analytics.sync'))->assertStatus(401);
});
