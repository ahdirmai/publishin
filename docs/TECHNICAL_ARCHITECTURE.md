# Technical Architecture
## Publishin — System Design & ERD
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  
**Architecture:** Laravel 12 Monolith + Vue.js + Inertia.js  
**Database:** MySQL 8.0  

---

## 1. Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                            │
│  Browser (Vue.js 3 + Inertia.js + Tailwind CSS)                │
│  Mobile PWA                                                     │
└────────────────────────┬────────────────────────────────────────┘
                         │ HTTPS
┌────────────────────────▼────────────────────────────────────────┐
│                         NGINX (Reverse Proxy)                   │
│  SSL Termination · Static Assets · Rate Limiting               │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                    LARAVEL 12 APPLICATION                        │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌──────────────────────┐   │
│  │   Web       │  │   API       │  │   Console/Queue      │   │
│  │  Routes     │  │  Routes     │  │   (Jobs & Commands)  │   │
│  │ (Inertia)   │  │ (Sanctum)   │  │                      │   │
│  └──────┬──────┘  └──────┬──────┘  └──────────┬───────────┘   │
│         │                │                     │               │
│  ┌──────▼────────────────▼─────────────────────▼───────────┐  │
│  │                  Service Layer                           │  │
│  │  PostService · AnalyticsService · PlatformService       │  │
│  │  AIService · ReportService · NotificationService        │  │
│  └───────────────────────┬──────────────────────────────────┘  │
│                          │                                      │
│  ┌───────────────────────▼──────────────────────────────────┐  │
│  │                  Data Layer                              │  │
│  │  Eloquent ORM · Spatie Media Library                     │  │
│  │  Spatie Permission · Repository Pattern                  │  │
│  └───────────────────────┬──────────────────────────────────┘  │
└──────────────────────────┼──────────────────────────────────────┘
                           │
         ┌─────────────────┼────────────────────┐
         │                 │                    │
┌────────▼─────┐  ┌────────▼──────┐  ┌─────────▼──────┐
│   MySQL 8.0  │  │  Redis 7.x    │  │  File Storage  │
│  (Primary DB)│  │  Cache+Queue  │  │  (Local/Spatie)│
└──────────────┘  └───────────────┘  └────────────────┘
         │
         │  External APIs
