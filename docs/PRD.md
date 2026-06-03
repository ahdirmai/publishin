# Product Requirements Document
## Publishin — Social Media Management Tool
### Wireframe Reference: `kontentku-wireframe.html`

**Version:** 1.1  
**Last Updated:** 2026-06-03  
**Status:** Draft  
**Design Acuan:** Wireframe `kontentku-wireframe.html` (aesthetic: editorial/sketch/monospace)

---

## 1. Product Overview

### 1.1 Vision
Platform manajemen media sosial untuk UMKM dan digital agency Indonesia — harga rupiah, UI Bahasa Indonesia, AI-powered untuk konten lokal, dengan aesthetic editorial yang membedakan dari tools generik.

### 1.2 Product Scope
- **Core Modules:** Content Scheduler + Analytics Dashboard + Reporting
- **Target Platform:** Web App (Desktop-first, PWA mobile)
- **Tech Stack:** Laravel 12 + Vue.js + Inertia.js + MySQL (Monolith)
- **Social Platforms:** Instagram, TikTok, Facebook, Twitter/X, YouTube
- **Design Language:** Editorial sketch aesthetic — monospace UI, serif italic KPI, grid paper background

---

## 2. Problem Statement

| Pain Point | Dampak | Frekuensi |
|---|---|---|
| Harus switch antar platform setiap hari | Buang waktu 1–2 jam/hari | Harian |
| Tidak ada data performa terpusat | Sulit ukur ROI konten | Mingguan |
| Tidak ada sistem jadwal terstruktur | Posting tidak konsisten | Harian |
| Tools internasional mahal & bahasa Inggris | Barrier adopsi tinggi | - |
| Tidak ada AI untuk konten Bahasa Indonesia | Caption & hashtag manual | Per-post |

---

## 3. User Personas

### 3.1 Primary — Solo Creator / UMKM Owner
**Contoh profil dari wireframe:** `@ahdirmai.id` — Solo Creator, Jakarta

- **Platform:** Instagram 38.2K followers, TikTok 7.4K, YouTube 2.7K studio
- **Kelola:** 3–5 akun lintas platform secara mandiri
- **Pain:** Tidak punya tim, harus manage konten + analytics sendiri
- **Goal:** Posting konsisten, tahu konten mana yang perform, hemat waktu
- **Budget:** Rp 149.000/bulan (Pro Plan)
- **Tech literacy:** Medium-High — familiar smartphone, sudah coba tools seperti Buffer/Later

### 3.2 Secondary — Digital Agency Manager
- **Kelola:** 10–50 klien sekaligus
- **Pain:** Reporting klien manual, approval konten kacau
- **Goal:** White-label report otomatis, approval workflow efisien
- **Budget:** Rp 299.000–499.000/bulan

### 3.3 Tertiary — Content Creator & Influencer
- **Kelola:** 3–4 platform personal brand
- **Pain:** Tidak tahu konten mana yang terbaik, jadwal tidak konsisten
- **Goal:** Analytics per konten detail (seperti screen "Content Detail" di wireframe)

---

## 4. Goals & Success Metrics

### 4.1 Business Goals
| Goal | Metrik | Target Y1 | Target Y2 |
|---|---|---|---|
| User Acquisition | Paid subscribers | 500 | 3.000 |
| Revenue | MRR | Rp 2,5 juta | Rp 15 juta |
| Retention | Churn rate | < 10%/bulan | < 7%/bulan |

### 4.2 Product Goals (berdasarkan wireframe flows)
| Feature Area | Metrik | Target |
|---|---|---|
| Onboarding | User connect ≥1 platform dalam 24 jam | > 70% |
| Scheduling | User buat ≥1 post via composer | > 60% dalam 7 hari |
| Analytics: Overview | User buka analytics 1x/minggu | > 50% |
| Analytics: Per Konten | User expand row detail konten | > 30% |
| AI Caption | AI generate dipakai di ≥1 post | > 40% |
| Reporting | User download/export laporan | > 20% bulanan |

