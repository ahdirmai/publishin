<?php

namespace App\Services;

use App\Models\SocialAccount;
use Saloon\Http\Connector;
use Saloon\Http\Request as SaloonRequest;
use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class TikTokService extends Connector
{
    public function __construct(protected SocialAccount $account)
    {
        $this->withTokenAuth($account->access_token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://open.tiktokapis.com';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept'       => 'application/json; charset=UTF-8',
            'Content-Type' => 'application/json; charset=UTF-8',
        ];
    }

    /**
     * Publish a video to TikTok using PULL method (TikTok fetches from URL).
     * Polls status up to $maxPolls times with $pollInterval seconds between.
     */
    public function publishVideo(string $videoUrl, string $caption, array $options = []): array
    {
        try {
            $isSandbox    = (bool) config('services.tiktok.sandbox', false);
            $privacyLevel = $isSandbox ? 'SELF_ONLY' : ($options['privacy_level'] ?? 'PUBLIC_TO_EVERYONE');

            $postInfo = [
                'title'                   => mb_substr($caption, 0, 2200),
                'privacy_level'           => $privacyLevel,
                'disable_duet'            => (bool) ($options['disable_duet'] ?? false),
                'disable_comment'         => (bool) ($options['disable_comment'] ?? false),
                'disable_stitch'          => (bool) ($options['disable_stitch'] ?? false),
                'video_cover_timestamp_ms'=> (int)  ($options['cover_timestamp_ms'] ?? 1000),
            ];

            $initRequest = new class ($videoUrl, $postInfo) extends SaloonRequest implements HasBody {
                use HasJsonBody;

                protected Method $method = Method::POST;

                public function __construct(
                    private string $videoUrl,
                    private array  $postInfo,
                ) {}

                public function resolveEndpoint(): string
                {
                    return '/v2/post/publish/video/init/';
                }

                protected function defaultBody(): array
                {
                    return [
                        'post_info'   => $this->postInfo,
                        'source_info' => [
                            'source'    => 'PULL_FROM_URL',
                            'video_url' => $this->videoUrl,
                        ],
                    ];
                }
            };

            $initResponse = $this->send($initRequest);
            $initData     = $initResponse->array();

            if (($initData['error']['code'] ?? '') !== 'ok') {
                $errMsg = $initData['error']['message'] ?? 'TikTok init failed';
                return ['error' => $errMsg];
            }

            $publishId = $initData['data']['publish_id'] ?? null;

            if (! $publishId) {
                return ['error' => 'TikTok: publish_id missing in init response'];
            }

            // Poll status — TikTok processes the video asynchronously
            $maxPolls     = (int) ($options['max_polls'] ?? 6);
            $pollInterval = (int) ($options['poll_interval'] ?? 10);

            for ($i = 0; $i < $maxPolls; $i++) {
                if ($i > 0) {
                    sleep($pollInterval);
                }

                $statusData = $this->getPublishStatus($publishId);

                $status = $statusData['data']['status'] ?? 'UNKNOWN';

                if ($status === 'PUBLISH_COMPLETE') {
                    $tiktokPostId = $statusData['data']['publicaly_available_post_id'][0]
                        ?? $publishId;

                    return ['id' => $tiktokPostId, 'publish_id' => $publishId];
                }

                if ($status === 'FAILED') {
                    $reason = $statusData['data']['fail_reason'] ?? 'Unknown failure';
                    return ['error' => "TikTok publish failed: {$reason}"];
                }

                // PROCESSING_UPLOAD or PROCESSING_DOWNLOAD — keep polling
            }

            // Still processing after max polls — store publish_id, mark as processing
            return ['id' => $publishId, 'publish_id' => $publishId, 'status' => 'processing'];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Fetch the publish status of a TikTok video upload.
     */
    public function getPublishStatus(string $publishId): array
    {
        try {
            $request = new class ($publishId) extends SaloonRequest implements HasBody {
                use HasJsonBody;

                protected Method $method = Method::POST;

                public function __construct(private string $publishId) {}

                public function resolveEndpoint(): string
                {
                    return '/v2/post/publish/status/fetch/';
                }

                protected function defaultBody(): array
                {
                    return ['publish_id' => $this->publishId];
                }
            };

            $response = $this->send($request);

            return $response->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Query the authenticated user's TikTok creator info.
     */
    public function getCreatorInfo(): array
    {
        try {
            $request = new class extends SaloonRequest implements HasBody {
                use HasJsonBody;

                protected Method $method = Method::POST;

                public function resolveEndpoint(): string
                {
                    return '/v2/post/publish/creator_info/query/';
                }

                protected function defaultBody(): array
                {
                    return [];
                }
            };

            $response = $this->send($request);

            return $response->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
