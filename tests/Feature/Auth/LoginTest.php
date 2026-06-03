<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login page renders', function () {
    $this->get(route('login'))->assertStatus(200);
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->post(route('login.store'), [
        'email'    => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('login fails with wrong password', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->post(route('login.store'), [
        'email'    => 'test@example.com',
        'password' => 'wrong',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('login requires email and password', function () {
    $this->post(route('login.store'), [])
        ->assertSessionHasErrors(['email', 'password']);
});

test('authenticated user is redirected away from login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});
