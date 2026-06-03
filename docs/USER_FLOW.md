# User Flow Documentation
## Publishin — User Journey Maps
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  

---

## Navigasi Sistem (dari wireframe)

```
Sidebar Nav:
  [✓] Dashboard      → Screen 1
  [ ] Kalender  [3]  → Screen 2  (badge = post minggu ini)
  [ ] Buat Konten    → Screen 3
  [ ] Analytics      → Screen 4
  [ ] Laporan        → Screen 5
  [ ] Pengaturan     → Screen 6
                     → Screen 7: Content Detail (drill-down dari Analytics)

URL pattern:
  yourdomain.com/dashboard
  yourdomain.com/calendar
  yourdomain.com/compose
  yourdomain.com/analytics
  yourdomain.com/reports
  yourdomain.com/settings
```

---

## 1. Onboarding Flow

### 1.1 Registrasi & Setup

```
[Landing Page — CTA "Mulai Gratis"]
          │
          ▼
┌─────────────────────────┐
│  Buat Akun              │
│  Nama: [__________]     │
│  Email: [__________]    │
│  Password: [__________] │
│  [Daftar]               │
│  atau [Daftar via Google]│
└──────────┬──────────────┘
           │
           ▼
[Email Verifikasi → klik link]
           │
           ▼
┌─────────────────────────┐
│  Pengaturan Awal        │
│  Tipe: Solo/UMKM/Agency │
│  Timezone: [WIB ▼]      │
└──────────┬──────────────┘
           │
           ▼
[Hubungkan Platform]
☑ Instagram → OAuth → terhubung ✓
☑ TikTok    → OAuth → terhubung ✓
☑ YouTube   → OAuth → terhubung ✓
☐ Facebook  → [+ Hubungkan]
☐ X/Twitter → [+ Hubungkan]
           │
           ▼
[Dashboard — dengan guided tooltip]
```

**Sticky note sidebar saat Pengaturan:**
> "atur akun & koneksi platform"

---

## 2. Dashboard Flow

```
[Screen: Dashboard]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: "Dashboard" | ⌕ Cari konten… | [+ Buat Konten] [Notifikasi (5)]

4 KPI Cards:
┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐
│Total       │ │Post        │ │Avg.        │ │Reach       │
│Followers   │ │Terjadwal   │ │Engagement  │ │(30 hari)   │
│48.3K       │ │12          │ │4.7%        │ │127K        │
│↑ +1.2K     │ │minggu ini  │ │↑ +0.3%     │ │↓ −8K       │
└────────────┘ └────────────┘ └────────────┘ └────────────┘

Charts (2 kolom):
[Engagement Trend 30 hari — line chart terracotta]
[Post per Platform — bar hatch: IG(28) TT(22) FB(14) X(10) YT(7)]

Bottom (2 kolom):
[Postingan Terbaru — table 5 rows]    [Notifikasi (4 items)]
                                       [Waktu Terbaik Post]
                                        IG Sen–Rab: 07:00
                                        TT Kam–Jum: 20:00
                                        YT Weekend: 10:00
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

User actions dari Dashboard:
[+ Buat Konten] → Screen 3 (Compose)
[Notifikasi]    → Panel notifikasi expand
[Lihat Semua →] → Screen 4 (Analytics)
```

---

## 3. Calendar Flow

### 3.1 View Kalender Bulanan

```
[Screen: Kalender]
Sidebar sticky: "atur jadwal & lihat semua post"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: [← Prev] Juni 2026 [Next →] | [+ Jadwalkan Post]

Legend: ● Published  ● Scheduled  ○ Draft

[Calendar Grid]
Min  Sen  Sel  Rab  Kam  Jum  Sab
      1    2    3    4    5    6
           ●Tu-Cap ●BtS  ○RevT
  7   8    9   10   11   12   13
 ●Th10    ●TkCh       ●Promo
...

[Minggu Ini · 3–9 Jun 2026 — Table]
Sel 3 Jun | Tutorial Reels: Caption AI | IG | 07:00 WIB | Scheduled
Rab 4 Jun | Behind the Scenes #12      | TT | 20:30 WIB | Scheduled
Kam 5 Jun | Review Tools Content Creator| YT | 10:00 WIB | Draft
Sab 7 Jun | Thread: 10 tips freelance   | X  | 09:00 WIB | Scheduled
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 3.2 Navigasi Bulan

```
[Klik ← Prev] → CM-- → re-render grid
[Klik Next →] → CM++ → re-render grid
```

### 3.3 Klik Tanggal / "+ Jadwalkan Post"

```
[Klik tanggal di grid] atau [+ Jadwalkan Post]
          │
          ▼
