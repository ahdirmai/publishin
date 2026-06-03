# Changelog

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased]

### Planned — Phase 5
- Pest unit + feature tests (≥70% coverage)
- Vitest component tests
- Playwright E2E flows
- Deployment: Nginx, Supervisor, Redis, queue workers

---

## [0.4.1] — 2026-06-03

Platform OAuth production-ready + Analytics Sync.

### Added

**TikTok Integration — App Review Approved ✅**
- TikTok OAuth v2 dengan PKCE (Proof Key for Code Exchange) di `SocialAccountController`
- Scopes live: `user.info.basic`, `user.info.profile`, `user.info.stats`, `video.list`, `video.upload`, `video.publish`
- `AnalyticsSyncService::importTikTokPosts()` — tarik video dari `/v2/video/list/`, buat `Post` + `PostVersion` otomatis, download thumbnail via Spatie Media Library
- Sync analytics per-video via `/v2/video/query/` — reach, like, comment, share
- `embed_link` disimpan ke `post_url` untuk video preview

**Instagram & Facebook OAuth**
- Instagram redirect lewat Facebook Graph API (bukan Instagram Basic Display API) — required untuk content publishing
- `handleInstagramCallback` — exchange code → user token → page tokens → IG Business Account linked ke FB Page
- Import post Instagram: `/me/accounts` → `/media` → `PostVersion` + thumbnail download

**Analytics Sync (PerKonten page)**
- "Sync Data" button di filter bar — import semua post dari platform + update analytics
- Per-row "Sync" button di expand panel — sync satu post
- Spinner + pesan hasil, reload data setelah sync

**Content Detail Improvements**
- KPI cards baca dari `post_versions.analytics_sum_*` (fix: sebelumnya baca tabel `post_analytics` yang kosong)
- Panel "Preview Post" — mock frame platform dengan avatar, username, gambar, caption
- Panel "Preview Video" — TikTok embed via `<iframe>`, fallback thumbnail + link
- Response tambah: `post_url`, `preview_url`, `platform_post_id`, `platform_username`

**Privacy Policy & Terms of Service**
- `/privacy` — 11 section termasuk section TikTok data eksplisit (wajib App Review)
- `/terms` — 14 section, hukum Indonesia (Pasal 1313 KUHPerdata)
- Footer links di landing page diperbarui

**Infrastructure**
- `trustProxies(at: '*')` di `bootstrap/app.php` — fix HTTPS mixed content di balik reverse proxy / Herd Expose
- `URL::forceScheme('https')` di `AppServiceProvider` — force HTTPS bila `APP_URL` https
- Migration `add_analytics_columns_to_post_versions` — 8 kolom analytics langsung di `post_versions`
- Migration `add_thumbnail_url_to_post_versions` — kolom fallback URL thumbnail external
- `PostVersion::$fillable` diperluas dengan semua kolom analytics + `thumbnail_url`, `caption`

### Fixed
- `analytics_sum_reach` column not found in ORDER clause — `withSum()` tidak bisa di-ORDER BY dalam query dengan JOIN, ganti baca kolom langsung dari `post_versions`
- TikTok token parsing — token di root response `{access_token:...}` bukan `data.access_token`
- TikTok `fields` parameter wajib di query string URL, bukan request body
- `posts.title` truncation — caption TikTok bisa >255 chars, pakai `mb_substr(caption, 0, 255)`
- Duplicate `Post` per video — ganti `firstOrCreate(title)` ke lookup by `platform_post_id` dulu
- `caption` kosong di PostVersion imported — sekarang disimpan saat import sehingga title muncul di analytics

---

## [0.4.0] — 2026-06-03

Phase 4 — Reports, Settings & Notifications selesai.

### Added

**Reports (4.1)**
- `ClientReport` model + `client_reports` migration (platforms JSON, 5 include booleans, status lifecycle)
- `ReportService` — queue(), getHistory(), getPreviewData()
- `GenerateClientReport` Job — 2 tries, 120s timeout, backoff [60,300], saves .txt to `storage/app/reports/{user_id}/`
- `ReportController` — index, store, preview, download
- `Pages/Reports/Index.vue` — period presets, custom date range, platform checkboxes, include section, live preview card, history table with download links