---

## 5. Screen Inventory (dari wireframe)

Wireframe mendefinisikan **7 screens utama:**

| # | Screen ID | Label | Fungsi |
|---|---|---|---|
| 1 | `dashboard` | Dashboard | Overview KPI, engagement trend, recent posts, notifikasi, waktu terbaik posting |
| 2 | `calendar` | Kalender | Monthly grid calendar, weekly post list, drag-reschedule |
| 3 | `compose` | Buat Konten | Composer — platform picker, caption + AI, media upload, jadwal |
| 4 | `analytics` | Analytics | Sub-tab: Overview (KPI + chart) \| Per Konten (sortable table + expand rows) |
| 5 | `reports` | Laporan | Konfigurasi laporan, preview, history, export PDF |
| 6 | `settings` | Pengaturan | Profil, platform connections, notifikasi toggles, billing |
| 7 | `content-detail` | [Detail Konten] | Drill-down dari "Lihat Detail →" di Per Konten — KPI 7 kolom, charts, insights |

---

## 6. Feature Requirements (MoSCoW)

### 6.1 MUST HAVE (MVP)

**Dashboard (Screen 1):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F001 | KPI cards (Followers, Scheduled, Engagement, Reach) | Tampil 4 kartu dengan nilai + delta vs periode lalu |
| F002 | Engagement trend chart (30 hari) | Line chart SVG dengan hatch/stroke style wireframe |
| F003 | Post per platform bar chart | Bar chart dengan hatch fill per platform |
| F004 | Recent posts table | 5 baris terbaru — konten, platform badge, reach, engagement %, status badge |
| F005 | Notification panel | 4+ notifikasi dengan dot indicator (merah/biru/hijau) |
| F006 | Best time to post widget | Per platform — IG, TT, YT dengan waktu optimal |

**Calendar (Screen 2):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F007 | Monthly calendar grid (7 kolom) | Tampil dot (pub/sched/draft) per cell tanggal |
| F008 | Prev/Next month navigation | Ganti bulan dengan update grid |
| F009 | Legend (Published/Scheduled/Draft) | 3 warna dot + label |
| F010 | Weekly post list table | Tabel di bawah calendar: tgl, konten, platform, waktu, status |
| F011 | Calendar cell click → Compose | Klik tanggal buka composer dengan tanggal pre-filled |

**Buat Konten / Compose (Screen 3):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F012 | Platform multi-select (IG, TT, FB, X, YT) | Checkbox + platform badge |
| F013 | Caption textarea | Multiline, auto-resize, karakter counter (`214 / 2200`) |
| F014 | AI Caption Generate button | Button "✦ AI Generate" — generate sample caption (integrasi Claude API) |
| F015 | Media upload dropzone | Drag & drop area, accept PNG/JPG/MP4/MOV, max 100MB |
| F016 | Jadwal posting (tanggal + waktu) | Date + time input, 2-kolom grid |
| F017 | Waktu terbaik hint | Teks `💡 Waktu terbaik IG: 07:00–09:00 WIB` |
| F018 | Preview Instagram | Mock post UI (avatar + placeholder image + caption preview) |
| F019 | Hashtag suggestions chips | AI-generated chips, klik untuk tambah ke caption |
| F020 | Simpan Draft | Simpan sebagai draft |
| F021 | Jadwalkan | Publish ke queue scheduler |

**Analytics Overview (Screen 4a):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F022 | KPI: Total Reach, Impressions, Engagement Rate, Follower Growth | 4 kartu dengan delta vs periode lalu |
| F023 | Reach per Platform bar chart | Hatch bar per platform (IG 62K, TT 47K, dst) |
| F024 | Pertumbuhan Followers line chart | Multi-line (IG + TT) dengan legend |
| F025 | Top 5 Posts table | Konten, Reach, Eng.%, Platform |
| F026 | Demografi Audiens (IG) | Progress bars usia, segment bar gender, top 3 kota |
| F027 | Filter: periode (30/7/90 hari) | Dropdown ganti data |
| F028 | Filter: platform | Dropdown filter per platform |

