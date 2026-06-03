# Meta API Setup Guide — Publishin

Panduan lengkap untuk mengkonfigurasi Instagram Graph API dan Facebook Pages API untuk aplikasi **Publishin** (Laravel 11 + Inertia.js).

---

## Daftar Isi

1. [Prerequisites](#1-prerequisites)
2. [Membuat Meta App di developers.facebook.com](#2-membuat-meta-app)
3. [Menambahkan Products](#3-menambahkan-products)
4. [Konfigurasi OAuth Redirect URIs](#4-konfigurasi-oauth-redirect-uris)
5. [Permissions yang Diperlukan](#5-permissions-yang-diperlukan)
6. [App Review Process](#6-app-review-process)
7. [Webhook Setup](#7-webhook-setup)
8. [Environment Variables di Laravel](#8-environment-variables-di-laravel)
9. [Testing di Development Mode](#9-testing-di-development-mode)
10. [Common Errors dan Fixes](#10-common-errors-dan-fixes)

---

## 1. Prerequisites

Sebelum memulai, pastikan hal-hal berikut sudah tersedia:

### Akun & Halaman yang Diperlukan

| Kebutuhan | Keterangan |
|---|---|
| Meta Business Account | Wajib. Buat di [business.facebook.com](https://business.facebook.com) |
| Facebook Page | Halaman bisnis (bukan profil pribadi). Harus dikelola oleh akun Meta Business |
| Instagram Business atau Creator Account | Harus terhubung ke Facebook Page di atas |
| Email Developer | Email aktif untuk pendaftaran di Meta for Developers |
| Domain Terverifikasi | `publishin.id` harus bisa diverifikasi di Business Settings |

### Menghubungkan Instagram ke Facebook Page

1. Buka **Facebook Page** → Settings → **Linked Accounts**
2. Klik **Connect account** di bagian Instagram
3. Masukkan username dan password Instagram
4. Pastikan Instagram sudah beralih ke tipe **Business** atau **Creator** (Settings → Account type and tools → Switch to Professional Account)

> **PENTING:** Instagram personal/biasa tidak bisa menggunakan Graph API. Wajib Business atau Creator account.

---

## 2. Membuat Meta App

### Langkah-langkah di Meta for Developers

**2.1 Buka Developer Portal**

Kunjungi [developers.facebook.com](https://developers.facebook.com) dan login dengan akun Facebook yang sudah terdaftar sebagai developer.

**2.2 Daftar sebagai Developer (jika belum)**

- Klik avatar profil → **Register**
- Verifikasi nomor telepon
- Accept kebijakan penggunaan

**2.3 Buat App Baru**

1. Klik **My Apps** → **Create App**
2. Pilih use case: **Other** (untuk mendapatkan akses penuh ke semua produk)
3. Klik **Next**
4. Pilih App type: **Business**
5. Klik **Next**
6. Isi detail app:

```
App Name:        Publishin
App Contact Email: dev@publishin.id
Business Account: [pilih Meta Business Account Publishin]
```

7. Klik **Create App**
8. Isi verifikasi keamanan jika diminta

**2.4 Catat App Credentials**

Setelah app dibuat, buka **Settings → Basic**:

```
App ID:     [akan terlihat di bagian atas]
App Secret: [klik Show untuk reveal]
```

> **Jangan pernah commit App Secret ke repository.** Simpan hanya di `.env`.

---

## 3. Menambahkan Products

Dari dashboard app, scroll ke bagian **Add Products to Your App**.

### 3.1 Facebook Login

1. Klik **Set Up** di **Facebook Login**
2. Pilih **Web**
3. Masukkan Site URL: `https://app.publishin.id`
4. Klik **Save** dan **Continue**
5. Selesaikan Quickstart wizard (atau skip)
6. Buka **Facebook Login → Settings**:
   - **Client OAuth Login**: ON
   - **Web OAuth Login**: ON
   - **Force Web OAuth Reauthentication**: OFF
   - **Embedded Browser OAuth Login**: OFF
   - **Valid OAuth Redirect URIs**: (diisi di bagian selanjutnya)

### 3.2 Instagram Graph API

1. Scroll kembali ke **Add Products**
2. Klik **Set Up** di **Instagram Graph API**
3. Pilih **Instagram API setup with Instagram Login** jika tersedia, atau lanjutkan dengan setup manual
4. Di panel kiri akan muncul menu **Instagram** → **Basic Display** dan **Instagram** → **Insights**

### 3.3 Pages API (Facebook Pages)

Pages API sudah otomatis tersedia melalui Facebook Login. Tambahkan permissions yang diperlukan di bagian **App Review** atau di **Graph API Explorer** untuk testing.

---

## 4. Konfigurasi OAuth Redirect URIs

### 4.1 Facebook Login Redirect URI

Buka **Facebook Login → Settings → Valid OAuth Redirect URIs**, tambahkan:

```
https://app.publishin.id/auth/facebook/callback
http://localhost/auth/facebook/callback
http://publishin.test/auth/facebook/callback
```

> **Catatan untuk Publishin:** Callback route yang digunakan ada di `routes/web.php`:
> ```php
> Route::get('/auth/{platform}/callback', [SocialAccountController::class, 'callback'])
>      ->name('social.callback');
> ```
> Sehingga URL callback adalah: `https://app.publishin.id/auth/facebook/callback`

### 4.2 Instagram OAuth Redirect URI

Buka **Products → Instagram → Basic Display → Settings**:

- **Valid OAuth Redirect URIs**: `https://app.publishin.id/auth/instagram/callback`
- **Deauthorize Callback URL**: `https://app.publishin.id/webhooks/instagram/deauth`
- **Data Deletion Request URL**: `https://app.publishin.id/webhooks/instagram/delete`

Tambahkan URI lokal untuk development:
```
http://localhost/auth/instagram/callback
http://publishin.test/auth/instagram/callback
```

### 4.3 App Domains

Buka **Settings → Basic → App Domains**, tambahkan:
```
publishin.id
app.publishin.id
```

---

## 5. Permissions yang Diperlukan

### 5.1 Instagram Graph API Permissions

| Permission | Fungsi di Publishin | Development Mode | Perlu App Review |
|---|---|---|---|
| `instagram_basic` | Login, baca profil, username | Ya (test users) | Tidak |
| `instagram_content_publish` | Publish foto/video/reels ke Instagram | Ya (test users) | **Ya** |
| `instagram_manage_insights` | Ambil metrik: reach, impressions, likes, comments, saves, shares | Ya (test users) | **Ya** |
| `instagram_manage_comments` | Baca dan balas komentar | Ya (test users) | **Ya** |
| `pages_show_list` | Lihat daftar Facebook Pages yang dikelola | Ya (test users) | Tidak |
| `public_profile` | Info dasar profil Facebook | Ya (semua user) | Tidak |

### 5.2 Facebook Pages API Permissions

| Permission | Fungsi di Publishin | Development Mode | Perlu App Review |
|---|---|---|---|
| `pages_show_list` | List semua halaman yang dikelola user | Ya (test users) | Tidak |
| `pages_read_engagement` | Baca insights: reach, impressions, engaged users | Ya (test users) | **Ya** |
| `pages_manage_posts` | Publish post ke Facebook Page | Ya (test users) | **Ya** |
| `pages_read_user_content` | Baca post yang dibuat user di halaman | Ya (test users) | **Ya** |
| `read_insights` | Akses Page Insights (aggregate metrics) | Ya (test users) | **Ya** |
| `business_management` | Kelola aset Meta Business (opsional) | Ya (test users) | **Ya** |

### 5.3 Cara Menambahkan Permissions

1. Buka **App Dashboard → App Review → Permissions and Features**
2. Cari nama permission
3. Klik **Request** di sebelah permission yang dibutuhkan
4. Pilih **Get Advanced Access** (untuk produksi) atau gunakan **Standard Access** (development)

---

## 6. App Review Process

### 6.1 Permissions yang Tidak Perlu Review

Berikut permissions yang langsung aktif tanpa review (disebut **Standard Access**):

- `public_profile`
- `email`
- `pages_show_list`
- `instagram_basic`

### 6.2 Permissions yang Perlu App Review

Permissions berikut memerlukan review Meta sebelum bisa digunakan oleh pengguna umum (bukan test user):

| Permission | Estimasi Review | Catatan |
|---|---|---|
| `instagram_content_publish` | 5–7 hari kerja | Perlu demo video |
| `instagram_manage_insights` | 5–7 hari kerja | Perlu demo video |
| `pages_manage_posts` | 5–7 hari kerja | Perlu privacy policy |
| `pages_read_engagement` | 5–7 hari kerja | Perlu privacy policy |
| `read_insights` | 7–14 hari kerja | Perlu penjelasan use case |

### 6.3 Proses Submit App Review

**Sebelum submit, siapkan:**

1. **Privacy Policy URL**: `https://publishin.id/privacy`
2. **Terms of Service URL**: `https://publishin.id/terms`
3. **App Icon**: 1024×1024 px
4. **Demo Video** (durasi 1–5 menit):
   - Tunjukkan alur lengkap: login → connect Instagram/Facebook → buat post → publish → lihat analytics
   - Video harus menunjukkan bahwa permission yang diminta *benar-benar digunakan*
5. **Use Case Description** (dalam bahasa Inggris, detail per permission)

**Langkah submit:**

1. Buka **App Review → Requests**
2. Klik **Add Items** untuk setiap permission
3. Isi form: business use case, bagaimana data digunakan, detail integrasi
4. Upload demo video
5. Klik **Submit for Review**

**Contoh use case description untuk `instagram_content_publish`:**

> Publishin is a social media scheduling platform that allows content creators and businesses to schedule and automatically publish Instagram posts. The `instagram_content_publish` permission is used to programmatically publish photo and video posts to connected Instagram Business accounts at scheduled times set by the user.

### 6.4 Mode App dan Dampaknya

| Mode App | Siapa yang Bisa Login | Batas |
|---|---|---|
| **Development** | Hanya Administrator, Developer, Tester yang ditambahkan manual | Max 25 test users |
| **Live** | Semua pengguna umum | Hanya permissions yang sudah approved |

Untuk mengubah ke Live mode: **App Dashboard → Settings → Basic → App Mode → Live** (pastikan semua requirements terpenuhi dulu).

---

## 7. Webhook Setup

### 7.1 Mengapa Webhook Diperlukan

Webhook digunakan untuk:
- Notifikasi real-time ketika ada komentar baru di post Instagram
- Update status akun (deauthorize, data deletion request)
- Notifikasi perubahan pada Facebook Page

### 7.2 Mendaftarkan Webhook di Meta

1. Buka **Products → Webhooks**
2. Klik **Add Subscriptions**
3. Pilih object type: **Instagram** atau **Page**
4. Isi:
   - **Callback URL**: `https://app.publishin.id/webhooks/meta`
   - **Verify Token**: string acak yang sama dengan `WEBHOOK_VERIFY_TOKEN` di `.env`

### 7.3 Verifikasi Webhook di Laravel

Meta akan mengirim GET request ke callback URL untuk verifikasi:

```php
// routes/web.php
Route::get('/webhooks/meta', function (Request $request) {
    $mode      = $request->query('hub_mode');
    $token     = $request->query('hub_verify_token');
    $challenge = $request->query('hub_challenge');

    if ($mode === 'subscribe' && $token === config('app.webhook_verify_token')) {
        return response($challenge, 200);
    }

    return response('Forbidden', 403);
});

// POST handler untuk menerima events
Route::post('/webhooks/meta', [WebhookController::class, 'handle'])
     ->middleware('webhook.signature'); // verifikasi X-Hub-Signature-256
```

### 7.4 Subscribed Fields

Untuk **Instagram** object, subscribe ke fields:
- `comments` — komentar baru
- `mentions` — mention di post/story
- `story_insights` — insight story setelah expired

Untuk **Page** object, subscribe ke fields:
- `feed` — post baru di page
- `messages` — pesan masuk
- `mention` — mention di post

---

## 8. Environment Variables di Laravel

### 8.1 File `.env`

Tambahkan variabel berikut ke file `.env`:

```dotenv
# ============================================================
# META (FACEBOOK & INSTAGRAM) API
# ============================================================

# Meta App Credentials (dari Settings → Basic di Meta for Developers)
FACEBOOK_APP_ID=your_meta_app_id_here
FACEBOOK_APP_SECRET=your_meta_app_secret_here

# Instagram — menggunakan App ID dan Secret yang sama dengan Facebook
# (Instagram Graph API terintegrasi dalam satu Meta App)
INSTAGRAM_CLIENT_ID="${FACEBOOK_APP_ID}"
INSTAGRAM_CLIENT_SECRET="${FACEBOOK_APP_SECRET}"

# Graph API Version (gunakan versi stabil terbaru)
# App saat ini menggunakan v19.0 (lihat InstagramService.php dan FacebookService.php)
GRAPH_API_VERSION=v19.0

# Webhook verify token — string acak, buat sendiri
META_WEBHOOK_VERIFY_TOKEN=your_random_webhook_verify_token_here
```

### 8.2 File `config/services.php`

Konfigurasi saat ini di `config/services.php` sudah benar:

```php
'instagram' => [
    'client_id'     => env('INSTAGRAM_CLIENT_ID'),
    'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
],

'facebook' => [
    'client_id'     => env('FACEBOOK_APP_ID'),
    'client_secret' => env('FACEBOOK_APP_SECRET'),
],
```

Tambahkan entry untuk webhook dan graph API version:

```php
'meta' => [
    'app_id'          => env('FACEBOOK_APP_ID'),
    'app_secret'      => env('FACEBOOK_APP_SECRET'),
    'graph_version'   => env('GRAPH_API_VERSION', 'v19.0'),
    'webhook_token'   => env('META_WEBHOOK_VERIFY_TOKEN'),
],
```

### 8.3 Menyimpan Long-Lived Access Token

Instagram short-lived token berlaku hanya 1 jam. Harus ditukar ke long-lived token (60 hari) setelah OAuth callback. Tambahkan proses exchange token di `handleInstagramCallback()`:

```php
// Tukar short-lived token ke long-lived token
$longLivedResponse = Http::get('https://graph.instagram.com/access_token', [
    'grant_type'        => 'ig_exchange_token',
    'client_secret'     => config('services.instagram.client_secret'),
    'access_token'      => $tokenData['access_token'],
]);

$longLivedToken = $longLivedResponse->json()['access_token'];
// Simpan $longLivedToken ke SocialAccount model, bukan short-lived token
```

---

## 9. Testing di Development Mode

### 9.1 Menambahkan Test Users

1. Buka **App Roles → Roles**
2. Klik **Add Testers**
3. Masukkan Facebook username tester
4. Tester akan menerima permintaan — harus dikonfirmasi di [developers.facebook.com/apps/invitations](https://developers.facebook.com/apps/invitations)

### 9.2 Graph API Explorer

Gunakan [Graph API Explorer](https://developers.facebook.com/tools/explorer/) untuk testing endpoint sebelum coding:

1. Pilih **App** (Publishin)
2. Generate token dengan permissions yang diperlukan
3. Test endpoint seperti:
   - `GET /me?fields=id,username` — cek koneksi Instagram
   - `GET /me/media?fields=id,caption,media_type,timestamp` — list media
   - `GET /{media-id}/insights?metric=impressions,reach` — ambil insights
   - `GET /me/accounts` — list Facebook Pages

### 9.3 Access Token Debugger

Selalu verifikasi token dengan [Token Debugger](https://developers.facebook.com/tools/debug/accesstoken/):
- Cek expiry date
- Verifikasi permissions yang di-grant
- Cek apakah token masih valid

### 9.4 Instagram Test Users

Untuk testing Instagram:
1. Buka **Instagram → Basic Display → User Token Generator**
2. Klik **Add or Remove Instagram Testers**
3. Masukkan Instagram username tester
4. Tester login ke Instagram → Settings → Apps and Websites → **Tester Invites** → Accept

---

## 10. Common Errors dan Fixes

### Error: `OAuthException` — Invalid OAuth 2.0 Access Token

```json
{
  "error": {
    "message": "Invalid OAuth access token.",
    "type": "OAuthException",
    "code": 190
  }
}
```

**Penyebab:** Token expired atau revoked.

**Fix:** Refresh token atau minta user reconnect. Cek expired_at di `SocialAccount` model dan schedule refresh sebelum expired.

---

### Error: `(#200) The user hasn't authorized the application to perform this action`

**Penyebab:** Permission yang diperlukan belum di-grant user, atau permission tidak ada di scope OAuth.

**Fix:** Pastikan semua permission yang diperlukan ada di `scope` parameter saat redirect:

```php
// Di SocialAccountController::redirect()
'scope' => 'instagram_basic,instagram_content_publish,instagram_manage_insights',
```

---

### Error: `(#10) Application does not have permission`

**Penyebab:** App belum mendapat Advanced Access untuk permission tersebut (masih dalam review atau belum submit review).

**Fix:**
- Gunakan test user (untuk development)
- Submit App Review untuk production access

---

### Error: `Invalid redirect_uri`

**Penyebab:** URL callback di kode berbeda dengan yang didaftarkan di Meta Developer Portal.

**Fix:** Pastikan URL di Valid OAuth Redirect URIs *exactly* sama, termasuk trailing slash dan `https://` vs `http://`:

```php
// Di SocialAccountController, pastikan menggunakan route() helper
'redirect_uri' => route('social.callback', 'instagram'),
// Hasilnya: https://app.publishin.id/auth/instagram/callback
```

---

### Error: `Media creation limit reached`

**Penyebab:** Instagram membatasi pembuatan media container. Batas: 25 container per 24 jam per user.

**Fix:** Implementasikan rate limiting di queue job publishing. Simpan timestamp terakhir publish per akun.

---

### Error: `The image url is not properly formatted or the image is not accessible`

**Penyebab:** URL gambar tidak bisa diakses oleh server Meta. Gambar harus publicly accessible.

**Fix:** Pastikan gambar disimpan di storage publik (S3, Cloudflare R2, dll) dan URL bisa diakses tanpa autentikasi. URL lokal (`localhost` atau IP internal) tidak akan bekerja.

---

### Error: `Unsupported post request` saat publish ke Facebook Page

**Penyebab:** Menggunakan user access token, bukan page access token.

**Fix:** Saat menyimpan Facebook Page di `handleFacebookCallback()`, gunakan `$page['access_token']` (page access token), bukan user access token. Kode yang ada di `SocialAccountController.php` sudah benar:

```php
'access_token' => $page['access_token'], // Page access token, bukan user token
```

---

### Error: App di-reject saat App Review

**Penyebab umum:**
1. Demo video tidak menunjukkan permission digunakan
2. Privacy Policy tidak membahas data Meta yang dikumpulkan
3. Use case tidak jelas

**Fix:**
- Buat demo video yang *explicitly* menampilkan setiap permission dalam aksi
- Pastikan Privacy Policy menyebut: "Kami mengakses data Instagram/Facebook untuk keperluan X, Y, Z"
- Tulis use case dalam bahasa Inggris yang jelas dan spesifik

---

## Referensi

- [Meta for Developers — Instagram Graph API](https://developers.facebook.com/docs/instagram-api)
- [Meta for Developers — Pages API](https://developers.facebook.com/docs/pages)
- [Graph API Explorer](https://developers.facebook.com/tools/explorer/)
- [App Review — Permissions and Features](https://developers.facebook.com/docs/app-review)
- [Access Token Debugger](https://developers.facebook.com/tools/debug/accesstoken/)
- [Meta API Changelog](https://developers.facebook.com/docs/graph-api/changelog)
