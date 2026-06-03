<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const navOpen = ref(false)
const isYearly = ref(false)
const openFaq = ref<number | null>(null)
const ctaEmail = ref('')
const ctaSubmitted = ref(false)

const prices = {
  monthly: ['Rp 99K', 'Rp 149K', 'Rp 299K'],
  yearly: ['Rp 79K', 'Rp 119K', 'Rp 239K'],
}

const faqItems = [
  {
    q: 'Apakah ada uji coba gratis?',
    a: 'Ya! Semua paket tersedia dengan masa uji coba 14 hari gratis tanpa memerlukan kartu kredit. Kamu bisa menggunakan semua fitur penuh selama periode trial.',
  },
  {
    q: 'Platform media sosial apa saja yang didukung?',
    a: 'Publishin mendukung Instagram, TikTok, Facebook, Twitter/X, LinkedIn, dan YouTube. Kami terus menambahkan platform baru berdasarkan permintaan pengguna.',
  },
  {
    q: 'Bagaimana cara mengelola akun klien?',
    a: 'Paket Pro dan Agency mendukung manajemen multi-klien. Kamu bisa membuat workspace terpisah untuk setiap klien, mengundang anggota tim, dan membuat laporan bermerek klien dengan mudah.',
  },
  {
    q: 'Seberapa aman data saya?',
    a: 'Keamanan adalah prioritas kami. Semua data dienkripsi dengan AES-256, server kami berlokasi di Indonesia (Equinix JK1), dan kami mematuhi UU PDP Indonesia. Kami tidak pernah menjual data kamu ke pihak ketiga.',
  },
  {
    q: 'Metode pembayaran apa yang diterima?',
    a: 'Kami menerima transfer bank (BCA, Mandiri, BNI, BRI), kartu kredit/debit Visa & Mastercard, GoPay, OVO, Dana, dan QRIS. Semua pembayaran diproses melalui Midtrans yang telah tersertifikasi PCI DSS.',
  },
  {
    q: 'Apakah saya bisa membatalkan kapan saja?',
    a: 'Tentu. Tidak ada kontrak jangka panjang. Kamu bisa membatalkan langganan kapan saja dari dashboard, dan akses akan tetap aktif hingga akhir periode billing berjalan.',
  },
]

function toggleFaq(i: number) {
  openFaq.value = openFaq.value === i ? null : i
}

function submitCTA() {
  if (!ctaEmail.value || !ctaEmail.value.includes('@')) return
  ctaSubmitted.value = true
}

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed')
          observer.unobserve(entry.target)
        }
      })
    },
    { threshold: 0.08 }
  )
  document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el))
})
</script>

