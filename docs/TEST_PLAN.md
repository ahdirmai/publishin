# Testing Strategy
## Publishin — Test Plan
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  
**Stack:** Laravel 12 + Pest PHP + Vitest + Playwright  

---

## 1. Testing Philosophy

- **Test behavior, not implementation** — test apa yang dilakukan, bukan bagaimana caranya
- **Unit tests untuk logic bisnis** — Services, Jobs, Validators
- **Feature tests untuk HTTP layer** — Controller responses, middleware, authorization
- **E2E tests untuk critical paths** — Onboarding, scheduling, analytics view
- **No mocking database** — gunakan SQLite in-memory atau MySQL test DB untuk isolation
- **Target coverage:** ≥ 80% untuk Services & Models, ≥ 70% overall

---

## 2. Test Layers

```
┌──────────────────────────────────────────┐
│          E2E Tests (Playwright)          │  ← ~20 critical user journeys
├──────────────────────────────────────────┤
│       Browser/Component Tests (Vitest)   │  ← Vue components
├──────────────────────────────────────────┤
│    Feature Tests (Pest - HTTP layer)     │  ← API endpoints, controllers
├──────────────────────────────────────────┤
│      Unit Tests (Pest - pure logic)      │  ← Services, Jobs, Helpers
└──────────────────────────────────────────┘
```

---

## 3. Backend Testing (Pest PHP)

### 3.1 Setup

```php
// tests/Pest.php
uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

// Custom helpers
function actingAsUser(string $plan = 'pro'): User {
    $user = User::factory()->withPlan($plan)->create();
    return login($user);
}

function createConnectedAccount(User $user, string $platform = 'instagram'): SocialAccount {
    return SocialAccount::factory()
        ->for($user)
        ->platform($platform)
        ->create();
}
```

### 3.2 Unit Tests — Services

**PostService Tests:**
```php
// tests/Unit/Services/PostServiceTest.php
describe('PostService', function () {

    it('creates a scheduled post with versions for each platform', function () {
        $user = User::factory()->withPlan('pro')->create();
        $accounts = SocialAccount::factory()->count(2)->for($user)->create();

        $post = app(PostService::class)->schedule($user, [
            'scheduled_at' => now()->addDay(),
            'versions' => $accounts->map(fn($a) => [
                'social_account_id' => $a->id,
                'caption' => 'Test caption',
            ])->toArray(),
        ]);

        expect($post->status)->toBe('scheduled')
            ->and($post->versions)->toHaveCount(2);
    });

    it('respects plan limits when creating posts', function () {
        $user = User::factory()->withPlan('starter')->create(); // limit 30 posts

        SocialAccount::factory()->for($user)->create();
        Post::factory()->scheduled()->count(30)->for($user)->create();

        expect(fn() => app(PostService::class)->schedule($user, [...]))
            ->toThrow(SubscriptionLimitException::class);
    });

    it('dispatches PublishScheduledPost job when publishing immediately', function () {
        Queue::fake();

        $post = Post::factory()->scheduled()->create();

        app(PostService::class)->publish($post);

        Queue::assertPushed(PublishScheduledPost::class);
    });

});
```

**AnalyticsService Tests:**
```php
describe('AnalyticsService', function () {

    it('aggregates metrics correctly across multiple accounts', function () {
        $user = User::factory()->create();
        $accounts = SocialAccount::factory()->count(3)->for($user)->create();

        // Seed analytics data
        foreach ($accounts as $account) {
            AccountAnalytic::factory()->for($account)->create([
                'date' => today(),
                'reach' => 1000,
                'engagement' => 50,
            ]);
        }

        $overview = app(AnalyticsService::class)->getOverview($user, [
            'from' => today()->toDateString(),
            'to' => today()->toDateString(),
        ]);

        expect($overview['totals']['reach'])->toBe(3000)
            ->and($overview['totals']['engagement'])->toBe(150);
    });

});
```

