# Changelog

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased]

### Planned — Phase 2
- Social platform OAuth (Instagram Graph API, Facebook Pages API)
- Compose screen (caption editor, media upload, scheduler)
- Calendar screen (monthly grid + weekly list)
- Publishing engine (Laravel Queue + exponential backoff retry)

---

## [0.1.0] — 2026-06-03

Initial release — Phase 1 Foundation & Auth selesai.

### Added

**Project Setup**
- Laravel 13.13.0 + PHP 8.3 project initialization
- Vue.js 3 + Inertia.js + TypeScript frontend stack
- Tailwind CSS v4 dengan design tokens editorial/sketch aesthetic
- Vite config (TypeScript, Vue plugin, Tailwind v4 plugin)
- Laravel Horizon (queue monitoring)
- Laravel Reverb (WebSocket)
- Spatie Media Library v11 (file uploads + conversions)
- Spatie Permission v6 (RBAC)
- Laravel Sanctum v4.3 (API auth)
- Saloon PHP v3 (typed HTTP client untuk platform APIs)
- Docker Compose setup (8 services: app, nginx, mysql, redis, queue, scheduler, reverb, horizon)
- Nginx config dengan SSL + WebSocket proxy

**Database Layer**
- 17 migrations: users, organizations, organization_members, subscription_plans, subscriptions, social_accounts, posts, post_versions, post_analytics, account_analytics, tags, post_tag, notification_settings, webhooks, webhook_deliveries, permission_tables (Spatie), media_table (Spatie)
- Models: User, Organization, OrganizationMember, SubscriptionPlan, Subscription, SocialAccount, Post, PostVersion, PostAnalytic, AccountAnalytic, Tag
- `SocialAccount` — enkripsi/dekripsi `access_token` + `refresh_token` via Laravel `Crypt`
- `PostVersion` — implements Spatie `HasMedia` dengan konversi thumb (36×36) dan preview (800px)
- `UserFactory` dengan `withPlan()` state, `SocialAccountFactory`, `PostFactory`
- `SubscriptionPlanSeeder` — 3 plans: Starter (Rp 99k), Pro (Rp 149k), Agency (Rp 299k)
- `RolePermissionSeeder` — 5 roles (super_admin, agency_owner, agency_admin, editor, viewer) + 17 permissions

**Auth**
- Register email/password
- Login email/password dengan session
- Logout
- Forgot/reset password
- Email verification (MustVerifyEmail + verify routes)
- Pages: `Auth/Login.vue`, `Auth/Register.vue`, `Auth/ForgotPassword.vue`, `Auth/VerifyEmail.vue`
- Middleware: `HandleInertiaRequests` (share auth + ziggy + flash), `CheckSubscriptionLimit`

**App Shell (Layout)**
- `AppLayout.vue` — sidebar + topbar + main slot + SVG hatch pattern defs global (h-r, h-b, h-g, h-k)
- `AppSidebar.vue` — 6 nav items, checkbox indicator, active state terracotta, plan badge, user name
- `AppTopbar.vue` — judul halaman, notifikasi bell, CTA "Buat Konten"
- `StickyNote.vue` — sticky note annotation per screen (rotate -1.2deg)
- `useStickyNote.ts` — composable mapping screen → teks note

**Design System**
- Custom Tailwind v4 tokens: colors (paper, ink, ink-2, ink-3, accent-r, accent-b, forest, sticky), fonts (JetBrains Mono sans, Georgia serif), border-radius 2px
- CSS utilities: `.kpi-value` (Georgia italic bold 34px), `.label-upper` (9px uppercase), `.grid-paper` (dot grid background)
- `AppButton.vue` — 4 variants (default/primary/danger/ghost), 3 sizes
- `AppCard.vue` — 2 styles (default 1.5px border / sm muted border)
- `AppToggle.vue` — toggle switch v-model 30×17px, ARIA-compliant
- `AppProgress.vue` — progress bar 5px height, 4 warna
- `KpiCard.vue` — serif italic KPI value, delta coloring (forest/terracotta/neutral)
- `PlatformBadge.vue` — IG/FB/TT/X/YT badge dengan brand colors
- `StatusBadge.vue` — 6 status (published/scheduled/draft/failed/publishing/cancelled)
- `HashtagChip.vue` — pill hashtag, clickable + removable modes

**Config**
- `config/publishin.php` — AI quota per plan, platform list, token refresh window
- `.env.example` — production template (MySQL, Redis, Reverb, Sanctum, Anthropic API)