<template>
  <Head title="Publishin — Platform SMM #1 Indonesia" />

  <!-- SVG Hatch Defs -->
  <svg width="0" height="0" style="position:absolute">
    <defs>
      <pattern id="hatch-r" width="6" height="6" patternTransform="rotate(45)" patternUnits="userSpaceOnUse">
        <line x1="0" y1="0" x2="0" y2="6" stroke="#C96442" stroke-width="1.2" stroke-opacity="0.18" />
      </pattern>
      <pattern id="hatch-b" width="6" height="6" patternTransform="rotate(45)" patternUnits="userSpaceOnUse">
        <line x1="0" y1="0" x2="0" y2="6" stroke="#3B6DB5" stroke-width="1.2" stroke-opacity="0.18" />
      </pattern>
      <pattern id="hatch-g" width="6" height="6" patternTransform="rotate(45)" patternUnits="userSpaceOnUse">
        <line x1="0" y1="0" x2="0" y2="6" stroke="#2A7A4B" stroke-width="1.2" stroke-opacity="0.18" />
      </pattern>
    </defs>
  </svg>

  <!-- Sticky Nav -->
  <nav>
    <div class="pg nav-inner">
      <a href="/" class="nav-logo">Publishin</a>
      <button class="hamburger" :class="{ open: navOpen }" @click="navOpen = !navOpen" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-links" :class="{ open: navOpen }">
        <a href="#fitur" class="nav-link" @click="navOpen = false">Fitur</a>
        <a href="#harga" class="nav-link" @click="navOpen = false">Harga</a>
        <a href="#testimoni" class="nav-link" @click="navOpen = false">Testimoni</a>
        <a href="#faq" class="nav-link" @click="navOpen = false">FAQ</a>
        <Link href="/login" class="nav-cta-ghost">Masuk</Link>
        <Link href="/waitlist" class="nav-cta">Coba Gratis</Link>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="pg">
      <div class="hero-eyebrow" data-reveal>
        <span class="eyebrow-dot"></span>
        Platform SMM #1 untuk Creator &amp; Agensi Indonesia
      </div>
      <h1 class="hero-h1" data-reveal data-delay="1">
        Satu dashboard untuk<br />semua sosial mediamu.
      </h1>
      <p class="hero-sub" data-reveal data-delay="2">
        Jadwalkan konten, analisis performa, dan kelola semua akun sosial media klienmu dari satu tempat. Hemat waktu hingga 12 jam per minggu.
      </p>
      <div class="hero-btns" data-reveal data-delay="3">
        <Link href="/waitlist" class="btn-pri">Mulai Gratis 14 Hari →</Link>
        <a href="#demo" class="btn-ghost">Lihat Demo</a>
      </div>
      <p class="hero-note" data-reveal data-delay="4">Tidak perlu kartu kredit · Setup 5 menit</p>

      <!-- Product Mockup -->
      <div class="mockup-wrap" data-reveal data-delay="5">
        <div class="mockup-bar">
          <div class="m-dots">
            <span class="m-dot" style="background:#FF5F57"></span>
            <span class="m-dot" style="background:#FEBC2E"></span>
            <span class="m-dot" style="background:#28C840"></span>
          </div>
          <div class="m-url">app.publishin.id/dashboard</div>
        </div>
        <div class="mockup-body">
          <div class="m-sidebar">
            <div class="m-logo-area">
              <div class="m-logo-mark">P·</div>
              <div class="m-logo-sub">Publishin</div>
            </div>
            <div class="m-nav-item act">
              <span class="m-chk">▦</span> Dashboard
            </div>
            <div class="m-nav-item">
              <span class="m-chk">◫</span> Kalender
            </div>
            <div class="m-nav-item">
              <span class="m-chk">◈</span> Konten
              <span class="m-nbadge">3</span>
            </div>
            <div class="m-nav-item">
              <span class="m-chk">◉</span> Analytics
            </div>
            <div class="m-nav-item">
              <span class="m-chk">◎</span> Klien
            </div>
            <div class="m-nav-item">
              <span class="m-chk">⊡</span> Laporan
            </div>
          </div>
          <div class="m-main">
            <div class="m-topbar">
              <div class="m-topbar-title">Dashboard</div>
              <div class="m-btn pri">+ Buat Konten</div>
            </div>
            <div class="m-kpi-row">
              <div class="m-kpi">
                <div class="m-kpi-n">48.2K</div>
                <div class="m-kpi-l">Total Reach</div>
                <div class="m-kpi-d up">↑ 12.4%</div>
              </div>
              <div class="m-kpi">
                <div class="m-kpi-n r">3.8%</div>
                <div class="m-kpi-l">Engagement</div>
                <div class="m-kpi-d up">↑ 0.6%</div>
              </div>
              <div class="m-kpi">
                <div class="m-kpi-n k">127</div>
                <div class="m-kpi-l">Post Terjadwal</div>
                <div class="m-kpi-d dn">↓ 2</div>
              </div>
              <div class="m-kpi">
                <div class="m-kpi-n">12</div>
                <div class="m-kpi-l">Klien Aktif</div>
                <div class="m-kpi-d up">↑ 2</div>
              </div>
            </div>
            <div class="m-chart-wrap">
              <div class="m-chart-lbl">Engagement Trend — 7 Hari Terakhir</div>
              <svg viewBox="0 0 320 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:80px">
                <path d="M0,60 C20,55 40,45 60,40 C80,35 100,50 120,42 C140,34 160,20 180,18 C200,16 220,28 240,24 C260,20 280,12 320,8" stroke="#C96442" stroke-width="1.5" fill="none" />
                <path d="M0,70 C20,65 40,58 60,55 C80,52 100,60 120,56 C140,52 160,42 180,40 C200,38 220,48 240,44 C260,40 280,32 320,28" stroke="#3B6DB5" stroke-width="1.5" fill="none" stroke-dasharray="3,2" />
                <line x1="0" y1="78" x2="320" y2="78" stroke="#E8E6E1" stroke-width="0.5" />
              </svg>
            </div>
            <table class="m-tbl">
              <thead>
                <tr>
                  <th>Konten</th>
                  <th>Platform</th>
                  <th>Jadwal</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Tips Produktivitas #12</td>
                  <td><span class="m-plt ig">IG</span></td>
                  <td>Hari ini 10:00</td>
                  <td><span class="m-mstat pub">Terbit</span></td>
                </tr>
                <tr>
                  <td>Behind the Scene Kantor</td>
                  <td><span class="m-plt tt">TT</span></td>
                  <td>Besok 14:00</td>
                  <td><span class="m-mstat sch">Terjadwal</span></td>
                </tr>
                <tr>
                  <td>Promo Ramadan Campaign</td>
                  <td><span class="m-plt fb">FB</span></td>
                  <td>03 Jun 09:00</td>
                  <td><span class="m-mstat sch">Terjadwal</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Social Proof Strip -->
  <div class="social-proof-strip">
    <div class="pg sps-inner">
      <span class="sps-label">Dipercaya oleh :</span>
      <span class="sps-agency">Kreasi Digital</span>
      <span class="sps-agency">MediaKu Agency</span>
      <span class="sps-agency">Viral Studio</span>
      <span class="sps-agency">SosMed Pro</span>
      <span class="sps-agency">KontenKita</span>
      <span class="sps-more">+340 agensi &amp; kreator aktif</span>
    </div>
  </div>

  <!-- Stats Row -->
  <div class="pg">
    <div class="stats-row" data-reveal>
      <div class="stat-item">
        <div class="stat-num">340+</div>
        <div class="stat-lbl">Agensi &amp; Kreator Aktif</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">2.8M</div>
        <div class="stat-lbl">Post Dipublikasikan</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">5</div>
        <div class="stat-lbl">Platform Terintegrasi</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">4.9★</div>
        <div class="stat-lbl">Rating Rata-rata</div>
      </div>
    </div>
  </div>

  <!-- Features Section -->
  <section id="fitur" class="section">
    <div class="pg">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">Fitur Unggulan</div>
        <h2 class="sec-title">Semua yang kamu butuhkan,<br />dalam satu platform.</h2>
        <p class="sec-sub">Dirancang khusus untuk kebutuhan creator dan agensi Indonesia.</p>
      </div>

      <!-- Feature Row 1: Kalender Visual -->
      <div class="feat-row" data-reveal="left">
        <div class="feat-text">
          <div class="feat-tag">Kalender Visual</div>
          <h3 class="feat-h">Rencanakan konten satu bulan ke depan dengan drag &amp; drop.</h3>
          <p class="feat-p">Lihat semua jadwal posting di semua platform dalam satu tampilan kalender. Drag konten untuk menjadwal ulang, filter per klien atau platform, dan deteksi celah konten sebelum terlambat.</p>
          <ul class="feat-list">
            <li>Tampilan kalender bulanan, mingguan &amp; harian</li>
            <li>Filter per klien, platform, dan status</li>
            <li>Deteksi otomatis waktu posting terbaik</li>
            <li>Sync dengan Google Calendar</li>
          </ul>
        </div>
        <div class="feat-visual" data-reveal="right">
          <div class="cal-mini">
            <div class="cal-mini-header">
              <span>◀</span>
              <strong>Juni 2026</strong>
              <span>▶</span>
            </div>
            <div class="cal-mini-grid">
              <div class="cal-mini-lbl">Sen</div>
              <div class="cal-mini-lbl">Sel</div>
              <div class="cal-mini-lbl">Rab</div>
              <div class="cal-mini-lbl">Kam</div>
              <div class="cal-mini-lbl">Jum</div>
              <div class="cal-mini-lbl">Sab</div>
              <div class="cal-mini-lbl">Min</div>
              <div class="cal-mini-cell"></div>
              <div class="cal-mini-cell">
                1<span class="cdot ig"></span>
              </div>
              <div class="cal-mini-cell">
                2<span class="cdot tt"></span>
              </div>
              <div class="cal-mini-cell today">3</div>
              <div class="cal-mini-cell">
                4<span class="cdot ig"></span><span class="cdot fb"></span>
              </div>
              <div class="cal-mini-cell">5</div>
              <div class="cal-mini-cell">6</div>
              <div class="cal-mini-cell">
                7<span class="cdot ig"></span>
              </div>
              <div class="cal-mini-cell">
                8<span class="cdot tt"></span>
              </div>
              <div class="cal-mini-cell">
                9<span class="cdot ig"></span>
              </div>
              <div class="cal-mini-cell">
                10<span class="cdot fb"></span>
              </div>
              <div class="cal-mini-cell">11</div>
              <div class="cal-mini-cell">12</div>
              <div class="cal-mini-cell">13</div>
              <div class="cal-mini-cell">
                14<span class="cdot ig"></span><span class="cdot tt"></span>
              </div>
              <div class="cal-mini-cell">15</div>
              <div class="cal-mini-cell">
                16<span class="cdot ig"></span>
              </div>
              <div class="cal-mini-cell">
                17<span class="cdot fb"></span>
              </div>
              <div class="cal-mini-cell">18</div>
              <div class="cal-mini-cell">19</div>
              <div class="cal-mini-cell">20</div>
              <div class="cal-mini-cell">
                21<span class="cdot tt"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Row 2: Analytics (reversed) -->
      <div class="feat-row rev" data-reveal="right">
        <div class="feat-text">
          <div class="feat-tag">Analytics &amp; Insight</div>
          <h3 class="feat-h">Data yang kamu butuhkan untuk tumbuh lebih cepat.</h3>
          <p class="feat-p">Dashboard analytics terpadu untuk semua platform. Lacak reach, engagement, follower growth, dan konten terbaik secara real-time. Bandingkan performa antar periode dan antar platform.</p>
          <ul class="feat-list">
            <li>Laporan real-time semua platform</li>
            <li>Analisis konten terbaik &amp; waktu optimal</li>
            <li>Grafik pertumbuhan follower</li>
            <li>Export data ke PDF &amp; Excel</li>
          </ul>
        </div>
        <div class="feat-visual" data-reveal="left">
          <div class="analytics-preview">
            <div class="ap-kpis">
              <div class="ap-kpi">
                <div class="ap-kpi-n">48.2K</div>
                <div class="ap-kpi-d up">↑ 12%</div>
                <div class="ap-kpi-l">Total Reach</div>
              </div>
              <div class="ap-kpi">
                <div class="ap-kpi-n">3.8%</div>
                <div class="ap-kpi-d up">↑ 0.6%</div>
                <div class="ap-kpi-l">Engagement</div>
              </div>
              <div class="ap-kpi">
                <div class="ap-kpi-n">+1.2K</div>
                <div class="ap-kpi-d up">↑ 18%</div>
                <div class="ap-kpi-l">Followers Baru</div>
              </div>
            </div>
            <div class="ap-chart">
              <div class="ap-chart-title">Pertumbuhan Follower — 30 Hari</div>
              <svg viewBox="0 0 280 70" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%">
                <defs>
                  <linearGradient id="ap-grad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#C96442" stop-opacity="0.2" />
                    <stop offset="100%" stop-color="#C96442" stop-opacity="0" />
                  </linearGradient>
                </defs>
                <path d="M0,60 C30,55 55,48 80,40 C105,32 130,35 155,25 C180,15 210,18 240,10 C260,5 270,4 280,3" stroke="#C96442" stroke-width="1.5" fill="none" />
                <path d="M0,60 C30,55 55,48 80,40 C105,32 130,35 155,25 C180,15 210,18 240,10 C260,5 270,4 280,3 L280,68 L0,68 Z" fill="url(#ap-grad)" />
                <line x1="0" y1="67" x2="280" y2="67" stroke="#E8E6E1" stroke-width="0.5" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Row 3: Buat Konten + AI -->
      <div class="feat-row" data-reveal="left">
        <div class="feat-text">
          <div class="feat-tag">Buat Konten + AI</div>
          <h3 class="feat-h">Tulis caption lebih cepat dengan bantuan AI.</h3>
          <p class="feat-p">Editor konten bawaan dengan AI assistant untuk generate caption, hashtag, dan ide konten. Preview tampilan di Instagram, TikTok, dan Facebook sebelum publish.</p>
          <ul class="feat-list">
            <li>AI caption generator bahasa Indonesia</li>
            <li>Rekomendasi hashtag otomatis</li>
            <li>Preview multi-platform real-time</li>
            <li>Library template konten siap pakai</li>
          </ul>
        </div>
        <div class="feat-visual" data-reveal="right">
          <div class="compose-preview">
            <div class="cp-form">
              <div class="cp-lbl">Caption</div>
              <div class="cp-textarea">✨ Tips produktivitas untuk kamu yang WFH! Berikut 5 cara ampuh agar tetap fokus saat bekerja dari rumah...</div>
              <button class="cp-ai-btn">✦ Generate dengan AI</button>
            </div>
            <div class="cp-preview">
              <div class="cp-plt-badge">IG</div>
              <div class="cp-preview-img"></div>
              <div class="cp-preview-text">
                <strong>publishin.id</strong><br />
                ✨ Tips produktivitas untuk kamu yang WFH! Berikut 5 cara ampuh...
                <span style="color:var(--accent-b)"> #produktivitas #wfh #tipshidup</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Row 4: Laporan Klien (reversed) -->
      <div class="feat-row rev" style="border-bottom:none;margin-bottom:0" data-reveal="right">
        <div class="feat-text">
          <div class="feat-tag">Laporan Klien</div>
          <h3 class="feat-h">Buat laporan profesional untuk klien dalam hitungan menit.</h3>
          <p class="feat-p">Generate laporan bermerek klien secara otomatis. Pilih periode, pilih metrik, dan laporan PDF siap dikirim. Hemat 3–4 jam kerja per klien per bulan.</p>
          <ul class="feat-list">
            <li>Template laporan white-label</li>
            <li>Auto-generate PDF bulanan</li>
            <li>Custom logo &amp; warna brand klien</li>
            <li>Kirim otomatis via email terjadwal</li>
          </ul>
        </div>
        <div class="feat-visual" data-reveal="left">
          <div class="report-preview">
            <div class="rp-header">
              <div class="rp-brand">
                <div class="rp-logo-placeholder"></div>
                <div>
                  <div class="rp-client-name">Brand Kosmetik A</div>
                  <div class="rp-period">Laporan Mei 2026</div>
                </div>
              </div>
              <div class="rp-badge">PDF</div>
            </div>
            <div class="rp-metrics">
              <div class="rp-metric">
                <div class="rp-metric-n">128K</div>
                <div class="rp-metric-l">Total Impressi</div>
              </div>
              <div class="rp-metric">
                <div class="rp-metric-n">4.2%</div>
                <div class="rp-metric-l">Avg. Engagement</div>
              </div>
              <div class="rp-metric">
                <div class="rp-metric-n">42</div>
                <div class="rp-metric-l">Post Terpublish</div>
              </div>
            </div>
            <div class="rp-bar-wrap">
              <div class="rp-bar-label">Top Konten Bulan Ini</div>
              <div class="rp-bar-item">
                <div class="rp-bar-title">Promo Flash Sale</div>
                <div class="rp-bar-track"><div class="rp-bar-fill" style="width:85%"></div></div>
                <div class="rp-bar-val">18.4K</div>
              </div>
              <div class="rp-bar-item">
                <div class="rp-bar-title">Tutorial Skincare</div>
                <div class="rp-bar-track"><div class="rp-bar-fill" style="width:62%"></div></div>
                <div class="rp-bar-val">13.2K</div>
              </div>
              <div class="rp-bar-item">
                <div class="rp-bar-title">Behind the Scene</div>
                <div class="rp-bar-track"><div class="rp-bar-fill" style="width:44%"></div></div>
                <div class="rp-bar-val">9.6K</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Comparison Table -->
  <section class="section" style="background:#fff;padding:64px 0">
    <div class="pg">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">Perbandingan</div>
        <h2 class="sec-title">Kenapa pilih Publishin?</h2>
        <p class="sec-sub">Fitur lengkap, harga terjangkau, dan dirancang untuk pasar Indonesia.</p>
      </div>
      <div style="overflow-x:auto" data-reveal>
        <table class="compare-tbl">
          <thead>
            <tr>
              <th>Fitur</th>
              <th style="color:var(--accent-r)">Publishin</th>
              <th>Hootsuite</th>
              <th>Buffer</th>
              <th>Spreadsheet</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Harga mulai</td>
              <td class="check-par"><strong>Rp 99K/bln</strong></td>
              <td>$99/bln (~Rp 1.6jt)</td>
              <td>$6/bln (~Rp 98K)</td>
              <td>Gratis</td>
            </tr>
            <tr>
              <td>Bahasa Indonesia</td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-n">✗</span></td>
            </tr>
            <tr>
              <td>Laporan klien white-label</td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-n">✗</span></td>
            </tr>
            <tr>
              <td>AI Caption Generator</td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-par">Parsial</span></td>
              <td><span class="check-n">✗</span></td>
            </tr>
            <tr>
              <td>Manajemen multi-klien</td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-par">Manual</span></td>
            </tr>
            <tr>
              <td>Pembayaran Rupiah</td>
              <td><span class="check-y">✓</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-n">✗</span></td>
              <td><span class="check-n">✗</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="harga" class="section">
    <div class="pg">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">Harga</div>
        <h2 class="sec-title">Transparan. Tanpa biaya tersembunyi.</h2>
        <p class="sec-sub">Mulai gratis 14 hari, upgrade kapan saja. Hemat 20% dengan paket tahunan.</p>
      </div>
      <div class="pricing-toggle" data-reveal>
        <span :class="{ active: !isYearly }">Bulanan</span>
        <button class="toggle-btn" @click="isYearly = !isYearly" :class="{ on: isYearly }" aria-label="Toggle yearly pricing">
          <span class="toggle-knob"></span>
        </button>
        <span :class="{ active: isYearly }">Tahunan <span class="save-badge">Hemat 20%</span></span>
      </div>
      <div class="pricing-grid" data-reveal>
        <!-- Starter -->
        <div class="price-card">
          <div class="price-tier">Starter</div>
          <div class="price-val">{{ isYearly ? prices.yearly[0] : prices.monthly[0] }}</div>
          <div class="price-period">/ bulan{{ isYearly ? ', ditagih tahunan' : '' }}</div>
          <ul class="price-features">
            <li>✓ 5 akun sosial media</li>
            <li>✓ 1 pengguna</li>
            <li>✓ Kalender konten</li>
            <li>✓ Analytics dasar</li>
            <li>✓ 50 post terjadwal/bulan</li>
            <li style="color:var(--ink-3)">✗ Manajemen klien</li>
            <li style="color:var(--ink-3)">✗ Laporan white-label</li>
            <li style="color:var(--ink-3)">✗ AI Generator</li>
          </ul>
          <Link href="/waitlist" class="price-btn">Coba Gratis</Link>
        </div>
        <!-- Pro (Popular) -->
        <div class="price-card pop">
          <div class="price-pop-badge">PALING POPULER</div>
          <div class="price-tier">Pro</div>
          <div class="price-val">{{ isYearly ? prices.yearly[1] : prices.monthly[1] }}</div>
          <div class="price-period">/ bulan{{ isYearly ? ', ditagih tahunan' : '' }}</div>
          <ul class="price-features">
            <li>✓ 20 akun sosial media</li>
            <li>✓ 3 pengguna</li>
            <li>✓ Kalender konten</li>
            <li>✓ Analytics lengkap</li>
            <li>✓ Post terjadwal tak terbatas</li>
            <li>✓ 10 klien</li>
            <li>✓ Laporan white-label</li>
            <li>✓ AI Caption Generator</li>
          </ul>
          <Link href="/waitlist" class="price-btn fill">Coba Gratis</Link>
        </div>
        <!-- Agency -->
        <div class="price-card">
          <div class="price-tier">Agency</div>
          <div class="price-val">{{ isYearly ? prices.yearly[2] : prices.monthly[2] }}</div>
          <div class="price-period">/ bulan{{ isYearly ? ', ditagih tahunan' : '' }}</div>
          <ul class="price-features">
            <li>✓ Akun tak terbatas</li>
            <li>✓ 10 pengguna</li>
            <li>✓ Semua fitur Pro</li>
            <li>✓ Klien tak terbatas</li>
            <li>✓ Laporan custom branding</li>
            <li>✓ Prioritas support</li>
            <li>✓ Onboarding dedicated</li>
            <li>✓ API access</li>
          </ul>
          <Link href="/waitlist" class="price-btn">Coba Gratis</Link>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section id="testimoni" class="section" style="background:#fff;padding:64px 0">
    <div class="pg">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">Testimoni</div>
        <h2 class="sec-title">Mereka sudah merasakannya.</h2>
        <p class="sec-sub">Lebih dari 340 agensi dan kreator aktif menggunakan Publishin setiap hari.</p>
      </div>
      <div class="testi-grid" data-reveal>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Dulu kami pakai 4 tool berbeda dan spreadsheet. Sekarang semua ada di Publishin. Tim kami hemat minimal 3 jam kerja per hari, dan klien kami lebih puas dengan laporan yang kami buat.</p>
          <div class="testi-by">
            <div class="testi-avatar" style="background:var(--accent-r)">RH</div>
            <div>
              <strong>Reza Hakim</strong><br />
              <span style="color:var(--ink-3);font-size:12px">Co-Founder, Kreasi Digital Agency</span>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Sebagai solo creator, Publishin adalah game changer. AI caption-nya bagus banget untuk bahasa Indonesia, dan kalender kontennya bikin aku lebih konsisten posting. Follower naik 40% dalam 2 bulan!</p>
          <div class="testi-by">
            <div class="testi-avatar" style="background:var(--accent-b)">SA</div>
            <div>
              <strong>Sari Andini</strong><br />
              <span style="color:var(--ink-3);font-size:12px">Content Creator, 180K followers</span>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Publishin membantu kami mengelola 25 klien sekaligus tanpa chaos. Laporan white-label-nya sangat profesional, klien kami terkesan. Harga jauh lebih masuk akal dibanding Hootsuite untuk market Indonesia.</p>
          <div class="testi-by">
            <div class="testi-avatar" style="background:var(--green)">BW</div>
            <div>
              <strong>Budi Wicaksono</strong><br />
              <span style="color:var(--ink-3);font-size:12px">CEO, MediaKu Social Agency</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Accordion -->
  <section id="faq" class="section">
    <div class="pg">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">FAQ</div>
        <h2 class="sec-title">Pertanyaan yang sering ditanya.</h2>
      </div>
      <div class="faq-list" data-reveal>
        <div
          v-for="(item, i) in faqItems"
          :key="i"
          class="faq-item"
          :class="{ open: openFaq === i }"
        >
          <div class="faq-q" @click="toggleFaq(i)">
            <span>{{ item.q }}</span>
            <span class="faq-toggle" :class="{ open: openFaq === i }">+</span>
          </div>
          <div class="faq-a" v-show="openFaq === i">
            {{ item.a }}
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="pg cta-inner">
      <div data-reveal>
        <h2 class="cta-h">Siap menghemat waktu &amp;<br /><em>menumbuhkan brand-mu?</em></h2>
        <p class="cta-sub">Bergabung dengan 340+ agensi dan kreator aktif. Coba gratis 14 hari, tanpa kartu kredit.</p>
        <div v-if="!ctaSubmitted" class="cta-form">
          <input
            v-model="ctaEmail"
            class="cta-input"
            type="email"
            placeholder="email@kamu.com"
            @keyup.enter="submitCTA"
          />
          <button class="btn-pri" @click="submitCTA">Daftar Sekarang →</button>
        </div>
        <div v-else class="cta-success">
          <span class="cta-success-icon">✓</span>
          Terima kasih! Kami akan segera menghubungimu.
        </div>
        <p class="cta-trust">🔒 Data kamu aman · Tidak ada spam · Batalkan kapan saja</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="pg footer-main">
      <div class="footer-grid">
        <!-- Brand Column -->
        <div class="footer-brand">
          <div class="footer-logo">Publishin</div>
          <p class="footer-tagline">Platform SMM #1 untuk creator dan agensi Indonesia. Jadwalkan, analisis, dan kelola semua sosial media dari satu dashboard.</p>
          <div class="footer-socials">
            <a href="#" class="footer-social-link" aria-label="Instagram">IG</a>
            <a href="#" class="footer-social-link" aria-label="Twitter">TW</a>
            <a href="#" class="footer-social-link" aria-label="LinkedIn">LI</a>
            <a href="#" class="footer-social-link" aria-label="TikTok">TT</a>
          </div>
        </div>
        <!-- Produk -->
        <div class="footer-col">
          <div class="footer-col-title">Produk</div>
          <ul class="footer-links">
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#harga">Harga</a></li>
            <li><a href="#testimoni">Testimoni</a></li>
            <li><Link href="/waitlist">Daftar Waitlist</Link></li>
            <li><a href="#">Changelog</a></li>
          </ul>
        </div>
        <!-- Perusahaan -->
        <div class="footer-col">
          <div class="footer-col-title">Perusahaan</div>
          <ul class="footer-links">
            <li><a href="#">Tentang Kami</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Karir</a></li>
            <li><a href="#">Press Kit</a></li>
            <li><a href="#">Kontak</a></li>
          </ul>
        </div>
        <!-- Bantuan -->
        <div class="footer-col">
          <div class="footer-col-title">Bantuan</div>
          <ul class="footer-links">
            <li><a href="#">Dokumentasi</a></li>
            <li><a href="#">Panduan Mulai</a></li>
            <li><a href="#">Status System</a></li>
            <li><a href="#">Kebijakan Privasi</a></li>
            <li><a href="#">Syarat &amp; Ketentuan</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Publishin.id · PT Publishin Teknologi Indonesia</span>
        <span>Dibuat dengan ♥ di Jakarta, Indonesia</span>
      </div>
    </div>
  </footer>
