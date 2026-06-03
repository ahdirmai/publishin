# Feature Inventory
## Publishin — Complete Feature List
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  
**Legend:** P0=Must MVP | P1=Should Q3 | P2=Could Q4 | P3=Wishlist Y2  
**Source:** Fitur diverifikasi langsung dari wireframe screens  

---

## Screen Coverage dari Wireframe

| Screen | ID | Fitur yang terlihat |
|---|---|---|
| Dashboard | `s-dashboard` | KPI 4 kartu, 2 charts, recent posts table, notifikasi panel, best time widget |
| Kalender | `s-calendar` | Monthly grid, prev/next, legend, weekly table |
| Buat Konten | `s-compose` | Platform picker, caption + AI, media upload, schedule, preview, hashtag chips |
| Analytics | `s-analytics` | Sub-tab Overview (KPI+chart+tabel+demografi) + Per Konten (sortable+expand rows+link detail) |
| Laporan | `s-reports` | Config form, preview, export PDF, riwayat table |
| Pengaturan | `s-settings` | Profil form, platform connections, notif toggles, billing card |
| Content Detail | `s-content-detail` | KPI 7 kolom, 3 charts, timeline, AI insights |

---

## 1. Auth & Akun

| ID | Feature | Screen | Priority | Effort | Status |
|---|---|---|---|---|---|
| AUTH-001 | Email/password registration | Auth | P0 | S | Planned |
| AUTH-002 | Email verification | Auth | P0 | S | Planned |
| AUTH-003 | Login email/password | Auth | P0 | S | Planned |
| AUTH-004 | Google OAuth login | Auth | P0 | M | Planned |
| AUTH-005 | Forgot/reset password | Auth | P0 | S | Planned |
| AUTH-006 | Logout | Auth | P0 | XS | Planned |
| AUTH-007 | Remember me (30 hari) | Auth | P1 | S | Planned |
| AUTH-008 | Two-factor auth (TOTP) | Auth | P2 | L | Planned |

---

## 2. Dashboard (Screen 1)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| DASH-001 | KPI card: Total Followers | `.kpi-val` "48.3K" + delta ↑ +1.2K | P0 | M | Planned |
| DASH-002 | KPI card: Post Terjadwal (minggu ini) | `.kpi-val.r` "12" | P0 | M | Planned |
| DASH-003 | KPI card: Avg Engagement Rate | `.kpi-val` "4.7%" + delta ↑ | P0 | M | Planned |
| DASH-004 | KPI card: Reach 30 hari | `.kpi-val.ink` "127K" + delta ↓ | P0 | M | Planned |
| DASH-005 | Engagement Trend line chart (30 hari) | SVG line chart terracotta | P0 | L | Planned |
| DASH-006 | Post per Platform bar chart | SVG bar chart hatch per platform | P0 | L | Planned |
| DASH-007 | Recent posts table (5 rows) | Konten, Platform badge, Reach, Eng.%, Status badge | P0 | M | Planned |
| DASH-008 | Tombol "Lihat Semua →" | Link ke Analytics | P0 | XS | Planned |
| DASH-009 | Notifikasi panel (4+ items) | `.notif` dengan dot berwarna | P0 | M | Planned |
| DASH-010 | Notifikasi badge (angka) | Badge merah di tombol header | P0 | S | Planned |
| DASH-011 | Best Time to Post widget | Per platform: IG/TT/YT dengan waktu | P0 | M | Planned |
| DASH-012 | Tombol "+ Buat Konten" | Link ke Compose | P0 | XS | Planned |
| DASH-013 | Search bar | Cari konten di topbar | P1 | M | Planned |

---

## 3. Kalender (Screen 2)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| CAL-001 | Monthly calendar grid | 7 kolom, nama hari, grid cells | P0 | L | Planned |
| CAL-002 | Cell today highlight | `.cal-cell.today` background terracotta muted | P0 | XS | Planned |
| CAL-003 | Dot status per tanggal | `.cdot.pub` hijau, `.cdot.sched` biru, `.cdot` putus = draft | P0 | M | Planned |
| CAL-004 | Label post di cell | `.ctag` teks 7.5px max-14 karakter + ellipsis | P0 | S | Planned |
| CAL-005 | Navigate prev/next bulan | `calPrev()` / `calNext()` — update grid | P0 | S | Planned |
| CAL-006 | Header bulan + tahun | "Juni 2026" — serif italic | P0 | XS | Planned |
| CAL-007 | Legend (Published/Scheduled/Draft) | 3 dots + label | P0 | XS | Planned |
| CAL-008 | Weekly list table | Tgl, Konten, Platform, Waktu, Status | P0 | M | Planned |
| CAL-009 | Klik tanggal → buka Compose | Pre-fill tanggal di composer | P0 | S | Planned |
| CAL-010 | Tombol "+ Jadwalkan Post" | Link ke Compose | P0 | XS | Planned |
| CAL-011 | Prev/next cell bulan lain | `.cal-cell.other` warna muted | P0 | XS | Planned |
| CAL-012 | Drag & drop reschedule | Geser post ke tanggal lain | P1 | XL | Planned |

