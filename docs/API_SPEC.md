# API Specification
## Publishin REST API v1
### Wireframe Reference: `kontentku-wireframe.html`

**Base URL:** `https://publishin.id/api/v1` *(URL dari wireframe — sesuaikan dengan domain final)*  
**Inertia Routes:** `https://publishin.id/{dashboard|calendar|compose|analytics|reports|settings}`  
**Auth:** Laravel Sanctum (Bearer Token)  
**Format:** JSON  
**Last Updated:** 2026-06-03  

---

## 1. API Overview

### 1.1 Versioning Strategy
- URL-based versioning: `/api/v1/`, `/api/v2/`
- Version header: `X-API-Version: 1`
- Deprecated versions mendapat header `X-API-Deprecated: true` dan berlaku 6 bulan sebelum sunset
- Breaking changes → major version bump

### 1.2 Authentication

**SPA Authentication (Inertia.js):**
```
POST /sanctum/csrf-cookie          → Get CSRF cookie
POST /api/v1/auth/login            → Login, set httpOnly cookie session
```

**API Token Authentication:**
```
POST /api/v1/auth/tokens           → Create personal access token
Authorization: Bearer {token}      → Attach to all requests
```

### 1.3 Request/Response Format

**Request Headers:**
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
X-API-Version: 1
```

**Success Response:**
```json
{
  "data": { ... } | [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}
```

**Error Response:**
```json
{
  "message": "Pesan error yang readable",
  "errors": {
    "field_name": ["Validasi error 1", "Validasi error 2"]
  },
  "error_code": "VALIDATION_ERROR"
}
```

### 1.4 HTTP Status Codes
| Code | Makna |
|---|---|
| 200 | OK — Request berhasil |
| 201 | Created — Resource baru berhasil dibuat |
| 204 | No Content — Berhasil, tidak ada response body |
| 400 | Bad Request — Request tidak valid |
| 401 | Unauthorized — Token tidak ada/invalid |
| 403 | Forbidden — Tidak punya izin |
| 404 | Not Found — Resource tidak ditemukan |
| 409 | Conflict — Duplikasi atau state conflict |
| 422 | Unprocessable Entity — Validasi gagal |
| 429 | Too Many Requests — Rate limit exceeded |
| 500 | Internal Server Error |
| 503 | Service Unavailable — Maintenance mode |

---

## 1.5 Wireframe Screen → Endpoint Mapping

| Wireframe Screen | Screen ID | Key Endpoints |
|---|---|---|
| Dashboard | `s-dashboard` | `GET /analytics/overview`, `GET /posts?status=scheduled`, `GET /ai/best-time/{id}` |
| Kalender | `s-calendar` | `GET /calendar?year=&month=` |
| Buat Konten | `s-compose` | `POST /posts`, `POST /media/upload`, `POST /ai/caption`, `POST /ai/hashtags` |
| Analytics Overview | `s-analytics` | `GET /analytics/overview`, `GET /analytics/audience` |
| Analytics Per Konten | `s-analytics` (tab) | `GET /analytics/posts` |
| Content Detail | `s-content-detail` | `GET /analytics/posts/{id}` |
| Laporan | `s-reports` | `GET /reports/generate`, `GET /reports` |
| Pengaturan | `s-settings` | `GET /social-accounts`, `PUT /auth/me`, `GET /subscription` |

---

## 2. Rate Limiting

| Endpoint Group | Limit | Window |
|---|---|---|
| Auth endpoints | 10 req | Per menit |
| General API | 60 req | Per menit |
| Analytics fetch | 120 req | Per menit |
| AI endpoints | 5 req | Per menit |
| Publishing | 30 req | Per menit |
| Platform OAuth | 10 req | Per menit |

**Rate Limit Response Headers:**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1717420860
Retry-After: 60         (hanya saat 429)
```

---

## 3. Authentication Endpoints

### POST /auth/login
Login dengan email dan password.

**Request:**
```json
{
  "email": "rizky@umkm.id",
  "password": "secret123",
  "remember": true
}
```

**Response 200:**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Rizky Firmansyah",
      "email": "rizky@umkm.id",
      "avatar": "https://yourdomain.com/storage/avatars/1.jpg",
      "timezone": "Asia/Jakarta",
      "subscription": {
        "plan": "pro",
        "expires_at": "2026-07-03T00:00:00Z"
      }
    },
    "token": "1|abc123def456..."   // Hanya untuk API token auth
  }
}
```

**Response 422:**
```json
{
  "message": "Kredensial tidak valid.",
  "error_code": "INVALID_CREDENTIALS"
}
```

---

### POST /auth/register
Registrasi user baru.

**Request:**
```json
{
  "name": "Rizky Firmansyah",
  "email": "rizky@umkm.id",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Response 201:**
```json
{
  "data": {
    "user": { ... },
    "message": "Registrasi berhasil. Cek email untuk verifikasi."
  }
}
```

---

### POST /auth/logout
**Response 204:** No content.

---

### GET /auth/me
Ambil data user yang sedang login.

**Response 200:**
```json
{
  "data": {
    "id": 1,
    "name": "Rizky Firmansyah",
    "email": "rizky@umkm.id",
    "timezone": "Asia/Jakarta",
    "roles": ["agency_owner"],
    "permissions": ["posts.create", "posts.publish", "analytics.view"],
    "subscription": {
      "plan": "pro",
      "plan_name": "Pro",
      "limits": {
        "social_accounts": 10,
        "scheduled_posts": 0,
        "team_members": 3
      },
      "usage": {
        "social_accounts": 4,
        "scheduled_posts": 12,
        "team_members": 2
      },
      "expires_at": "2026-07-03T00:00:00Z"
    }
  }
}
```

---

### POST /auth/forgot-password
**Request:** `{ "email": "user@example.com" }`  
**Response 200:** `{ "message": "Link reset dikirim ke email." }`

---

### POST /auth/reset-password
**Request:**
```json
{
  "token": "reset_token_from_email",
  "email": "user@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

### POST /auth/tokens
Buat personal access token (untuk API access - plan Agency).

**Request:** `{ "name": "My Integration", "abilities": ["posts:read", "analytics:read"] }`  
**Response 201:** `{ "data": { "token": "2|xyz...", "expires_at": null } }`

---

### DELETE /auth/tokens/{id}
Revoke token.  
**Response 204**

---

## 4. Social Accounts

### GET /social-accounts
Ambil semua connected social accounts.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "platform": "instagram",
      "platform_user_id": "123456789",
      "username": "@rizky_umkm",
      "display_name": "Rizky UMKM Store",
      "avatar_url": "https://...",
      "is_active": true,
      "token_expires_at": "2026-09-01T00:00:00Z",
      "token_status": "valid",        // 'valid', 'expiring_soon', 'expired'
      "last_synced_at": "2026-06-03T02:00:00Z",
      "stats": {
        "followers": 12500,
        "total_posts": 342
      }
    }
  ]
}
```

---

### POST /social-accounts/connect/{platform}
Mulai OAuth flow untuk connect akun.

**Path params:** `platform` = instagram | facebook | tiktok | twitter | youtube

**Response 200:**
```json
{
  "data": {
    "redirect_url": "https://api.instagram.com/oauth/authorize?..."
  }
}
```

---

### GET /social-accounts/callback/{platform}
OAuth callback handler (dipanggil oleh platform, bukan user).

---

### DELETE /social-accounts/{id}
Disconnect social account.  
**Response 204**

---

### POST /social-accounts/{id}/reconnect
Refresh OAuth token yang expired.  
**Response 200:** `{ "data": { "redirect_url": "..." } }`

---

### POST /social-accounts/{id}/sync
Trigger manual sync analytics dari platform.  
**Response 202:** `{ "message": "Sync dijadwalkan." }`

---

## 5. Posts

### GET /posts
Ambil daftar posts.

**Query Params:**
| Param | Type | Default | Keterangan |
|---|---|---|---|
| `status` | string | all | draft\|scheduled\|published\|failed |
| `platform` | string | all | instagram\|facebook\|tiktok\|twitter |
| `account_id` | int | - | Filter by social account |
| `from` | date | - | Filter dari tanggal (YYYY-MM-DD) |
| `to` | date | - | Filter sampai tanggal |
| `tag` | string | - | Filter by tag name |
| `page` | int | 1 | Halaman |
| `per_page` | int | 20 | Max 100 |

**Response 200:**
```json
{
  "data": [
    {
      "id": 42,
      "title": "Promo Hari Raya",
      "status": "scheduled",
      "scheduled_at": "2026-06-10T09:00:00+07:00",
      "tags": [{ "id": 1, "name": "promo", "color": "#EF9F27" }],
      "versions": [
        {
          "id": 10,
          "social_account": {
            "id": 1,
            "platform": "instagram",
            "username": "@rizky_umkm"
          },
          "caption": "Promo gila-gilaan! ...",
          "status": "pending",
          "media": [
            {
              "id": 5,
              "url": "https://yourdomain.com/storage/...",
              "thumb_url": "https://yourdomain.com/storage/...",
              "type": "image",
              "mime_type": "image/jpeg"
            }
          ]
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 87,
    "last_page": 5
  }
}
```

---

### POST /posts
Buat post baru (draft atau langsung schedule).

**Request:**
```json
{
  "title": "Promo Hari Raya",
  "scheduled_at": "2026-06-10T09:00:00+07:00",
  "status": "scheduled",
  "tag_ids": [1, 3],
  "is_recurring": false,
  "versions": [
    {
      "social_account_id": 1,
      "caption": "Promo gila-gilaan Hari Raya! 🎉\n\n#promo #umkm #fashion",
      "hashtags": ["promo", "umkm", "fashion"],
      "media_ids": [5, 6],
      "platform_options": {
        "instagram": {
          "post_type": "feed",     // feed | reel | story
          "location": null,
          "collaborators": []
        }
      }
    },
    {
      "social_account_id": 2,
      "caption": "Hayo siapa yang udah tau promo kita?",   // Custom untuk Facebook
      "media_ids": [5, 6]
    }
  ]
}
```

**Response 201:**
```json
{
  "data": {
    "id": 42,
    "status": "scheduled",
    "scheduled_at": "2026-06-10T09:00:00+07:00",
    ...
  }
}
```

---

### GET /posts/{id}
Ambil detail post.  
**Response 200:** Object post dengan semua versions + analytics jika sudah published.

---

### PUT /posts/{id}
Update post (hanya status draft atau scheduled).

**Request:** Sama dengan POST tapi partial update diperbolehkan.  
**Response 200:** Updated post object.

---

### DELETE /posts/{id}
Hapus post (soft delete).  
**Response 204**

---

### POST /posts/{id}/publish
Publish post immediately (skip schedule).  
**Response 202:**
```json
{ "message": "Post sedang dipublish ke semua platform." }
```

---

### POST /posts/{id}/cancel
Cancel scheduled post.  
**Response 200:** `{ "data": { "status": "cancelled" } }`

---

### POST /posts/{id}/duplicate
Duplicate post sebagai draft baru.  
**Response 201:** New post object.

---

### GET /calendar
Ambil posts untuk calendar view.

**Query Params:** `year` (int), `month` (int), `account_id` (int, optional)

**Response 200:**
```json
{
  "data": {
    "2026-06-10": [
      {
        "id": 42,
        "title": "Promo Hari Raya",
        "status": "scheduled",
        "scheduled_at": "2026-06-10T09:00:00+07:00",
        "platforms": ["instagram", "facebook"],
        "thumb_url": "..."
      }
    ]
  }
}
```

---

## 6. Media Upload

### POST /media/upload
Upload media file.

**Request:** `multipart/form-data`
```
file: [binary]
collection: post_media
```

**Response 201:**
```json
{
  "data": {
    "id": 5,
    "url": "https://yourdomain.com/storage/media/5/image.jpg",
    "thumb_url": "https://yourdomain.com/storage/media/5/conversions/thumb.jpg",
    "preview_url": "https://yourdomain.com/storage/media/5/conversions/preview.jpg",
    "type": "image",
    "mime_type": "image/jpeg",
    "size": 245678,
    "width": 1080,
    "height": 1080
  }
}
```

**Constraints:**
- Image: max 10MB, format: jpeg/png/gif/webp
- Video: max 500MB, format: mp4/mov, max 60 menit
- Max 10 file per post

---

## 7. Analytics

### GET /analytics/overview
Dashboard overview semua platform.

**Query Params:** `from`, `to`, `account_ids[]` (array)

**Response 200:**
```json
{
  "data": {
    "period": { "from": "2026-05-01", "to": "2026-05-31" },
    "totals": {
      "reach": 85420,
      "impressions": 124500,
      "engagement": 4230,
      "engagement_rate": 4.95,
      "new_followers": 342,
      "posts_published": 28
    },
    "by_platform": [
      {
        "platform": "instagram",
        "account_id": 1,
        "username": "@rizky_umkm",
        "reach": 52000,
        "engagement_rate": 5.2,
        "followers": 12500,
        "follower_growth": 215
      }
    ],
    "daily_chart": [
      { "date": "2026-05-01", "reach": 2800, "engagement": 142 }
    ]
  }
}
```

---

### GET /analytics/posts
Performa konten.

**Query Params:** `from`, `to`, `platform`, `account_id`, `sort_by` (reach|engagement|likes), `order` (asc|desc), `page`

**Response 200:**
```json
{
  "data": [
    {
      "post_version_id": 10,
      "post_id": 42,
      "caption_preview": "Promo gila-gilaan!...",
      "platform": "instagram",
      "published_at": "2026-05-15T09:02:00+07:00",
      "thumb_url": "...",
      "metrics": {
        "likes": 523,
        "comments": 47,
        "shares": 89,
        "saves": 210,
        "reach": 8400,
        "impressions": 11200,
        "engagement_rate": 10.35
      }
    }
  ]
}
```

---

### GET /analytics/posts/{id}
Detail performa satu konten — feed data untuk screen `s-content-detail` (wireframe).

**Response 200:**
```json
{
  "data": {
    "post_version_id": 10,
    "post_id": 42,
    "title": "Tips Copywriting #14: Headline yang Bikin Stop Scroll",
    "platform": "instagram",
    "content_type": "carousel",
    "published_at": "2026-05-28T09:00:00+07:00",
    "status": "published",
    "thumb_url": "...",
    "caption": "...",
    "metrics": {
      "reach": 4200,
      "impressions": 9800,
      "likes": 312,
      "comments": 47,
      "shares": 83,
      "saves": 198,
      "engagement_rate": 6.21,
      "deltas": {
        "reach_vs_avg": 8.2,
        "engagement_rate_vs_avg": 0.4
      }
    },
    "daily_reach": [
      { "day": 1, "reach": 1820 },
      { "day": 2, "reach": 980 },
      { "day": 3, "reach": 560 },
      { "day": 4, "reach": 620 },
      { "day": 5, "reach": 90 },
      { "day": 6, "reach": 85 },
      { "day": 7, "reach": 45 }
    ],
    "insights": [
      { "type": "high_performance", "label": "Performa Tinggi", "description": "ER 6.2%, masuk top 25% konten akun ini" },
      { "type": "evergreen",        "label": "Evergreen",       "description": "198 simpan — cocok di-repurpose" },
      { "type": "viral_potential",  "label": "Viral Potential", "description": "Viral coefficient di atas rata-rata" }
    ],
    "account_averages": {
      "reach": 3890,
      "engagement_rate": 5.8
    }
  }
}
```

---

### GET /analytics/audience
Insight audiens.

**Query Params:** `account_id` (required)

**Response 200:**
```json
{
  "data": {
    "account_id": 1,
    "platform": "instagram",
    "demographics": {
      "gender": { "male": 38, "female": 62 },
      "age_groups": [
        { "range": "18-24", "percentage": 28 },
        { "range": "25-34", "percentage": 42 }
      ],
      "top_cities": [
        { "city": "Jakarta", "percentage": 35 },
        { "city": "Surabaya", "percentage": 18 }
      ]
    },
    "active_hours": [
      { "hour": 9, "activity_score": 85 },
      { "hour": 19, "activity_score": 92 }
    ],
    "active_days": [
      { "day": "Monday", "activity_score": 78 }
    ]
  }
}
```

---

### GET /analytics/accounts/{id}/growth
Pertumbuhan follower & reach over time.

**Query Params:** `from`, `to`, `granularity` (daily|weekly|monthly)

---

### GET /reports/generate
Generate laporan PDF.

**Query Params:** `from`, `to`, `account_ids[]`, `white_label` (bool, Agency plan only)

**Response 200:** Binary PDF stream atau `{ "data": { "download_url": "..." } }`

---

## 8. AI Features

### POST /ai/caption
Generate caption dengan AI.

**Request:**
```json
{
  "platform": "instagram",
  "topic": "Promo hari raya diskon 50%",
  "tone": "friendly",             // friendly | professional | humorous | casual
  "language": "id",               // id | en
  "include_hashtags": true,
  "include_cta": true,
  "account_id": 1,                // Opsional — untuk personalisasi berdasarkan brand voice
  "max_length": 300
}
```

**Response 200:**
```json
{
  "data": {
    "caption": "Hayo siapa yang udah siap-siap lebaran? 🎉\n\nKita ada promo spesial hari raya dengan diskon hingga 50% untuk semua koleksi! Jangan sampai kehabisan ya!\n\n#promolebaran #diskon50 #fashionmurah #umkmindonesia",
    "hashtags": ["promolebaran", "diskon50", "fashionmurah", "umkmindonesia"],
    "alternatives": [
      "Lebaran makin meriah dengan koleksi terbaru kita! ..."
    ],
    "character_count": 210,
    "tokens_used": 450
  }
}
```

**Rate limit:** 5 req/menit, 50 req/hari (Pro), 200 req/hari (Agency)

---

### POST /ai/hashtags
Sarankan hashtag relevan.

**Request:**
```json
{
  "topic": "kuliner Jakarta street food",
  "platform": "instagram",
  "count": 20,
  "mix": true          // true = mix popular + niche hashtags
}
```

**Response 200:**
```json
{
  "data": {
    "hashtags": [
      { "tag": "kulinerjakarta", "estimated_reach": "high", "type": "popular" },
      { "tag": "streetfoodjakarta", "estimated_reach": "medium", "type": "niche" }
    ]
  }
}
```

---

### GET /ai/best-time/{account_id}
Rekomendasi waktu terbaik posting.

**Response 200:**
```json
{
  "data": {
    "account_id": 1,
    "platform": "instagram",
    "recommendations": [
      {
        "day": "Tuesday",
        "hour": 19,
        "score": 92,
        "reason": "Engagement rate tertinggi 19.2% pada periode ini"
      },
      {
        "day": "Saturday",
        "hour": 10,
        "score": 87,
        "reason": "Peak traffic audiens kamu pada akhir pekan"
      }
    ],
    "based_on_days": 90,
    "confidence": "high"
  }
}
```

---

## 9. Organization & Team (Agency Plan)

### GET /organization
Ambil info organisasi.

### PUT /organization
Update info organisasi (nama, logo, brand color).

### GET /organization/members
Daftar anggota tim.

**Response 200:**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Budi",
      "email": "budi@agency.id",
      "role": "editor",
      "joined_at": "2026-05-01T00:00:00Z",
      "last_active_at": "2026-06-02T14:30:00Z"
    }
  ]
}
```

---

### POST /organization/members/invite
Undang anggota baru.

**Request:** `{ "email": "newmember@agency.id", "role": "editor" }`  
**Response 201:** `{ "message": "Undangan dikirim ke newmember@agency.id" }`

---

### PUT /organization/members/{userId}
Update role anggota.

**Request:** `{ "role": "admin" }`

---

### DELETE /organization/members/{userId}
Remove anggota dari organisasi.  
**Response 204**

---

## 10. Subscription & Billing

### GET /subscription
Info langganan aktif.

### GET /subscription/plans
Daftar semua plan.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Starter",
      "slug": "starter",
      "price_monthly": 99000,
      "price_yearly": 990000,
      "limits": {
        "social_accounts": 3,
        "scheduled_posts": 30,
        "team_members": 1,
        "clients": 1
      },
      "features": {
        "ai_caption": false,
        "white_label": false,
        "api_access": false
      }
    }
  ]
}
```