</template>

<style>
/* ===========================
   CSS VARIABLES & RESET
   =========================== */
:root {
  --paper: #FAFAF8;
  --ink: #1C1B1A;
  --ink-2: #4A4944;
  --ink-3: #928E89;
  --accent-r: #C96442;
  --accent-b: #3B6DB5;
  --green: #2A7A4B;
  --f-sans: 'JetBrains Mono', monospace;
  --f-disp: Georgia, 'Times New Roman', serif;
  --bdr: 1px solid #E8E6E1;
  --bdr-m: 1px solid #F0EDE8;
  --radius: 2px;
}

*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: var(--f-sans);
  font-size: 14px;
  color: var(--ink);
  background-color: var(--paper);
  background-image:
    linear-gradient(rgba(28,27,26,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(28,27,26,0.04) 1px, transparent 1px);
  background-size: 20px 20px;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

a {
  color: inherit;
  text-decoration: none;
}

ul {
  list-style: none;
}

/* ===========================
   LAYOUT
   =========================== */
.pg {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 28px;
}

/* ===========================
   NAVIGATION
   =========================== */
nav {
  position: sticky;
  top: 0;
  background: rgba(250, 250, 248, 0.96);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  z-index: 100;
  border-bottom: var(--bdr);
}

.nav-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 56px;
}