---

## 4. Buat Konten / Compose (Screen 3)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| COM-001 | Platform multi-select checkboxes | IG, TT, FB, X, YT — label + badge | P0 | M | Planned |
| COM-002 | Caption textarea | `.ftextarea` multiline | P0 | S | Planned |
| COM-003 | Character counter | "214 / 2200" — update live | P0 | S | Planned |
| COM-004 | AI Caption Generate | "✦ AI Generate" button → ganti caption | P0 | L | Planned |
| COM-005 | Caption preview real-time | `updatePreview()` — sync textarea → preview | P0 | M | Planned |
| COM-006 | Media upload dropzone | Drag & drop + click, PNG/JPG/MP4/MOV max 100MB | P0 | L | Planned |
| COM-007 | Jadwal tanggal + waktu | Date + time input, 2-kolom | P0 | M | Planned |
| COM-008 | Waktu terbaik hint | "💡 Waktu terbaik IG: 07:00–09:00 WIB" | P0 | S | Planned |
| COM-009 | Instagram post preview | Mock IG post: avatar, placeholder img, caption, reactions | P0 | M | Planned |
| COM-010 | Hashtag chips (AI saran) | `.hchip` clickable chips | P0 | M | Planned |
| COM-011 | Simpan Draft button | Simpan status draft | P0 | S | Planned |
| COM-012 | Jadwalkan button | Publish ke queue + alert "✓" | P0 | M | Planned |
| COM-013 | Custom caption per platform | Override caption per platform (bukan single) | P1 | L | Planned |
| COM-014 | First comment (IG hashtag) | Publish hashtag di komentar pertama | P2 | M | Planned |

---

## 5. Analytics — Overview (Screen 4a)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| ANA-001 | Sub-tab: Overview / Per Konten | `.sub-tabs` navigation | P0 | S | Planned |
| ANA-002 | Filter periode (30/7/90 hari) | Dropdown di topbar | P0 | M | Planned |
| ANA-003 | Filter platform | Dropdown "Semua Platform / IG / TT / YT / X" | P0 | M | Planned |
| ANA-004 | KPI: Total Reach + delta | "127K ↓ −6%" | P0 | M | Planned |
| ANA-005 | KPI: Impressions + delta | "284K ↑ +12%" | P0 | M | Planned |
| ANA-006 | KPI: Engagement Rate + industri avg | "4.7% (industri avg: 2.3%)" | P0 | M | Planned |
| ANA-007 | KPI: Follower Growth | "+1.2K net new" | P0 | M | Planned |
| ANA-008 | Reach per Platform bar chart | Hatch bars: IG(62K) TT(47K) FB(11K) YT(7K) | P0 | L | Planned |
| ANA-009 | Pertumbuhan Followers line chart | Multi-line IG + TT dengan legend | P0 | L | Planned |
| ANA-010 | Top 5 Postingan table | Konten, Reach, Eng.%, Platform | P0 | M | Planned |
| ANA-011 | Demografi Audiens IG | Usia (progress bars), Gender (segment bar), Top 3 kota | P0 | L | Planned |

---

## 6. Analytics — Per Konten (Screen 4b)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| PER-001 | Sortable table | Sort by: Reach ↓, Eng. Rate ↓, Impresi ↓, Terbaru | P0 | L | Planned |
| PER-002 | Filter by content type | Dropdown: Carousel, Reels, Video, Thread, Foto | P0 | M | Planned |
| PER-003 | Post count label | "12 postingan" — update saat filter | P0 | XS | Planned |
| PER-004 | Post thumbnail placeholder | `.pc-thumb` hatch pattern 36×36px | P0 | S | Planned |
| PER-005 | Post title + tanggal + detail link | `.pc-title-name` + `.pc-title-date` + `[Lihat Detail →]` | P0 | M | Planned |
| PER-006 | Platform badge column | `.plt` per platform | P0 | XS | Planned |
| PER-007 | Content type column | Carousel/Reels/Video/Thread/Foto | P0 | XS | Planned |
| PER-008 | Numeric columns (tabular-nums) | Reach, Impresi, Like, Komentar, Share, Save | P0 | S | Planned |
| PER-009 | Engagement Rate color coding | Hijau ≥7%, biru ≥4%, abu <4% (`erClass()`) | P0 | S | Planned |
| PER-010 | Expand [+] button per row | `.expand-btn` — rotate jadi × saat open | P0 | M | Planned |
| PER-011 | Expand panel: 6 metric cards | Reach, Impresi, Like, Komentar, Share, Simpan — masing-masing dengan delta | P0 | L | Planned |
| PER-012 | Expand panel: distribusi chart | `buildSparkSVG()` — bar chart 4 metrics | P0 | L | Planned |
| PER-013 | Expand panel: proporsi bars | Horizontal bars Like/Komentar/Share/Simpan | P0 | M | Planned |
| PER-014 | Expand panel: metadata row | Dipublikasi, Tipe, Platform, Eng. Rate | P0 | S | Planned |
| PER-015 | "Lihat Detail →" link | Buka Content Detail screen | P0 | S | Planned |