---

### POST /subscription/upgrade
Upgrade/downgrade plan.

**Request:** `{ "plan_id": 2, "billing_cycle": "monthly" }`  
**Response 200:** `{ "data": { "payment_url": "https://payment.midtrans.com/..." } }`

---

### POST /subscription/cancel
Cancel langganan (berlaku di akhir periode).  
**Response 200:** `{ "message": "Langganan akan dibatalkan pada 2026-07-03." }`

---

## 11. Webhook Management

### GET /webhooks
Daftar webhook endpoints terdaftar.

### POST /webhooks
Daftarkan webhook endpoint baru.

**Request:**
```json
{
  "url": "https://your-app.com/webhooks/publishin",
  "events": ["post.published", "post.failed", "analytics.updated"]
}
```

**Response 201:**
```json
{
  "data": {
    "id": 1,
    "url": "https://your-app.com/webhooks/publishin",
    "secret": "whsec_abc123...",     // Simpan ini untuk verifikasi signature
    "events": ["post.published"],
    "is_active": true
  }
}
```

---

### PUT /webhooks/{id}
Update webhook.

### DELETE /webhooks/{id}
Hapus webhook.

### POST /webhooks/{id}/test
Kirim test payload ke webhook endpoint.

---

## 12. Platform Webhook Receivers