┌────────▼──────────────────────────────────────┐
│  Instagram Graph API  ·  Facebook Pages API   │
│  TikTok for Developers  ·  Twitter/X API v2   │
│  YouTube Data API  ·  Claude AI API           │
└───────────────────────────────────────────────┘
```

---

## 2. Technology Stack

| Layer | Teknologi | Versi | Keterangan |
|---|---|---|---|
| Framework | Laravel | 12.x | PHP 8.3+ |
| Frontend | Vue.js | 3.x | Composition API |
| Bridge | Inertia.js | 2.x | SPA tanpa REST overhead |
| Styling | Tailwind CSS | 3.x | + HeadlessUI |
| Database | MySQL | 8.0 | InnoDB engine |
| Cache/Queue | Redis | 7.x | Cache + BullMQ equivalent (Laravel Queue) |
| File Storage | Spatie Media Library | 11.x | Local disk + CDN ready |
| Auth/Permission | Spatie Permission | 6.x | RBAC dengan roles & permissions |
| Auth Token | Laravel Sanctum | 4.x | SPA + API token auth |
| Build Tool | Vite | 5.x | Asset bundling |
| Queue Worker | Laravel Horizon | 5.x | Queue monitoring + Redis |
| Full-text Search | Laravel Scout + MySQL | - | Search posts & analytics |
| API Client | Saloon PHP | 3.x | Typed HTTP client untuk platform APIs |
| AI Integration | Claude API (Anthropic) | - | Caption, hashtag, insights |
| WebSocket | Laravel Reverb | 1.x | Real-time notifications |
| Task Scheduling | Laravel Scheduler | built-in | Cron-based jobs |

---

## 3. Screen Architecture (dari Wireframe)

7 screens dari `kontentku-wireframe.html` mapping ke Inertia pages + Laravel routes:

| Screen ID | Wireframe | Inertia Page | Route | Controller |
|---|---|---|---|---|
| `s-dashboard` | Dashboard | `Dashboard/Index.vue` | `GET /dashboard` | `DashboardController` |
| `s-calendar` | Kalender | `Calendar/Index.vue` | `GET /calendar` | `CalendarController` |
| `s-compose` | Buat Konten | `Compose/Index.vue` | `GET /compose` | `PostController@create` |
| `s-analytics` | Analytics | `Analytics/Overview.vue` | `GET /analytics` | `AnalyticsController` |
| `s-analytics` (tab) | Per Konten | `Analytics/PerKonten.vue` | `GET /analytics/posts` | `AnalyticsController` |
| `s-content-detail` | Content Detail | `Analytics/ContentDetail.vue` | `GET /analytics/posts/{id}` | `AnalyticsController@show` |
| `s-reports` | Laporan | `Reports/Index.vue` | `GET /reports` | `ReportController` |
| `s-settings` | Pengaturan | `Settings/Index.vue` | `GET /settings` | `SettingsController` |

### Design Tokens (dari wireframe CSS)

```css
/* Diimplementasi via Tailwind config custom theme */
:root {
  --paper:    #FAFAF8;   /* bg-paper — app shell background */
  --ink:      #1C1B1A;   /* text-ink — primary text + borders */
  --ink-2:    #4A4944;   /* text-ink-2 — secondary text */
  --ink-3:    #928E89;   /* text-ink-3 — muted text, placeholders */
  --accent-r: #C96442;   /* text-accent-r / bg-accent-r — primary CTA (terracotta) */
  --accent-b: #3B6DB5;   /* text-accent-b — secondary, links */
  --green:    #2A7A4B;   /* status Published */
  --sticky:   #FFF8C5;   /* sticky note sidebar annotation */
  --f-sans:   ui-monospace,'JetBrains Mono','IBM Plex Mono',Menlo,monospace;
  --f-disp:   'Georgia','Iowan Old Style',serif;
  --bdr:      1.5px solid #1C1B1A;   /* card & button border */
  --bdr-m:    1px solid rgba(28,27,26,0.22);  /* subtle inner borders */
  --r:        2px;       /* border-radius — NO rounded corners */
}
```

### SVG Chart Pattern (hatch fill, TIDAK pakai chart library)

```js
// resources/js/utils/svgCharts.ts
// Matches wireframe buildSparkSVG() and buildBigBarSVG()
const HATCH_PATTERNS = {
  'h-r': 'url(#h-r)',  // terracotta — IG / primary bars
  'h-b': 'url(#h-b)',  // blue — TT / secondary
  'h-g': 'url(#h-g)',  // green — published
  'h-k': 'url(#h-k)',  // ink — generic
}
// Pattern defs disertakan di AppLayout.vue sebagai SVG defs global
```

### Sidebar Sticky Notes per Screen

```ts
// resources/js/composables/useStickyNote.ts
const STICKY_NOTES: Record<string, string> = {
  dashboard:      'halaman overview, mulai dari sini ~',
  calendar:       'atur jadwal & lihat semua post',
  compose:        'tulis & jadwalkan konten baru',
  analytics:      'pantau performa semua platform',
  'analytics/posts': 'pantau performa semua platform',
  reports:        'buat laporan untuk klien',
  settings:       'atur akun & koneksi platform',
}
```

---

## 4. Directory Structure

```
app/
├── Console/
│   └── Commands/
│       ├── FetchPlatformAnalytics.php
│       └── ProcessScheduledPosts.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── PostController.php
│   │   ├── AnalyticsController.php
│   │   ├── SocialAccountController.php
│   │   ├── CalendarController.php
│   │   ├── ReportController.php
│   │   └── Api/
│   │       └── V1/
│   │           ├── PostController.php
│   │           └── AnalyticsController.php
│   ├── Middleware/
│   │   ├── CheckSubscriptionLimit.php
│   │   └── VerifyPlatformWebhook.php
│   └── Requests/
│       ├── StorePostRequest.php
│       └── UpdatePostRequest.php
├── Jobs/
│   ├── PublishScheduledPost.php
│   ├── FetchAccountAnalytics.php
│   ├── ProcessWebhookPayload.php
│   └── GenerateClientReport.php
├── Models/
│   ├── User.php
│   ├── Organization.php
│   ├── SocialAccount.php
│   ├── Post.php
│   ├── PostPlatform.php
│   ├── PostAnalytic.php
│   ├── AccountAnalytic.php
│   ├── SubscriptionPlan.php
│   ├── Subscription.php
│   └── Webhook.php
├── Services/
│   ├── PostService.php
│   ├── AnalyticsService.php
│   ├── PlatformService.php
│   ├── AIService.php
│   ├── ReportService.php
│   └── Platform/
│       ├── InstagramService.php
│       ├── FacebookService.php
│       ├── TikTokService.php
│       ├── TwitterService.php
│       └── YouTubeService.php
└── Policies/
    ├── PostPolicy.php
    └── SocialAccountPolicy.php