**AIService Tests:**
```php
describe('AIService', function () {

    it('generates caption with correct structure', function () {
        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [['text' => 'Generated caption with #hashtag']]
            ])
        ]);

        $result = app(AIService::class)->generateCaption([
            'topic' => 'promo ramadan',
            'platform' => 'instagram',
            'tone' => 'friendly',
        ]);

        expect($result)
            ->toHaveKey('caption')
            ->toHaveKey('hashtags')
            ->and($result['caption'])->not->toBeEmpty();
    });

    it('enforces daily AI quota per plan', function () {
        $user = User::factory()->withPlan('starter')->create();
        Cache::put("ai.quota.{$user->id}", 50, now()->addDay()); // Starter limit = 50

        expect(fn() => app(AIService::class)->generateCaption([...], $user))
            ->toThrow(AIQuotaExceededException::class);
    });

});
```

### 3.3 Feature Tests — HTTP Layer

**Auth Tests:**
```php
// tests/Feature/Auth/AuthTest.php
describe('Authentication', function () {

    it('allows user to login with valid credentials', function () {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure([
            'data' => ['user' => ['id', 'name', 'email'], 'token']
        ]);
    });

    it('returns 422 with invalid credentials', function () {
        postJson('/api/v1/auth/login', [
            'email' => 'wrong@email.com',
            'password' => 'wrongpass',
        ])->assertUnprocessable()->assertJson([
            'error_code' => 'INVALID_CREDENTIALS'
        ]);
    });

    it('rate limits login attempts', function () {
        $user = User::factory()->create();

        foreach (range(1, 10) as $i) {
            postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'wrong',
            ]);
        }

        postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertTooManyRequests();
    });

});
```

**Post API Tests:**
```php
// tests/Feature/Api/PostControllerTest.php
describe('POST /api/v1/posts', function () {

    it('creates scheduled post successfully', function () {
        $user = actingAsUser('pro');
        $account = createConnectedAccount($user);

        postJson('/api/v1/posts', [
            'scheduled_at' => now()->addDay()->toISOString(),
            'status' => 'scheduled',
            'versions' => [
                [
                    'social_account_id' => $account->id,
                    'caption' => 'Test caption',
                ]
            ]
        ])->assertCreated()->assertJsonStructure([
            'data' => ['id', 'status', 'scheduled_at', 'versions']
        ]);

        assertDatabaseHas('posts', ['user_id' => $user->id, 'status' => 'scheduled']);
    });

    it('rejects unauthenticated requests', function () {
        postJson('/api/v1/posts', [])->assertUnauthorized();
    });

    it('enforces social account ownership', function () {
        $user = actingAsUser();
        $otherUserAccount = SocialAccount::factory()->create(); // different user

        postJson('/api/v1/posts', [
            'versions' => [
                ['social_account_id' => $otherUserAccount->id, 'caption' => 'Test']
            ]
        ])->assertForbidden();
    });

    it('validates scheduled_at is in the future', function () {
        $user = actingAsUser();
        $account = createConnectedAccount($user);

        postJson('/api/v1/posts', [
            'scheduled_at' => now()->subHour()->toISOString(), // past
            'versions' => [['social_account_id' => $account->id, 'caption' => 'Test']]
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['scheduled_at']);
    });

});
```

**Analytics API Tests:**
```php
// tests/Feature/Api/AnalyticsControllerTest.php
describe('GET /api/v1/analytics/overview', function () {

    it('returns aggregated analytics for user accounts', function () {
        $user = actingAsUser();
        $account = createConnectedAccount($user);

        AccountAnalytic::factory()->for($account)->create([
            'date' => today(),
            'reach' => 5000,
        ]);

        getJson('/api/v1/analytics/overview?from=' . today()->toDateString())
            ->assertOk()
            ->assertJsonPath('data.totals.reach', 5000);
    });

    it('filters by date range', function () {
        $user = actingAsUser();
        $account = createConnectedAccount($user);

        AccountAnalytic::factory()->for($account)->create(['date' => '2026-05-01', 'reach' => 1000]);
        AccountAnalytic::factory()->for($account)->create(['date' => '2026-04-01', 'reach' => 2000]);

        getJson('/api/v1/analytics/overview?from=2026-05-01&to=2026-05-31')
            ->assertOk()
            ->assertJsonPath('data.totals.reach', 1000);
    });

});
```