.nav-logo {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 20px;
  text-decoration: underline;
  text-underline-offset: 3px;
  color: var(--ink);
  flex-shrink: 0;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  font-size: 13px;
  color: var(--ink-2);
  padding: 6px 10px;
  border-radius: var(--radius);
  transition: color 0.2s;
}

.nav-link:hover {
  color: var(--ink);
}

.nav-cta-ghost {
  font-size: 13px;
  padding: 6px 14px;
  border: var(--bdr);
  border-radius: var(--radius);
  color: var(--ink-2);
  transition: all 0.2s;
}

.nav-cta-ghost:hover {
  color: var(--ink);
  border-color: var(--ink-3);
}

.nav-cta {
  font-size: 13px;
  padding: 6px 16px;
  background: var(--ink);
  color: var(--paper);
  border-radius: var(--radius);
  transition: opacity 0.2s;
}

.nav-cta:hover {
  opacity: 0.85;
}

/* Hamburger */
.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.hamburger span {
  display: block;
  width: 22px;
  height: 1.5px;
  background: var(--ink);
  transition: all 0.25s;
  transform-origin: center;
}

.hamburger.open span:nth-child(1) {
  transform: translateY(6.5px) rotate(45deg);
}

.hamburger.open span:nth-child(2) {
  opacity: 0;
}