resources/
└── js/
    ├── Pages/                          # Inertia page components (1:1 dengan wireframe screens)
    │   ├── Auth/                       # Login, Register, ForgotPassword
    │   ├── Dashboard/                  # s-dashboard — KPI, charts, recent posts, notif
    │   ├── Calendar/                   # s-calendar — monthly grid, weekly table
    │   ├── Compose/                    # s-compose — composer, preview, hashtag chips
    │   ├── Analytics/
    │   │   ├── Overview.vue            # s-analytics tab Overview
    │   │   ├── PerKonten.vue           # s-analytics tab Per Konten
    │   │   └── ContentDetail.vue       # s-content-detail (drill-down)
    │   ├── Reports/                    # s-reports — config, preview, history
    │   └── Settings/                   # s-settings — profil, platform, notif, billing
    ├── Components/
    │   ├── ui/                         # AppButton, AppCard, AppToggle, AppProgress
    │   ├── layout/                     # AppSidebar, AppTopbar, StickyNote
    │   ├── platform/                   # PlatformBadge, StatusBadge, HashtagChip
    │   ├── analytics/                  # KpiCard, SparkChart, DistribusiChart, ProporsiBar
    │   └── calendar/                   # CalendarGrid, CalendarCell, WeeklyTable
    └── composables/
        ├── useToast.ts
        ├── usePlatform.ts
        ├── useAnalytics.ts
        └── useCalendar.ts
```

---

## 4. Database Design (ERD)

### 4.1 Core Schema

```sql
-- =============================================
-- USERS & ORGANIZATIONS
-- =============================================

CREATE TABLE users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    avatar          VARCHAR(500) NULL,
    timezone        VARCHAR(64) DEFAULT 'Asia/Jakarta',
    locale          VARCHAR(10) DEFAULT 'id',
    email_verified_at TIMESTAMP NULL,
    last_login_at   TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,
    INDEX idx_email (email)
);

CREATE TABLE organizations (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id        BIGINT UNSIGNED NOT NULL,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    logo            VARCHAR(500) NULL,
    brand_color     VARCHAR(7) DEFAULT '#378ADD',   -- Untuk white-label report
    website         VARCHAR(500) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_owner (owner_id)
);

CREATE TABLE organization_members (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    role            ENUM('owner','admin','editor','viewer') DEFAULT 'editor',
    invited_at      TIMESTAMP NULL,
    joined_at       TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_org_user (organization_id, user_id)
);

-- =============================================
-- SUBSCRIPTIONS
-- =============================================

