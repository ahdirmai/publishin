# Task List — Development
## Publishin — Social Media Management Tool
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03 (Sprint 3 complete)  
**Stack:** Laravel 13 + Vue.js 3 + Inertia.js + MySQL  
**Legend:** `[ ]` Todo | `[→]` In Progress | `[x]` Done  

---

## Phase 1 — Foundation & Auth
**Target:** 2–3 minggu  
**Goal:** Project setup, database, auth berjalan, sidebar + layout wireframe-faithful

### 1.1 Project Setup
- [x] Init Laravel 12 project (installed Laravel 13.13.0)
- [x] Install & config Inertia.js + Vue.js 3 + TypeScript
- [x] Setup Tailwind CSS dengan custom theme (design tokens dari wireframe)
- [x] Config Vite untuk asset bundling (vite.config.ts)
- [x] Setup Docker Compose (app, nginx, mysql, redis, queue, scheduler, reverb)
- [x] Config `.env` production template
- [ ] Setup GitHub repo + `.gitignore`
- [x] Install Spatie Media Library v11
- [x] Install Spatie Permission v6
- [x] Install Laravel Horizon + config queues (publishing, analytics, ai, reports, notifications, webhooks)
- [x] Install Laravel Reverb (WebSocket)
- [x] Install Saloon PHP v3 (platform API client)
- [x] Setup Laravel Sanctum

### 1.2 Database & Models
- [x] Migration: `users` (+ timezone, locale, last_login_at)
- [x] Migration: `organizations` + `organization_members`
- [x] Migration: `subscription_plans` + `subscriptions`
- [x] Migration: `social_accounts` (encrypted token fields)
- [x] Migration: `posts` + `post_versions`
- [x] Migration: `post_analytics` + `account_analytics`
- [x] Migration: `tags` + `post_tag`
- [x] Migration: `notification_settings`
- [x] Migration: `webhooks` + `webhook_deliveries`
- [x] Model: `User` dengan enkripsi token via `Crypt` facade
- [x] Model: `Organization`, `SocialAccount`, `Post`, `PostVersion`
- [x] Model: `PostAnalytic`, `AccountAnalytic`
- [x] Seeders: `SubscriptionPlanSeeder` (Starter Rp 99k, Pro Rp 149k, Agency Rp 299k+)
- [x] Seeders: `RolePermissionSeeder` (super_admin, agency_owner, agency_admin, editor, viewer)
- [x] Factory: `UserFactory` dengan `withPlan()` helper
- [x] Factory: `SocialAccountFactory`, `PostFactory`

### 1.3 App Shell (Layout Wireframe)
- [x] `AppLayout.vue` — sidebar + topbar + slot konten + SVG hatch defs global
- [x] `AppSidebar.vue` — nav items dengan checkbox indicator, active state terracotta, plan badge
- [x] `AppTopbar.vue` — judul screen, tombol notifikasi + badge, CTA button
- [x] `StickyNote.vue` — annotation per screen (rotate -1.2deg, bg sticky yellow)
- [x] `useStickyNote.ts` — mapping screen → teks sticky note
- [x] Tailwind config: custom colors (paper, ink, ink-2, ink-3, accent-r, accent-b, forest, sticky)
- [x] Tailwind config: fontFamily (JetBrains Mono sans, Georgia serif), borderRadius 2px
- [x] Grid paper background CSS pattern
- [x] SVG hatch fill pattern defs (h-r, h-b, h-g, h-k) inline di AppLayout

### 1.4 Auth
- [x] `AUTH-001` Register email/password — `POST /register`
- [x] `AUTH-002` Email verification
- [x] `AUTH-003` Login email/password — `POST /login`
- [ ] `AUTH-004` Google OAuth login
- [x] `AUTH-005` Forgot/reset password
- [x] `AUTH-006` Logout
- [x] Middleware: `CheckSubscriptionLimit`
- [x] Pages: `Auth/Login.vue`, `Auth/Register.vue`, `Auth/ForgotPassword.vue`

