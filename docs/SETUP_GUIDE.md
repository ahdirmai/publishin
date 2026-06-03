# Setup Guide — Publishin

Urutan instalasi dari nol sampai production-ready.

---

## 1. Prerequisites

| Requirement | Versi Minimum |
|-------------|---------------|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL / MariaDB | 8.0 / 10.11+ |
| Redis | 7.x |
| Nginx | 1.24+ |
| Supervisor | 4.x |

Extensions PHP yang wajib aktif:
```
mbstring, pdo_mysql, redis, gd, curl, zip, bcmath, openssl
```

---

## 2. Clone & Install Dependencies

```bash
git clone https://github.com/youruser/publishin.git
cd publishin

composer install --no-dev --optimize-autoloader
npm install
npm run build
```

---

## 3. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — isi semua nilai di bawah.

---

## 4. Variabel Wajib

### App

```env
APP_NAME=Publishin
APP_ENV=production
APP_KEY=                        # diisi otomatis oleh artisan key:generate
APP_DEBUG=false
APP_URL=https://publishin.id
APP_LOCALE=id
```

### Database (MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=publishin
DB_USERNAME=publishin
DB_PASSWORD=your_strong_password
```

Buat DB dan user:
```sql
CREATE DATABASE publishin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'publishin'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON publishin.* TO 'publishin'@'localhost';
FLUSH PRIVILEGES;
```

### Redis

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null             # isi jika Redis pakai auth
REDIS_PORT=6379

QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_STORE=redis
```

### Email (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io      # ganti ke provider real di prod
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@publishin.id"
MAIL_FROM_NAME="Publishin"
```

Provider yang direkomendasikan: **Mailgun**, **Postmark**, atau **Amazon SES**.

---

## 5. API Keys

### Anthropic (Claude AI)

Dipakai untuk: AI caption generator, saran hashtag, best posting time.

1. Daftar di [console.anthropic.com](https://console.anthropic.com)
2. Buat API key baru
3. Isi `.env`:

```env
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxx
```

---

### Redirect URI — Environment

OAuth callback URL berbeda per environment. Tambahkan **semua** ke masing-masing platform dashboard agar bisa test lokal maupun production.

| Environment | Base URL | Contoh Callback |
|-------------|----------|----------------|
| Local (Herd) | `http://publishin.test` | `http://publishin.test/auth/instagram/callback` |
| Production | `https://publishin.id` | `https://publishin.id/auth/instagram/callback` |

Di `.env`:
```env
# Local
APP_URL=http://publishin.test

# Production
APP_URL=https://publishin.id
```

Route `social.callback` otomatis build dari `APP_URL`:
```
route('social.callback', 'instagram')
→ {APP_URL}/auth/instagram/callback
```

Daftarkan **kedua URI** di tiap platform agar tidak ganti-ganti saat pindah environment.

---

### Meta (Instagram + Facebook)

Satu app untuk keduanya.