### 3.4 Job Tests

```php
// tests/Unit/Jobs/PublishScheduledPostTest.php
describe('PublishScheduledPost Job', function () {

    it('publishes post to each platform version', function () {
        $mockInstagram = mock(InstagramService::class);
        $mockInstagram->shouldReceive('publishPost')->once()->andReturn('ig_post_id_123');

        app()->instance(InstagramService::class, $mockInstagram);

        $post = Post::factory()->scheduled()->create();
        PostVersion::factory()->for($post)->instagram()->create();

        (new PublishScheduledPost($post))->handle();

        assertDatabaseHas('post_versions', ['platform_post_id' => 'ig_post_id_123']);
    });

    it('marks post version as failed on platform error', function () {
        $mockInstagram = mock(InstagramService::class);
        $mockInstagram->shouldReceive('publishPost')
            ->andThrow(new PlatformApiException('Media format not supported'));

        $version = PostVersion::factory()->instagram()->create();
        
        (new PublishScheduledPost($version->post))->handle();

        expect($version->fresh()->status)->toBe('failed')
            ->and($version->fresh()->error_message)->toContain('Media format');
    });

    it('retries on temporary platform errors', function () {
        Queue::fake();

        $mockInstagram = mock(InstagramService::class);
        $mockInstagram->shouldReceive('publishPost')
            ->andThrow(new PlatformRateLimitException());

        $post = Post::factory()->scheduled()->create();
        PostVersion::factory()->for($post)->instagram()->create();

        $job = new PublishScheduledPost($post);
        expect(fn() => $job->handle())
            ->toThrow(PlatformRateLimitException::class);
        // Laravel retries jobs that throw — check retry config
    });

});
```

### 3.5 Model Policy Tests

```php
// tests/Unit/Policies/PostPolicyTest.php
describe('PostPolicy', function () {

    it('allows owner to edit their post', function () {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->draft()->create();

        expect($user->can('update', $post))->toBeTrue();
    });

    it('prevents editing published posts', function () {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->published()->create();

        expect($user->can('update', $post))->toBeFalse();
    });

    it('prevents other users from editing', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->draft()->create();

        expect($other->can('update', $post))->toBeFalse();
    });

});
```

---

## 4. Frontend Testing (Vitest + Vue Test Utils)

### 4.1 Setup

```ts
// vitest.config.ts
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    globals: true,
    setupFiles: ['./resources/js/tests/setup.ts'],
  },
})
```

### 4.2 Component Tests

```ts
// resources/js/tests/components/AppButton.test.ts
import { mount } from '@vue/test-utils'
import AppButton from '@/components/ui/AppButton.vue'

describe('AppButton', () => {
  it('renders slot content', () => {
    const wrapper = mount(AppButton, {
      slots: { default: 'Jadwalkan' }
    })
    expect(wrapper.text()).toBe('Jadwalkan')
  })

  it('shows loading spinner when loading prop is true', () => {
    const wrapper = mount(AppButton, { props: { loading: true } })
    expect(wrapper.find('.animate-spin').exists()).toBe(true)
  })

  it('is disabled when loading', () => {
    const wrapper = mount(AppButton, { props: { loading: true } })
    expect(wrapper.attributes('disabled')).toBeDefined()
  })

  it('applies danger variant classes', () => {
    const wrapper = mount(AppButton, { props: { variant: 'danger' } })
    expect(wrapper.classes()).toContain('bg-red-600')
  })
})
```

