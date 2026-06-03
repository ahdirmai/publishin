<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            'threads'   => null,
            'tiktok'    => null,
            default     => abort(404),
        };

        if ($platform === 'instagram') {
            // Instagram API with Instagram Login (new product — no Facebook account required)
            $state = Str::random(16);
            session(['instagram_state' => $state]);

            $url = 'https://api.instagram.com/oauth/authorize?' . http_build_query([
                'client_id'     => config('services.instagram.client_id'),
                'redirect_uri'  => route('social.callback', 'instagram'),
                'scope'         => 'instagram_business_basic,instagram_business_content_publish,instagram_business_manage_comments,instagram_business_manage_messages',
                'response_type' => 'code',
                'state'         => $state,
            ]);

            return redirect($url);
        }

        if ($platform === 'threads') {
            $state = Str::random(16);
            session(['threads_state' => $state]);

            $url = 'https://www.threads.net/oauth/authorize?' . http_build_query([
                'client_id'     => config('services.threads.client_id'),
                'redirect_uri'  => route('social.callback', 'threads'),
                'scope'         => 'threads_basic,threads_content_publish,threads_manage_insights,threads_manage_replies',
                'response_type' => 'code',
                'state'         => $state,
            ]);

            return redirect($url);
        }

        if ($platform === 'tiktok') {
            $codeVerifier  = bin2hex(random_bytes(32));
            $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
            $state         = Str::random(16);

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
    }

    /**
     * Handle the OAuth callback from the platform.
     */
    public function callback(Request $request, string $platform)
    {
        $request->validate(['code' => 'required']);

        // State validation
        if ($platform === 'tiktok') {
            if ($request->query('state') !== session('tiktok_state')) {
                return redirect()->route('settings.index')->withErrors(['error' => 'State mismatch — coba lagi.']);
            }
        } elseif ($platform === 'instagram') {
            if ($request->query('state') !== session('instagram_state')) {
                return redirect()->route('settings.index')->withErrors(['error' => 'State mismatch — coba lagi.']);
            }
        } elseif ($platform === 'threads') {
            if ($request->query('state') !== session('threads_state')) {
                return redirect()->route('settings.index')->withErrors(['error' => 'State mismatch — coba lagi.']);
            }
        }

        return match ($platform) {
            'instagram' => $this->handleInstagramCallback($request),
            'threads'   => $this->handleThreadsCallback($request),
            'tiktok'    => $this->handleTikTokCallback($request),
            default     => abort(404),
        };
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
            // Step 1 — exchange code for short-lived token
            $tokenResponse = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id'     => config('services.instagram.client_id'),
                'client_secret' => config('services.instagram.client_secret'),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => route('social.callback', 'instagram'),
                'code'          => $request->query('code'),
            ]);

            $tokenData = $tokenResponse->json();
            \Log::info('Instagram token response', ['data' => $tokenData]);

            if (! isset($tokenData['access_token'])) {
                throw new \RuntimeException('Gagal mendapatkan token Instagram: ' . json_encode($tokenData));
            }

            $shortToken = $tokenData['access_token'];
            $igId       = $tokenData['user_id'];

            // Step 2 — exchange for long-lived token (60 days)
            $longTokenResponse = Http::get('https://graph.instagram.com/access_token', [
                'grant_type'    => 'ig_exchange_token',
                'client_id'     => config('services.instagram.client_id'),
                'client_secret' => config('services.instagram.client_secret'),
                'access_token'  => $shortToken,
            ]);

            $longTokenData = $longTokenResponse->json();
            \Log::info('Instagram long-lived token response', ['data' => $longTokenData]);

            $accessToken = $longTokenData['access_token'] ?? $shortToken;
            $expiresIn   = $longTokenData['expires_in'] ?? 5184000;

            // Step 3 — get user profile
            $profileResponse = Http::get("https://graph.instagram.com/v21.0/{$igId}", [
                'fields'       => 'id,username,name,profile_picture_url,followers_count,biography',
                'access_token' => $accessToken,
            ]);

            $profile = $profileResponse->json();
            \Log::info('Instagram profile response', ['data' => $profile]);

            if (! isset($profile['id'])) {
                throw new \RuntimeException('Gagal mengambil profil Instagram: ' . json_encode($profile));
            }

            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'instagram',
                    'platform_user_id' => $profile['id'],
                ],
                [
                    'username'         => $profile['username'] ?? $profile['name'] ?? $igId,
                    'display_name'     => $profile['name'] ?? $profile['username'] ?? $igId,
                    'access_token'     => $accessToken,
                    'token_expires_at' => now()->addSeconds($expiresIn),
                    'follower_count'   => $profile['followers_count'] ?? 0,
                    'is_active'        => true,
                ]
            );

            session()->forget('instagram_state');

        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('settings.index')->with('success', 'Akun Instagram berhasil dihubungkan.');
    }

    private function handleThreadsCallback(Request $request)
    {
        if ($request->query('error')) {
            return redirect()->route('settings.index')
                ->withErrors(['error' => 'Koneksi Threads dibatalkan: ' . $request->query('error_description', 'unknown')]);
        }

        try {
            // Step 1 — exchange code for short-lived token
            $tokenResponse = Http::asForm()->post('https://graph.threads.net/oauth/access_token', [
                'client_id'     => config('services.threads.client_id'),
                'client_secret' => config('services.threads.client_secret'),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => route('social.callback', 'threads'),
                'code'          => $request->query('code'),
            ]);

            $tokenData = $tokenResponse->json();
            \Log::info('Threads token response', ['data' => $tokenData]);

            if (! isset($tokenData['access_token'])) {
                throw new \RuntimeException('Gagal mendapatkan token Threads: ' . json_encode($tokenData));
            }

            $shortToken = $tokenData['access_token'];
            $userId     = $tokenData['user_id'];

            // Step 2 — exchange for long-lived token (60 days)
            $longTokenResponse = Http::get('https://graph.threads.net/access_token', [
                'grant_type'    => 'th_exchange_token',
                'client_id'     => config('services.threads.client_id'),
                'client_secret' => config('services.threads.client_secret'),
                'access_token'  => $shortToken,
            ]);

            $longTokenData = $longTokenResponse->json();
            $accessToken   = $longTokenData['access_token'] ?? $shortToken;
            $expiresIn     = $longTokenData['expires_in'] ?? 5184000;

            // Step 3 — get user profile
            $profileResponse = Http::get("https://graph.threads.net/v1.0/{$userId}", [
                'fields'       => 'id,username,name,threads_profile_picture_url,threads_biography,followers_count',
                'access_token' => $accessToken,
            ]);

            $profile = $profileResponse->json();
            \Log::info('Threads profile response', ['data' => $profile]);

            if (! isset($profile['id'])) {
                throw new \RuntimeException('Gagal mengambil profil Threads: ' . json_encode($profile));
            }

            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'threads',
                    'platform_user_id' => $profile['id'],
                ],
                [
                    'username'         => $profile['username'] ?? $userId,
                    'display_name'     => $profile['name'] ?? $profile['username'] ?? $userId,
                    'access_token'     => $accessToken,
                    'token_expires_at' => now()->addSeconds($expiresIn),
                    'follower_count'   => $profile['followers_count'] ?? 0,
                    'is_active'        => true,
                ]
            );

            session()->forget('threads_state');

        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('settings.index')->with('success', 'Akun Threads berhasil dihubungkan.');
    }

    private function handleTikTokCallback(Request $request)
    {
        if ($request->query('error')) {
            return redirect()->route('settings.index')
                ->withErrors(['error' => 'Koneksi TikTok dibatalkan: ' . $request->query('error_description', 'unknown')]);
        }

        try {
            \Log::info('TikTok callback', [
                'has_code'     => (bool) $request->query('code'),
                'state_match'  => $request->query('state') === session('tiktok_state'),
                'has_verifier' => (bool) session('tiktok_code_verifier'),
                'auth_id'      => auth()->id(),
            ]);

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

            $token = $tokenData['access_token'] ?? $tokenData['data']['access_token'] ?? null;

            if (! $token) {
                throw new \RuntimeException('Gagal mendapatkan TikTok access token: ' . json_encode($tokenData));
            }

            $accessToken  = $token;
            $refreshToken = $tokenData['refresh_token'] ?? $tokenData['data']['refresh_token'] ?? null;
            $expiresIn    = $tokenData['expires_in'] ?? $tokenData['data']['expires_in'] ?? 86400;
            $openId       = $tokenData['open_id'] ?? $tokenData['data']['open_id'];

            $displayName   = $openId;
            $followerCount = 0;

            try {
                $profileResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                ])->get('https://open.tiktokapis.com/v2/user/info/?fields=open_id,display_name,avatar_url,follower_count');

                $profileData   = $profileResponse->json();
                $user          = $profileData['data']['user'] ?? [];
                $displayName   = $user['display_name'] ?? $openId;
                $followerCount = $user['follower_count'] ?? 0;
            } catch (\Throwable $e) {
                \Log::warning('TikTok profile fetch failed', ['error' => $e->getMessage()]);
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

            session()->forget(['tiktok_code_verifier', 'tiktok_state']);

        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('settings.index')->with('success', 'Akun TikTok berhasil dihubungkan.');
    }
}