CREATE TABLE subscription_plans (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,          -- 'Starter', 'Pro', 'Agency'
    slug            VARCHAR(100) NOT NULL UNIQUE,
    price_monthly   INT UNSIGNED NOT NULL,          -- IDR, dalam rupiah
    price_yearly    INT UNSIGNED NOT NULL,
    max_social_accounts INT UNSIGNED DEFAULT 3,
    max_scheduled_posts INT UNSIGNED DEFAULT 30,    -- 0 = unlimited
    max_team_members    INT UNSIGNED DEFAULT 1,
    max_clients         INT UNSIGNED DEFAULT 1,
    has_ai_features     TINYINT(1) DEFAULT 0,
    has_white_label     TINYINT(1) DEFAULT 0,
    has_api_access      TINYINT(1) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE subscriptions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    plan_id         BIGINT UNSIGNED NOT NULL,
    status          ENUM('active','cancelled','expired','past_due') DEFAULT 'active',
    billing_cycle   ENUM('monthly','yearly') DEFAULT 'monthly',
    current_period_start TIMESTAMP NOT NULL,
    current_period_end   TIMESTAMP NOT NULL,
    cancelled_at    TIMESTAMP NULL,
    trial_ends_at   TIMESTAMP NULL,
    payment_gateway VARCHAR(50) NULL,               -- 'midtrans', 'xendit'
    external_id     VARCHAR(255) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    INDEX idx_user_status (user_id, status)
);

-- =============================================
-- SOCIAL ACCOUNTS
-- =============================================

CREATE TABLE social_accounts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    organization_id BIGINT UNSIGNED NULL,
    platform        ENUM('instagram','facebook','tiktok','twitter','youtube') NOT NULL,
    platform_user_id VARCHAR(255) NOT NULL,
    username        VARCHAR(255) NOT NULL,
    display_name    VARCHAR(255) NULL,
    avatar_url      VARCHAR(500) NULL,
    access_token    TEXT NOT NULL,                  -- Encrypted (AES-256)
    refresh_token   TEXT NULL,                      -- Encrypted
    token_expires_at TIMESTAMP NULL,
    page_id         VARCHAR(255) NULL,              -- FB/IG business page ID
    scopes          JSON NULL,                      -- OAuth scopes granted
    is_active       TINYINT(1) DEFAULT 1,
    last_synced_at  TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL,
    UNIQUE KEY uq_platform_user (platform, platform_user_id, user_id),
    INDEX idx_user_platform (user_id, platform)
);

-- =============================================
-- POSTS & SCHEDULING
-- =============================================

CREATE TABLE posts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    organization_id BIGINT UNSIGNED NULL,
    title           VARCHAR(500) NULL,
    status          ENUM('draft','scheduled','published','failed','cancelled') DEFAULT 'draft',
    scheduled_at    TIMESTAMP NULL,
    published_at    TIMESTAMP NULL,
    is_recurring    TINYINT(1) DEFAULT 0,
    recur_pattern   VARCHAR(100) NULL,              -- e.g. 'weekly', 'monthly'
    recur_end_at    TIMESTAMP NULL,
    parent_post_id  BIGINT UNSIGNED NULL,           -- For recurring children
    notes           TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_post_id) REFERENCES posts(id) ON DELETE SET NULL,
    INDEX idx_user_status (user_id, status),
    INDEX idx_scheduled (scheduled_at, status)
);