```ts
// resources/js/tests/components/PlatformBadge.test.ts
import PlatformBadge from '@/components/platform/PlatformBadge.vue'

describe('PlatformBadge', () => {
  it.each([
    ['instagram', 'Instagram', 'bg-pink-100'],
    ['facebook', 'Facebook', 'bg-blue-100'],
    ['tiktok', 'TikTok', 'bg-gray-900'],
  ])('%s platform renders correctly', (platform, label, bgClass) => {
    const wrapper = mount(PlatformBadge, { props: { platform } })
    expect(wrapper.text()).toContain(label)
    expect(wrapper.classes()).toContain(bgClass)
  })
})
```

### 4.3 Composable Tests

```ts
// resources/js/tests/composables/useAnalytics.test.ts
import { useAnalytics } from '@/composables/useAnalytics'
import { vi } from 'vitest'

describe('useAnalytics', () => {
  it('fetches overview data on mount', async () => {
    const mockFetch = vi.fn().mockResolvedValue({
      data: { totals: { reach: 5000 } }
    })
    vi.mock('@/api/analytics', () => ({ getOverview: mockFetch }))

    const { overview, loading } = useAnalytics()
    
    expect(loading.value).toBe(true)
    await nextTick()
    expect(mockFetch).toHaveBeenCalled()
    expect(overview.value?.totals.reach).toBe(5000)
  })
})
```

---

## 5. Wireframe Element → `data-testid` Mapping

`data-testid` diambil langsung dari wireframe HTML element IDs dan class names:

| Wireframe Element | Screen | `data-testid` |
|---|---|---|
| `.kpi-val` (Followers) | Dashboard | `kpi-total-followers` |
| `.kpi-val` (Scheduled) | Dashboard | `kpi-post-terjadwal` |
| `.kpi-val` (Engagement) | Dashboard | `kpi-engagement-rate` |
| `.kpi-val` (Reach) | Dashboard | `kpi-reach-30d` |
| Engagement trend chart SVG | Dashboard | `chart-engagement-trend` |
| Post per platform chart | Dashboard | `chart-post-per-platform` |
| Recent posts table | Dashboard | `table-recent-posts` |
| Notifikasi panel | Dashboard | `panel-notifications` |
| Best time widget | Dashboard | `widget-best-time` |
| `calPrev()` / `calNext()` | Calendar | `cal-btn-prev` / `cal-btn-next` |
| `.cal-cell.today` | Calendar | `cal-cell-today` |
| `.cdot.pub` / `.cdot.sched` | Calendar | `cal-dot-published` / `cal-dot-scheduled` |
| `.ctag` | Calendar | `cal-post-tag` |
| Weekly table | Calendar | `table-weekly-posts` |
| Platform checkboxes (IG,TT...) | Compose | `platform-checkbox-{platform}` |
| `.ftextarea` | Compose | `caption-input` |
| Character counter | Compose | `caption-char-count` |
| "✦ AI Generate" button | Compose | `ai-caption-button` |
| Media dropzone | Compose | `media-dropzone` |
| Date input | Compose | `schedule-date` |
| Time input | Compose | `schedule-time` |
| Waktu terbaik hint | Compose | `best-time-hint` |
| IG preview mock | Compose | `preview-instagram` |
| `.hchip` | Compose | `hashtag-chip` |
| "Simpan Draft" button | Compose | `btn-save-draft` |
| "Jadwalkan" button | Compose | `btn-schedule` |
| Sub-tab Overview | Analytics | `tab-overview` |
| Sub-tab Per Konten | Analytics | `tab-per-konten` |
| Period filter dropdown | Analytics | `filter-period` |
| Platform filter dropdown | Analytics | `filter-platform` |
| Sort dropdown | Per Konten | `sort-posts` |
| Content type filter | Per Konten | `filter-content-type` |
| `.expand-btn` [+] | Per Konten | `row-expand-btn` |
| "Lihat Detail →" link | Per Konten | `link-content-detail` |
| "← Kembali ke Analitik" | Content Detail | `btn-back-analytics` |
| KPI Row 7 kolom | Content Detail | `kpi-col-{metric}` (reach, impressions, likes...) |
| `.cd-timeline-bars` | Content Detail | `chart-daily-reach` |
| AI Insights panel | Content Detail | `panel-insights` |
| Export PDF button | Reports | `btn-export-pdf` |
| Platform connection list | Settings | `platform-connections-list` |
| Notif toggles | Settings | `notif-toggle-{type}` |
| "Simpan Perubahan" | Settings | `btn-save-settings` |