Endpoint ini menerima webhook dari platform pihak ketiga.

### POST /webhooks/receive/instagram
Menerima webhook dari Instagram/Meta.

**Verification (GET):**
```
GET /webhooks/receive/instagram?hub.mode=subscribe&hub.verify_token={token}&hub.challenge={challenge}
Response 200: hub.challenge value
```

**Payload (POST):**
```json
{
  "object": "instagram",
  "entry": [
    {
      "id": "PAGE_ID",
      "changes": [
        {
          "field": "mentions",
          "value": { ... }
        }
      ]
    }
  ]
}
```

Signature verification via `X-Hub-Signature-256` header.

---

### POST /webhooks/receive/tiktok
Menerima webhook dari TikTok.

---

## 13. Outgoing Webhook Payload Examples

### post.published
```json
{
  "event": "post.published",
  "timestamp": "2026-06-10T09:02:15Z",
  "data": {
    "post_id": 42,
    "post_version_id": 10,
    "platform": "instagram",
    "platform_post_id": "18023456789",
    "published_at": "2026-06-10T09:02:15Z",
    "post_url": "https://www.instagram.com/p/CxYZ123/"
  }
}
```

### post.failed
```json
{
  "event": "post.failed",
  "timestamp": "2026-06-10T09:00:45Z",
  "data": {
    "post_id": 42,
    "post_version_id": 10,
    "platform": "instagram",
    "error_code": "MEDIA_UPLOAD_FAILED",
    "error_message": "Media format tidak didukung"
  }
}
```