.hamburger.open span:nth-child(3) {
  transform: translateY(-6.5px) rotate(-45deg);
}

/* ===========================
   BUTTONS
   =========================== */
.btn-pri {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 24px;
  background: var(--ink);
  color: var(--paper);
  font-family: var(--f-sans);
  font-size: 13px;
  border: none;
  border-radius: var(--radius);
  cursor: pointer;
  transition: opacity 0.2s;
  text-decoration: none;
}

.btn-pri:hover {
  opacity: 0.85;
}

.btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 24px;
  background: transparent;
  color: var(--ink-2);
  font-family: var(--f-sans);
  font-size: 13px;
  border: var(--bdr);
  border-radius: var(--radius);
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.btn-ghost:hover {
  color: var(--ink);
  border-color: var(--ink-3);
}

/* ===========================
   HERO
   =========================== */
.hero {
  padding: 80px 0 60px;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: var(--ink-2);
  background: #fff;
  border: var(--bdr);
  border-radius: 20px;
  padding: 5px 14px;
  margin-bottom: 28px;
}

.eyebrow-dot {
  width: 7px;
  height: 7px;
  background: var(--green);
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}

.hero-h1 {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: clamp(48px, 6vw, 88px);
  line-height: 1.02;
  letter-spacing: -3px;
  color: var(--ink);
  margin-bottom: 24px;
  max-width: 820px;
}

.hero-sub {
  font-size: 16px;
  color: var(--ink-2);
  max-width: 560px;
  line-height: 1.7;
  margin-bottom: 32px;
}

.hero-btns {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.hero-note {
  font-size: 12px;
  color: var(--ink-3);
  margin-bottom: 56px;
}

/* ===========================
   MOCKUP
   =========================== */
.mockup-wrap {
  border: var(--bdr);
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 32px rgba(28,27,26,0.08), 0 1px 4px rgba(28,27,26,0.04);
  max-width: 900px;
}

.mockup-bar {
  background: #F0EDE8;
  border-bottom: var(--bdr);
  padding: 10px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.m-dots {
  display: flex;
  gap: 5px;
  flex-shrink: 0;
}

.m-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: block;
}

.m-url {
  flex: 1;
  background: #fff;
  border: var(--bdr);
  border-radius: 3px;
  padding: 3px 10px;
  font-size: 11px;
  color: var(--ink-3);
  text-align: center;
  max-width: 260px;
  margin: 0 auto;
}

.mockup-body {
  display: flex;
  min-height: 340px;
}

.m-sidebar {
  width: 160px;
  border-right: var(--bdr);
  padding: 16px 0;
  flex-shrink: 0;
  background: #FAFAF8;
}

.m-logo-area {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 14px 16px;
  border-bottom: var(--bdr-m);
  margin-bottom: 8px;
}

.m-logo-mark {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 16px;
  color: var(--accent-r);
}

.m-logo-sub {
  font-size: 11px;
  font-weight: 600;
  color: var(--ink);
}

.m-nav-item {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 7px 14px;
  font-size: 11px;
  color: var(--ink-3);
  cursor: pointer;
  position: relative;
}

.m-nav-item.act {
  color: var(--ink);
  background: rgba(201, 100, 66, 0.08);
  border-right: 2px solid var(--accent-r);
}

.m-chk {
  font-size: 13px;
}

.m-nbadge {
  margin-left: auto;
  background: var(--accent-r);
  color: #fff;
  font-size: 9px;
  padding: 1px 5px;
  border-radius: 8px;
}

.m-main {
  flex: 1;
  padding: 16px;
  overflow: hidden;
}

.m-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.m-topbar-title {
  font-size: 13px;
  font-weight: 600;
}

.m-btn {
  font-size: 10px;
  padding: 5px 10px;
  border: var(--bdr);
  border-radius: var(--radius);
  color: var(--ink-2);
  cursor: pointer;
}

.m-btn.pri {
  background: var(--ink);
  color: var(--paper);
  border-color: var(--ink);
}

.m-kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  margin-bottom: 16px;
}

