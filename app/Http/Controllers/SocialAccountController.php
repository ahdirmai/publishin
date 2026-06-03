<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class SocialAccountController extends Controller
{
    /**
     * Redirect the user to the platform's OAuth authorization page.
     */
    public function redirect(Request $request, string $platform)
    {
        match ($platform) {
            'instagram' => null,
            'facebook'  => null,
            'tiktok'    => null,
            default     => abort(404),
        };

        if ($platform === 'instagram') {
            // Instagram Graph API requires OAuth through Facebook dialog
            // User must have IG Business/Creator account linked to a FB Page
            $url = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
                'client_id'     => config('services.facebook.client_id'),
                'redirect_uri'  => route('social.callback', 'instagram'),
                'scope'         => 'pages_show_list,pages_read_engagement,pages_manage_posts,instagram_basic,instagram_content_publish',
                'response_type' => 'code',
                'state'         => csrf_token(),
            ]);

            return redirect($url);
        }

        if ($platform === 'tiktok') {
            $codeVerifier  = bin2hex(random_bytes(32));
            $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
            $state         = \Illuminate\Support\Str::random(16);

            session([
                'tiktok_code_verifier' => $codeVerifier,
                'tiktok_state'         => $state,
            ]);

            $scopes = 'user.info.basic,user.info.profile,user.info.stats,video.list,video.upload,video.publish';

            $url = 'https://www.tiktok.com/v2/auth/authorize/?' . http_build_query([
                'client_key'            => config('services.tiktok.client_key'),
                'response_type'         => 'code',
                'scope'                 => $scopes,
                'redirect_uri'          => route('social.callback', 'tiktok'),
                'state'                 => $state,
                'code_challenge'        => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]);

            return redirect($url);
        }

        // Facebook
        $url = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
            'client_id'     => config('services.facebook.client_id'),
            'redirect_uri'  => route('social.callback', 'facebook'),
            'scope'         => 'pages_show_list,pages_read_engagement,pages_manage_posts,read_insights',
            'response_type' => 'code',
            'state'         => csrf_token(),
        ]);

        return redirect($url);
    }

    /**
     * Handle the OAuth callback from the platform.
     */
    public function callback(Request $request, string $platform)
    {
        // Basic validation
        $request->validate(['code' => 'required']);

        // CSRF / state check
        if ($platform === 'tiktok') {
            if ($request->query('state') !== session('tiktok_state')) {
                return redirect()->route('settings.index')->withErrors(['error' => 'State mismatch — coba lagi.']);
            }
        } elseif ($request->query('state') !== csrf_token()) {
            return redirect()->back()->withErrors(['error' => 'CSRF mismatch']);
        }

        if ($platform === 'instagram') {
            return $this->handleInstagramCallback($request);
        }

        if ($platform === 'facebook') {
            return $this->handleFacebookCallback($request);
        }

        if ($platform === 'tiktok') {
            return $this->handleTikTokCallback($request);
        }

        abort(404);
    }

    /**
     * Deactivate (disconnect) a social account.
     */
    public function destroy(Request $request, SocialAccount $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $account->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Platform berhasil diputuskan.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function handleInstagramCallback(Request $request)
    {
        try {
            // Exchange code for user access token via Graph API
            $tokenResponse = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id'     => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri'  => route('social.callback', 'instagram'),
                'code'          => $request->query('code'),
            ]);

            $tokenData = $tokenResponse->json();

            if (! isset($tokenData['access_token'])) {
                $detail = $tokenData['error']['message'] ?? json_encode($tokenData);
                throw new \RuntimeException('Gagal mendapatkan token: ' . $detail);
            }

            $userToken = $tokenData['access_token'];

            // Get FB Pages managed by user
            $pagesResponse = Http::get('https://graph.facebook.com/v19.0/me/accounts', [
                'access_token' => $userToken,
                'fields'       => 'id,name,access_token,instagram_business_account',
            ]);

            $pages = $pagesResponse->json('data', []);

            if (empty($pages)) {
                throw new \RuntimeException('Tidak ada Facebook Page ditemukan. Pastikan akun Instagram tipe Business/Creator dan sudah dihubungkan ke Facebook Page.');
            }

            $connected = 0;
            foreach ($pages as $page) {
                if (empty($page['instagram_business_account']['id'])) {
                    continue;
                }

                $igId        = $page['instagram_business_account']['id'];
                $pageToken   = $page['access_token'];

                // Get IG account details
                $igResponse = Http::get("https://graph.facebook.com/v19.0/{$igId}", [
                    'fields'       => 'id,username,name,followers_count',
                    'access_token' => $pageToken,
                ]);

                $ig = $igResponse->json();

                if (! isset($ig['id'])) {
                    continue;
                }

                SocialAccount::updateOrCreate(
                    [
                        'user_id'          => auth()->id(),
                        'platform'         => 'instagram',
                        'platform_user_id' => $ig['id'],
                    ],
                    [
                        'username'       => $ig['username'] ?? $ig['name'],
                        'display_name'   => $ig['name'] ?? $ig['username'],
                        'access_token'   => $pageToken,
                        'page_id'        => $page['id'],
                        'follower_count' => $ig['followers_count'] ?? 0,
                        'is_active'      => true,
                    ]
                );
                $connected++;
            }

            if ($connected === 0) {
                throw new \RuntimeException('Tidak ada akun Instagram Business/Creator yang terhubung ke Facebook Page Anda.');
            }

        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('settings.index')->with('success', 'Akun Instagram berhasil dihubungkan.');
    }

    private function handleFacebookCallback(Request $request)
    {
        try {
            // Exchange authorization code for a user access token
            $tokenResponse = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id'     => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri'  => route('social.callback', 'facebook'),
                'code'          => $request->query('code'),
            ]);

            $tokenData = $tokenResponse->json();

            if (! isset($tokenData['access_token'])) {
                throw new \RuntimeException('Failed to retrieve Facebook access token.');
            }

            // Retrieve the list of pages the user manages
            $pagesResponse = Http::get('https://graph.facebook.com/me/accounts', [
                'access_token' => $tokenData['access_token'],
            ]);

            $pages = $pagesResponse->json();

            if (empty($pages['data'])) {
                throw new \RuntimeException('No Facebook Pages found for this account.');
            }

            // Use the first page for simplicity
            $page = $pages['data'][0];

            // Create or update the SocialAccount record
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'facebook',
                    'platform_user_id' => $page['id'],
                ],
                [
                    'page_id'      => $page['id'],
                    'display_name' => $page['name'],
                    'username'     => $page['name'],
                    'access_token' => $page['access_token'],
                    'is_active'    => true,
                ]
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Halaman Facebook berhasil dihubungkan.');
    }

    private function handleTikTokCallback(Request $request)
    {
        // TikTok returns error param when user cancels
        if ($request->query('error')) {
            return redirect()->route('settings.index')
                ->withErrors(['error' => 'Koneksi TikTok dibatalkan: ' . $request->query('error_description', 'unknown')]);
        }

        try {
            \Log::info('TikTok callback', [
                'has_code'          => (bool) $request->query('code'),
                'state_match'       => $request->query('state') === session('tiktok_state'),
                'has_verifier'      => (bool) session('tiktok_code_verifier'),
                'auth_id'           => auth()->id(),
            ]);

            // Exchange code for access token (PKCE — include code_verifier)
            $tokenResponse = Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/token/', [
                'client_key'    => config('services.tiktok.client_key'),
                'client_secret' => config('services.tiktok.client_secret'),
                'code'          => $request->query('code'),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => route('social.callback', 'tiktok'),
                'code_verifier' => session('tiktok_code_verifier'),
            ]);

            $tokenData = $tokenResponse->json();

            \Log::info('TikTok token response', ['data' => $tokenData]);

            // TikTok returns token at root OR nested under 'data' depending on version
            $token = $tokenData['access_token'] ?? $tokenData['data']['access_token'] ?? null;

            if (! $token) {
                throw new \RuntimeException('Gagal mendapatkan TikTok access token: ' . json_encode($tokenData));
            }

            $accessToken  = $token;
            $refreshToken = $tokenData['refresh_token'] ?? $tokenData['data']['refresh_token'] ?? null;
            $expiresIn    = $tokenData['expires_in'] ?? $tokenData['data']['expires_in'] ?? 86400;
            $openId       = $tokenData['open_id'] ?? $tokenData['data']['open_id'];

            // Fetch user profile (optional — fallback to open_id if fails)
            $displayName    = $openId;
            $followerCount  = 0;

            try {
                $profileResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                ])->get('https://open.tiktokapis.com/v2/user/info/?fields=open_id,display_name,avatar_url,follower_count');

                $profileData = $profileResponse->json();
                \Log::info('TikTok profile response', ['data' => $profileData]);

                $user          = $profileData['data']['user'] ?? [];
                $displayName   = $user['display_name'] ?? $openId;
                $followerCount = $user['follower_count'] ?? 0;
            } catch (\Throwable $e) {
                \Log::warning('TikTok profile fetch failed, using open_id as fallback', ['error' => $e->getMessage()]);
            }

            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'tiktok',
                    'platform_user_id' => $openId,
                ],
                [
                    'username'         => $displayName,
                    'display_name'     => $displayName,
                    'access_token'     => $accessToken,
                    'refresh_token'    => $refreshToken,
                    'token_expires_at' => now()->addSeconds($expiresIn),
                    'follower_count'   => $followerCount,
                    'is_active'        => true,
                ]
            );

            \Log::info('TikTok account saved', ['open_id' => $openId, 'user_id' => auth()->id()]);

            session()->forget(['tiktok_code_verifier', 'tiktok_state']);

        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('settings.index')->with('success', 'Akun TikTok berhasil dihubungkan.');
    }
}
