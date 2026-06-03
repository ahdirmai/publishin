<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function create(Request $request): Response
    {
        $accounts = auth()->user()
            ->socialAccounts()
            ->where('is_active', true)
            ->get(['id', 'platform', 'username', 'display_name', 'avatar_url', 'follower_count', 'is_active', 'token_expires_at']);

        $prefilledDate = $request->query('date');

        return Inertia::render('Compose/Index', [
            'socialAccounts' => $accounts,
            'prefilledDate'  => $prefilledDate,
            'post'           => null,
        ]);
    }

    public function edit(Post $post): Response
    {
        abort_if($post->user_id !== auth()->id(), 403);

        $accounts = auth()->user()
            ->socialAccounts()
            ->where('is_active', true)
            ->get(['id', 'platform', 'username', 'display_name', 'avatar_url', 'follower_count', 'is_active', 'token_expires_at']);

        return Inertia::render('Compose/Index', [
            'post'           => $post->load('versions.socialAccount'),
            'socialAccounts' => $accounts,
            'prefilledDate'  => null,
        ]);
    }

    public function store(StorePostRequest $request, PostService $postService): RedirectResponse
    {
        $post = $postService->createPost($request->validated(), auth()->id());

        if ($request->input('status') === 'scheduled') {
            return redirect()->route('calendar')->with('success', 'Konten berhasil dijadwalkan!');
        }

        return redirect()->route('compose')->with('success', 'Draft disimpan.');
    }

    public function update(Request $request, Post $post, PostService $postService): RedirectResponse
    {
        abort_if($post->user_id !== auth()->id(), 403);
        abort_if($post->status !== 'draft', 422);

        $data = $request->validate([
            'platforms'    => ['required', 'array', 'min:1'],
            'platforms.*'  => ['string', 'in:instagram,facebook,tiktok,twitter,youtube'],
            'caption'      => ['nullable', 'string', 'max:2200'],
            'content_type' => ['nullable', 'string', 'in:feed,reel,story,video,carousel,thread,foto'],
            'status'       => ['required', 'string', 'in:draft,scheduled'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        $postService->updatePost($post, $data);

        return redirect()->back()->with('success', 'Draft diperbarui.');
    }

    public function destroy(Post $post, PostService $postService): RedirectResponse
    {
        abort_if($post->user_id !== auth()->id(), 403);

        $postService->deletePost($post);

        return redirect()->route('calendar')->with('success', 'Konten dihapus.');
    }
}
