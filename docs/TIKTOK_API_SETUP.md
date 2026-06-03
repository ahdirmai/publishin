# TikTok API Setup Guide — Publishin

Panduan lengkap untuk mengkonfigurasi TikTok Login Kit dan Content Posting API untuk aplikasi **Publishin** (Laravel 11 + Inertia.js).

> **Peringatan Penting:** TikTok Content Posting API memiliki proses review yang ketat dan membutuhkan waktu **2–4 minggu** setelah submission. Rencanakan timeline pengembangan dengan mempertimbangkan waktu review ini.

---

## Daftar Isi

1. [Prerequisites](#1-prerequisites)
2. [Membuat App di TikTok for Developers](#2-membuat-app-di-tiktok-for-developers)
3. [Mengaktifkan Products: Login Kit dan Content Posting API](#3-mengaktifkan-products)
4. [Konfigurasi OAuth Redirect URI](#4-konfigurasi-oauth-redirect-uri)
5. [Scopes yang Diperlukan](#5-scopes-yang-diperlukan)
6. [App Review dan Sandbox Process](#6-app-review-dan-sandbox-process)
7. [Content Posting API — Limits dan Format](#7-content-posting-api--limits-dan-format)
8. [Environment Variables di Laravel](#8-environment-variables-di-laravel)
9. [Sandbox Testing](#9-sandbox-testing)
10. [Common Errors dan Alasan Rejection](#10-common-errors-dan-alasan-rejection)

---

## 1. Prerequisites

### Akun yang Diperlukan

| Kebutuhan | Keterangan |
|---|---|
| TikTok Developer Account | Daftar di [developers.tiktok.com](https://developers.tiktok.com) |
| TikTok Business Account | Diperlukan untuk Content Posting API. Switch di TikTok app → Settings → Manage account → Switch to Business Account |
| Verified Email | Email harus terverifikasi di akun TikTok |
| Privacy Policy URL | `https://publishin.id/privacy` — wajib ada sebelum submit review |
| Terms of Service URL | `https://publishin.id/terms` — wajib ada sebelum submit review |

### Persyaratan Teknis

- **App harus sudah live/deployed** ke `https://app.publishin.id` sebelum submit review. TikTok akan mengunjungi URL untuk verifikasi.
- **Domain HTTPS** wajib. HTTP tidak diterima.
- **Privacy Policy harus accessible** tanpa login.

---

## 2. Membuat App di TikTok for Developers

### 2.1 Registrasi Developer Account

1. Kunjungi [developers.tiktok.com](https://developers.tiktok.com)
2. Klik **Login** di pojok kanan atas
3. Login dengan akun TikTok (bisa menggunakan TikTok mobile app untuk scan QR code)
4. Lengkapi profil developer:
   - Nama perusahaan/organisasi
   - Website
   - Nomor telepon
5. Verifikasi email jika diminta

### 2.2 Membuat Aplikasi Baru

1. Dari dashboard, klik **Manage apps** → **Create app**
2. Pilih platform: **Web**
3. Isi informasi app:

```
App name:        Publishin
App type:        Web
Category:        Social Media Tools
Description:     Social media scheduling platform for content creators and businesses.
                 Schedule and auto-publish TikTok videos with performance analytics.
```

4. Isi **Basic Information**:
   - **App icon**: 512×512 px atau lebih besar, format PNG
   - **App website**: `https://publishin.id`
   - **Privacy policy URL**: `https://publishin.id/privacy`
   - **Terms of service URL**: `https://publishin.id/terms`

5. Klik **Submit** untuk membuat app

### 2.3 Catat App Credentials

Setelah app dibuat, buka **App Details → App credentials**:

```
Client Key:    (tampil sebagai "Client key" atau "App ID")
Client Secret: (klik untuk reveal)
```

> **JANGAN** commit Client Secret ke repository. Simpan hanya di `.env`.

---

## 3. Mengaktifkan Products

### 3.1 Login Kit

Login Kit memungkinkan user TikTok untuk login ke Publishin menggunakan akun TikTok mereka.

1. Dari App Dashboard, buka tab **Products**
2. Temukan **Login Kit** → klik **Add product** atau **Configure**
3. Isi **Redirect URI for Login Kit**:
   ```
   https://app.publishin.id/auth/tiktok/callback
   ```
4. Tambahkan URL development (opsional, untuk sandbox):
   ```
   http://localhost/auth/tiktok/callback
   http://publishin.test/auth/tiktok/callback
   ```
5. Klik **Save changes**

### 3.2 Content Posting API

Content Posting API memungkinkan Publishin untuk upload dan publish video ke TikTok atas nama user.

1. Dari tab **Products**, temukan **Content Posting API**
2. Klik **Add product**
3. Konfigurasi akan memerlukan informasi tambahan:
   - **Use case description**: Jelaskan bagaimana fitur ini digunakan di Publishin
   - **Target audience**: Content creators, SME, digital marketing agencies

> **Catatan:** Content Posting API tidak langsung aktif. Harus melewati proses App Review terpisah.

---

## 4. Konfigurasi OAuth Redirect URI

### 4.1 URL Redirect yang Digunakan Publishin

Berdasarkan struktur route di `routes/web.php`:

```
https://app.publishin.id/auth/tiktok/callback
```

TikTok menggunakan route pattern yang sama dengan platform lain di Publishin:
```php
Route::get('/auth/{platform}/callback', [SocialAccountController::class, 'callback'])
     ->name('social.callback');
```

### 4.2 Mendaftarkan Redirect URI

Di **App Dashboard → Login Kit → Redirect URI**:

```
Production:
https://app.publishin.id/auth/tiktok/callback

Development/Sandbox (opsional):
http://localhost/auth/tiktok/callback
```

> **PENTING:** TikTok sangat ketat soal exact match redirect URI. Perbedaan satu karakter (termasuk trailing slash) akan menyebabkan error `invalid_redirect_uri`.

---

## 5. Scopes yang Diperlukan

### 5.1 Daftar Scopes

| Scope | Fungsi di Publishin | Perlu Review |
|---|---|---|
| `user.info.basic` | Ambil profil user: display name, avatar, follower count | Tidak (default tersedia) |
| `user.info.profile` | Profil lengkap termasuk bio | Tidak |
| `user.info.stats` | Statistik akun: likes, videos, followers | Ya |
| `video.list` | List video yang pernah di-upload user | Ya |
| `video.upload` | Upload video (Phase 1 dari 2-phase publish) | **Ya — review ketat** |
| `video.publish` | Publish video yang sudah di-upload | **Ya — review ketat** |

### 5.2 Menambahkan Scopes

1. Buka **App Dashboard → Login Kit → Scope**
2. Klik **Request scopes**
3. Pilih scopes yang diperlukan
4. Untuk `video.upload` dan `video.publish`: status akan **Pending** sampai App Review disetujui

### 5.3 Flow OAuth untuk TikTok di Laravel

```php
// SocialAccountController.php — tambahkan case 'tiktok'
if ($platform === 'tiktok') {
    $codeVerifier  = bin2hex(random_bytes(32)); // PKCE
    $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

    session(['tiktok_code_verifier' => $codeVerifier]);

    $url = 'https://www.tiktok.com/v2/auth/authorize/?' . http_build_query([
        'client_key'            => config('services.tiktok.client_key'),
        'response_type'         => 'code',
        'scope'                 => 'user.info.basic,video.upload,video.publish',
        'redirect_uri'          => route('social.callback', 'tiktok'),
        'state'                 => csrf_token(),
        'code_challenge'        => $codeChallenge,
        'code_challenge_method' => 'S256',
    ]);

    return redirect($url);
}
```

> **PKCE (Proof Key for Code Exchange)** wajib untuk TikTok OAuth v2. Berbeda dengan Meta yang tidak mewajibkan PKCE.

### 5.4 Token Exchange

```php
// Tukar code dengan access token
$tokenResponse = Http::post('https://open.tiktokapis.com/v2/oauth/token/', [
    'client_key'    => config('services.tiktok.client_key'),
    'client_secret' => config('services.tiktok.client_secret'),
    'code'          => $request->query('code'),
    'grant_type'    => 'authorization_code',
    'redirect_uri'  => route('social.callback', 'tiktok'),
    'code_verifier' => session('tiktok_code_verifier'),
]);
```

---

## 6. App Review dan Sandbox Process

### 6.1 Tahapan Review TikTok

TikTok memiliki proses review yang jauh lebih ketat dan lambat dibanding Meta:

```
Sandbox (Testing) → Submit Review → TikTok Review (2–4 minggu) → Approved / Rejected → Production
```

### 6.2 Sebelum Submit Review — Checklist

- [ ] App sudah live di HTTPS
- [ ] Privacy Policy tersedia di URL publik dan membahas data TikTok
- [ ] Terms of Service tersedia
- [ ] Demo video disiapkan (5–10 menit, harus menunjukkan semua scope dalam aksi)
- [ ] Business entity information lengkap di developer portal
- [ ] App icon berkualitas tinggi sudah diupload
- [ ] Deskripsi app ditulis dalam bahasa Inggris yang jelas

### 6.3 Informasi yang Diperlukan Saat Review

Untuk setiap scope, TikTok akan meminta:

**`video.upload` dan `video.publish`:**

```
Use Case Title:
"Schedule and Auto-publish TikTok Videos"

Detailed Description:
Publishin is a social media scheduling platform. Users connect their TikTok 
account to schedule video uploads. At the scheduled time, Publishin 
automatically uses the video.upload scope to upload the video file to TikTok's 
servers, then uses video.publish scope to publish it to the user's TikTok 
profile. The user retains full control over content, privacy settings, 
captions, and hashtags.

Data Usage:
- Video files are stored temporarily on Publishin servers then uploaded to TikTok
- We do not share video content with third parties
- Access tokens are encrypted at rest
```

### 6.4 Demo Video Requirements

Video demo harus menunjukkan:

1. User login ke Publishin menggunakan TikTok Login Kit
2. User memberikan consent untuk setiap scope yang diminta
3. User membuat jadwal post baru dengan upload video
4. Video berhasil terpublish di TikTok (tampilkan di aplikasi TikTok)
5. Tampilkan dashboard analytics yang menggunakan data TikTok

**Format video:**
- Resolusi minimal 720p
- Durasi: 5–10 menit (jangan lebih pendek dari 5 menit)
- Format: MP4
- Harus menampilkan *seluruh flow*, bukan hanya bagian-bagian tertentu

### 6.5 Alasan Umum Rejection dan Cara Menghindari

| Alasan Rejection | Cara Menghindari |
|---|---|
| Privacy Policy tidak membahas TikTok data | Tambahkan section khusus TikTok di Privacy Policy |
| Demo video tidak menunjukkan scope dalam aksi | Pastikan setiap scope terlihat jelas digunakan di video |
| App tidak bisa diakses publik | Deploy ke production HTTPS sebelum submit |
| Use case tidak jelas atau generik | Tulis use case yang sangat spesifik, sebutkan nama fitur Publishin |
| App icon kualitas rendah | Gunakan icon 1024×1024 px professional |
| Scope diminta tapi tidak digunakan | Hanya request scope yang benar-benar digunakan |

### 6.6 Setelah Approval

Setelah approved, scope akan berubah dari **Sandbox** ke **Production** access. Tidak ada perubahan kode yang diperlukan — hanya status di developer portal yang berubah.

---

## 7. Content Posting API — Limits dan Format

### 7.1 Spesifikasi Video

| Parameter | Spesifikasi |
|---|---|
| **Format** | MP4, MOV, WEBM |
| **Resolusi minimum** | 360p (direkomendasikan 1080p atau lebih) |
| **Aspect ratio** | 9:16 (portrait, standar TikTok), 16:9, 1:1 |
| **Durasi minimum** | 3 detik |
| **Durasi maksimum** | 10 menit (untuk sebagian besar akun); 60 menit untuk akun tertentu |
| **Ukuran file maksimum** | 4 GB |
| **Frame rate** | 23–60 fps |
| **Bitrate video** | Minimal 516 Kbps |
| **Audio** | AAC atau MP3, stereo, 128 Kbps+ |

### 7.2 API Rate Limits

| Limit | Nilai |
|---|---|
| Upload per hari per akun | 5 video (default) |
| Upload per jam per akun | 5 video |
| Concurrent upload sessions | 1 per akun |
| API calls per hari per app | 2,000 (sandbox), 100,000 (production) |

> **Catatan:** Limit upload 5 video/hari adalah limit default. Bisa ditingkatkan dengan menghubungi TikTok Business support.

### 7.3 Dua Mode Upload

**Mode 1: Direct Post** — Video langsung dipublish ke TikTok

```php
// POST https://open.tiktokapis.com/v2/post/publish/video/init/
$initResponse = Http::withToken($accessToken)
    ->post('https://open.tiktokapis.com/v2/post/publish/video/init/', [
        'post_info' => [
            'title'             => '#scheduledpost via @publishin',
            'privacy_level'     => 'PUBLIC_TO_EVERYONE', // atau FOLLOWER_OF_CREATOR / SELF_ONLY
            'disable_duet'      => false,
            'disable_comment'   => false,
            'disable_stitch'    => false,
            'video_cover_timestamp_ms' => 1000,
        ],
        'source_info' => [
            'source'     => 'FILE_UPLOAD',
            'video_size' => $videoSizeBytes,
            'chunk_size' => 10 * 1024 * 1024, // 10MB per chunk
            'total_chunk_count' => ceil($videoSizeBytes / (10 * 1024 * 1024)),
        ],
    ]);

$uploadUrl = $initResponse->json()['data']['upload_url'];
$publishId = $initResponse->json()['data']['publish_id'];
```

**Mode 2: Content Posting — Upload lalu Schedule**

Untuk scheduling, gunakan `publish_id` yang didapat dari init, simpan ke database, lalu trigger publish pada waktu yang dijadwalkan.

### 7.4 Privacy Level Options

| Nilai | Keterangan |
|---|---|
| `PUBLIC_TO_EVERYONE` | Semua orang bisa lihat |
| `MUTUAL_FOLLOW_FRIENDS` | Hanya mutual followers |
| `FOLLOWER_OF_CREATOR` | Hanya followers |
| `SELF_ONLY` | Hanya creator sendiri (private) |

> **PENTING:** TikTok mewajibkan user memilih sendiri privacy level. Publishin tidak boleh memilih privacy level tanpa consent eksplisit dari user.

---

## 8. Environment Variables di Laravel

### 8.1 File `.env`

Tambahkan variabel berikut ke file `.env`:

```dotenv
# ============================================================
# TIKTOK API
# ============================================================

# TikTok App Credentials (dari App Details → App credentials)
TIKTOK_CLIENT_KEY=your_tiktok_client_key_here
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret_here

# TikTok API Base URL (tidak perlu diubah)
TIKTOK_API_BASE=https://open.tiktokapis.com
TIKTOK_AUTH_BASE=https://www.tiktok.com

# Sandbox mode (true untuk development, false untuk production)
TIKTOK_SANDBOX=true
```

### 8.2 File `config/services.php`

Tambahkan entry TikTok:

```php
'tiktok' => [
    'client_key'    => env('TIKTOK_CLIENT_KEY'),
    'client_secret' => env('TIKTOK_CLIENT_SECRET'),
    'api_base'      => env('TIKTOK_API_BASE', 'https://open.tiktokapis.com'),
    'auth_base'     => env('TIKTOK_AUTH_BASE', 'https://www.tiktok.com'),
    'sandbox'       => env('TIKTOK_SANDBOX', true),
],
```

### 8.3 Update `.env.example`

Tambahkan ke file `.env.example` agar developer lain tahu variabel yang diperlukan:

```dotenv
# Social Platform OAuth
INSTAGRAM_CLIENT_ID=
INSTAGRAM_CLIENT_SECRET=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
META_WEBHOOK_VERIFY_TOKEN=

# TikTok API
TIKTOK_CLIENT_KEY=
TIKTOK_CLIENT_SECRET=
TIKTOK_SANDBOX=true
```

---

## 9. Sandbox Testing

### 9.1 Cara Kerja Sandbox TikTok

Berbeda dengan Meta yang menggunakan Test Users biasa, TikTok memisahkan Sandbox sebagai environment khusus:

| Aspek | Sandbox | Production |
|-------|---------|------------|
| Credentials | Sama (Client Key + Secret) | Sama |
| OAuth flow | Sama | Sama |
| Redirect URI | `publishin.test` didukung | HTTPS wajib |
| Video publish | Tidak muncul di TikTok asli | Muncul di profil user |
| API endpoint publish | `/v2/post/publish/sandbox/video/init/` | `/v2/post/publish/video/init/` |
| App Review | **Tidak diperlukan** | Wajib sebelum production |
| Scopes | Semua tersedia langsung | Butuh approval per scope |
| Rate limit | 2,000 calls/hari | 100,000 calls/hari |

### 9.2 Setup Sandbox — Langkah Awal

#### A. Aktifkan Sandbox di Developer Portal

1. Buka **App Dashboard** di [developers.tiktok.com](https://developers.tiktok.com)
2. Pilih app Publishin
3. Sidebar kiri → **Sandbox** → **Manage users**
4. Klik **Add user** → masukkan TikTok username developer/tester
5. Tester buka TikTok → notifikasi undangan masuk → **Accept**

> Sandbox users harus menerima undangan dari dalam TikTok app (bukan web). Notifikasi masuk ke DM / Activity.

#### B. Daftarkan Redirect URI untuk Local

Di **App Dashboard → Products → Login Kit → Redirect URI**, tambahkan:

```
http://publishin.test/auth/tiktok/callback
https://publishin.id/auth/tiktok/callback
```

> TikTok mengizinkan `http://` hanya untuk domain non-publik seperti `publishin.test`. Domain production **wajib** HTTPS.

#### C. Set `.env` untuk Local Development

```env
TIKTOK_CLIENT_KEY=your_tiktok_client_key
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret
TIKTOK_SANDBOX=true

APP_URL=http://publishin.test
```

---

### 9.3 Verifikasi Setup Lokal

```bash
# Pastikan redirect URL terbentuk benar
php artisan tinker
>>> route('social.callback', 'tiktok')
# Output: http://publishin.test/auth/tiktok/callback
```

Kalau outputnya `https://` padahal local, cek `APP_URL` di `.env` dan `php artisan config:clear`.

---

### 9.4 OAuth Flow di Sandbox

Flow sama persis dengan production. Yang berbeda hanya:
- Redirect ke `publishin.test` (HTTP)
- Hanya sandbox users (yang sudah diundang) yang bisa OAuth

```php
// SocialAccountController.php
// Tidak ada perubahan kode — sandbox vs production ditentukan oleh TIKTOK_SANDBOX env
// untuk switch endpoint publish saja (bukan OAuth)
```

Setelah user OAuth berhasil, access token yang didapat valid untuk semua API call termasuk sandbox publish.

---

### 9.5 Testing Content Posting di Sandbox

Gunakan endpoint sandbox untuk publish — video tidak muncul di TikTok nyata tapi response identik dengan production.

```php
// TikTokService.php
private function publishEndpoint(): string
{
    return config('services.tiktok.sandbox')
        ? 'https://open.tiktokapis.com/v2/post/publish/sandbox/video/init/'
        : 'https://open.tiktokapis.com/v2/post/publish/video/init/';
}

public function initVideoUpload(string $accessToken, array $postInfo, int $videoSizeBytes): array
{
    $response = Http::withHeaders([
        'Authorization' => "Bearer {$accessToken}",
        'Content-Type'  => 'application/json; charset=UTF-8',
    ])->post($this->publishEndpoint(), [
        'post_info' => [
            'title'                    => $postInfo['caption'],
            'privacy_level'            => $postInfo['privacy_level'] ?? 'SELF_ONLY',
            'disable_duet'             => false,
            'disable_comment'          => false,
            'disable_stitch'           => false,
            'video_cover_timestamp_ms' => 1000,
        ],
        'source_info' => [
            'source'            => 'FILE_UPLOAD',
            'video_size'        => $videoSizeBytes,
            'chunk_size'        => 10 * 1024 * 1024, // 10MB
            'total_chunk_count' => (int) ceil($videoSizeBytes / (10 * 1024 * 1024)),
        ],
    ]);

    return $response->json('data');
}
```

> Di sandbox, set `privacy_level` ke `SELF_ONLY` untuk semua test — aman, tidak ada video yang bocor ke publik meskipun production.

---

### 9.6 Cek Status Publish

Endpoint status sama untuk sandbox dan production:

```php
$statusResponse = Http::withHeaders([
    'Authorization' => "Bearer {$accessToken}",
    'Content-Type'  => 'application/json; charset=UTF-8',
])->post('https://open.tiktokapis.com/v2/post/publish/status/fetch/', [
    'publish_id' => $publishId,
]);

$status = $statusResponse->json('data.status');

// Nilai status yang mungkin:
// PROCESSING_UPLOAD   — video sedang diupload chunk-by-chunk
// PROCESSING_DOWNLOAD — TikTok sedang memproses video
// SEND_TO_USER_INBOX  — masuk inbox creator (Direct Post mode)
// FAILED              — gagal, cek data.fail_reason
// PUBLISHED           — berhasil (di sandbox: tercatat tapi tidak muncul di profil)
```

---

### 9.7 Perbedaan Sandbox vs Production — Ringkasan Kode

Satu-satunya perbedaan yang perlu di-switch via `TIKTOK_SANDBOX`:

| Endpoint | Sandbox | Production |
|----------|---------|------------|
| Init video upload | `/v2/post/publish/sandbox/video/init/` | `/v2/post/publish/video/init/` |
| Semua endpoint lain | Sama | Sama |

OAuth, token exchange, status check, dan user info — semua endpoint identik.

---

### 9.8 Checklist Sebelum Submit App Review

Setelah semua test di sandbox berhasil, checklist sebelum submit review ke production:

- [ ] `TIKTOK_SANDBOX=false` di production `.env`
- [ ] `APP_URL=https://publishin.id` di production
- [ ] Privacy Policy URL live dan membahas data TikTok secara eksplisit
- [ ] Terms of Service URL live
- [ ] App icon 512×512+ sudah diupload di portal
- [ ] Demo video disiapkan (5–10 menit, lihat Bagian 6.4)
- [ ] Semua scopes yang diminta terbukti digunakan di demo video
- [ ] App bisa diakses publik di `https://publishin.id`
- [ ] Business entity info lengkap di developer portal

---

## 10. Common Errors dan Alasan Rejection

### 10.1 OAuth Errors

**Error: `invalid_redirect_uri`**

```
The redirect_uri is not in the allowed list.
```

**Fix:** Verifikasi exact match di **Login Kit → Redirect URI**. Cek: trailing slash, `http` vs `https`, subdomain.

---

**Error: `access_denied`**

```
User cancelled the authorization.
```

**Fix:** User memilih "Cancel" saat diminta permissions. Handle dengan redirect ke halaman informatif, jangan langsung error.

---

**Error: `invalid_scope`**

```
The requested scope is not supported or authorized.
```

**Fix:** Scope belum approved di App Review. Untuk development, gunakan sandbox mode dan pastikan scope sudah di-request di dashboard.

---

### 10.2 Content Posting API Errors

**Error: `spam_risk_too_high`**

**Penyebab:** Terlalu banyak upload dalam waktu singkat, atau konten terdeteksi sebagai spam.

**Fix:**
- Implementasikan rate limiting: maks 5 video/hari per akun
- Tambahkan delay antara multiple uploads
- Pastikan video tidak identik satu sama lain

---

**Error: `video_pull_failed`**

**Penyebab:** Video URL tidak bisa diakses oleh TikTok (jika menggunakan URL upload mode, bukan FILE_UPLOAD).

**Fix:** Gunakan `FILE_UPLOAD` source type, bukan `PULL_FROM_URL`, untuk kontrol yang lebih baik.

---

**Error: `chunk_upload_failed`**

**Penyebab:** Upload chunk gagal karena network timeout atau file corrupt.

**Fix:** Implementasikan retry logic untuk setiap chunk. Simpan `upload_url` dan `publish_id` di database agar bisa resume jika gagal.

---

**Error: `video_processing_failed`**

**Penyebab:** Video format tidak sesuai atau file corrupt.

**Fix:**
- Validasi format sebelum upload: MP4, MOV, WEBM saja
- Validasi ukuran: maks 4 GB
- Validasi durasi: minimal 3 detik
- Gunakan FFprobe untuk validasi di server sebelum kirim ke TikTok

---

### 10.3 Alasan Rejection saat App Review

| Alasan | Detail | Solusi |
|---|---|---|
| **Insufficient privacy policy** | Privacy Policy tidak menyebutkan data TikTok yang dikumpulkan | Tambahkan section khusus: "Data dari TikTok: kami mengakses X, Y, Z untuk keperluan..." |
| **Demo video too short** | Video kurang dari 5 menit atau tidak menunjukkan semua scope | Buat video 7–10 menit yang menampilkan setiap fitur secara mendetail |
| **App not accessible** | Reviewer tidak bisa mengakses `https://app.publishin.id` | Pastikan production server aktif sebelum submit review |
| **Scope over-request** | Request scope yang tidak terlihat digunakan di demo | Hapus scope yang tidak digunakan, atau tampilkan penggunaannya di demo video |
| **No clear user benefit** | Benefit untuk user tidak jelas dari deskripsi | Tulis use case yang berfokus pada manfaat user, bukan teknis |
| **Duplicate functionality** | Fitur sudah ada di TikTok native app | Tekankan nilai tambah scheduling dan multi-platform management |
| **Policy violation** | App atau konten melanggar TikTok Community Guidelines | Review seluruh app untuk pastikan compliance |

---

### 10.4 Token Refresh

TikTok access token berlaku **24 jam** (jauh lebih pendek dari Meta). Refresh token berlaku **365 hari**.

```php
// Refresh access token sebelum expired
$refreshResponse = Http::post('https://open.tiktokapis.com/v2/oauth/token/', [
    'client_key'    => config('services.tiktok.client_key'),
    'client_secret' => config('services.tiktok.client_secret'),
    'grant_type'    => 'refresh_token',
    'refresh_token' => $account->refresh_token,
]);

$newTokenData = $refreshResponse->json()['data'];

$account->update([
    'access_token'  => $newTokenData['access_token'],
    'refresh_token' => $newTokenData['refresh_token'],
    'token_expires_at' => now()->addSeconds($newTokenData['expires_in']),
]);
```

> **KRITIS:** Schedule artisan command untuk refresh token TikTok setiap 20 jam agar token tidak pernah expired sebelum digunakan untuk scheduled posts.

```php
// routes/console.php
Schedule::command('social:refresh-tiktok-tokens')
         ->everyTwentyHours()
         ->withoutOverlapping();
```

---

## Referensi

- [TikTok for Developers — Getting Started](https://developers.tiktok.com/doc/getting-started-create-an-app)
- [TikTok Login Kit](https://developers.tiktok.com/doc/login-kit-web)
- [TikTok Content Posting API](https://developers.tiktok.com/doc/content-posting-api-get-started)
- [TikTok OAuth Scopes Reference](https://developers.tiktok.com/doc/tiktok-api-scopes)
- [TikTok Video Upload Guide](https://developers.tiktok.com/doc/content-posting-api-media-transfer-guide)
- [TikTok API Rate Limits](https://developers.tiktok.com/doc/content-posting-api-reference-direct-post)
- [TikTok App Review Guidelines](https://developers.tiktok.com/doc/app-review-guidelines)
