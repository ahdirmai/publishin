<?php

use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('compose page requires auth', function () {
    $this->get(route('compose'))->assertRedirect(route('login'));
});

test('compose page renders for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('compose'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('Compose/Index'));
});

test('store draft requires at least one platform', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'caption' => 'Test caption',
            'status'  => 'draft',
        ])
        ->assertSessionHasErrors('platforms');
});

test('store draft succeeds with valid data', function () {
    $user = User::factory()->create();
    SocialAccount::factory()->for($user)->create(['platform' => 'tiktok', 'is_active' => true]);

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'platforms'    => ['tiktok'],
            'caption'      => 'My test post',
            'status'       => 'draft',
            'content_type' => 'video',
        ])
        ->assertRedirect(route('compose'));

    $this->assertDatabaseHas('posts', ['user_id' => $user->id]);
});

test('scheduled post requires scheduled_at', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'platforms' => ['tiktok'],
            'caption'   => 'Scheduled post',
            'status'    => 'scheduled',
        ])
        ->assertSessionHasErrors('scheduled_at');
});

test('destroy post owned by user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect();

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

test('cannot destroy post owned by other user', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();
    $post  = Post::factory()->for($other)->create();

    $this->actingAs($user)
        ->delete(route('posts.destroy', $post))
        ->assertStatus(403);
});