**Analytics Per Konten (Screen 4b):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F029 | Sortable post table | Sort by: Reach, Eng. Rate, Impresi, Terbaru |
| F030 | Filter by content type | Carousel, Reels, Video, Thread, Foto |
| F031 | Expand row inline | Klik [+] expand row — tampil 6 metric cards + distribusi chart + breakdown bars |
| F032 | Post count label | "12 postingan" |
| F033 | Platform & type columns | Badge platform + tipe teks |
| F034 | Eng. Rate color coding | Hijau ≥7%, biru ≥4%, abu <4% |

**Content Detail (Screen 7):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F035 | Back navigation ke Analytics Per Konten | Tombol `← Kembali ke Analitik` |
| F036 | Header card: thumb + judul + tags | Platform badge, tipe, tanggal, status |
| F037 | KPI Row 7 kolom | Reach, Impresi, Like, Komentar, Share, Simpan, Eng. Rate |
| F038 | Distribusi Engagement bar chart | Like/Komentar/Share/Simpan bars |
| F039 | Proporsi Aksi breakdown bars | Horizontal bar dengan label + nilai |
| F040 | vs Rata-rata Akun | Bandingkan reach & ER konten ini vs rata-rata akun |
| F041 | Reach Harian 7 Hari Pertama | Bar chart timeline H1–H7 |
| F042 | Insight Konten | Auto-generated insights (Performa Tinggi, Evergreen, Viral Potential, dst) |

**Laporan (Screen 5):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F043 | Konfigurasi: periode dropdown | Pilih bulan atau custom |
| F044 | Konfigurasi: platform checkboxes | Multi-select IG/TT/YT/FB/X |
| F045 | Konfigurasi: sertakan checkboxes | KPI, Chart, Top 10, Demografi, White-label |
| F046 | Preview laporan mini | Preview inline sebelum export |
| F047 | Export PDF | Generate & download PDF |
| F048 | Riwayat laporan | Table dengan tombol Download per row |

**Pengaturan (Screen 6):**
| ID | Feature | Acceptance Criteria |
|---|---|---|
| F049 | Profil: nama, email, timezone | Edit + save |
| F050 | Platform Terhubung | List status connected accounts + tombol "Hubungkan" untuk yang belum |
| F051 | Notifikasi toggles | 5 toggle: email mingguan, push published, push mention, email bulanan, push jadwal |
| F052 | Plan & Billing card | Info plan aktif, masa berlaku, fitur, tombol Upgrade + Batalkan |

**Auth:**
| ID | Feature |
|---|---|
| F053 | Register email + password |
| F054 | Login email + password |
| F055 | Forgot/reset password |
| F056 | Email verifikasi |

### 6.2 SHOULD HAVE (Q3)
- F057: AI Caption multi-tone (friendly/professional/humorous)
- F058: White-label report (hapus logo, custom branding)
- F059: Team collaboration + roles (Agency plan)
- F060: Post approval workflow (Draft → Review → Scheduled)
- F061: Auto-repeat/evergreen posts
- F062: Mobile PWA installable

### 6.3 COULD HAVE (Q4)
- F063: Trend Alert (hashtag trending Indonesia)
- F064: Sentiment monitoring komentar
- F065: Competitor benchmarking
- F066: API publik (Agency plan)
- F067: WhatsApp Business integration

---

## 7. Pricing (dari wireframe)