[Screen 3: Buat Konten — tanggal pre-filled]
```

---

## 4. Buat Konten (Compose) Flow

### 4.1 Alur Utama

```
[Screen: Buat Konten]
Sidebar sticky: "tulis & jadwalkan konten baru"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: "Buat Konten" | [Simpan Draft] [Jadwalkan]

LEFT COLUMN:
┌─────────────────────────────────────────┐
│  PLATFORM                               │
│  ☑ IG Instagram                        │
│  ☐ TT TikTok                           │
│  ☐ FB Facebook                         │
│  ☐  X Twitter                          │
│  ☐ YT YouTube                          │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  CAPTION                                │
│  ┌───────────────────────────────────┐  │
│  │ Hai semua! 👋                     │  │
│  │                                   │  │
│  │ Mau share tips caption...         │  │
│  │                                   │  │
│  │ #ContentCreator #TipsMarketing    │  │
│  └───────────────────────────────────┘  │
│  214 / 2200            [✦ AI Generate] │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  MEDIA                                  │
│  ┌─────────────────────────────────┐   │
│  │  + Upload Gambar / Video         │   │
│  │  PNG · JPG · MP4 · MOV · 100MB   │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  JADWAL POSTING                         │
│  Tanggal: [2026-06-04]                  │
│  Waktu: [07:00]                         │
│  💡 Waktu terbaik IG: 07:00–09:00 WIB  │
└─────────────────────────────────────────┘

RIGHT COLUMN:
┌─────────────────────────────────────────┐
│  Preview · Instagram                    │
│  ┌─────────────────────────────────┐   │
│  │ [A] @ahdirmai.id · Jakarta IG   │   │
│  │ [ Image / Video placeholder ]   │   │
│  │ Hai semua! 👋ᅰ                  │   │
│  │ Mau share tips...               │   │
│  │ ♡ Suka  💬 Komentar  ⟳ Bagikan │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  Hashtag Saran · AI                     │
│  #ContentCreator  #MarketingDigital     │
│  #TipsKonten  #Freelance                │
│  #SoloCreator  #BisnisDaring            │
│  #KontenIndonesia                       │
└─────────────────────────────────────────┘
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 4.2 AI Caption Generate Flow

```
[User klik "✦ AI Generate"]
          │
          ▼ (loading ~2 detik)
[Caption berubah menjadi AI-generated]
"Konsistensi adalah kunci! 🔑
Setelah 90 hari posting tiap hari:
→ Reach naik 3×
→ Engagement: 6.2%
#ContentCreator #Growth"
          │
Character counter update otomatis
Preview kanan update real-time
```

### 4.3 Jadwalkan Flow

```
[User klik "Jadwalkan"]
          │
          ▼
[Validasi: platform dipilih? caption ada? jadwal valid?]
          │
          ├── Gagal validasi → highlight field error
          └── Berhasil →
              [Post masuk queue]
              [Alert: "Konten dijadwalkan! ✓"]
              [Redirect ke Calendar — tampilkan post baru di grid]
```

### 4.4 Simpan Draft Flow

```
[User klik "Simpan Draft"]
          │
          ▼
[Simpan dengan status draft]
[Muncul di Calendar dengan dot garis-putus (draft)]
```

---

## 5. Analytics Flow

### 5.1 Overview Tab

```
[Screen: Analytics — tab Overview aktif]
Sidebar sticky: "pantau performa semua platform"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: "Analytics" | [30 Hari Terakhir ▼] [Semua Platform ▼]

Sub-tabs: [Overview ●] [Per Konten]

4 KPI:
Total Reach: 127K ↓ −6%
Impressions: 284K ↑ +12%
Engagement Rate: 4.7% (industri avg: 2.3%)
Follower Growth: +1.2K

Charts:
[Reach per Platform — hatch bars: IG(62K) TT(47K) FB(11K) YT(7K)]
[Pertumbuhan Followers — multi-line: IG solid, TT dashed]

Tables:
[Top 5 Postingan]           [Demografi Audiens · IG]
Tips Copywriting | 4,231|6.2%  Usia: 18-24(38%) 25-34(45%)...
Behind the scenes|12,480|8.1%  Gender: F62% M38%
...                            Lokasi: Jakarta(34%) Surabaya(18%)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 5.2 Filter Analytics

```
[User ganti filter periode: "7 Hari"]
          │
          ▼ (reload data)
[KPI cards update, chart update]

[User ganti filter platform: "Instagram"]
          │
          ▼
[Data hanya tampilkan metrik IG]
```

### 5.3 Per Konten Tab

```
[User klik tab "Per Konten"]
          │
          ▼
[renderPCTable() dipanggil]

