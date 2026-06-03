# Publishin

Platform manajemen media sosial untuk UMKM dan digital agency Indonesia — harga rupiah, UI Bahasa Indonesia, AI-powered untuk konten lokal.

## Tech Stack

- **Backend:** Laravel 13 + PHP 8.3
- **Frontend:** Vue.js 3 (Composition API) + Inertia.js + TypeScript
- **Styling:** Tailwind CSS v4 (design token editorial/sketch aesthetic)
- **Database:** MySQL 8.0
- **Queue:** Laravel Horizon + Redis
- **WebSocket:** Laravel Reverb
- **File Storage:** Spatie Media Library v11
- **RBAC:** Spatie Permission v6
- **Platform API Client:** Saloon PHP v3
- **Auth:** Laravel Sanctum

## Fitur Utama

- **Content Scheduler** — Jadwalkan konten ke Instagram, Facebook, TikTok, Twitter/X, YouTube
- **Analytics Dashboard** — KPI terpusat, charts performa, demografi audiens, sync otomatis dari platform API
- **Platform Import** — Tarik video/post yang sudah ada di TikTok & Instagram langsung ke dashboard
- **AI Caption Generator** — Generate caption Bahasa Indonesia via Claude API
- **Laporan PDF** — Export laporan white-label untuk klien
- **Kalender Konten** — Overview bulanan semua konten terjadwal

## Requirement

- PHP 8.3+
- Node.js 20+
- MySQL 8.0 atau SQLite (dev)
- Redis 7+
- Composer 2.x

## Instalasi (Development)

```bash
# Clone dan install dependencies
git clone <repo-url> publishin
cd publishin

composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Jalankan migrasi dan seeding
php artisan migrate
php artisan db:seed

# Build assets
npm run dev
```

## Instalasi (Docker — Production)

Server pakai Cloudflare Tunnel — tidak perlu SSL cert di server. Lihat [`docs/SERVER_SETUP.md`](docs/SERVER_SETUP.md) untuk panduan lengkap.

```bash
cp .env.example .env
# Edit .env — isi DB_PASSWORD, REVERB_APP_KEY, ANTHROPIC_API_KEY,
#             TELESCOPE_ALLOWED_EMAILS, dll

docker compose up -d

# Setelah container up
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan db:seed --class=SubscriptionPlanSeeder
docker compose exec app php artisan db:seed --class=RolePermissionSeeder
```

## Struktur Project

```
app/
├── Http/Controllers/
│   ├── Auth/                   # Login, Register, ForgotPassword
│   ├── DashboardController.php
│   ├── PostController.php      # CRUD post
│   ├── MediaController.php     # Upload via Spatie Media Library
│   ├── CalendarController.php  # Calendar index + JSON API
│   ├── SocialAccountController.php  # OAuth Instagram/Facebook/TikTok (PKCE)
│   └── MarketingController.php # Landing page + waitlist
├── Http/Requests/
│   └── StorePostRequest.php    # Validasi post (pesan Bahasa Indonesia)
├── Models/            # User, Post, SocialAccount, Subscription, Waitlist, dll
├── Services/
│   ├── PostService.php         # createPost, schedulePost, saveDraft
│   ├── AnalyticsService.php    # KPI, overview, per-post detail, vs avg
│   ├── AnalyticsSyncService.php # Import posts + sync metrics dari platform API
│   ├── AIService.php           # Claude API — caption, hashtag, best-time
│   ├── ReportService.php       # Generate client report (queue-based)
│   ├── NotificationService.php # In-app notifications + setting toggles
│   ├── InstagramService.php    # Saloon v3 — publish, insights
│   └── FacebookService.php     # Saloon v3 — publish, insights
├── Jobs/
│   └── PublishScheduledPost.php  # Queue job, retry 3x, exponential backoff
├── Console/
│   └── Commands/ProcessScheduledPosts.php  # publishin:process-scheduled
└── Http/Middleware/   # HandleInertiaRequests, CheckSubscriptionLimit

database/
├── migrations/        # 18 migrations (+ waitlists)
├── seeders/           # SubscriptionPlanSeeder, RolePermissionSeeder
└── factories/         # UserFactory (withPlan), SocialAccountFactory, PostFactory

resources/js/
├── Pages/             # Inertia pages
│   ├── Auth/          # Login, Register, ForgotPassword, VerifyEmail
│   ├── Dashboard/     # Index
│   ├── Compose/       # Index — 2-col editor, IG preview, hashtag chips
│   ├── Calendar/      # Index — monthly grid + weekly table
│   ├── Analytics/     # Overview, PerKonten (table + sync), ContentDetail (video preview)
│   ├── Reports/       # Index — generate + download client reports
│   ├── Settings/      # Index — profile, platform connections, notifications, billing
│   └── Marketing/     # Index (landing page), Waitlist, Privacy, Terms
├── Layouts/           # AppLayout (sidebar + topbar + hatch SVG defs)
├── Components/
│   ├── calendar/      # CalendarGrid, CalendarCell, WeeklyTable
│   ├── layout/        # AppSidebar, AppTopbar, StickyNote
│   └── ui/            # AppButton, AppCard, AppToggle, AppProgress,
│                      # KpiCard, PlatformBadge, StatusBadge, HashtagChip
└── composables/       # useStickyNote

docker/
├── Dockerfile         # PHP 8.3-fpm-alpine
├── nginx/default.conf # Nginx HTTP (Cloudflare Tunnel handle SSL)
└── mysql/my.cnf       # MySQL utf8mb4 + InnoDB tuning

tests/
├── Feature/           # Auth, Analytics, Posts, Settings
└── Unit/Services/     # AnalyticsService unit tests

docs/
└── SERVER_SETUP.md    # Panduan deploy lengkap (Cloudflare Tunnel)
```

## Plans & Pricing

| Plan | Harga | Fitur |
|---|---|---|
| Starter | Rp 99.000/bln | 3 akun sosial, 30 post/bln |
| Pro | Rp 149.000/bln | 10 akun, unlimited post, AI caption, white-label report |
| Agency | Rp 299.000+/bln | Unlimited, team collaboration, API access |

## Roles

| Role | Akses |
|---|---|
| `super_admin` | Full access |
| `agency_owner` | Full + billing management |
| `agency_admin` | Full kecuali billing |
| `editor` | Buat & edit post, lihat analytics |
| `viewer` | Read-only |

## Development Progress

Lihat [`../docs/TASK_LIST.md`](../docs/TASK_LIST.md) untuk progress lengkap.

| Phase | Status |
|---|---|
| Phase 1 — Foundation & Auth | ✅ Done (v0.1.0) |
| Phase 2 — Content Management | ✅ Done (v0.2.0) |
| Phase 3 — Analytics & AI | ✅ Done (v0.4.0) |
| Phase 4 — Reports, Settings & Notifications | ✅ Done (v0.4.0) |
| Phase 4.5 — Platform OAuth & Analytics Sync | ✅ Done (v0.4.1) |
| Phase 5 — Polish & Deploy | ✅ Done (v0.5.0) |

### Platform API Status

| Platform | OAuth | Publish | Import | Analytics Sync |
|---|---|---|---|---|
| TikTok | ✅ Approved | 🔲 | ✅ | ✅ |
| Instagram | ✅ | 🔲 | ✅ | 🔲 |
| Facebook | ✅ | 🔲 | 🔲 | 🔲 |
| YouTube | 🔲 | 🔲 | 🔲 | 🔲 |
| Twitter/X | 🔲 | 🔲 | 🔲 | 🔲 |

## License

Private — All rights reserved.
