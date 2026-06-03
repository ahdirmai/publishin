<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('register page renders', function () {
    $this->get(route('register'))->assertStatus(200);
});

test('user can register with valid data', function () {
    $this->post(route('register.store'), [
        'name'                  => 'Test User',
        'email'                 => 'new@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
});

test('register fails with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->post(route('register.store'), [
        'name'                  => 'Another User',
        'email'                 => 'existing@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

test('register requires all fields', function () {
    $this->post(route('register.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('register requires password confirmation', function () {
    $this->post(route('register.store'), [
        'name'     => 'Test',
        'email'    => 'test@example.com',
        'password' => 'password123',
    ])->assertSessionHasErrors('password');
});