### 1.5 UI Components (Design System)
- [x] `AppButton.vue` — variants: default/primary/danger/ghost, uppercase monospace, 1.5px ink border
- [x] `AppCard.vue` — default (border-ink 1.5px) + sm (border muted)
- [x] `AppToggle.vue` — 30×17px, ink fill saat ON
- [x] `AppProgress.vue` — 5px height, bg eeede9
- [x] `KpiCard.vue` — serif italic bold 34px value, 9px uppercase label, delta ↑↓
- [x] `PlatformBadge.vue` — IG/TT/FB/X/YT dengan warna per platform
- [x] `StatusBadge.vue` — published(forest)/scheduled(blue)/draft(muted)/failed(red)
- [x] `HashtagChip.vue` — rounded-full, border muted, clickable

---

## Phase 2 — Content Management (Compose + Calendar)
**Target:** 2–3 minggu  
**Goal:** User bisa buat konten, jadwalkan, lihat di kalender

### 2.1 Social Platform Connections
- [x] `PLAT-001` Connect Instagram (Business/Creator) — Instagram Graph API OAuth
- [x] `PLAT-002` Connect Facebook Page — Facebook Pages API OAuth
- [ ] `PLAT-003` Connect TikTok *(needs app review)*
- [x] `PLAT-006` Disconnect platform
- [x] `PLAT-007` OAuth token refresh otomatis (7 hari sebelum expired)
- [ ] `PLAT-008` Banner token expiring warning
- [ ] `PLAT-009` Status indikator (✓ / warning / error)
- [x] `InstagramService.php` via Saloon — publish post, fetch basic metrics
- [x] `FacebookService.php` via Saloon — publish post, fetch basic metrics
- [x] Controller: `SocialAccountController` (connect, callback, disconnect, sync)

### 2.2 Buat Konten / Compose (Screen 3 — `s-compose`)
- [x] `COM-001` Platform multi-select checkboxes (IG, TT, FB, X, YT)
- [x] `COM-002` Caption textarea multiline
- [x] `COM-003` Character counter live update (214 / 2200)
- [x] `COM-005` Caption preview real-time → sync ke preview panel
- [x] `COM-006` Media upload dropzone (drag & drop + click, PNG/JPG/MP4/MOV max 100MB)
- [x] `COM-007` Jadwal tanggal + waktu (date + time input 2-kolom)
- [x] `COM-008` Waktu terbaik hint (💡 static per platform untuk awal)
- [x] `COM-009` Instagram post preview mock
- [x] `COM-011` Simpan Draft button → `POST /posts` status=draft
- [x] `COM-012` Jadwalkan button → `POST /posts` status=scheduled + validasi
- [x] `PostService.php` — schedule(), saveDraft(), validate()
- [x] `StorePostRequest.php` validasi
- [x] Page: `Compose/Index.vue` (2-kolom: form kiri, preview kanan)

### 2.3 Media Upload
- [x] `POST /api/v1/media/upload` — Spatie Media Library
- [x] Conversions: thumb (36×36px), preview (800px wide)
- [x] Validasi: image max 10MB, video max 100MB, format png/jpg/mp4/mov
- [x] `MediaController.php`

### 2.4 Kalender (Screen 2 — `s-calendar`)
- [x] `CAL-001` Monthly calendar grid 7 kolom
- [x] `CAL-002` Cell today highlight (terracotta muted)
- [x] `CAL-003` Dot status per tanggal (.pub hijau, .sched biru, .draft putus)
- [x] `CAL-004` Label post di cell (max 14 karakter + ellipsis)
- [x] `CAL-005` Navigate prev/next bulan
- [x] `CAL-006` Header bulan + tahun (serif italic)
- [x] `CAL-007` Legend (Published/Scheduled/Draft)
- [x] `CAL-008` Weekly list table (tgl, konten, platform, waktu, status)
- [x] `CAL-009` Klik tanggal → buka Compose dengan tanggal pre-filled
- [x] `CAL-010` Tombol "+ Jadwalkan Post"
- [x] `CAL-011` Prev/next cell bulan lain (warna muted)
- [x] `GET /api/v1/calendar?year=&month=` endpoint
- [x] `CalendarController.php`
- [x] Page: `Calendar/Index.vue`
- [x] Component: `CalendarGrid.vue`, `CalendarCell.vue`, `WeeklyTable.vue`

