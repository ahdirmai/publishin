<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function createPost(array $data, int $userId): Post
    {
        return DB::transaction(function () use ($data, $userId): Post {
            $caption = $data['caption'] ?? null;
            $title   = $caption ? mb_substr($caption, 0, 80) : 'Konten Baru';

            $post = Post::create([
                'user_id'      => $userId,
                'title'        => $title,
                'status'       => $data['status'],
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);

            foreach ($data['platforms'] as $platform) {
                $account = SocialAccount::where('user_id', $userId)
                    ->where('platform', $platform)
                    ->where('is_active', true)
                    ->first();

                if ($account) {
                    PostVersion::create([
                        'post_id'          => $post->id,
                        'social_account_id' => $account->id,
                        'caption'          => $caption,
                        'content_type'     => $data['content_type'] ?? 'feed',
                        'status'           => 'pending',
                        'platform_options' => $data['platform_options'] ?? null,
                    ]);
                }
            }

            return $post->load('versions');
        });
    }

    public function updatePost(Post $post, array $data): Post
    {
        if ($post->status !== 'draft') {
            throw new \Exception('Hanya post berstatus draft yang dapat diperbarui.');
        }

        return DB::transaction(function () use ($post, $data): Post {
            $caption = $data['caption'] ?? null;
            $title   = $caption ? mb_substr($caption, 0, 80) : 'Konten Baru';

            $post->update([
                'title'        => $title,
                'status'       => $data['status'],
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);

            PostVersion::where('post_id', $post->id)
                ->where('status', 'pending')
                ->delete();

            foreach ($data['platforms'] as $platform) {
                $account = SocialAccount::where('user_id', $post->user_id)
                    ->where('platform', $platform)
                    ->where('is_active', true)
                    ->first();

                if ($account) {
                    PostVersion::create([
                        'post_id'          => $post->id,
                        'social_account_id' => $account->id,
                        'caption'          => $caption,
                        'content_type'     => $data['content_type'] ?? 'feed',
                        'status'           => 'pending',
                        'platform_options' => $data['platform_options'] ?? null,
                    ]);
                }
            }

            return $post->refresh();
        });
    }

    public function saveDraft(array $data, int $userId): Post
    {
        $data['status'] = 'draft';

        return $this->createPost($data, $userId);
    }

    public function schedulePost(array $data, int $userId): Post
    {
        $data['status'] = 'scheduled';

        return $this->createPost($data, $userId);
    }

    public function deletePost(Post $post): void
    {
        PostVersion::where('post_id', $post->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $post->delete();
    }
}
