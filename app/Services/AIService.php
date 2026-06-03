<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-6';
    private string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
    }

    public function generateCaption(User $user, array $params): array
    {
        $this->enforceRateLimit($user);

        $platform = $params['platform'] ?? 'instagram';
        $topic    = $params['topic'] ?? '';
        $tone     = $params['tone'] ?? 'santai';
        $keywords = $params['keywords'] ?? [];

        $platformGuide = match ($platform) {
            'instagram' => 'Instagram (max 2200 karakter, bisa emoji, hashtag di akhir)',
            'tiktok'    => 'TikTok (singkat, energik, hook di kalimat pertama, max 500 karakter)',
            'facebook'  => 'Facebook (lebih panjang oke, ajak interaksi/komentar)',
            'twitter'   => 'Twitter/X (max 280 karakter, padat dan to-the-point)',
            default     => $platform,
        };

        $toneGuide = match ($tone) {
            'formal'       => 'formal dan profesional',
            'santai'       => 'santai dan conversational, seperti ngobrol dengan teman',
            'gen-z'        => 'Gen-Z: singkat, pakai bahasa gaul Indonesia yang relevan',
            'profesional'  => 'profesional tapi tetap hangat dan approachable',
            default        => 'santai',
        };

        $keywordStr = implode(', ', $keywords);
        $prompt = <<<PROMPT
Kamu adalah copywriter media sosial Indonesia yang ahli membuat caption viral dan engaging.

Buat caption untuk platform {$platformGuide}.

Topik/konteks: {$topic}
Tone: {$toneGuide}
Keywords untuk disertakan (bila relevan): {$keywordStr}

Ketentuan:
- Tulis dalam Bahasa Indonesia yang natural dan authentic
- Mulai dengan hook yang kuat di kalimat pertama
- Sesuaikan format dan panjang dengan platform
- Jangan terjemahan kaku
- Sertakan 1-2 call to action yang natural
- Untuk Instagram, tambahkan 5-10 hashtag relevan di akhir (pisah dengan baris baru)

Balas HANYA dengan caption siap pakai, tanpa penjelasan tambahan.
PROMPT;

        try {
            $response = $this->callClaude($prompt, 600);
            $caption  = trim($response);

            return [
                'caption'  => $caption,
                'platform' => $platform,
                'tone'     => $tone,
            ];
        } catch (\Throwable $e) {
            Log::error('AIService::generateCaption failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function suggestHashtags(User $user, array $params): array
    {
        $this->enforceRateLimit($user);

        $caption  = $params['caption'] ?? '';
        $platform = $params['platform'] ?? 'instagram';
        $niche    = $params['niche'] ?? '';

        $prompt = <<<PROMPT
Kamu adalah pakar hashtag media sosial Indonesia.

Berdasarkan caption dan niche berikut, berikan 15 hashtag terbaik untuk {$platform} di Indonesia.

Caption: {$caption}
Niche: {$niche}

Ketentuan:
- Campur: 5 hashtag besar (>500K post), 5 medium (50K-500K), 5 niche (<50K)
- Bahasa Indonesia dan Inggris yang relevan
- Prioritaskan yang trending di Indonesia
- Jangan hashtag generik tidak berguna seperti #like4like

Balas HANYA dalam format JSON array string, contoh: ["#contentcreator", "#tipshidup"]
PROMPT;

        try {
            $response   = $this->callClaude($prompt, 300);
            $clean      = preg_replace('/^```json\s*|\s*```$/m', '', trim($response));
            $hashtags   = json_decode($clean, true) ?? [];

            return ['hashtags' => array_slice($hashtags, 0, 15)];
        } catch (\Throwable $e) {
            Log::error('AIService::suggestHashtags failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getBestTime(User $user, string $platform): array
    {
        $defaults = [
            'instagram' => [
                'morning'   => ['time' => '07:00–09:00', 'label' => 'Pagi WIB', 'score' => 92],
                'afternoon' => ['time' => '12:00–13:00', 'label' => 'Siang WIB', 'score' => 78],
                'evening'   => ['time' => '19:00–21:00', 'label' => 'Malam WIB', 'score' => 88],
            ],
            'tiktok' => [
                'morning'   => ['time' => '06:00–08:00', 'label' => 'Pagi WIB', 'score' => 80],
                'evening'   => ['time' => '19:00–22:00', 'label' => 'Malam WIB', 'score' => 95],
            ],
            'facebook' => [
                'morning'   => ['time' => '09:00–11:00', 'label' => 'Pagi WIB', 'score' => 85],
                'afternoon' => ['time' => '13:00–15:00', 'label' => 'Siang WIB', 'score' => 88],
            ],
            'youtube' => [
                'afternoon' => ['time' => '15:00–17:00', 'label' => 'Sore WIB', 'score' => 82],
                'evening'   => ['time' => '20:00–22:00', 'label' => 'Malam WIB', 'score' => 90],
            ],
        ];

        return $defaults[$platform] ?? $defaults['instagram'];
    }

    // ── Private ─────────────────────────────────────────────────

    private function callClaude(string $prompt, int $maxTokens = 500): string
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('ANTHROPIC_API_KEY not configured');
        }

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/messages", [
            'model'      => $this->model,
            'max_tokens' => $maxTokens,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException("Claude API error: {$response->status()} — {$response->body()}");
        }

        return $response->json('content.0.text', '');
    }

    private function enforceRateLimit(User $user): void
    {
        $key     = "ai-rate:{$user->id}";
        $perMin  = 5;
        $perDay  = 50; // Starter default — Pro/Agency higher

        if (RateLimiter::tooManyAttempts("{$key}:min", $perMin)) {
            throw new \Illuminate\Http\Exceptions\ThrottleRequestsException('Terlalu banyak permintaan AI. Tunggu 1 menit.');
        }

        if (RateLimiter::tooManyAttempts("{$key}:day", $perDay)) {
            throw new \Illuminate\Http\Exceptions\ThrottleRequestsException('Kuota AI harian habis. Reset besok.');
        }

        RateLimiter::hit("{$key}:min", 60);
        RateLimiter::hit("{$key}:day", 86400);
    }
}