┌──────────────────────────────────────────────────────────────────┐
│ [Urutkan: Reach ↓ ▼]  [Semua Tipe ▼]                12 postingan │
├──┬──────────────────────────────┬──────┬─────────┬──────┬───────┤
│  │ Konten                       │Platf.│Tipe     │Reach │Eng.%  │
├──┼──────────────────────────────┼──────┼─────────┼──────┼───────┤
│+ │[IMG] Reels: 1 Menit Tips...  │IG   │Reels    │18.7K │9.4% ● │
│  │      15 Mei 2026             │     │         │      │       │
│  │      [Lihat Detail →]        │     │         │      │       │
├──┼──────────────────────────────┼──────┼─────────┼──────┼───────┤
│+ │[IMG] Batch Content 1 Minggu  │TT   │Video    │22.1K │11.2%● │
│  │      3 Mei 2026              │     │         │      │       │
│  │      [Lihat Detail →]        │     │         │      │       │
└──┴──────────────────────────────┴──────┴─────────┴──────┴───────┘
```

### 5.4 Expand Row Inline

```
[User klik [+] di baris]
          │
          ▼
[Row expand → tampil detail panel]
┌──────────────────────────────────────────────────────┐
│ [Reach] [Impresi] [Like] [Komentar] [Share] [Simpan] │
│ [Bar chart distribusi]  [Breakdown bars proporsi aksi]│
│ Dipublikasi: 15 Mei 2026 | Tipe: Reels | ER: 9.4%   │
└──────────────────────────────────────────────────────┘
          │
[Klik [+] lagi → collapse]

[Klik "Lihat Detail →"] → Buka Content Detail screen
```

---

## 6. Content Detail Flow (Screen 7)

```
[Dari Per Konten: klik "Lihat Detail →"]
          │
          ▼
[Screen: Content Detail]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: [← Kembali ke Analitik]          [IG platform badge]

Header Card:
┌────────────────────────────────────────────────────┐
│ [IMG]  Tips Copywriting #14: Headline yang Bikin   │
│        Stop Scroll                                 │
│        [IG] [Carousel] [28 Mei 2026] [● Dipublikasi]│
└────────────────────────────────────────────────────┘

KPI Row (7 kolom):
┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐
│Reach │ │Impr. │ │Like  │ │Komen.│ │Share │ │Simpan│ │Eng.% │
│4.2K  │ │9.8K  │ │ 312  │ │  47  │ │  83  │ │ 198  │ │6.2%  │
│↑ +8% │ │↑ +11%│ │      │ │      │ │viral │ │ever  │ │Baik  │
└──────┘ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘

3 Kolom Body:
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│Distribusi       │ │Proporsi Aksi    │ │vs Rata-rata     │
│Engagement       │ │Like  ████  312  │ │Akun             │
│[Bar chart]      │ │Komen ██    47   │ │Reach: 4.2K      │
│Like/Komen/Share │ │Share ███   83   │ │↑ +8% dari avg   │
│/Simpan bars     │ │Simpa ████  198  │ │ER: 6.2%         │
│                 │ │                 │ │↑ +0.4pp         │
└─────────────────┘ └─────────────────┘ └─────────────────┘

Reach Harian 7 Hari Pertama:
[Bar timeline H1–H7, peak di H4]

Insight Konten:
[★] Performa Tinggi — ER 6.2%, masuk top 25%
[◈] Evergreen — 198 simpan, baik di-repurpose
[↗] Viral Potential — viral coefficient di atas rata-rata
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[Klik "← Kembali ke Analitik"] → Kembali ke screen Analytics,
                                  sub-tab Per Konten aktif
```

---

## 7. Laporan Flow

### 7.1 Generate Laporan

```
[Screen: Laporan]
Sidebar sticky: "buat laporan untuk klien"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: "Laporan" | [↓ Export PDF]

LEFT: Konfigurasi
  Periode: [Mei 2026 ▼]
  Platform: ☑ IG  ☑ TT  ☑ YT  ☐ FB  ☐ X
  Sertakan:
    ☑ KPI Summary
    ☑ Grafik Engagement
    ☑ Top 10 Posts
    ☑ Demografi Audiens
    ☐ White-label (hapus logo Publishin)   ← Agency plan

RIGHT: Preview
  "Laporan Performa · Mei 2026"
  @ahdirmai.id · 3 Jun 2026
  [REACH 127K] [ENG. 4.7%] [POSTS 81]
  [CHART ENGAGEMENT placeholder]
  ...dan 4 halaman lagi
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[User klik "↓ Export PDF"]
          │
          ▼
[Job generate report di background]
[Toast: "Laporan sedang diproses…"]
          │ ~5 detik
          ▼