---

## 6. E2E Testing (Playwright)

### 5.1 Setup

```ts
// playwright.config.ts
import { defineConfig } from '@playwright/test'

export default defineConfig({
  testDir: './tests/e2e',
  baseURL: 'http://localhost:8000',
  use: {
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'mobile', use: { ...devices['Pixel 5'] } },
  ],
})
```

### 5.2 Critical E2E Test Cases

**User Registration & Onboarding:**
```ts
// tests/e2e/auth/registration.spec.ts
test('user can register and complete onboarding', async ({ page }) => {
  await page.goto('/register')
  
  await page.fill('[name=name]', 'Rizky Test')
  await page.fill('[name=email]', 'rizky.test@example.com')
  await page.fill('[name=password]', 'password123')
  await page.fill('[name=password_confirmation]', 'password123')
  await page.click('[type=submit]')

  // Email verification page
  await expect(page).toHaveURL('/email/verify')
  await expect(page.locator('h1')).toContainText('Verifikasi Email')
})

test('user can login and reach dashboard', async ({ page }) => {
  await page.goto('/login')
  await page.fill('[name=email]', 'user@example.com')
  await page.fill('[name=password]', 'password')
  await page.click('[type=submit]')

  await expect(page).toHaveURL('/dashboard')
  await expect(page.locator('nav')).toContainText('Dashboard')
})
```

**Scheduling Flow:**
```ts
// tests/e2e/scheduling/create-post.spec.ts
test('user can create and schedule a post', async ({ page, context }) => {
  // Login as seeded user with connected Instagram account
  await loginAsTestUser(page)

  await page.goto('/calendar')
  await page.click('[data-testid=create-post-button]')

  // Select platform
  await page.check('[data-testid=platform-checkbox-instagram]')

  // Upload media
  const fileInput = page.locator('input[type=file]')
  await fileInput.setInputFiles('./tests/fixtures/test-image.jpg')
  await expect(page.locator('[data-testid=media-preview]')).toBeVisible()

  // Caption
  await page.fill('[data-testid=caption-input]', 'Test post dari Playwright!')

  // Schedule
  await page.fill('[data-testid=schedule-date]', '2026-12-01')
  await page.fill('[data-testid=schedule-time]', '09:00')

  await page.click('[data-testid=schedule-button]')
  
  await expect(page.locator('[data-testid=toast-success]'))
    .toContainText('Post berhasil dijadwalkan')

  // Verify appears in calendar
  await expect(page.locator('[data-testid=calendar-post-item]')).toBeVisible()
})

test('AI caption generator works', async ({ page }) => {
  await loginAsProUser(page)
  await page.goto('/posts/create')

  await page.click('[data-testid=ai-caption-button]')
  await page.fill('[data-testid=ai-topic-input]', 'Promo hari raya diskon 50%')
  await page.click('[data-testid=ai-generate-button]')

  await expect(page.locator('[data-testid=ai-result]')).toBeVisible({ timeout: 10000 })
  await page.click('[data-testid=ai-use-caption]')

  await expect(page.locator('[data-testid=caption-input]')).not.toBeEmpty()
})
```