---

## 7. Content Detail (Screen 7)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| DET-001 | Back navigation | `← Kembali ke Analitik` → kembali ke Per Konten tab | P0 | S | Planned |
| DET-002 | Platform badge di topbar | `.plt` badge kanan topbar | P0 | XS | Planned |
| DET-003 | Header card: thumb + title | `.cd-thumb-lg` + `.cd-title` serif italic | P0 | M | Planned |
| DET-004 | Header tags: platform, tipe, tanggal, status | `.cd-tags` row dengan badges | P0 | S | Planned |
| DET-005 | KPI Row 7 kolom | Reach, Impresi, Like, Komentar, Share, Simpan, Eng. Rate | P0 | M | Planned |
| DET-006 | KPI delta per metrik | ↑/↓ vs avg dengan warna | P0 | M | Planned |
| DET-007 | Chart: Distribusi Engagement | `buildBigBarSVG()` — 4 bars besar | P0 | L | Planned |
| DET-008 | Proporsi Aksi horizontal bars | Like/Komentar/Share/Simpan dengan fill warna berbeda | P0 | M | Planned |
| DET-009 | vs Rata-rata Akun | Bandingkan reach & ER konten ini vs avg semua konten | P0 | M | Planned |
| DET-010 | Reach Harian 7 Hari Pertama | `.cd-timeline-bars` — H1–H7 bars | P0 | L | Planned |
| DET-011 | Peak H4 + total 7 hari label | Summary di bawah timeline | P0 | S | Planned |
| DET-012 | AI Insights auto-generated | ★ Performa Tinggi, ◈ Evergreen, ↗ Viral, ▶ Format Video, ◎ Diskusi Aktif | P0 | L | Planned |
| DET-013 | Insight kondisional | Hanya tampil insight yang relevan berdasarkan data | P0 | M | Planned |

---

## 8. Laporan (Screen 5)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| REP-001 | Config: periode dropdown | Mei 2026, April 2026, Q1 2026, Custom | P0 | M | Planned |
| REP-002 | Config: platform checkboxes | IG, TT, YT, FB, X — multi-select | P0 | S | Planned |
| REP-003 | Config: sertakan checkboxes | KPI, Chart, Top 10, Demografi, White-label | P0 | S | Planned |
| REP-004 | White-label option | Hapus logo platform (Agency plan only) | P1 | M | Planned |
| REP-005 | Preview laporan mini | Preview inline di card kanan | P0 | M | Planned |
| REP-006 | Export PDF button | "↓ Export PDF" → generate + download | P0 | L | Planned |
| REP-007 | Riwayat laporan table | Nama, Platform, Periode, Dibuat, [↓ Download] | P0 | M | Planned |
| REP-008 | Auto-send laporan ke klien | Jadwal bulanan kirim email | P2 | L | Planned |

---

## 9. Pengaturan (Screen 6)

| ID | Feature | Wireframe Element | Priority | Effort | Status |
|---|---|---|---|---|---|
| SET-001 | Profil: avatar display | Inisial dalam circle border ink | P0 | S | Planned |
| SET-002 | Profil: nama + email edit | Input fields | P0 | S | Planned |
| SET-003 | Profil: zona waktu dropdown | WIB, WITA, WIT | P0 | S | Planned |
| SET-004 | Platform terhubung list | Status ✓ Terhubung / [+ Hubungkan] per platform | P0 | M | Planned |
| SET-005 | Platform follower count display | "@ahdirmai.id · 38.2K" | P0 | S | Planned |
| SET-006 | Notifikasi 5 toggle items | Email mingguan, push published, push mention, email bulanan, push jadwal | P0 | M | Planned |
| SET-007 | Plan & Billing card | Info plan, harga Rp 149.000/bln, masa berlaku, fitur list | P0 | M | Planned |
| SET-008 | Tombol Upgrade ke Enterprise | Alert "Halaman upgrade…" | P0 | S | Planned |
| SET-009 | Tombol Batalkan langganan | Konfirmasi dialog | P1 | M | Planned |
| SET-010 | Simpan Perubahan button | Alert "Perubahan disimpan! ✓" | P0 | S | Planned |