### 2.5 Publishing Engine
- [x] `PUB-001` Queue-based publishing (Laravel Queue + Redis)
- [x] `PUB-002` Status tracking (Pending → Publishing → Published/Failed)
- [x] `PUB-003` Retry 3x dengan exponential backoff
- [x] `PUB-004` Partial publish per platform (satu gagal, lainnya tetap publish)
- [x] `PublishScheduledPost.php` Job
- [x] `ProcessScheduledPosts.php` Command (cron setiap menit)

---

## Phase 3 — Analytics & AI
**Target:** 3–4 minggu  
**Goal:** Analytics dashboard + per konten + content detail + AI features

### 3.1 Analytics Data Fetching
- [x] `FetchAccountAnalytics.php` Command — sync metrics dari platform harian
- [x] `FetchPostAnalytics.php` Job — sync per-post metrics setelah publish
- [x] `AnalyticsService.php` — getOverview(), getPostList(), getPostDetail(), getBestTimes()
- [ ] `ProcessWebhookPayload.php` Job
- [x] Instagram Graph API: impressions, reach, likes, comments, saves, shares
- [x] Facebook Pages API: reach, engagement metrics
- [x] Caching: overview cache 1 jam, dashboard cache 30 menit (Redis)

### 3.2 Analytics Overview (Screen 4a — `s-analytics` tab Overview)
- [x] `ANA-001` Sub-tab navigation Overview / Per Konten
- [x] `ANA-002` Filter periode (30/7/90 hari) dropdown
- [x] `ANA-003` Filter platform dropdown
- [x] `ANA-004` KPI: Total Reach + delta vs periode lalu
- [x] `ANA-005` KPI: Impressions + delta
- [x] `ANA-006` KPI: Engagement Rate + industri avg (2.3%)
- [x] `ANA-007` KPI: Follower Growth net new
- [x] `ANA-008` Reach per Platform bar chart (SVG hatch bars)
- [x] `ANA-009` Pertumbuhan Followers line chart (multi-line SVG)
- [x] `ANA-010` Top 5 Postingan table
- [x] `ANA-011` Demografi Audiens IG (progress bars usia, segment gender, top 3 kota)
- [x] Page: `Analytics/Overview.vue`

### 3.3 Analytics Per Konten (Screen 4b)
- [x] `PER-001` Sortable table (Reach ↓, Eng. Rate ↓, Impresi ↓, Terbaru)
- [x] `PER-002` Filter by content type (Carousel, Reels, Video, Thread, Foto)
- [x] `PER-003` Post count label update saat filter
- [x] `PER-004` Post thumbnail placeholder (36×36px)
- [x] `PER-005` Post title + tanggal + "Lihat Detail →" link
- [x] `PER-006` Platform badge column
- [x] `PER-007` Content type column
- [x] `PER-008` Numeric columns (reach, impresi, like, komentar, share, save)
- [x] `PER-009` Engagement Rate color coding (hijau ≥7%, biru ≥4%, abu <4%)
- [x] `PER-010` Expand [+] button per row (rotate → ×)
- [x] `PER-011` Expand panel: 6 metric cards
- [x] `PER-012` Expand panel: distribusi chart (4 metrics bars)
- [x] `PER-013` Expand panel: proporsi bars horizontal
- [x] `PER-014` Expand panel: metadata row (dipublikasi, tipe, platform, ER)
- [x] `PER-015` "Lihat Detail →" link → Content Detail
- [x] Page: `Analytics/PerKonten.vue`