**Analytics Flow:**
```ts
// tests/e2e/analytics/dashboard.spec.ts
test('analytics dashboard loads and displays metrics', async ({ page }) => {
  await loginAsTestUser(page)
  await page.goto('/analytics')

  await expect(page.locator('[data-testid=metric-reach]')).toBeVisible()
  await expect(page.locator('[data-testid=metric-engagement]')).toBeVisible()
  await expect(page.locator('[data-testid=chart-container]')).toBeVisible()
})

test('user can filter analytics by date range', async ({ page }) => {
  await loginAsTestUser(page)
  await page.goto('/analytics')

  await page.click('[data-testid=date-filter]')
  await page.click('[data-testid=filter-last-30-days]')

  await expect(page.locator('[data-testid=loading-skeleton]')).toBeVisible()
  await expect(page.locator('[data-testid=loading-skeleton]')).not.toBeVisible({ timeout: 5000 })
  await expect(page.locator('[data-testid=metric-reach]')).toBeVisible()
})

test('user can export analytics as PDF', async ({ page }) => {
  await loginAsProUser(page)
  await page.goto('/analytics')

  const downloadPromise = page.waitForEvent('download')
  await page.click('[data-testid=export-pdf-button]')
  const download = await downloadPromise
  
  expect(download.suggestedFilename()).toMatch(/laporan.*\.pdf/)
})
```

**Content Detail Flow (Screen 7 — `s-content-detail`):**
```ts
// tests/e2e/analytics/content-detail.spec.ts
test('user can drill down to content detail from per konten tab', async ({ page }) => {
  await loginAsTestUser(page)
  await page.goto('/analytics')

  // Switch to Per Konten tab
  await page.click('[data-testid=tab-per-konten]')
  await expect(page.locator('[data-testid=sort-posts]')).toBeVisible()

  // Click Lihat Detail on first row
  await page.click('[data-testid=link-content-detail]')

  // Should navigate to content detail screen
  await expect(page).toHaveURL(/\/analytics\/posts\/\d+/)
  await expect(page.locator('[data-testid=btn-back-analytics]')).toBeVisible()
  await expect(page.locator('[data-testid=kpi-col-reach]')).toBeVisible()
  await expect(page.locator('[data-testid=chart-daily-reach]')).toBeVisible()
  await expect(page.locator('[data-testid=panel-insights]')).toBeVisible()
})

test('content detail shows 7 KPI columns from wireframe', async ({ page }) => {
  await loginAsTestUser(page)
  const postId = await seedPublishedPost()   // test helper
  await page.goto(`/analytics/posts/${postId}`)

  // Verify 7 KPI columns: reach, impressions, likes, comments, shares, saves, engagement_rate
  for (const metric of ['reach', 'impressions', 'likes', 'comments', 'shares', 'saves', 'engagement-rate']) {
    await expect(page.locator(`[data-testid=kpi-col-${metric}]`)).toBeVisible()
  }
})

test('back navigation returns to per konten tab', async ({ page }) => {
  await loginAsTestUser(page)
  const postId = await seedPublishedPost()
  await page.goto(`/analytics/posts/${postId}`)

  await page.click('[data-testid=btn-back-analytics]')
  await expect(page).toHaveURL('/analytics/posts')
  await expect(page.locator('[data-testid=tab-per-konten]')).toHaveClass(/active/)
})
```

---

## 6. Performance Testing

### 6.1 Target Metrics
| Endpoint | P50 | P95 | P99 |
|---|---|---|---|
| GET /dashboard | 200ms | 500ms | 1000ms |
| GET /analytics/overview | 300ms | 800ms | 1500ms |
| POST /posts | 150ms | 400ms | 800ms |
| POST /ai/caption | 2000ms | 5000ms | 8000ms |
| GET /calendar | 200ms | 600ms | 1200ms |

### 6.2 Load Test Script (k6)