.m-kpi {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 10px;
}

.m-kpi-n {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 18px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1;
  margin-bottom: 2px;
}

.m-kpi-n.r { color: var(--accent-r); }
.m-kpi-n.k { color: var(--accent-b); }

.m-kpi-l {
  font-size: 9px;
  color: var(--ink-3);
  margin-bottom: 3px;
}

.m-kpi-d {
  font-size: 9px;
  font-weight: 600;
}

.m-kpi-d.up { color: var(--green); }
.m-kpi-d.dn { color: var(--accent-r); }

.m-chart-wrap {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 10px;
  margin-bottom: 12px;
}

.m-chart-lbl {
  font-size: 9px;
  color: var(--ink-3);
  margin-bottom: 6px;
}

.m-tbl {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
}

.m-tbl th {
  text-align: left;
  font-size: 9px;
  color: var(--ink-3);
  font-weight: 500;
  padding: 4px 6px;
  border-bottom: var(--bdr);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.m-tbl td {
  padding: 5px 6px;
  border-bottom: var(--bdr-m);
  color: var(--ink-2);
}

.m-plt {
  display: inline-block;
  font-size: 8px;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 2px;
  color: #fff;
}

.m-plt.ig { background: #E1306C; }
.m-plt.tt { background: #010101; }
.m-plt.fb { background: #1877F2; }

.m-mstat {
  display: inline-block;
  font-size: 9px;
  padding: 1px 6px;
  border-radius: 2px;
}

.m-mstat.pub {
  background: rgba(42, 122, 75, 0.12);
  color: var(--green);
}

.m-mstat.sch {
  background: rgba(59, 109, 181, 0.12);
  color: var(--accent-b);
}

/* ===========================
   SOCIAL PROOF STRIP
   =========================== */
.social-proof-strip {
  background: #fff;
  border-top: var(--bdr);
  border-bottom: var(--bdr);
  padding: 16px 0;
}

.sps-inner {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  padding-top: 0;
  padding-bottom: 0;
}

.sps-label {
  font-size: 11px;
  color: var(--ink-3);
  flex-shrink: 0;
}

.sps-agency {
  font-size: 12px;
  color: var(--ink-2);
  font-weight: 500;
  padding: 3px 10px;
  border: var(--bdr);
  border-radius: 2px;
  background: var(--paper);
}

.sps-more {
  font-size: 11px;
  color: var(--ink-3);
  margin-left: auto;
}

/* ===========================
   STATS ROW
   =========================== */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0;
  border: var(--bdr);
  border-radius: 4px;
  overflow: hidden;
  background: #fff;
  margin: 48px 0;
}

.stat-item {
  padding: 32px 24px;
  border-right: var(--bdr);
  text-align: center;
}

.stat-item:last-child {
  border-right: none;
}

.stat-num {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 40px;
  font-weight: 700;
  color: var(--accent-r);
  line-height: 1;
  margin-bottom: 6px;
}

.stat-lbl {
  font-size: 12px;
  color: var(--ink-3);
}

/* ===========================
   SECTIONS
   =========================== */
.section {
  padding: 80px 0;
}

.sec-header {
  text-align: center;
  margin-bottom: 56px;
}

.sec-eyebrow {
  display: inline-block;
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--ink-3);
  margin-bottom: 16px;
  position: relative;
  padding: 0 24px;
}

.sec-eyebrow::before,
.sec-eyebrow::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 40px;
  height: 1px;
  background: var(--ink-3);
  opacity: 0.4;
}

.sec-eyebrow::before { right: 100%; margin-right: -20px; }
.sec-eyebrow::after { left: 100%; margin-left: -20px; }

.sec-title {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: clamp(28px, 3.5vw, 48px);
  font-weight: 700;
  line-height: 1.15;
  letter-spacing: -1.5px;
  color: var(--ink);
  margin-bottom: 14px;
}

.sec-sub {
  font-size: 15px;
  color: var(--ink-2);
  max-width: 500px;
  margin: 0 auto;
  line-height: 1.7;
}

/* ===========================
   FEATURE ROWS
   =========================== */
.feat-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  align-items: center;
  margin-bottom: 48px;
  padding-bottom: 48px;
  border-bottom: var(--bdr-m);
}

.feat-row.rev {
  direction: rtl;
}

.feat-row.rev > * {
  direction: ltr;
}

.feat-visual {
  background: #fff;
  border: var(--bdr);
  border-radius: 4px;
  padding: 24px;
  overflow: hidden;
}

.feat-tag {
  display: inline-block;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--accent-r);
  border: 1px solid var(--accent-r);
  border-radius: var(--radius);
  padding: 2px 8px;
  margin-bottom: 14px;
}

.feat-h {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: clamp(20px, 2vw, 28px);
  font-weight: 700;
  line-height: 1.3;
  letter-spacing: -0.5px;
  color: var(--ink);
  margin-bottom: 14px;
}

.feat-p {
  font-size: 14px;
  color: var(--ink-2);
  line-height: 1.7;
  margin-bottom: 20px;
}

.feat-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.feat-list li {
  font-size: 13px;
  color: var(--ink-2);
  padding-left: 4px;
}

.feat-list li::before {
  content: '✓ ';
  color: var(--green);
  font-weight: 700;
}

/* ===========================
   CALENDAR MINI
   =========================== */
.cal-mini {
  font-size: 11px;
}

.cal-mini-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  font-size: 12px;
  color: var(--ink-2);
}

.cal-mini-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
}

.cal-mini-lbl {
  font-size: 9px;
  text-transform: uppercase;
  color: var(--ink-3);
  text-align: center;
  padding: 2px 0 4px;
  letter-spacing: 0.5px;
}

.cal-mini-cell {
  min-height: 32px;
  padding: 3px;
  font-size: 10px;
  color: var(--ink-2);
  border: 1px solid transparent;
  border-radius: var(--radius);
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.cal-mini-cell.today {
  background: var(--ink);
  color: #fff;
  font-weight: 700;
  border-color: var(--ink);
}

.cdot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}

