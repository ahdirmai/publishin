<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('settings page requires auth', function () {
    $this->get(route('settings.index'))->assertRedirect(route('login'));
});

test('settings page renders for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('settings.index'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Settings/Index')
            ->has('user')
            ->has('accounts')
            ->has('notifications')
        );
});

test('update profile with valid data', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

    $this->actingAs($user)
        ->patchJson(route('api.settings.profile'), [
            'name'     => 'New Name',
            'email'    => 'new@example.com',
            'timezone' => 'Asia/Jakarta',
        ])
        ->assertStatus(200)
        ->assertJson(['message' => 'Profil berhasil disimpan.']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
});

test('profile update rejects duplicate email', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user)
        ->patchJson(route('api.settings.profile'), [
            'name'     => 'Test',
            'email'    => 'taken@example.com',
            'timezone' => 'Asia/Jakarta',
        ])
        ->assertStatus(422);
});

test('update notifications saves all toggles', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patchJson(route('api.settings.notifications'), [
            'email_weekly_summary'   => true,
            'push_post_published'    => false,
            'push_mentions'          => true,
            'email_monthly_report'   => false,
            'push_schedule_reminder' => true,
        ])
        ->assertStatus(200)
        ->assertJson(['message' => 'Pengaturan notifikasi disimpan.']);

    $this->assertDatabaseHas('notification_settings', [
        'user_id'              => $user->id,
        'email_weekly_summary' => 1,
        'push_post_published'  => 0,
    ]);
});
