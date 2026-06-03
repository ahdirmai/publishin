# Publishin — Claude Code Context

## Project

Laravel 13 + Vue 3 + Inertia.js SaaS for social media scheduling and analytics.
Domain: `publishin.ahdirmai.id` | Stack: PHP 8.3, MySQL 8.0, Redis 7, Nginx, Docker Compose.

## Stack

- **Backend**: Laravel 13, PHP-FPM 8.3
- **Frontend**: Vue 3 (Composition API, TypeScript), Inertia.js, Tailwind CSS
- **Queue**: Redis + Laravel Queue worker
- **WebSocket**: Laravel Reverb (port 8080 internal)
- **Media**: Spatie Media Library v11 (thumbnails → `post_media` collection)
- **Tunnel**: Cloudflare Tunnel → `localhost:8003` → Nginx (no SSL certs on server)

## Docker Compose Services

| Service     | Container             | Port              |
|-------------|----------------------|-------------------|
| app         | publishin_app        | internal :9000    |
| nginx       | publishin_nginx      | host 8003 → 80   |
| mysql       | publishin_mysql      | internal :3306    |
| redis       | publishin_redis      | internal :6379    |
| queue       | publishin_queue      | —                 |
| scheduler   | publishin_scheduler  | —                 |
| reverb      | publishin_reverb     | internal :8080    |

## Key Commands (run inside app container)

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan storage:link
docker compose exec app php artisan queue:restart
```

## Deploy Flow

```bash
git pull origin main
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan queue:restart
```

## Environment Notes

- `.env` is **gitignored** — never commit credentials
- `APP_ENV=production`, `APP_DEBUG=false` on server
- `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`, `CACHE_STORE=redis`
- `DB_HOST=mysql` (service name inside Docker network)
- `REDIS_HOST=redis` (service name inside Docker network)
- `REVERB_HOST=publishin.ahdirmai.id`, `REVERB_SCHEME=https`, `REVERB_PORT=443`

## Social Platform OAuth

| Platform  | Status     | Auth URL                              |
|-----------|------------|---------------------------------------|
| TikTok    | Active ✅  | PKCE OAuth v2                         |
| Instagram | Active ✅  | Instagram Login API (no FB required)  |
| Threads   | Active ✅  | `threads.net/oauth/authorize`         |
| Facebook  | Removed    | —                                     |
| Twitter   | Coming soon| —                                     |
| YouTube   | Coming soon| —                                     |

## Key Files

```
app/Services/AnalyticsSyncService.php   # Platform import + sync logic
app/Services/AnalyticsService.php       # Analytics queries (reads analytics_sum_* columns)
app/Http/Controllers/SocialAccountController.php  # OAuth redirect + callback
resources/js/Pages/Analytics/PerKonten.vue        # Per-content analytics + sync button
resources/js/Pages/Analytics/ContentDetail.vue    # Post detail with video preview
resources/js/Pages/Settings/Index.vue             # Platform connections UI
```

## Database Notes

- `post_versions` table holds `analytics_sum_reach`, `analytics_sum_impressions`, etc. as direct columns (not joined from `post_analytics` table)
- Do NOT use `withSum()`/`withAvg()` in ORDER BY — use direct column references
- Thumbnails stored via Spatie Media Library, collection `post_media`, conversion `thumb`

## Security

- All API credentials in `.env` only (gitignored)
- `trustProxies(at: '*')` enabled — X-Forwarded-Proto from Cloudflare trusted
- `fastcgi_param HTTPS on` set in nginx — Laravel sees HTTPS correctly