---

## 10. Social Platform Connections

| ID | Feature | Priority | Effort | Status | Notes |
|---|---|---|---|---|---|
| PLAT-001 | Connect Instagram (Business/Creator) | P0 | L | Planned | Instagram Graph API |
| PLAT-002 | Connect Facebook Page | P0 | L | Planned | Facebook Pages API |
| PLAT-003 | Connect TikTok | P1 | XL | Planned | Perlu TikTok app review 2–4 minggu |
| PLAT-004 | Connect Twitter/X | P2 | L | Planned | API v2 berbayar $100+/bln |
| PLAT-005 | Connect YouTube | P1 | L | Planned | YouTube Data API |
| PLAT-006 | Disconnect platform | P0 | S | Planned | |
| PLAT-007 | OAuth token refresh otomatis | P0 | M | Planned | 7 hari sebelum expired |
| PLAT-008 | Banner token expiring warning | P0 | S | Planned | |
| PLAT-009 | Status indikator (✓ / warning / error) | P0 | S | Planned | Dari wireframe Settings screen |

---

## 11. Publishing Engine

| ID | Feature | Priority | Effort | Status |
|---|---|---|---|---|
| PUB-001 | Queue-based publishing (Laravel Queue + Redis) | P0 | L | Planned |
| PUB-002 | Status tracking (Pending → Publishing → Published/Failed) | P0 | M | Planned |
| PUB-003 | Retry 3x dengan exponential backoff | P0 | M | Planned |
| PUB-004 | Partial publish per platform | P0 | M | Planned |
| PUB-005 | Post URL setelah publish tersimpan | P1 | S | Planned |
| PUB-006 | Real-time status via WebSocket (Laravel Reverb) | P1 | M | Planned |

---

## 12. AI Features

| ID | Feature | Priority | Effort | Status | Notes |
|---|---|---|---|---|---|
| AI-001 | Caption generator (Bahasa Indonesia) | P0 | L | Planned | `genCaption()` di wireframe — Claude API |
| AI-002 | Hashtag suggestions AI | P0 | M | Planned | Chips di compose screen |
| AI-003 | Best Time to Post per platform | P0 | M | Planned | Widget di Dashboard |
| AI-004 | Caption generator multi-tone | P1 | M | Planned | Friendly/Professional/Humorous |
| AI-005 | Content insights auto-generated | P0 | L | Planned | `openContentDetail()` insights di Content Detail |
| AI-006 | Trend alert (hashtag trending Indonesia) | P2 | XL | Planned | |
| AI-007 | Sentiment monitoring komentar | P2 | XL | Planned | |

---

## 13. Subscriptions & Billing

| ID | Feature | Priority | Effort | Notes |
|---|---|---|---|---|
| BILL-001 | Plan Starter (Rp 99.000/bln) | P0 | M | 3 akun, 30 post |
| BILL-002 | Plan Pro (Rp 149.000/bln) | P0 | M | Dari wireframe Settings screen |
| BILL-003 | Plan Enterprise | P0 | M | Unlimited + team + API |
| BILL-004 | Free trial | P0 | M | |
| BILL-005 | Upgrade/downgrade | P0 | M | |
| BILL-006 | Invoice download | P0 | M | |
| BILL-007 | Subscription expiry notification | P1 | S | |

---

## 14. Sidebar UX (dari wireframe)

| ID | Feature | Priority | Effort | Notes |
|---|---|---|---|---|
| UX-001 | Logo "K·" serif italic dengan underline | P0 | S | Dari wireframe — nama belum final |
| UX-002 | Plan badge "Pro Plan" di bawah logo | P0 | XS | |
| UX-003 | Nav item dengan checkbox indicator | P0 | S | ✓ saat active |
| UX-004 | Active nav: terracotta background + border | P0 | S | `.nav-item.active` |
| UX-005 | Sticky note annotation per screen | P1 | M | Konten berbeda per screen, rotate -1.2deg |
| UX-006 | Nav badge angka (kalender: 3 post minggu ini) | P0 | S | |

---

## Effort Size Reference

| Size | Estimasi |
|---|---|
| XS | < 1 hari |
| S | 1–2 hari |
| M | 3–5 hari |
| L | 1–2 minggu |
| XL | 2–4 minggu |

---

## Summary Count

| Priority | Count |
|---|---|
| P0 (MVP) | 91 fitur |
| P1 (Q3) | 23 fitur |
| P2 (Q4) | 12 fitur |
| P3 (Y2) | 3 fitur |
| **Total** | **129 fitur** |

> Semua fitur P0 diverifikasi langsung dari wireframe `kontentku-wireframe.html`. Tidak ada asumsi tanpa referensi wireframe.
