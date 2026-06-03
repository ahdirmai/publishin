<?php

namespace App\Services;

use App\Models\SocialAccount;
use Saloon\Http\Connector;
use Saloon\Http\Request as SaloonRequest;
use Saloon\Enums\Method;

class FacebookService extends Connector
{
    public function __construct(protected SocialAccount $account)
    {
        $this->withTokenAuth($account->access_token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://graph.facebook.com/v19.0';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get information about the connected Facebook Page.
     */
    public function getPageInfo(): array
    {
        try {
            $pageId  = $this->account->page_id;
            $request = new class ($pageId) extends SaloonRequest {
                protected Method $method = Method::GET;

                public function __construct(private string $pageId) {}

                public function resolveEndpoint(): string
                {
                    return "/{$this->pageId}";
                }

                protected function defaultQuery(): array
                {
                    return [
                        'fields' => 'id,name,followers_count,fan_count',
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
     * Publish a post (with optional image) to the connected Facebook Page.
     */
    public function publishPost(string $message, ?string $imageUrl = null): array
    {
        try {
            $pageId = $this->account->page_id;

            if ($imageUrl !== null) {
                // Photo post
                $request = new class ($pageId, $message, $imageUrl) extends SaloonRequest {
                    protected Method $method = Method::POST;

                    public function __construct(
                        private string $pageId,
                        private string $message,
                        private string $imageUrl,
                    ) {}

                    public function resolveEndpoint(): string
                    {
                        return "/{$this->pageId}/photos";
                    }

                    protected function defaultBody(): array
                    {
                        return [
                            'url'       => $this->imageUrl,
                            'message'   => $this->message,
                            'published' => true,
                        ];
                    }
                };
            } else {
                // Text / link post
                $request = new class ($pageId, $message) extends SaloonRequest {
                    protected Method $method = Method::POST;

                    public function __construct(
                        private string $pageId,
                        private string $message,
                    ) {}

                    public function resolveEndpoint(): string
                    {
                        return "/{$this->pageId}/feed";
                    }

                    protected function defaultBody(): array
                    {
                        return [
                            'message' => $this->message,
                        ];
                    }
                };
            }

            $response = $this->send($request);

            return $response->array();
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get insights for a specific post.
     */
    public function getPostInsights(string $postId): array
    {
        try {
            $request = new class ($postId) extends SaloonRequest {
                protected Method $method = Method::GET;

                public function __construct(private string $postId) {}

                public function resolveEndpoint(): string
                {
                    return "/{$this->postId}/insights";
                }

                protected function defaultQuery(): array
                {
                    return [
                        'metric' => 'post_impressions,post_reach,post_engaged_users',
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