.cdot.ig { background: #E1306C; }
.cdot.tt { background: #010101; }
.cdot.fb { background: #1877F2; }

/* ===========================
   ANALYTICS PREVIEW
   =========================== */
.analytics-preview {
  font-size: 11px;
}

.ap-kpis {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 16px;
}

.ap-kpi {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 12px;
}

.ap-kpi-n {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 20px;
  font-weight: 700;
  color: var(--ink);
  line-height: 1;
  margin-bottom: 2px;
}

.ap-kpi-d {
  font-size: 10px;
  font-weight: 600;
  margin-bottom: 2px;
}

.ap-kpi-d.up { color: var(--green); }

.ap-kpi-l {
  font-size: 9px;
  color: var(--ink-3);
}

.ap-chart {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 12px;
}

.ap-chart-title {
  font-size: 10px;
  color: var(--ink-3);
  margin-bottom: 8px;
}

/* ===========================
   COMPOSE PREVIEW
   =========================== */
.compose-preview {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  font-size: 11px;
}

.cp-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cp-lbl {
  font-size: 10px;
  color: var(--ink-3);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.cp-textarea {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 10px;
  font-size: 11px;
  color: var(--ink-2);
  line-height: 1.6;
  min-height: 80px;
  font-family: var(--f-sans);
}

.cp-ai-btn {
  background: linear-gradient(135deg, #C96442, #3B6DB5);
  color: #fff;
  border: none;
  border-radius: var(--radius);
  padding: 7px 12px;
  font-size: 10px;
  cursor: pointer;
  font-family: var(--f-sans);
}

.cp-preview {
  background: var(--paper);
  border: var(--bdr);
  border-radius: 4px;
  overflow: hidden;
  position: relative;
}

.cp-plt-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: #E1306C;
  color: #fff;
  font-size: 8px;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 2px;
}

.cp-preview-img {
  height: 80px;
  background: linear-gradient(135deg, #F0EDE8, #E8E3DC);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cp-preview-text {
  padding: 8px;
  font-size: 10px;
  color: var(--ink-2);
  line-height: 1.5;
}

/* ===========================
   REPORT PREVIEW
   =========================== */
.report-preview {
  font-size: 11px;
}

.rp-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: var(--bdr);
}

.rp-brand {
  display: flex;
  align-items: center;
  gap: 10px;
}

.rp-logo-placeholder {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, var(--accent-r), var(--accent-b));
  border-radius: 4px;
}

.rp-client-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--ink);
}

.rp-period {
  font-size: 10px;
  color: var(--ink-3);
}

.rp-badge {
  background: var(--accent-r);
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 2px;
}

.rp-metrics {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin-bottom: 16px;
}

.rp-metric {
  background: var(--paper);
  border: var(--bdr);
  border-radius: var(--radius);
  padding: 10px;
  text-align: center;
}

.rp-metric-n {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 16px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 2px;
}

.rp-metric-l {
  font-size: 9px;
  color: var(--ink-3);
}

.rp-bar-wrap {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.rp-bar-label {
  font-size: 10px;
  color: var(--ink-3);
  margin-bottom: 4px;
}

.rp-bar-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.rp-bar-title {
  font-size: 10px;
  color: var(--ink-2);
  width: 100px;
  flex-shrink: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.rp-bar-track {
  flex: 1;
  height: 6px;
  background: var(--paper);
  border: var(--bdr);
  border-radius: 3px;
  overflow: hidden;
}

.rp-bar-fill {
  height: 100%;
  background: var(--accent-r);
  border-radius: 3px;
}

.rp-bar-val {
  font-size: 10px;
  color: var(--ink-3);
  width: 40px;
  text-align: right;
  flex-shrink: 0;
}

/* ===========================
   COMPARISON TABLE
   =========================== */
.compare-tbl {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  margin-top: 8px;
}

.compare-tbl th {
  text-align: left;
  padding: 12px 16px;
  font-size: 11px;
  font-weight: 600;
  border-bottom: 2px solid var(--ink);
  background: var(--paper);
}

.compare-tbl td {
  padding: 11px 16px;
  border-bottom: var(--bdr);
  color: var(--ink-2);
}

.compare-tbl tr:hover td {
  background: #F8F6F2;
}

.check-y {
  color: var(--green);
  font-weight: 700;
  font-size: 15px;
}

.check-n {
  color: var(--ink-3);
  font-size: 15px;
}

.check-par {
  color: var(--accent-b);
  font-size: 12px;
}

/* ===========================
   PRICING
   =========================== */
.pricing-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 32px;
  font-size: 13px;
  color: var(--ink-3);
}

.pricing-toggle span {
  transition: color 0.2s;
}

.pricing-toggle span.active {
  color: var(--ink);
  font-weight: 600;
}

.toggle-btn {
  width: 44px;
  height: 24px;
  background: var(--ink-3);
  border: none;
  border-radius: 12px;
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
  flex-shrink: 0;
}

.toggle-btn.on {
  background: var(--ink);
}

.toggle-knob {
  position: absolute;
  width: 18px;
  height: 18px;
  background: #fff;
  border-radius: 50%;
  top: 3px;
  left: 3px;
  transition: transform 0.2s;
}

.toggle-btn.on .toggle-knob {
  transform: translateX(20px);
}

.save-badge {
  font-size: 10px;
  background: var(--green);
  color: #fff;
  padding: 1px 6px;
  border-radius: 10px;
  margin-left: 4px;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.price-card {
  background: #fff;
  border: var(--bdr);
  border-radius: 4px;
  padding: 28px 24px;
  position: relative;
  transition: box-shadow 0.2s;
}

.price-card:hover {
  box-shadow: 0 4px 20px rgba(28,27,26,0.08);
}

.price-card.pop {
  border: 2px solid var(--accent-r);
  box-shadow: 0 4px 24px rgba(201, 100, 66, 0.12);
}

.price-pop-badge {
  position: absolute;
  top: -1px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--accent-r);
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1px;
  padding: 3px 12px;
  border-radius: 0 0 4px 4px;
  white-space: nowrap;
}

.price-tier {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--ink-3);
  margin-bottom: 12px;
  margin-top: 12px;
}

.price-card.pop .price-tier {
  margin-top: 20px;
}

.price-val {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 38px;
  color: var(--ink);
  line-height: 1;
  margin-bottom: 4px;
}

.price-period {
  font-size: 11px;
  color: var(--ink-3);
  margin-bottom: 24px;
}

.price-features {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 24px;
}

.price-features li {
  font-size: 13px;
  color: var(--ink-2);
}

.price-btn {
  display: block;
  text-align: center;
  padding: 10px 20px;
  border: var(--bdr);
  border-radius: var(--radius);
  font-size: 13px;
  color: var(--ink);
  cursor: pointer;
  transition: all 0.2s;
  font-family: var(--f-sans);
  text-decoration: none;
}

.price-btn:hover {
  border-color: var(--ink-3);
  background: var(--paper);
}

.price-btn.fill {
  background: var(--ink);
  color: var(--paper);
  border-color: var(--ink);
}

.price-btn.fill:hover {
  opacity: 0.85;
  background: var(--ink);
}

/* ===========================
   TESTIMONIALS
   =========================== */
.testi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.testi-card {
  background: var(--paper);
  border: var(--bdr);
  border-radius: 4px;
  padding: 24px;
}

.testi-stars {
  color: #F4B942;
  font-size: 14px;
  letter-spacing: 2px;
  margin-bottom: 12px;
}

.testi-q {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 14px;
  color: var(--ink-2);
  line-height: 1.7;
  margin-bottom: 20px;
  position: relative;
}

.testi-q::before {
  content: '\201C';
  font-size: 32px;
  color: var(--accent-r);
  line-height: 0;
  vertical-align: -0.4em;
  margin-right: 3px;
}

.testi-by {
  display: flex;
  align-items: center;
  gap: 12px;
}

.testi-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
}

/* ===========================
   FAQ
   =========================== */
.faq-list {
  max-width: 720px;
  margin: 0 auto;
}

.faq-item {
  border-bottom: var(--bdr);
}

.faq-q {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 0;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: var(--ink);
  gap: 16px;
  user-select: none;
}