### 3.4 Content Detail (Screen 7 — `s-content-detail`)
- [x] `DET-001` Back navigation → kembali ke Per Konten tab
- [x] `DET-002` Platform badge di topbar
- [x] `DET-003` Header card: thumb + title (serif italic)
- [x] `DET-004` Header tags: platform, tipe, tanggal, status badges
- [x] `DET-005` KPI Row 7 kolom (Reach, Impresi, Like, Komentar, Share, Simpan, Eng. Rate)
- [x] `DET-006` KPI delta per metrik (↑/↓ vs avg)
- [x] `DET-007` Chart: Distribusi Engagement (4 bars besar)
- [x] `DET-008` Proporsi Aksi horizontal bars (4 warna berbeda)
- [x] `DET-009` vs Rata-rata Akun comparison
- [x] `DET-010` Reach Harian 7 Hari Pertama bar timeline
- [x] `DET-011` Peak H + total 7 hari label
- [x] `DET-012` AI Insights auto-generated (★ Performa Tinggi, ◈ Evergreen, ↗ Viral, ▶ Format Video, ◎ Diskusi Aktif)
- [x] `DET-013` Insight kondisional (tampil hanya jika relevan berdasarkan data)
- [x] Page: `Analytics/ContentDetail.vue`

### 3.5 AI Features
- [x] `AI-001` Caption generator Bahasa Indonesia — Claude API
- [x] `AI-002` Hashtag suggestions — Claude API → `.hchip` chips di Compose
- [x] `AI-003` Best Time to Post per platform (widget Dashboard)
- [x] `AI-005` Content insights auto-generated — logic kondisional untuk DET-012/013
- [x] `AIService.php` — generateCaption(), suggestHashtags(), getBestTime()
- [x] `POST /api/v1/ai/caption` endpoint
- [x] `POST /api/v1/ai/hashtags` endpoint
- [x] `GET /api/v1/ai/best-time/{platform}` endpoint
- [x] Rate limiting AI: 5 req/menit, 50 req/hari (Starter)
- [x] `COM-004` AI Caption Generate button di Compose ("✦ AI Generate")
- [x] `COM-010` Hashtag chips AI saran (✦ Saran AI button)

### 3.6 Dashboard (Screen 1 — `s-dashboard`)
- [x] `DASH-001` KPI card: Total Followers (+ delta)
- [x] `DASH-002` KPI card: Post Terjadwal minggu ini
- [x] `DASH-003` KPI card: Avg Engagement Rate (+ delta)
- [x] `DASH-004` KPI card: Reach 30 hari (+ delta)
- [x] `DASH-005` Engagement Trend line chart SVG (30 hari)
- [x] `DASH-006` Post per Platform bar chart SVG (hatch per platform)
- [x] `DASH-007` Recent posts table (5 rows)
- [x] `DASH-008` Tombol "Lihat Semua →" → Analytics
- [x] `DASH-009` Notifikasi panel
- [x] `DASH-011` Best Time to Post widget (IG/TT/YT dengan waktu)
- [x] `DashboardController.php` (real data via AnalyticsService)
- [x] Page: `Dashboard/Index.vue`

---

## Phase 4 — Reports, Settings & Subscriptions
**Target:** 2–3 minggu  
**Goal:** Laporan PDF, settings lengkap, billing, notifikasi

### 4.1 Laporan (Screen 5 — `s-reports`)
- [x] `REP-001` Config: periode dropdown (bulan / Q / Custom)
- [x] `REP-002` Config: platform checkboxes multi-select
- [x] `REP-003` Config: sertakan checkboxes (KPI, Chart, Top 10, Demografi, White-label)
- [x] `REP-005` Preview laporan mini inline
- [x] `REP-006` Export PDF ("↓ Export PDF") — generate + download
- [x] `REP-007` Riwayat laporan table dengan [↓ Download] per row
- [x] `ReportService.php` — getHistory(), queue(), getPreviewData()
- [x] `GenerateClientReport.php` Job
- [x] `POST /api/v1/reports` + `POST /api/v1/reports/preview` + `GET /api/v1/reports/{id}/download`
- [x] `ReportController.php`
- [x] Page: `Reports/Index.vue`