**Langkah:**
1. Buka [developers.facebook.com](https://developers.facebook.com)
2. Buat app baru → pilih tipe **Business**
3. Tambah produk: **Instagram Basic Display** + **Facebook Login**
4. Di App Settings → Basic: catat App ID dan App Secret
5. Set OAuth Redirect URIs (tambahkan keduanya):
   ```
   http://publishin.test/auth/instagram/callback
   https://publishin.id/auth/instagram/callback
   http://publishin.test/auth/facebook/callback
   https://publishin.id/auth/facebook/callback
   ```
6. Request permissions:
   - Instagram: `instagram_basic`, `instagram_content_publish`, `instagram_manage_insights`
   - Facebook: `pages_manage_posts`, `pages_read_engagement`, `pages_show_list`

```env
INSTAGRAM_CLIENT_ID=your_meta_app_id
INSTAGRAM_CLIENT_SECRET=your_meta_app_secret
FACEBOOK_APP_ID=your_meta_app_id
FACEBOOK_APP_SECRET=your_meta_app_secret
```

> App ID dan Secret sama untuk IG dan FB karena satu Meta app.

**Testing lokal:** tambahkan akun developer sebagai **Test User** di App Dashboard → Roles. Test users bisa OAuth tanpa App Review.

**Status review:** IG content publish dan FB page posting butuh App Review sebelum bisa dipakai user lain. Estimasi 1–2 minggu.

---

### TikTok

Lihat `docs/TIKTOK_API_SETUP.md` untuk panduan lengkap termasuk sandbox.

**Redirect URIs (daftarkan keduanya di Login Kit):**
```
http://publishin.test/auth/tiktok/callback
https://publishin.id/auth/tiktok/callback
```

**Submit App Review — estimasi 2–4 minggu.** Gunakan sandbox untuk development selama menunggu.

```env
TIKTOK_CLIENT_KEY=your_tiktok_client_key
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret
TIKTOK_SANDBOX=true                          # true saat local/dev, false di production
```

---

### YouTube (Google)

**Langkah:**
1. Buka [console.cloud.google.com](https://console.cloud.google.com)
2. Buat project baru
3. Aktifkan **YouTube Data API v3**
4. Credentials → OAuth 2.0 Client ID → tipe **Web Application**
5. Authorized redirect URIs (tambahkan keduanya):
   ```
   http://publishin.test/auth/youtube/callback
   https://publishin.id/auth/youtube/callback
   ```
6. Request scopes: `https://www.googleapis.com/auth/youtube.upload`, `https://www.googleapis.com/auth/youtube.readonly`

```env
YOUTUBE_CLIENT_ID=your_google_client_id
YOUTUBE_CLIENT_SECRET=your_google_client_secret
```

> Google OAuth mendukung `http://` untuk local development — tidak perlu HTTPS di `publishin.test`.

---

### Twitter / X

**Langkah:**
1. Daftar di [developer.twitter.com](https://developer.twitter.com)
2. Buat project + app
3. **Free tier** hanya read-only — butuh **Basic ($100/bln)** untuk posting
4. App Settings → User authentication settings → Callback URI:
   ```
   http://publishin.test/auth/twitter/callback
   https://publishin.id/auth/twitter/callback
   ```

```env
TWITTER_CLIENT_ID=your_twitter_client_id
TWITTER_CLIENT_SECRET=your_twitter_client_secret
```

---

## 6. WebSocket — Laravel Reverb

Generate key dan secret (string random 32 karakter):

```bash
php artisan tinker
>>> str()->random(32)  # jalankan 2x untuk key dan secret
```

```env
REVERB_APP_ID=publishin
REVERB_APP_KEY=your_32_char_random_key
REVERB_APP_SECRET=your_32_char_random_secret
REVERB_HOST=publishin.id
REVERB_PORT=8080
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 7. Payment Gateway (Phase 4.3 — belum diimplementasi)

Pilih salah satu:

### Midtrans (rekomendasi)

1. Daftar di [midtrans.com](https://midtrans.com)
2. Dashboard → Settings → Access Keys
3. Salin Server Key dan Client Key

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxx   # prefix SB- untuk sandbox
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxx
MIDTRANS_IS_PRODUCTION=false                # ganti true di prod
```

### Xendit (alternatif)

```env
XENDIT_SECRET_KEY=xnd_development_xxxxxxx
XENDIT_WEBHOOK_TOKEN=your_webhook_token
```

---

## 8. Migrasi & Storage

```bash
php artisan migrate --force
php artisan storage:link
```

---

## 9. Nginx Config

```nginx
server {
    listen 80;
    server_name publishin.id www.publishin.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name publishin.id www.publishin.id;

    root /var/www/publishin/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/publishin.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/publishin.id/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Laravel Reverb WebSocket
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }
}
```

---

## 10. Supervisor (Queue Worker + Reverb)

Buat file `/etc/supervisor/conf.d/publishin.conf`:

```ini
[program:publishin-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/publishin/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/publishin-worker.log

[program:publishin-reverb]
command=php /var/www/publishin/artisan reverb:start --host=127.0.0.1 --port=8080
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/publishin-reverb.log

[program:publishin-scheduler]
command=bash -c "while [ true ]; do php /var/www/publishin/artisan schedule:run; sleep 60; done"
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/publishin-scheduler.log
```

Aktifkan:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start all
```

---

## 11. Cache Warmup & Optimization (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 12. Checklist Setup

### Wajib (app tidak bisa jalan tanpa ini)
- [ ] `APP_KEY` di-generate
- [ ] MySQL: DB + user + password
- [ ] `php artisan migrate`
- [ ] Redis berjalan
- [ ] Nginx configured + SSL
- [ ] Supervisor running (queue worker)

### Fitur AI
- [ ] `ANTHROPIC_API_KEY` diisi

### Email
- [ ] SMTP configured (Mailgun/Postmark/SES)

### Platform Connections
- [ ] Meta app dibuat (IG + FB OAuth)
- [ ] App Review Meta disubmit
- [ ] TikTok app dibuat + App Review disubmit (2–4 minggu)
- [ ] Google Cloud project + YouTube API enabled
- [ ] Twitter/X Developer account (Basic tier jika perlu posting)

### Real-time
- [ ] Reverb key/secret di-generate
- [ ] Nginx proxy ke port 8080 aktif
- [ ] Supervisor `publishin-reverb` running

### Payment (Phase 4.3)
- [ ] Pilih Midtrans atau Xendit
- [ ] Daftar + dapatkan API keys
- [ ] Implementasi `SubscriptionController` + webhook

---

## Prioritas Setup

```
Tahap 1 (sekarang)   → APP_KEY, DB, Redis, Nginx, Queue Worker
Tahap 2 (minggu ini) → Anthropic API, SMTP
Tahap 3 (bulan ini)  → Meta app + App Review submit
Tahap 4 (sambil tunggu review) → TikTok + YouTube + Twitter API
Tahap 5 (siap launch) → Payment gateway + Reverb WebSocket
```