.faq-toggle {
  color: var(--accent-r);
  font-size: 20px;
  font-weight: 300;
  flex-shrink: 0;
  transition: transform 0.25s;
  line-height: 1;
}

.faq-toggle.open {
  transform: rotate(45deg);
}

.faq-a {
  padding: 0 0 18px;
  font-size: 14px;
  color: var(--ink-2);
  line-height: 1.75;
}

/* ===========================
   CTA SECTION
   =========================== */
.cta-section {
  background: #fff;
  position: relative;
  padding: 80px 0;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  inset: 0;
  background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='6' height='6'%3E%3Cline x1='0' y1='6' x2='6' y2='0' stroke='%23C96442' stroke-width='0.8' stroke-opacity='0.12'/%3E%3C/svg%3E");
  pointer-events: none;
}

.cta-inner {
  position: relative;
  text-align: center;
  padding-top: 0;
  padding-bottom: 0;
}

.cta-h {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: clamp(30px, 4vw, 56px);
  line-height: 1.15;
  letter-spacing: -2px;
  color: var(--ink);
  margin-bottom: 16px;
}

.cta-h em {
  color: var(--accent-r);
  font-style: italic;
}

.cta-sub {
  font-size: 15px;
  color: var(--ink-2);
  margin-bottom: 32px;
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.7;
}

.cta-form {
  display: flex;
  gap: 10px;
  max-width: 440px;
  margin: 0 auto 16px;
  flex-wrap: wrap;
  justify-content: center;
}

.cta-input {
  flex: 1;
  min-width: 220px;
  padding: 12px 16px;
  border: var(--bdr);
  border-radius: var(--radius);
  font-family: var(--f-sans);
  font-size: 13px;
  background: #fff;
  color: var(--ink);
  outline: none;
  transition: border-color 0.2s;
}

.cta-input:focus {
  border-color: var(--ink-3);
}

.cta-input::placeholder {
  color: var(--ink-3);
}

.cta-success {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 15px;
  color: var(--green);
  font-weight: 600;
  margin-bottom: 16px;
}

.cta-success-icon {
  width: 28px;
  height: 28px;
  background: var(--green);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.cta-trust {
  font-size: 10px;
  color: var(--ink-3);
}

/* ===========================
   FOOTER
   =========================== */
.footer {
  background: var(--ink);
  color: var(--paper);
  padding: 60px 0 0;
}

.footer-main {
  padding-bottom: 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 40px;
  padding-bottom: 48px;
}

.footer-logo {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 22px;
  text-decoration: underline;
  text-underline-offset: 3px;
  color: #fff;
  margin-bottom: 14px;
}

.footer-tagline {
  font-size: 12px;
  color: rgba(250,250,248,0.55);
  line-height: 1.7;
  margin-bottom: 20px;
  max-width: 260px;
}

.footer-socials {
  display: flex;
  gap: 8px;
}

.footer-social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: rgba(250,250,248,0.08);
  border: 1px solid rgba(250,250,248,0.12);
  border-radius: var(--radius);
  font-size: 9px;
  font-weight: 700;
  color: rgba(250,250,248,0.6);
  transition: all 0.2s;
  text-decoration: none;
}

.footer-social-link:hover {
  background: rgba(250,250,248,0.15);
  color: #fff;
}

.footer-col-title {
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(250,250,248,0.4);
  margin-bottom: 16px;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-links a {
  font-size: 13px;
  color: rgba(250,250,248,0.6);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #fff;
}

.footer-bottom {
  border-top: 1px solid rgba(250,250,248,0.08);
  padding: 20px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 11px;
  color: rgba(250,250,248,0.35);
  flex-wrap: wrap;
  gap: 8px;
}

/* ===========================
   SCROLL REVEAL
   =========================== */
[data-reveal] {
  opacity: 0;
  transform: translateY(28px);
  transition:
    opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

[data-reveal="left"] {
  transform: translateX(-28px);
}

[data-reveal="right"] {
  transform: translateX(28px);
}

[data-reveal="scale"] {
  transform: scale(0.95);
}

.revealed {
  opacity: 1 !important;
  transform: none !important;
}

[data-delay="1"] { transition-delay: 0.08s; }
[data-delay="2"] { transition-delay: 0.16s; }
[data-delay="3"] { transition-delay: 0.24s; }
[data-delay="4"] { transition-delay: 0.32s; }
[data-delay="5"] { transition-delay: 0.40s; }

/* ===========================
   RESPONSIVE
   =========================== */
@media (max-width: 768px) {
  .hamburger {
    display: flex;
  }

  .nav-links {
    display: none;
    position: fixed;
    top: 56px;
    left: 0;
    right: 0;
    background: rgba(250,250,248,0.98);
    backdrop-filter: blur(6px);
    flex-direction: column;
    padding: 20px 28px;
    border-bottom: var(--bdr);
    gap: 4px;
    z-index: 99;
  }

  .nav-links.open {
    display: flex;
  }

  .nav-link {
    padding: 10px 4px;
    border-bottom: var(--bdr-m);
    width: 100%;
  }

  .nav-cta-ghost,
  .nav-cta {
    margin-top: 8px;
    text-align: center;
    padding: 10px;
  }

  .hero {
    padding: 48px 0 40px;
  }

  .hero-h1 {
    font-size: clamp(36px, 8vw, 60px);
    letter-spacing: -2px;
  }

  .mockup-body {
    flex-direction: column;
    min-height: auto;
  }

  .m-sidebar {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    padding: 10px;
  }

  .m-logo-area {
    width: 100%;
    border-bottom: var(--bdr-m);
    margin-bottom: 4px;
    padding-bottom: 8px;
  }

  .m-nav-item {
    padding: 5px 10px;
    border: var(--bdr);
    border-radius: var(--radius);
    font-size: 10px;
  }

  .m-kpi-row {
    grid-template-columns: repeat(2, 1fr);
  }

  .stats-row {
    grid-template-columns: repeat(2, 1fr);
  }

  .stat-item:nth-child(2) {
    border-right: none;
  }

  .stat-item:nth-child(3),
  .stat-item:nth-child(4) {
    border-top: var(--bdr);
  }

  .feat-row,
  .feat-row.rev {
    grid-template-columns: 1fr;
    direction: ltr;
  }

  .feat-row.rev > * {
    direction: ltr;
  }

  .pricing-grid {
    grid-template-columns: 1fr;
    max-width: 400px;
    margin: 0 auto;
  }

  .testi-grid {
    grid-template-columns: 1fr;
  }

  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 28px;
  }

  .footer-brand {
    grid-column: 1 / -1;
  }

  .compare-tbl {
    font-size: 11px;
  }

  .compare-tbl th,
  .compare-tbl td {
    padding: 8px 10px;
  }

  .compose-preview {
    grid-template-columns: 1fr;
  }

  .ap-kpis {
    grid-template-columns: 1fr;
  }

  .cta-form {
    flex-direction: column;
    align-items: stretch;
  }
}

@media (max-width: 480px) {
  .sps-inner {
    gap: 10px;
  }

  .sps-more {
    margin-left: 0;
    width: 100%;
  }

  .footer-grid {
    grid-template-columns: 1fr;
  }

  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }

  .hero-btns {
    flex-direction: column;
  }

  .hero-btns .btn-pri,
  .hero-btns .btn-ghost {
    text-align: center;
    justify-content: center;
  }
}
</style>