```js
// tests/load/api-load-test.js
import http from 'k6/http'
import { check, sleep } from 'k6'

export const options = {
  stages: [
    { duration: '2m', target: 50 },    // ramp up
    { duration: '5m', target: 100 },   // steady state
    { duration: '2m', target: 0 },     // ramp down
  ],
  thresholds: {
    http_req_duration: ['p(95)<800'],
    http_req_failed: ['rate<0.01'],     // < 1% error rate
  },
}

export default function () {
  const params = {
    headers: {
      'Authorization': `Bearer ${__ENV.TEST_TOKEN}`,
      'Accept': 'application/json',
    }
  }

  const r1 = http.get(`${__ENV.BASE_URL}/api/v1/posts`, params)
  check(r1, { 'posts status 200': (r) => r.status === 200 })

  const r2 = http.get(`${__ENV.BASE_URL}/api/v1/analytics/overview`, params)
  check(r2, { 'analytics status 200': (r) => r.status === 200 })

  sleep(1)
}
```

---

## 7. Security Testing Checklist

| Test | Method | Priority |
|---|---|---|
| SQL Injection pada semua input | Automated (OWASP ZAP) | High |
| XSS pada caption/rich text fields | Manual + automated | High |
| CSRF protection aktif | Feature test | High |
| Token tidak ter-expose di logs | Code review | High |
| OAuth state parameter validated | Feature test | High |
| Rate limiting berjalan | Feature test | Medium |
| Authorization semua endpoint | Policy tests | High |
| Encrypted tokens di DB | Unit test | High |
| HTTPS enforced | Deployment check | High |
| File upload validation | Feature test | High |

---

## 8. Test Data & Factories

```php
// database/factories/UserFactory.php
class UserFactory extends Factory {
    public function withPlan(string $plan): static {
        return $this->afterCreating(function (User $user) use ($plan) {
            $planModel = SubscriptionPlan::where('slug', $plan)->first();
            Subscription::factory()->for($user)->for($planModel)->create();
        });
    }
}

// database/factories/PostFactory.php
class PostFactory extends Factory {
    public function scheduled(): static {
        return $this->state(['status' => 'scheduled', 'scheduled_at' => now()->addHour()]);
    }

    public function published(): static {
        return $this->state(['status' => 'published', 'published_at' => now()->subHour()]);
    }
}

// database/factories/SocialAccountFactory.php
class SocialAccountFactory extends Factory {
    public function instagram(): static {
        return $this->state(['platform' => 'instagram']);
    }

    public function withExpiredToken(): static {
        return $this->state(['token_expires_at' => now()->subDay()]);
    }
}
```

---

## 9. CI Test Pipeline

```yaml
# .github/workflows/test.yml
name: Tests

on: [push, pull_request]

jobs:
  php-tests:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: publishin_test
          MYSQL_ROOT_PASSWORD: password
        ports: ['3306:3306']
      redis:
        image: redis:7
        ports: ['6379:6379']

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_mysql, redis

      - run: composer install --no-dev
      - run: cp .env.testing .env
      - run: php artisan key:generate
      - run: php artisan migrate --force
      - run: ./vendor/bin/pest --parallel --coverage --min=70

  js-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '20' }
      - run: npm ci
      - run: npm run test:unit -- --coverage

  e2e-tests:
    runs-on: ubuntu-latest
    needs: [php-tests, js-tests]
    steps:
      - uses: actions/checkout@v4
      - run: npm ci && npx playwright install --with-deps chromium
      - run: php artisan serve &
      - run: npx playwright test
      - uses: actions/upload-artifact@v4
        if: failure()
        with:
          name: playwright-report
          path: playwright-report/
```

---

## 10. Test Coverage Reports

```bash
# Generate coverage report
./vendor/bin/pest --coverage --coverage-html storage/reports/coverage

# Coverage targets
# - app/Services/: ≥ 85%
# - app/Models/: ≥ 80%
# - app/Jobs/: ≥ 80%
# - app/Http/Controllers/: ≥ 70%
# - Overall: ≥ 70%
```

---

*Update test plan setiap kali fitur baru ditambahkan atau behavior diubah.*