CREATE TABLE post_versions (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id             BIGINT UNSIGNED NOT NULL,
    social_account_id   BIGINT UNSIGNED NOT NULL,
    caption             TEXT NULL,
    media_urls          JSON NULL,
    hashtags            JSON NULL,
    platform_options    JSON NULL,                  -- Platform-specific options
    status              ENUM('pending','publishing','published','failed') DEFAULT 'pending',
    platform_post_id    VARCHAR(255) NULL,          -- ID dari platform setelah publish
    error_message       TEXT NULL,
    published_at        TIMESTAMP NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (social_account_id) REFERENCES social_accounts(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_account_status (social_account_id, status)
);

CREATE TABLE tags (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    color       VARCHAR(7) DEFAULT '#378ADD',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_tag (user_id, name)
);

CREATE TABLE post_tag (
    post_id BIGINT UNSIGNED NOT NULL,
    tag_id  BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- =============================================
-- ANALYTICS
-- =============================================

CREATE TABLE account_analytics (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    social_account_id   BIGINT UNSIGNED NOT NULL,
    date                DATE NOT NULL,
    followers           INT UNSIGNED DEFAULT 0,
    following           INT UNSIGNED DEFAULT 0,
    total_posts         INT UNSIGNED DEFAULT 0,
    reach               INT UNSIGNED DEFAULT 0,
    impressions         INT UNSIGNED DEFAULT 0,
    profile_views       INT UNSIGNED DEFAULT 0,
    website_clicks      INT UNSIGNED DEFAULT 0,
    raw_data            JSON NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (social_account_id) REFERENCES social_accounts(id) ON DELETE CASCADE,
    UNIQUE KEY uq_account_date (social_account_id, date),
    INDEX idx_date (date)
);

CREATE TABLE post_analytics (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_version_id     BIGINT UNSIGNED NOT NULL,
    social_account_id   BIGINT UNSIGNED NOT NULL,
    fetched_at          TIMESTAMP NOT NULL,
    likes               INT UNSIGNED DEFAULT 0,
    comments            INT UNSIGNED DEFAULT 0,
    shares              INT UNSIGNED DEFAULT 0,
    saves               INT UNSIGNED DEFAULT 0,
    reach               INT UNSIGNED DEFAULT 0,
    impressions         INT UNSIGNED DEFAULT 0,
    clicks              INT UNSIGNED DEFAULT 0,
    engagement_rate     DECIMAL(5,2) DEFAULT 0.00,
    video_views         INT UNSIGNED DEFAULT 0,
    video_play_time     INT UNSIGNED DEFAULT 0,    -- Detik
    raw_data            JSON NULL,
    FOREIGN KEY (post_version_id) REFERENCES post_versions(id) ON DELETE CASCADE,
    FOREIGN KEY (social_account_id) REFERENCES social_accounts(id) ON DELETE CASCADE,
    INDEX idx_account_date (social_account_id, fetched_at),
    INDEX idx_post_version (post_version_id)
);

-- =============================================
-- WEBHOOKS
-- =============================================

CREATE TABLE webhooks (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    url             VARCHAR(500) NOT NULL,
    secret          VARCHAR(255) NOT NULL,
    events          JSON NOT NULL,                 -- Array of event types
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE webhook_deliveries (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    webhook_id      BIGINT UNSIGNED NOT NULL,
    event           VARCHAR(100) NOT NULL,
    payload         JSON NOT NULL,
    response_code   SMALLINT UNSIGNED NULL,
    response_body   TEXT NULL,
    status          ENUM('pending','success','failed') DEFAULT 'pending',
    attempts        TINYINT UNSIGNED DEFAULT 0,
    next_retry_at   TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (webhook_id) REFERENCES webhooks(id) ON DELETE CASCADE,
    INDEX idx_webhook_status (webhook_id, status)
);

-- =============================================
-- NOTIFICATIONS
-- =============================================

CREATE TABLE notification_settings (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL UNIQUE,
    post_published  TINYINT(1) DEFAULT 1,
    post_failed     TINYINT(1) DEFAULT 1,
    weekly_report   TINYINT(1) DEFAULT 1,
    token_expiring  TINYINT(1) DEFAULT 1,
    sentiment_alert TINYINT(1) DEFAULT 0,
    email_enabled   TINYINT(1) DEFAULT 1,
    push_enabled    TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- SPATIE TABLES (auto-generated via migration)
-- model_has_roles
-- model_has_permissions
-- roles
-- permissions
-- role_has_permissions
-- media (Spatie Media Library)
-- =============================================
```

---

## 5. Model Relationships

```php
// User.php
class User extends Model {
    use HasRoles, HasApiTokens;

    public function organizations(): BelongsToMany
    public function ownedOrganizations(): HasMany
    public function socialAccounts(): HasMany
    public function posts(): HasMany
    public function subscription(): HasOne
    public function notificationSettings(): HasOne
}

// Post.php
class Post extends Model {
    use SoftDeletes;

    public function user(): BelongsTo
    public function organization(): BelongsTo
    public function versions(): HasMany          // post_versions
    public function tags(): BelongsToMany
    public function parent(): BelongsTo          // recurring parent
    public function children(): HasMany          // recurring children
    
    // Scope
    public function scopeScheduled($query)
    public function scopeDraft($query)
    public function scopePublished($query)
}

// SocialAccount.php
class SocialAccount extends Model {
    use Encryptable;                             // Custom trait untuk encrypt tokens

    public function user(): BelongsTo
    public function organization(): BelongsTo
    public function postVersions(): HasMany
    public function accountAnalytics(): HasMany
    
    public function getDecryptedAccessToken(): string
    public function isTokenExpired(): bool
    public function needsRefresh(): bool
}

// PostVersion.php
class PostVersion extends Model {
    public function post(): BelongsTo
    public function socialAccount(): BelongsTo
    public function analytics(): HasMany
    public function getMedia(): MediaCollection    // Spatie Media Library
}
```

---

## 6. Service Layer Pattern

```php
// app/Services/PostService.php
class PostService {
    public function __construct(
        private readonly PlatformService $platformService,
        private readonly AIService $aiService,
    ) {}

    public function schedule(User $user, array $data): Post
    public function publish(Post $post): void
    public function publishToAccount(PostVersion $version): void
    public function reschedule(Post $post, Carbon $newTime): Post
    public function cancel(Post $post): void
    public function duplicate(Post $post): Post
}

// app/Services/Platform/InstagramService.php
class InstagramService implements PlatformServiceInterface {
    public function publishPost(PostVersion $version): string   // Returns platform_post_id
    public function fetchAccountAnalytics(SocialAccount $account, Carbon $date): array
    public function fetchPostAnalytics(PostVersion $version): array
    public function refreshToken(SocialAccount $account): void
    public function validateWebhook(Request $request): bool
}
```

---

## 7. Queue Architecture

```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => null,
    ],
]

// Queue yang digunakan:
// - 'publishing'    : PublishScheduledPost jobs (high priority)
// - 'analytics'    : FetchPlatformAnalytics jobs (normal priority)
// - 'webhooks'     : ProcessWebhookPayload jobs (normal priority)
// - 'reports'      : GenerateClientReport jobs (low priority)
// - 'notifications': SendNotification jobs (normal priority)
// - 'ai'           : AI/Claude API calls (normal priority)
```

**Laravel Horizon Config:**
```php
// config/horizon.php
'environments' => [
    'production' => [
        'supervisor-publishing' => [
            'queue' => ['publishing'],
            'processes' => 5,
            'tries' => 3,
            'timeout' => 60,
        ],
        'supervisor-analytics' => [
            'queue' => ['analytics'],
            'processes' => 3,
            'tries' => 2,
        ],
        'supervisor-default' => [
            'queue' => ['webhooks', 'notifications', 'reports', 'ai'],
            'processes' => 3,
            'balance' => 'auto',
        ],
    ],
]
```

---

## 8. Scheduled Commands

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void {
    // Publish scheduled posts — setiap menit
    $schedule->job(new ProcessScheduledPosts)->everyMinute()->withoutOverlapping();

    // Fetch analytics harian — jam 03:00 WIB
    $schedule->job(new FetchDailyAnalytics)->dailyAt('03:00')->timezone('Asia/Jakarta');

    // Refresh platform tokens yang akan expired
    $schedule->job(new RefreshExpiringTokens)->hourly();

    // Generate weekly report — Senin jam 08:00 WIB
    $schedule->job(new GenerateWeeklyReports)->weeklyOn(1, '08:00')->timezone('Asia/Jakarta');

    // Cleanup old webhook deliveries (> 30 hari)
    $schedule->command('webhooks:cleanup')->weekly();
}
```

---

## 9. Caching Strategy

```php
// Cache keys & TTL
Cache::remember("user.{$userId}.subscription", now()->addHour(), ...)
Cache::remember("account.{$accountId}.analytics.{$date}", now()->addHours(6), ...)
Cache::remember("post.{$postId}.analytics", now()->addMinutes(30), ...)
Cache::remember("ai.best_time.{$accountId}", now()->addDay(), ...)

// Cache invalidation
// Post published → invalidate post analytics cache
// Daily analytics fetched → invalidate account analytics cache
// Subscription changed → invalidate user subscription cache
```

---

## 10. File Storage (Spatie Media Library)

```php
// Model registrations
class PostVersion extends Model implements HasMedia {
    use InteractsWithMedia;

    public function registerMediaCollections(): void {
        $this->addMediaCollection('post_media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'video/mp4'])
            ->withResponsiveImages();
    }

    public function registerMediaConversions(?Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(400)->height(400)->fit('crop');
        $this->addMediaConversion('preview')
            ->width(800);
    }
}
```

**Storage Config:**
- Local disk: `/storage/app/public/media`
- Max file size: 50MB video, 10MB image
- Auto-convert video thumbnail
- Responsive images untuk gambar
- Symlink ke `public/storage`

---

## 11. Security Architecture

### 11.1 Token Encryption
```php
// app/Traits/Encryptable.php
trait Encryptable {
    public function setAccessTokenAttribute(string $value): void {
        $this->attributes['access_token'] = encrypt($value);
    }

    public function getAccessTokenAttribute(string $value): string {
        return decrypt($value);
    }
}
```

### 11.2 Rate Limiting
```php
// app/Http/Kernel.php — throttle API
Route::middleware(['throttle:api'])->group(function() { ... });

// config/rate_limits
'api' => [
    'per_minute' => 60,
    'per_hour' => 1000,
],
'ai' => [
    'per_minute' => 5,
    'per_hour' => 50,
],
'publishing' => [
    'per_hour' => 100,
],
```

### 11.3 Authorization (Spatie)
```php
// Roles
// - super_admin: akses semua fitur platform
// - agency_owner: kelola org, semua klien
// - agency_admin: kelola member & klien
// - editor: buat & jadwalkan post
// - viewer: hanya lihat analytics

// Permissions
// posts.create, posts.edit, posts.delete, posts.publish
// analytics.view, analytics.export
// accounts.connect, accounts.delete
// members.invite, members.remove
// billing.manage
// reports.generate, reports.white_label
```

---

## 12. WebSocket (Laravel Reverb)

```php
// Real-time events via Reverb
class PostPublishedEvent implements ShouldBroadcast {
    public function broadcastOn(): array {
        return [new PrivateChannel("user.{$this->userId}")];
    }
}

class PostFailedEvent implements ShouldBroadcast { ... }
class AnalyticsUpdatedEvent implements ShouldBroadcast { ... }
```

```js
// Vue.js - resources/js/composables/useRealtime.ts
import Echo from 'laravel-echo'
import Pusher from 'pusher-js' // Reverb uses Pusher protocol

const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws'],
})

echo.private(`user.${userId}`)
    .listen('PostPublishedEvent', (e) => {
        toast.success(`Post berhasil dipublish ke ${e.platform}!`)
    })
    .listen('PostFailedEvent', (e) => {
        toast.error(`Gagal publish: ${e.error}`)
    })
```

---

*Arsitektur ini didesain untuk monolith yang dapat di-extract menjadi microservices di masa depan jika skala membutuhkan.*
