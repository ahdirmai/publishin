<?php

namespace App\Services;

use App\Models\SocialAccount;
use Saloon\Http\Connector;
use Saloon\Http\Request as SaloonRequest;
use Saloon\Enums\Method;

class InstagramService extends Connector
{
    public function __construct(protected SocialAccount $account)
    {
        $this->withTokenAuth($account->access_token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://graph.instagram.com/v19.0';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get the authenticated user's Instagram profile.
     */
    public function getProfile(): array
    {
        try {
            $request = new class extends SaloonRequest {
                protected Method $method = Method::GET;

                public function resolveEndpoint(): string
                {
                    return '/me';
                }

                protected function defaultQuery(): array
                {
                    return [
                        'fields' => 'id,username,followers_count,media_count',
                    ];
                }
            };

            $response = $this->send($request);

            return $response->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Publish a single image post to Instagram.
     * Step 1: Create a media container.
     * Step 2: Publish the container.
     */
    public function publishImagePost(string $imageUrl, string $caption): array
    {
        try {
            // Step 1 — create the media container
            $createRequest = new class ($imageUrl, $caption) extends SaloonRequest {
                protected Method $method = Method::POST;

                public function __construct(
                    private string $imageUrl,
                    private string $caption,
                ) {}

                public function resolveEndpoint(): string
                {
                    return '/me/media';
                }

                protected function defaultBody(): array
                {
                    return [
                        'image_url'  => $this->imageUrl,
                        'caption'    => $this->caption,
                        'media_type' => 'IMAGE',
                    ];
                }
            };

            $createResponse = $this->send($createRequest);
            $createData     = $createResponse->array();

            // Step 2 — publish the container
            $publishRequest = new class ($createData['id']) extends SaloonRequest {
                protected Method $method = Method::POST;

                public function __construct(private string $creationId) {}

                public function resolveEndpoint(): string
                {
                    return '/me/media_publish';
                }

                protected function defaultBody(): array
                {
                    return [
                        'creation_id' => $this->creationId,
                    ];
                }
            };

            $publishResponse = $this->send($publishRequest);

            return $publishResponse->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get insights for a specific media object.
     */
    public function getMediaInsights(string $mediaId): array
    {
        try {
            $request = new class ($mediaId) extends SaloonRequest {
                protected Method $method = Method::GET;

                public function __construct(private string $mediaId) {}

                public function resolveEndpoint(): string
                {
                    return "/{$this->mediaId}/insights";
                }

                protected function defaultQuery(): array
                {
                    return [
                        'metric' => 'impressions,reach,likes_count,comments_count,shares,saved',
                    ];
                }
            };

            $response = $this->send($request);

            return $response->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
