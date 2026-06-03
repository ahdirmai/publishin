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
- **Analytics Dashboard** — KPI terpusat, charts performa, demografi audiens
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

```bash
cp .env.example .env
# Edit .env — isi DB_PASSWORD, REVERB_APP_KEY, ANTHROPIC_API_KEY, dll

# Letakkan SSL cert di docker/nginx/ssl/
docker compose up -d

# Setelah container up
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=SubscriptionPlanSeeder
docker compose exec app php artisan db:seed --class=RolePermissionSeeder
```

## Struktur Project

```
app/
├── Http/Controllers/
│   ├── Auth/          # Login, Register, ForgotPassword
│   └── DashboardController.php
├── Models/            # User, Post, SocialAccount, Subscription, dll
└── Http/Middleware/   # HandleInertiaRequests, CheckSubscriptionLimit

database/
├── migrations/        # 17 migrations
├── seeders/           # SubscriptionPlanSeeder, RolePermissionSeeder
└── factories/         # UserFactory (withPlan), SocialAccountFactory, PostFactory

resources/js/
├── Pages/             # Inertia pages
│   ├── Auth/          # Login, Register, ForgotPassword, VerifyEmail
│   └── Dashboard/     # Index
├── Layouts/           # AppLayout (sidebar + topbar + hatch SVG defs)
├── Components/
│   ├── layout/        # AppSidebar, AppTopbar, StickyNote
│   └── ui/            # AppButton, AppCard, AppToggle, AppProgress,
│                      # KpiCard, PlatformBadge, StatusBadge, HashtagChip
└── composables/       # useStickyNote

docker/
├── Dockerfile         # PHP 8.3-fpm-alpine
└── nginx/default.conf # Nginx + SSL + WebSocket proxy
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
| Phase 1 — Foundation & Auth | 98% ✓ |
| Phase 2 — Content Management | Belum mulai |
| Phase 3 — Analytics & AI | Belum mulai |
| Phase 4 — Reports & Settings | Belum mulai |
| Phase 5 — Polish & Deploy | Belum mulai |

## License

Private — All rights reserved.