[Toast success + download otomatis]
[Muncul di Riwayat Laporan]
```

### 7.2 Riwayat Laporan

```
[Table bawah]
Laporan Mei 2026   | IG, TT, YT | 1–31 Mei 2026 | 1 Jun 2026 | [↓ Download]
Laporan April 2026 | IG, TT, FB | 1–30 Apr 2026 | 2 Mei 2026 | [↓ Download]
Laporan Q1 2026    | Semua      | Jan–Mar 2026  | 5 Apr 2026 | [↓ Download]
```

---

## 8. Pengaturan Flow

### 8.1 Halaman Settings

```
[Screen: Pengaturan]
Sidebar sticky: "atur akun & koneksi platform"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Topbar: "Pengaturan" | [Simpan Perubahan]

LEFT COLUMN:
┌─────────────────────────────────────────┐
│  PROFIL AKUN                            │
│  [A]  Ahdir Mai                         │
│       @ahdirmai.id · Solo Creator       │
│       Pro Plan · aktif Des 2026         │
│                                         │
│  Nama: [Ahdir Mai]                      │
│  Email: [ahdir@ahdirmai.id]             │
│  Zona Waktu: [Asia/Jakarta (WIB) ▼]     │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  PLATFORM TERHUBUNG                     │
│  [IG] @ahdirmai.id · 38.2K ✓ Terhubung │
│  [TT] @ahdirmai · 7.4K     ✓ Terhubung │
│  [YT] Ahdir Mai Studio 2.7K✓ Terhubung │
│  [FB] Facebook Page     [+ Hubungkan]   │
│  [X]  X / Twitter       [+ Hubungkan]   │
└─────────────────────────────────────────┘

RIGHT COLUMN:
┌─────────────────────────────────────────┐
│  NOTIFIKASI                             │
│  Email: ringkasan mingguan      [ON  ●] │
│  Push: konten berhasil publish  [ON  ●] │
│  Push: mention & komentar baru  [OFF  ] │
│  Email: laporan bulanan otomatis[ON  ●] │
│  Push: pengingat jadwal posting [ON  ●] │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  PLAN & BILLING                         │
│  ┌─────────────────────────────────┐   │
│  │ Pro Plan          Rp 149.000/bln│   │
│  │ Aktif hingga 31 Desember 2026   │   │
│  │ ✓ 10 akun sosial media          │   │
│  │ ✓ Unlimited post scheduling     │   │
│  │ ✓ AI caption generator          │   │
│  │ ✓ Custom report + white-label   │   │
│  │ ✓ Priority support              │   │
│  └─────────────────────────────────┘   │
│  [Upgrade ke Enterprise]  [Batalkan]    │
└─────────────────────────────────────────┘
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 8.2 Connect Platform OAuth

```
[Klik "+ Hubungkan" untuk Facebook]
          │
          ▼
[Redirect ke Facebook OAuth]
"Redirect ke Facebook OAuth…"
          │ User approve
          ▼
[Callback → token tersimpan]
[Status: ✓ Terhubung]
```

### 8.3 Toggle Notifikasi

```
[Klik toggle "Push: mention & komentar baru"]
          │ OFF → ON
          ▼
[State toggle berubah, belum tersimpan]

[Klik "Simpan Perubahan"]
          │
          ▼
["Perubahan disimpan! ✓"]
```

---

## 9. Error & Recovery Flows

### 9.1 Post Gagal Publish

```
[Scheduled job berjalan]
          │ Platform error
          ▼
[Notifikasi dot merah di Dashboard]
"Post 'Tutorial Reels' gagal dipublish ke Instagram"
          │
[User klik notifikasi]
          │
          ▼
[Detail error + opsi]
[Edit & Jadwalkan Ulang] [Hapus]
```

### 9.2 Platform Token Expired

```
[Banner kuning di top dashboard]
"Token Instagram @ahdirmai.id akan expired 3 hari lagi"
[Perbarui Koneksi]
          │
          ▼
[OAuth ulang → token diperbarui]
[Notif hijau: "TikTok koneksi berhasil diperpanjang"]
```

### 9.3 Content Tidak Perform (Insight)

```
[Content Detail → Insight Konten]
[○] Performa Normal
    "Konten ini berkinerja sesuai rata-rata.
     Coba A/B test judul atau thumbnail."
```

---

## 10. Mobile / PWA Flow

### 10.1 Responsive Layout Wireframe

```
Mobile (< 900px):
┌────────────────────────────────┐
│ [K·] [Dashboard][Kalen][Buat] →│  ← Sidebar jadi topbar horizontal
├────────────────────────────────┤
│ [2 KPI] [2 KPI]                │  ← Grid 2 kolom
│ [Chart full width]             │
│ [Recent posts table]           │
└────────────────────────────────┘

Bottom nav (mobile tambahan):
[Dashboard] [Kalender] [+] [Analytics] [Settings]
```

---

*Flow ini merepresentasikan wireframe `kontentku-wireframe.html` secara faithful. Setiap screen name, label, dan aksi mengikuti wireframe.*