**Webhook Signature Verification:**
```php
$signature = hash_hmac('sha256', $rawPayload, $webhookSecret);
$expected = 'sha256=' . $signature;
// Compare with X-Publishin-Signature header (nama header sesuai nama produk final)
```

---

## 14. Error Codes Reference

| Error Code | HTTP | Keterangan |
|---|---|---|
| `INVALID_CREDENTIALS` | 422 | Email/password salah |
| `EMAIL_NOT_VERIFIED` | 403 | Email belum diverifikasi |
| `SUBSCRIPTION_REQUIRED` | 403 | Fitur butuh plan berbayar |
| `SUBSCRIPTION_LIMIT_REACHED` | 403 | Kuota plan habis |
| `PLATFORM_TOKEN_EXPIRED` | 400 | OAuth token platform expired |
| `PLATFORM_API_ERROR` | 502 | Error dari platform API |
| `PLATFORM_RATE_LIMITED` | 429 | Rate limit dari platform |
| `MEDIA_TOO_LARGE` | 422 | File melebihi batas ukuran |
| `MEDIA_FORMAT_UNSUPPORTED` | 422 | Format file tidak didukung |
| `POST_ALREADY_PUBLISHED` | 409 | Post sudah dipublish |
| `POST_NOT_EDITABLE` | 403 | Post tidak bisa diedit (sudah published) |
| `AI_QUOTA_EXCEEDED` | 429 | Kuota AI harian habis |
| `VALIDATION_ERROR` | 422 | Validasi request gagal |
| `RESOURCE_NOT_FOUND` | 404 | Resource tidak ditemukan |
| `FORBIDDEN` | 403 | Tidak punya izin |
| `SERVER_ERROR` | 500 | Internal server error |

---

## 15. SDK & Integration Examples

### PHP (Guzzle)
```php
$client = new GuzzleHttp\Client([
    'base_uri' => 'https://yourdomain.com/api/v1/',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ]
]);

$response = $client->get('posts', [
    'query' => ['status' => 'scheduled', 'per_page' => 50]
]);

$posts = json_decode($response->getBody())->data;
```

### JavaScript (fetch)
```javascript
const API_BASE = 'https://yourdomain.com/api/v1'

const fetchPosts = async (params = {}) => {
  const query = new URLSearchParams(params)
  const res = await fetch(`${API_BASE}/posts?${query}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json',
    }
  })
  if (!res.ok) throw await res.json()
  return res.json()
}
```

---

*Seluruh endpoint yang membutuhkan auth mengembalikan 401 jika token tidak valid atau expired.*
