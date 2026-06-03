<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostVersion;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsSyncService
{
    public function syncForUser(User $user): array
    {
        $imported = 0;
        $synced   = 0;
        $failed   = 0;
        $errors   = [];

        // Step 1 — import posts from each connected platform
        $accounts = SocialAccount::where('user_id', $user->id)->where('is_active', true)->get();

        foreach ($accounts as $account) {
            try {
                $count = match ($account->platform) {
                    'tiktok'    => $this->importTikTokPosts($user, $account),
                    'instagram' => $this->importInstagramPosts($user, $account),
                    'facebook'  => $this->importFacebookPosts($user, $account),
                    default     => 0,
                };
                $imported += $count;
            } catch (\Throwable $e) {
                $errors[] = "Import {$account->platform}: " . $e->getMessage();
                Log::warning('Analytics import failed', ['platform' => $account->platform, 'error' => $e->getMessage()]);
            }
        }

        // Step 2 — sync analytics for all published post_versions
        $versions = PostVersion::query()
            ->with('socialAccount')
            ->whereHas('post', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'published')
            ->whereNotNull('platform_post_id')
            ->where('published_at', '>=', now()->subDays(90))
            ->get();

        foreach ($versions as $pv) {
            try {
                $this->syncPostVersion($pv);
                $synced++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "PV#{$pv->id}: " . $e->getMessage();
                Log::warning('Analytics sync failed', ['post_version_id' => $pv->id, 'error' => $e->getMessage()]);
            }
        }

        return compact('imported', 'synced', 'failed', 'errors');
    }

    public function syncPostVersion(PostVersion $pv): void
    {
        $account = $pv->socialAccount;

        if (! $account || ! $account->is_active) {
            throw new \RuntimeException('Social account not found or inactive.');
        }

        if (! $pv->platform_post_id) {
            throw new \RuntimeException('No platform_post_id stored.');
        }

        $metrics = match ($account->platform) {
            'instagram' => $this->fetchInstagramMetrics($pv->platform_post_id, $account->access_token),
            'facebook'  => $this->fetchFacebookMetrics($pv->platform_post_id, $account->access_token),
            'tiktok'    => $this->fetchTikTokMetrics($pv->platform_post_id, $account->access_token),
            default     => throw new \RuntimeException("Platform {$account->platform} sync not supported."),
        };

        $this->saveMetrics($pv, $metrics);
    }

    // -------------------------------------------------------------------------
    // Import helpers — pull existing posts from platform into DB
    // -------------------------------------------------------------------------

    private function importTikTokPosts(User $user, SocialAccount $account): int
    {
        $token    = $account->access_token;
        $imported = 0;
        $cursor   = null;

        do {
            $fields = 'id,title,cover_image_url,embed_link,like_count,comment_count,share_count,view_count,create_time,duration';
            $body   = ['max_count' => 20];
            if ($cursor) {
                $body['cursor'] = $cursor;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ])->post('https://open.tiktokapis.com/v2/video/list/?fields=' . $fields, $body);

            $data = $response->json();

            Log::info('TikTok video list', ['response' => $data]);

            if (isset($data['error']['code']) && $data['error']['code'] !== 'ok') {
                throw new \RuntimeException('TikTok list: ' . ($data['error']['message'] ?? json_encode($data['error'])));
            }

            $videos  = $data['data']['videos'] ?? [];
            $hasMore = $data['data']['has_more'] ?? false;
            $cursor  = $data['data']['cursor'] ?? null;

            foreach ($videos as $video) {
                $videoId     = $video['id'];
                $rawCaption  = trim($video['title'] ?? '');
                $caption     = $rawCaption ?: ('TikTok video ' . $videoId);
                $title       = mb_substr($caption, 0, 255);
                $publishedAt = isset($video['create_time'])
                    ? Carbon::createFromTimestamp($video['create_time'])
                    : now();

                $existing = PostVersion::where('social_account_id', $account->id)
                    ->where('platform_post_id', $videoId)
                    ->first();

                $post = $existing
                    ? $existing->post
                    : Post::create([
                        'user_id'      => $user->id,
                        'title'        => $title,
                        'status'       => 'published',
                        'published_at' => $publishedAt,
                    ]);

                $reach    = (int) ($video['view_count'] ?? 0);
                $likes    = (int) ($video['like_count'] ?? 0);
                $comments = (int) ($video['comment_count'] ?? 0);
                $shares   = (int) ($video['share_count'] ?? 0);
                $er = $reach > 0 ? round(($likes + $comments + $shares) / $reach * 100, 4) : 0;

                $pv = PostVersion::updateOrCreate(
                    [
                        'social_account_id' => $account->id,
                        'platform_post_id'  => $videoId,
                    ],
                    [
                        'post_id'      => $post->id,
                        'caption'      => $caption,
                        'content_type' => 'video',
                        'status'       => 'published',
                        'published_at' => $publishedAt,
                        'post_url'     => $video['embed_link'] ?? null,
                        'analytics_sum_reach'           => $reach,
                        'analytics_sum_impressions'     => $reach,
                        'analytics_sum_likes'           => $likes,
                        'analytics_sum_comments'        => $comments,
                        'analytics_sum_shares'          => $shares,
                        'analytics_sum_saves'           => 0,
                        'analytics_avg_engagement_rate' => $er,
                        'analytics_fetched_at'          => now(),
                    ]
                );

                // Download thumbnail via Spatie (skip if already has media)
                $coverUrl = $video['cover_image_url'] ?? null;
                if ($coverUrl && ! $pv->hasMedia('post_media')) {
                    try {
                        $pv->addMediaFromUrl($coverUrl)
                            ->toMediaCollection('post_media');
                    } catch (\Throwable $e) {
                        Log::warning('TikTok thumbnail download failed', [
                            'video_id' => $videoId, 'error' => $e->getMessage(),
                        ]);
                    }
                }

                $imported++;
            }

        } while ($hasMore && $cursor);

        return $imported;
    }

    private function importInstagramPosts(User $user, SocialAccount $account): int
    {
        $token    = $account->access_token;
        $igId     = $account->platform_user_id;
        $imported = 0;

        $response = Http::get("https://graph.facebook.com/v19.0/{$igId}/media", [
            'fields'       => 'id,caption,media_type,timestamp,thumbnail_url,permalink',
            'access_token' => $token,
            'limit'        => 50,
        ]);

        $data  = $response->json();

        if (isset($data['error'])) {
            throw new \RuntimeException('IG media list: ' . ($data['error']['message'] ?? json_encode($data['error'])));
        }

        foreach ($data['data'] ?? [] as $media) {
            $mediaId     = $media['id'];
            $fullCaption = trim($media['caption'] ?? '');
            $caption     = $fullCaption ?: ('IG post ' . $mediaId);
            $title       = mb_substr($caption, 0, 255);
            $publishedAt = isset($media['timestamp']) ? Carbon::parse($media['timestamp']) : now();
            $type        = strtolower($media['media_type'] ?? 'photo');
            $contentType = match ($type) {
                'video'          => 'video',
                'carousel_album' => 'carousel',
                default          => 'photo',
            };

            $existing = PostVersion::where('social_account_id', $account->id)
                ->where('platform_post_id', $mediaId)->first();

            $post = $existing
                ? $existing->post
                : Post::create([
                    'user_id'      => $user->id,
                    'title'        => $title,
                    'status'       => 'published',
                    'published_at' => $publishedAt,
                ]);

            $pv = PostVersion::updateOrCreate(
                ['social_account_id' => $account->id, 'platform_post_id' => $mediaId],
                [
                    'post_id'      => $post->id,
                    'caption'      => $caption,
                    'content_type' => $contentType,
                    'status'       => 'published',
                    'published_at' => $publishedAt,
                    'post_url'     => $media['permalink'] ?? null,
                ]
            );

            // Download thumbnail via Spatie (video has thumbnail_url field, photo use media_url)
            $thumbUrl = $media['thumbnail_url'] ?? null;
            if ($thumbUrl && ! $pv->hasMedia('post_media')) {
                try {
                    $pv->addMediaFromUrl($thumbUrl)->toMediaCollection('post_media');
                } catch (\Throwable $e) {
                    Log::warning('IG thumbnail download failed', ['media_id' => $mediaId, 'error' => $e->getMessage()]);
                }
            }

            $imported++;
        }

        return $imported;
    }

    private function importFacebookPosts(User $user, SocialAccount $account): int
    {
        $token    = $account->access_token;
        $pageId   = $account->page_id ?? $account->platform_user_id;
        $imported = 0;

        $response = Http::get("https://graph.facebook.com/v19.0/{$pageId}/posts", [
            'fields'       => 'id,message,created_time,permalink_url',
            'access_token' => $token,
            'limit'        => 50,
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \RuntimeException('FB posts list: ' . ($data['error']['message'] ?? json_encode($data['error'])));
        }

        foreach ($data['data'] ?? [] as $fbPost) {
            $postId      = $fbPost['id'];
            $message     = mb_substr(trim($fbPost['message'] ?? ''), 0, 255) ?: ('FB post ' . $postId);
            $publishedAt = isset($fbPost['created_time']) ? Carbon::parse($fbPost['created_time']) : now();

            $existing = PostVersion::where('social_account_id', $account->id)
                ->where('platform_post_id', $postId)->first();

            $post = $existing
                ? $existing->post
                : Post::create([
                    'user_id'      => $user->id,
                    'title'        => $message,
                    'status'       => 'published',
                    'published_at' => $publishedAt,
                ]);

            PostVersion::updateOrCreate(
                ['social_account_id' => $account->id, 'platform_post_id' => $postId],
                [
                    'post_id'      => $post->id,
                    'content_type' => 'photo',
                    'status'       => 'published',
                    'published_at' => $publishedAt,
                    'post_url'     => $fbPost['permalink_url'] ?? null,
                ]
            );

            $imported++;
        }

        return $imported;
    }

    // -------------------------------------------------------------------------
    // Per-post analytics fetch
    // -------------------------------------------------------------------------

    private function fetchInstagramMetrics(string $mediaId, string $token): array
    {
        $response = Http::get("https://graph.facebook.com/v19.0/{$mediaId}/insights", [
            'metric'       => 'reach,impressions,likes,comments,shares,saved',
            'access_token' => $token,
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \RuntimeException('IG API: ' . ($data['error']['message'] ?? json_encode($data['error'])));
        }

        $map = [];
        foreach ($data['data'] ?? [] as $item) {
            $map[$item['name']] = $item['values'][0]['value'] ?? 0;
        }

        return [
            'reach'       => $map['reach'] ?? 0,
            'impressions' => $map['impressions'] ?? 0,
            'likes'       => $map['likes'] ?? 0,
            'comments'    => $map['comments'] ?? 0,
            'shares'      => $map['shares'] ?? 0,
            'saves'       => $map['saved'] ?? 0,
        ];
    }

    private function fetchFacebookMetrics(string $postId, string $token): array
    {
        $response = Http::get("https://graph.facebook.com/v19.0/{$postId}/insights", [
            'metric'       => 'post_impressions_unique,post_impressions,post_reactions_like_total,post_comments,post_shares',
            'access_token' => $token,
        ]);

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \RuntimeException('FB API: ' . ($data['error']['message'] ?? json_encode($data['error'])));
        }

        $map = [];
        foreach ($data['data'] ?? [] as $item) {
            $map[$item['name']] = $item['values'][0]['value'] ?? 0;
        }

        return [
            'reach'       => $map['post_impressions_unique'] ?? 0,
            'impressions' => $map['post_impressions'] ?? 0,
            'likes'       => $map['post_reactions_like_total'] ?? 0,
            'comments'    => $map['post_comments'] ?? 0,
            'shares'      => $map['post_shares'] ?? 0,
            'saves'       => 0,
        ];
    }

    private function fetchTikTokMetrics(string $videoId, string $token): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type'  => 'application/json',
        ])->post('https://open.tiktokapis.com/v2/video/query/', [
            'filters' => ['video_ids' => [$videoId]],
            'fields'  => 'id,like_count,comment_count,share_count,view_count',
        ]);

        $data = $response->json();

        if (isset($data['error']['code']) && $data['error']['code'] !== 'ok') {
            throw new \RuntimeException('TikTok API: ' . ($data['error']['message'] ?? json_encode($data['error'])));
        }

        $video = $data['data']['videos'][0] ?? [];

        return [
            'reach'       => $video['view_count'] ?? 0,
            'impressions' => $video['view_count'] ?? 0,
            'likes'       => $video['like_count'] ?? 0,
            'comments'    => $video['comment_count'] ?? 0,
            'shares'      => $video['share_count'] ?? 0,
            'saves'       => 0,
        ];
    }

    private function saveMetrics(PostVersion $pv, array $metrics): void
    {
        $reach    = $metrics['reach'] ?? 0;
        $likes    = $metrics['likes'] ?? 0;
        $comments = $metrics['comments'] ?? 0;
        $shares   = $metrics['shares'] ?? 0;
        $saves    = $metrics['saves'] ?? 0;

        $engagements = $likes + $comments + $shares + $saves;
        $er = $reach > 0 ? round($engagements / $reach * 100, 4) : 0;

        $pv->update([
            'analytics_sum_reach'           => $reach,
            'analytics_sum_impressions'     => $metrics['impressions'] ?? 0,
            'analytics_sum_likes'           => $likes,
            'analytics_sum_comments'        => $comments,
            'analytics_sum_shares'          => $shares,
            'analytics_sum_saves'           => $saves,
            'analytics_avg_engagement_rate' => $er,
            'analytics_fetched_at'          => now(),
        ]);
    }
}