**Settings (4.2)**
- `NotificationSetting` model + migration (5 boolean toggles per user)
- `SettingsController` — index, updateProfile, updateNotifications
- `Pages/Settings/Index.vue` — 2-kolom: profil+platform kiri (avatar inisial, nama/email/timezone form, platform connections), notif+billing kanan (5 AppToggle items, Plan & Billing card)

**Notification System (4.4)**
- `NotificationService` — getForUser(), getUnreadCount(), markAllRead(), markRead(), send() with NotificationSetting toggle checks
- `NotificationController` — index (list + unread count JSON), markAllRead, markRead
- `HandleInertiaRequests` — shared `notif_count` (live unread count injected into every Inertia response)
- `AppTopbar.vue` — badge shows real unread count from `$page.props.notif_count`, hidden when zero

**AI Integration (Phase 3 continuation)**
- `AIController` — generateCaption, suggestHashtags, bestTime (delegates to AIService, 429 on quota exceeded)
- `AIService` — Claude API claude-sonnet-4-6, rate limiting 5/min + 50/day per user
- `Pages/Compose/Index.vue` — "✦ AI Generate" and "✦ Saran AI" buttons wired to real endpoints

**Analytics (Phase 3)**
- `AnalyticsService` — getDashboard(), getOverview(), getPostList(), getPostDetail() with Redis caching (30min/1hr)
- `AnalyticsController` — overview, perKonten, contentDetail, overviewData, postListData
- `Pages/Analytics/Overview.vue` — KPI cards, reach bar chart SVG, follower growth multi-line SVG, audience demographics, period/platform filters
- `Pages/Analytics/PerKonten.vue` — paginated post table, expandable rows with metric cards + distribution bars
- `Pages/Analytics/ContentDetail.vue` — 7-col KPI row, daily reach SVG, vs rata-rata comparison, AI insights panel
- `Pages/Dashboard/Index.vue` — real KPI data, engagement trend SVG, postsPerPlatform bar chart, recent posts table, notifications panel, best times widget
- `FetchPostAnalytics` Job + `publishin:fetch-account-analytics` command (daily 02:00)

---

## [0.2.0] — 2026-06-03

Phase 2 — Content Management selesai.

### Added

**Social Platform Connections**
- `SocialAccountController` — OAuth redirect + callback untuk Instagram & Facebook, disconnect
- `InstagramService` (Saloon v3) — publishImagePost, getProfile, getMediaInsights
- `FacebookService` (Saloon v3) — publishPost, getPageInfo, getPostInsights
- `config/services.php` — instagram + facebook OAuth credentials
- `.env.example` — INSTAGRAM_CLIENT_ID/SECRET, FACEBOOK_APP_ID/SECRET

**Compose Screen (2.2)**
- `StorePostRequest` — validasi platforms, caption, scheduled_at, status (Indonesian messages)
- `PostService` — createPost(), updatePost(), saveDraft(), schedulePost(), deletePost() dengan DB::transaction
- `PostController` — create, edit, store, update, destroy
- `Pages/Compose/Index.vue` — 2-kolom: platform checkboxes, caption + char counter, media dropzone, date/time + best-time hint, IG preview panel, hashtag chips AI saran

**Media Upload (2.3)**
- `MediaController` — upload via Spatie Media Library, ownership check, return thumb/preview URLs

**Calendar Screen (2.4)**
- `CalendarController` — index (Inertia) + data (JSON API), group posts by date
- `Pages/Calendar/Index.vue` — prev/next navigation, legend, CalendarGrid, WeeklyTable
- `Components/calendar/CalendarGrid.vue` — 42-cell Mon-start grid, posts grouped by date
- `Components/calendar/CalendarCell.vue` — today highlight, status dots, post label truncated
- `Components/calendar/WeeklyTable.vue` — sortable current-week table, platform + status badges

**Publishing Engine (2.5)**
- `PublishScheduledPost` Job — queue-based, partial publish per platform, retry 3x, exponential backoff via Laravel `$backoff`
- `ProcessScheduledPosts` Command — `publishin:process-scheduled`, dispatch jobs for due posts
- `routes/console.php` — `Schedule::command('publishin:process-scheduled')->everyMinute()`

**Marketing Pages (pre-Phase 2)**
- `Marketing/Index.vue` — full landing page (hero, features, pricing, testimonials, FAQ, footer)
- `Marketing/Waitlist.vue` — waitlist signup with Inertia form, app preview mockup
- `MarketingController` — serve marketing pages + store waitlist
- `waitlists` migration + `Waitlist` model

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
