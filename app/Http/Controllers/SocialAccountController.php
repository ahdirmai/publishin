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
            default     => abort(404),
        };

        if ($platform === 'instagram') {
            $url = 'https://api.instagram.com/oauth/authorize?' . http_build_query([
                'client_id'     => config('services.instagram.client_id'),
                'redirect_uri'  => route('social.callback', 'instagram'),
                'scope'         => 'instagram_basic,instagram_content_publish,instagram_manage_insights',
                'response_type' => 'code',
                'state'         => csrf_token(),
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

        // CSRF check
        if ($request->query('state') !== csrf_token()) {
            return redirect()->back()->withErrors(['error' => 'CSRF mismatch']);
        }

        if ($platform === 'instagram') {
            return $this->handleInstagramCallback($request);
        }

        if ($platform === 'facebook') {
            return $this->handleFacebookCallback($request);
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
            // Exchange authorization code for an access token
            $tokenResponse = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id'     => config('services.instagram.client_id'),
                'client_secret' => config('services.instagram.client_secret'),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => route('social.callback', 'instagram'),
                'code'          => $request->query('code'),
            ]);

            $tokenData = $tokenResponse->json();

            if (! isset($tokenData['access_token'])) {
                throw new \RuntimeException('Failed to retrieve Instagram access token.');
            }

            // Fetch basic profile information
            $profileResponse = Http::get('https://graph.instagram.com/me', [
                'fields'       => 'id,username',
                'access_token' => $tokenData['access_token'],
            ]);

            $profile = $profileResponse->json();

            if (! isset($profile['id'])) {
                throw new \RuntimeException('Failed to retrieve Instagram profile.');
            }

            // Create or update the SocialAccount record
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'instagram',
                    'platform_user_id' => $profile['id'],
                ],
                [
                    'username'     => $profile['username'],
                    'display_name' => $profile['username'],
                    'access_token' => $tokenData['access_token'],
                    'is_active'    => true,
                ]
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Akun Instagram berhasil dihubungkan.');
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
}