### 4.2 Pengaturan (Screen 6 — `s-settings`)
- [x] `SET-001` Profil: avatar inisial circle
- [x] `SET-002` Profil: nama + email edit
- [x] `SET-003` Profil: zona waktu dropdown (WIB/WITA/WIT)
- [x] `SET-004` Platform terhubung list (status + tombol Hubungkan)
- [x] `SET-005` Platform follower count display
- [x] `SET-006` Notifikasi 5 toggle items
- [x] `SET-007` Plan & Billing card (info plan, harga, masa berlaku, fitur)
- [x] `SET-008` Tombol Upgrade ke Enterprise
- [x] `SET-010` Simpan Perubahan button + alert "✓"
- [x] `SettingsController.php`
- [x] Page: `Settings/Index.vue` (2-kolom: profil+platform kiri, notif+billing kanan)

### 4.3 Subscriptions & Billing
- [ ] `BILL-001` Plan Starter (Rp 99.000/bln) — 3 akun, 30 post
- [ ] `BILL-002` Plan Pro (Rp 149.000/bln) — 10 akun, unlimited scheduling, AI, white-label
- [ ] `BILL-003` Plan Agency/Enterprise — unlimited + team + API
- [ ] `BILL-004` Free trial
- [ ] `BILL-005` Upgrade/downgrade flow
- [ ] `BILL-006` Invoice download
- [ ] Payment gateway integration (Midtrans atau Xendit — TBD OQ-2)
- [ ] `SubscriptionController.php`
- [ ] Middleware: `CheckSubscriptionLimit` enforcement di semua feature gates

### 4.4 Notification System
- [x] `DASH-009/010` Notifikasi panel + badge di topbar (live unread count via shared Inertia data)
- [x] `SET-006` Email: ringkasan mingguan (toggle stored in notification_settings)
- [x] `SET-006` Push: konten berhasil publish
- [x] `SET-006` Push: mention & komentar baru
- [x] `SET-006` Email: laporan bulanan otomatis
- [x] `SET-006` Push: pengingat jadwal posting
- [ ] `PLAT-008` Banner token expiring warning
- [ ] Error recovery flow: post gagal publish → notif dot merah
- [x] `NotificationService.php`
- [x] `NotificationController.php` — index, markAllRead, markRead
- [ ] Laravel Reverb WebSocket event: `PostPublished`, `PostFailed`
- [ ] `PUB-006` Real-time status via WebSocket

### 4.5 Additional Platform Connections
- [ ] `PLAT-005` Connect YouTube — YouTube Data API
- [ ] `PLAT-003` Connect TikTok *(P1 — perlu app review 2–4 minggu, mulai submit lebih awal)*
- [ ] `YouTubeService.php`, `TikTokService.php` via Saloon

---

## Phase 5 — Polish, Testing & Deployment
**Target:** 2–3 minggu  
**Goal:** Test coverage, bug fix, performance, deploy ke home server

### 5.1 Backend Testing (Pest PHP)
- [ ] Unit tests: `PostServiceTest` — schedule, validate, plan limits
- [ ] Unit tests: `AnalyticsServiceTest` — aggregate metrics, date range filter
- [ ] Unit tests: `AIServiceTest` — caption generate, quota enforcement
- [ ] Unit tests: `PublishScheduledPostTest` — publish, retry, partial fail
- [ ] Unit tests: `PostPolicyTest` — owner, published lock, other user
- [ ] Feature tests: `AuthTest` — login, register, rate limit
- [ ] Feature tests: `PostControllerTest` — CRUD, auth, plan enforcement
- [ ] Feature tests: `AnalyticsControllerTest` — overview, per konten, content detail
- [ ] Feature tests: `SocialAccountControllerTest` — connect, disconnect, token refresh
- [ ] Feature tests: `AIControllerTest` — caption, hashtag, quota
- [ ] Coverage target: Services ≥85%, Models ≥80%, overall ≥70%