| Plan | Harga | Fitur |
|---|---|---|
| **Starter** | Rp 99.000/bln | 3 akun, 30 post, analytics dasar |
| **Pro** | **Rp 149.000/bln** | 10 akun sosial, unlimited scheduling, AI caption, custom report + white-label, priority support |
| **Agency / Enterprise** | Rp 299.000+/bln | Unlimited klien & akun, team collaboration, white-label, API access |

> **Note:** Wireframe screen Settings menampilkan Pro Plan = Rp 149.000/bln. Ini adalah harga final yang dipakai di PRD. Sebelumnya PRD v1.0 mencantumkan Rp 249.000 — sudah dikoreksi sesuai wireframe.

---

## 8. Non-Functional Requirements

### 8.1 Performance
- Page load time < 2 detik (LCP)
- API response < 500ms P95
- Scheduled post published ±2 menit dari waktu ditentukan
- Analytics dashboard load < 3 detik untuk 90 hari data

### 8.2 Design Fidelity
- Implementasi harus faithful ke wireframe `kontentku-wireframe.html`
- Monospace font untuk semua UI text
- Serif italic bold untuk semua KPI/angka besar
- Border 1.5px solid ink (bukan shadow)
- Border radius 2px (bukan rounded modern)
- Grid paper background di app shell

### 8.3 Security
- HTTPS enforced
- OAuth token terenkripsi di DB (AES-256)
- Rate limiting per user/IP
- Compliance UU PDP 2024

---

## 9. User Stories (Core Flows — sesuai wireframe)

```
US-001: Sebagai content creator, saya ingin lihat semua KPI platform
        di satu dashboard sehingga tidak perlu buka banyak app.
        AC: Dashboard tampilkan 4 KPI cards + 2 charts + recent posts

US-002: Sebagai user, saya ingin lihat kalender konten bulan ini
        sehingga bisa plan konten ke depan.
        AC: Calendar grid 7×5, dot berwarna per status, navigasi prev/next

US-003: Sebagai user, saya ingin buat konten dengan AI generate caption
        sehingga hemat waktu nulis.
        AC: Tekan "✦ AI Generate" → caption muncul di textarea

US-004: Sebagai user, saya ingin lihat performa per konten
        sehingga tahu konten mana yang paling berdampak.
        AC: Tab "Per Konten" → table sortable → expand row → lihat detail

US-005: Sebagai user, saya ingin lihat detail satu konten secara mendalam
        sehingga bisa belajar dari konten yang perform.
        AC: Klik "Lihat Detail →" → Content Detail screen dengan 7 KPI + charts + insights

US-006: Sebagai agency, saya ingin export laporan berbranding agensi saya
        sehingga bisa kirim ke klien dengan profesional.
        AC: Centang "White-label", export PDF tanpa logo platform

US-007: Sebagai user, saya ingin terima notifikasi saat konten berhasil dipublish
        sehingga tahu tanpa harus cek manual.
        AC: Notifikasi muncul di panel + email/push sesuai settings
```

---

## 10. Release Plan

| Fase | Timeline | Deliverables |
|---|---|---|
| MVP | Q3 2026 (3–4 bulan) | Screen 1–7 + Auth + Platform IG & FB |
| Growth | Q4 2026 (2–3 bulan) | TikTok integration, AI features, PWA |
| Scale | Q1 2027+ | Agency features, white-label, API publik |

---

## 11. Open Questions

| # | Pertanyaan | Owner | Deadline |
|---|---|---|---|
| OQ-1 | Nama produk final? (lihat 5 opsi di akhir dokumen) | Business | 2026-06-15 |
| OQ-2 | Payment gateway — Midtrans vs Xendit? | Business | 2026-06-30 |
| OQ-3 | Twitter/X API berbayar $100+/bln — include MVP atau tidak? | Tech | 2026-06-20 |
| OQ-4 | YouTube analytics — include MVP atau Q3? | Product | 2026-06-20 |

---

*PRD ini diupdate berdasarkan wireframe `kontentku-wireframe.html` v0.1 (2026-06-03).*