### 5.2 Frontend Testing (Vitest)
- [ ] Component tests: `AppButton`, `PlatformBadge`, `KpiCard`, `StatusBadge`
- [ ] Component tests: `CalendarGrid`, `CalendarCell`
- [ ] Component tests: `HashtagChip`, `AppToggle`
- [ ] Composable tests: `useAnalytics`, `useCalendar`, `useStickyNote`

### 5.3 E2E Testing (Playwright)
- [ ] Auth flow: register → verify → login
- [ ] Compose flow: pilih platform → caption → media → jadwalkan → lihat di calendar
- [ ] AI caption generate flow
- [ ] Analytics flow: overview → per konten → expand row → content detail
- [ ] Content detail: drill-down, 7 KPI verify, back navigation
- [ ] Settings flow: update profil, toggle notifikasi, simpan
- [ ] Reports flow: config → preview → export PDF

### 5.4 Performance & Security
- [ ] Analytics dashboard load < 3 detik untuk 90 hari data
- [ ] Page LCP < 2 detik
- [ ] API response P95 < 500ms
- [ ] OAuth token enkripsi AES-256 verify (unit test)
- [ ] Rate limiting semua endpoint (feature test)
- [ ] CSRF protection aktif
- [ ] File upload validation (type, size, malicious file)
- [ ] SQL injection check (OWASP ZAP scan)
- [ ] XSS pada caption/rich text fields
- [ ] HTTPS enforced di Nginx config

### 5.5 Deployment (Home Server)
- [ ] Setup Docker Compose production di `/opt/publishin/`
- [ ] MySQL: create DB `publishin_production` + user
- [ ] Host Nginx config + SSL via Certbot (publishin.id)
- [ ] WebSocket proxy config untuk Reverb (port 8080)
- [ ] `.env` production sesuai template DEPLOYMENT_GUIDE.md
- [ ] `php artisan migrate --force` + seed subscription plans
- [ ] Setup `backup.sh` — daily backup MySQL + storage ke HDD lokal
- [ ] Setup GitHub Actions CI/CD pipeline → auto deploy on push ke `main`
- [ ] Setup crontab: certbot renew, backup, laravel scheduler
- [ ] Horizon dashboard akses via proxy (basic auth protected)
- [ ] Smoke test semua screens di production
- [ ] Load test: k6 100 concurrent users, P95 < 800ms

---

## Backlog (Post-Launch)

| ID | Feature | Priority |
|---|---|---|
| CAL-012 | Drag & drop reschedule kalender | P1 |
| COM-013 | Custom caption per platform | P1 |
| AI-004 | Caption multi-tone (friendly/professional/humorous) | P1 |
| REP-004 | White-label report (Agency plan) | P1 |
| REP-008 | Auto-send laporan ke klien bulanan | P2 |
| PLAT-004 | Connect Twitter/X (API v2 $100+/bln) | P2 |
| COM-014 | First comment IG hashtag | P2 |
| AUTH-008 | Two-factor auth (TOTP) | P2 |
| AI-006 | Trend alert hashtag trending Indonesia | P2 |
| AI-007 | Sentiment monitoring komentar | P2 |
| SET-009 | Batalkan langganan dengan konfirmasi | P1 |

---

## Progress Summary

| Phase | Tasks | Done | % |
|---|---|---|---|
| Phase 1 — Foundation & Auth | 47 | 46 | 98% |
| Phase 2 — Content Management | 40 | 37 | 93% |
| Phase 3 — Analytics & AI | 57 | 0 | 0% |
| Phase 4 — Reports & Settings | 38 | 0 | 0% |
| Phase 5 — Polish & Deploy | 35 | 0 | 0% |
| **Total** | **217** | **83** | **38%** |

---

*Update status task: ganti `[ ]` → `[→]` saat mulai, `[x]` saat selesai. Update tabel Progress Summary setiap akhir sprint.*
