<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const navOpen = ref(false)
const isYearly = ref(false)
const openFaq = ref<number | null>(null)
const ctaEmail = ref('')
const ctaSubmitted = ref(false)

const prices = {
  monthly: ['Rp 99K', 'Rp 249K', 'Rp 699K'],
  yearly: ['Rp 79K', 'Rp 199K', 'Rp 559K'],
}

const faqItems = [
  {
    q: 'Apakah ada uji coba gratis?',
    a: 'Ya! Semua plan dilengkapi dengan 14 hari uji coba gratis tanpa kartu kredit. Kamu bisa eksplorasi semua fitur sebelum memutuskan untuk berlangganan.',
  },
  {
    q: 'Platform apa saja yang didukung?',
    a: 'Publishin mendukung Instagram, TikTok, Facebook, X (Twitter), dan YouTube. Kami terus menambahkan platform baru — LinkedIn dan Threads sedang dalam pengembangan.',
  },
  {
    q: 'Apakah bisa pakai untuk mengelola akun klien?',
    a: 'Tentu. Plan Pro dan Agency dirancang untuk freelancer SMM dan agensi digital. Kamu bisa mengelola banyak akun klien dalam satu workspace, set approval workflow, dan buat laporan white-label dengan logo brandmu sendiri.',
  },
  {
    q: 'Apakah data akun sosmed saya aman?',
    a: 'Ya. Kami menggunakan OAuth resmi dari setiap platform — Publishin tidak pernah menyimpan password akun sosmedmu. Semua data dienkripsi end-to-end dan disimpan di server yang berlokasi di Indonesia.',
  },
  {
    q: 'Bagaimana cara pembayaran?',
    a: 'Pembayaran dalam Rupiah melalui transfer bank, GoPay, OVO, DANA, dan kartu kredit/debit Visa/Mastercard. Tidak ada biaya tambahan atau konversi mata uang asing.',
  },
  {
    q: 'Apakah bisa cancel kapan saja?',
    a: 'Ya, kamu bisa cancel langganan kapan saja. Tidak ada kontrak jangka panjang, tidak ada biaya penalti. Akses tetap aktif hingga akhir periode tagihan yang sudah dibayar.',
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
      <pattern id="hatch-r" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="#C96442" stroke-width="1.5" opacity=".35" />
      </pattern>
      <pattern id="hatch-b" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="#3B6DB5" stroke-width="1.5" opacity=".35" />
      </pattern>
      <pattern id="hatch-g" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="#2A7A4B" stroke-width="1.5" opacity=".35" />
      </pattern>
    </defs>
  </svg>

  <div class="pg">
    <!-- Nav -->
    <nav>
      <span class="nav-logo">Publishin</span>
      <button class="nav-hamburger" :class="{ open: navOpen }" @click="navOpen = !navOpen" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-links" :class="{ open: navOpen }">
        <span class="nav-link" @click="navOpen = false"><a href="#fitur">Fitur</a></span>
        <span class="nav-link" @click="navOpen = false"><a href="#harga">Harga</a></span>
        <span class="nav-link" @click="navOpen = false"><a href="#testimoni">Testimoni</a></span>
        <span class="nav-link" @click="navOpen = false"><a href="#faq">FAQ</a></span>
        <Link href="/login" class="nav-cta-ghost">Masuk</Link>
        <Link href="/waitlist" class="nav-cta">Coba Gratis</Link>
      </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="hero" data-reveal>
      <div class="hero-eyebrow">
        <svg width="6" height="6"><circle cx="3" cy="3" r="3" fill="#2A7A4B" /></svg>
        Platform SMM #1 untuk Creator &amp; Agensi Indonesia
      </div>
      <h1 class="hero-h1">Satu dashboard untuk semua<br /><em>sosial mediamu.</em></h1>
      <p class="hero-sub">Jadwalkan, analisis, dan optimalkan konten Instagram, TikTok, Facebook, X, dan YouTube — semuanya dalam satu platform yang dirancang khusus untuk Indonesia.</p>
      <div class="hero-btns">
        <Link href="/waitlist" class="btn-pri">Mulai Gratis 14 Hari →</Link>
        <a href="#fitur" class="btn-ghost">Lihat Demo</a>
      </div>
      <p style="font-size:10px;color:var(--ink-3);margin-bottom:36px">Tidak perlu kartu kredit · Setup 5 menit · Cancel kapan saja</p>

      <!-- Product Mockup -->
      <div class="mockup-wrap">
        <div class="mockup-bar">
          <div class="m-dots">
            <span class="m-dot"></span>
            <span class="m-dot"></span>
            <span class="m-dot"></span>
          </div>
          <div class="m-url">app.publishin.id/dashboard</div>
          <span style="font-size:10px;color:var(--ink-3)">@ahdirmai.id</span>
        </div>
        <div class="mockup-body">
          <div class="m-sidebar">
            <div class="m-logo-area">
              <div class="m-logo-mark">P·</div>
              <div class="m-logo-sub">Pro Plan</div>
            </div>
            <div class="m-nav-item act"><span class="m-chk"></span>Dashboard</div>
            <div class="m-nav-item"><span class="m-chk"></span>Kalender<span class="m-nbadge">3</span></div>
            <div class="m-nav-item"><span class="m-chk"></span>Buat Konten</div>
            <div class="m-nav-item"><span class="m-chk"></span>Analytics</div>
            <div class="m-nav-item"><span class="m-chk"></span>Laporan</div>
            <div class="m-nav-item" style="margin-top:auto"><span class="m-chk"></span>Pengaturan</div>
          </div>
          <div class="m-main">
            <div class="m-topbar">
              <div class="m-topbar-title">Dashboard</div>
              <span style="flex:1"></span>
              <button class="m-btn">+ Buat Konten</button>
              <button class="m-btn pri" style="position:relative">
                Notifikasi
                <span style="position:absolute;top:-5px;right:-5px;width:14px;height:14px;background:var(--accent-r);color:#fff;border-radius:50%;font-size:7px;display:grid;place-items:center;border:2px solid #fff">5</span>
              </button>
            </div>
            <div class="m-kpi-row">
              <div class="m-kpi"><div class="m-kpi-n">48.3K</div><div class="m-kpi-l">Total Followers</div><div class="m-kpi-d up">↑ +1.2K bulan ini</div></div>
              <div class="m-kpi"><div class="m-kpi-n r">12</div><div class="m-kpi-l">Post Terjadwal</div><div class="m-kpi-d">minggu ini</div></div>
              <div class="m-kpi"><div class="m-kpi-n">4.7%</div><div class="m-kpi-l">Avg. Engagement</div><div class="m-kpi-d up">↑ +0.3% vs bln lalu</div></div>
              <div class="m-kpi"><div class="m-kpi-n k">127K</div><div class="m-kpi-l">Reach (30 hari)</div><div class="m-kpi-d dn">↓ −8K vs bln lalu</div></div>
            </div>
            <div class="m-chart-wrap">
              <div class="m-chart-lbl">Engagement Trend · 30 hari terakhir</div>
              <svg viewBox="0 0 520 80" width="100%" height="80">
                <line x1="0" y1="70" x2="520" y2="70" stroke="rgba(0,0,0,.1)" stroke-width="1" />
                <line x1="0" y1="48" x2="520" y2="48" stroke="rgba(0,0,0,.06)" stroke-width="1" stroke-dasharray="3,3" />
                <line x1="0" y1="26" x2="520" y2="26" stroke="rgba(0,0,0,.06)" stroke-width="1" stroke-dasharray="3,3" />
                <text x="2" y="25" font-size="7" fill="rgba(0,0,0,.35)" font-family="monospace">6%</text>
                <text x="2" y="47" font-size="7" fill="rgba(0,0,0,.35)" font-family="monospace">4%</text>
                <text x="2" y="69" font-size="7" fill="rgba(0,0,0,.35)" font-family="monospace">2%</text>
                <polyline points="18,60 58,52 98,56 138,40 178,46 218,62 258,50 298,36 338,42 378,28 418,20 458,26 498,22" fill="none" stroke="var(--accent-b)" stroke-width="1.5" />
                <circle cx="378" cy="28" r="3" fill="var(--accent-r)" stroke="var(--ink)" stroke-width="1.5" />
              </svg>
            </div>
            <table class="m-tbl">
              <thead>
                <tr><th>Konten</th><th>Platform</th><th>Reach</th><th>Eng.</th><th>Status</th></tr>
              </thead>
              <tbody>
                <tr><td>Tips Content Creator 2026</td><td><span class="m-plt ig">IG</span></td><td>24.8K</td><td>5.2%</td><td><span class="m-mstat pub">Tayang</span></td></tr>
                <tr><td>Behind the scenes edit video</td><td><span class="m-plt tt">TT</span></td><td>38.1K</td><td>7.8%</td><td><span class="m-mstat pub">Tayang</span></td></tr>
                <tr><td>Review tools editing terbaru</td><td><span class="m-plt ig">IG</span></td><td>—</td><td>—</td><td><span class="m-mstat sch">Terjadwal</span></td></tr>
                <tr><td>Kolaborasi brand awareness</td><td><span class="m-plt fb">FB</span></td><td>—</td><td>—</td><td><span class="m-mstat sch">Terjadwal</span></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof Strip -->
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;padding:18px 20px;border:var(--bdr-m);border-radius:4px;background:#fff;margin-bottom:48px">
      <span style="font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ink-3)">Dipercaya oleh :</span>
      <span style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:14px;color:var(--ink-2)">IDN Creator</span>
      <span style="border-right:var(--bdr-m);height:16px"></span>
      <span style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:14px;color:var(--ink-2)">Magnivate</span>
      <span style="border-right:var(--bdr-m);height:16px"></span>
      <span style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:14px;color:var(--ink-2)">Kreavi Agency</span>
      <span style="border-right:var(--bdr-m);height:16px"></span>
      <span style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:14px;color:var(--ink-2)">SociaBuzz Pro</span>
      <span style="border-right:var(--bdr-m);height:16px"></span>
      <span style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:14px;color:var(--ink-2)">Konten.co</span>
      <span style="margin-left:auto;font-size:9px;color:var(--ink-3)">+340 agensi &amp; kreator aktif</span>
    </div>

    <!-- Stats Row -->
    <div class="stats-row" data-reveal="scale">
      <div class="stat-item"><div class="stat-num">340+</div><div class="stat-lbl">Pengguna aktif beta</div></div>
      <div class="stat-item"><div class="stat-num">2.8M</div><div class="stat-lbl">Post terjadwalkan</div></div>
      <div class="stat-item"><div class="stat-num">5</div><div class="stat-lbl">Platform terhubung</div></div>
      <div class="stat-item"><div class="stat-num">4.9★</div><div class="stat-lbl">Rating beta tester</div></div>
    </div>

    <!-- Features -->
    <section class="section" id="fitur">
      <div class="sec-header" data-reveal>
        <div class="sec-eyebrow">Fitur Utama</div>
        <h2 class="sec-title">Dirancang untuk cara kerja<br />kreator Indonesia.</h2>
        <p class="sec-sub">Bukan tools generic dari luar yang susah dipahami. Publishin dibangun dari nol untuk kebutuhan spesifik creator dan agensi lokal.</p>
      </div>

      <!-- Feature 1: Calendar -->
      <div class="feat-row" data-reveal="left">
        <div>
          <div class="feat-tag">Perencanaan Konten</div>
          <h3 class="feat-h">Kalender visual untuk semua platform, satu tampilan.</h3>
          <p class="feat-p">Tidak perlu spreadsheet atau notes berantakan. Drag &amp; drop konten ke kalender, lihat jadwal semua platform dalam satu view, dan set approval workflow untuk tim.</p>
          <ul class="feat-list">
            <li>Drag &amp; drop jadwal konten ke tanggal manapun</li>
            <li>Filter per platform: Instagram, TikTok, Facebook, X, YouTube</li>
            <li>Preview konten sebelum publish — pastikan copy &amp; media sesuai</li>
            <li>Notifikasi keterlambatan dan konflik jadwal</li>
            <li>Sync dengan Google Calendar untuk tim</li>
          </ul>
        </div>
        <div class="feat-visual">
          <div style="background:#EDEBE7;border-bottom:var(--bdr-m);padding:8px 12px;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-3);display:flex;justify-content:space-between">
            <span>Kalender · Juni 2026</span>
            <span>◉ IG ◈ TT ◉ FB</span>
          </div>
          <div style="padding:0">
            <div class="cal-mini">
              <div class="cal-mini-grid">
                <div class="cal-mini-lbl">Sen</div><div class="cal-mini-lbl">Sel</div><div class="cal-mini-lbl">Rab</div><div class="cal-mini-lbl">Kam</div><div class="cal-mini-lbl">Jum</div><div class="cal-mini-lbl">Sab</div><div class="cal-mini-lbl">Min</div>
                <div class="cal-mini-cell"></div>
                <div class="cal-mini-cell">1</div>
                <div class="cal-mini-cell">2<br /><span class="cdot ig"></span></div>
                <div class="cal-mini-cell today" style="border:1.5px solid var(--accent-r)">3 <span class="cdot tt"></span><span class="cdot ig"></span></div>
                <div class="cal-mini-cell">4</div>
                <div class="cal-mini-cell">5 <span class="cdot fb"></span></div>
                <div class="cal-mini-cell">6</div>
                <div class="cal-mini-cell">7</div>
                <div class="cal-mini-cell">8 <span class="cdot ig"></span></div>
                <div class="cal-mini-cell">9</div>
                <div class="cal-mini-cell">10<br /><span class="cdot tt"></span></div>
                <div class="cal-mini-cell">11<span class="cdot ig"></span></div>
                <div class="cal-mini-cell">12</div>
                <div class="cal-mini-cell">13</div>
                <div class="cal-mini-cell">14</div>
                <div class="cal-mini-cell">15<span class="cdot fb"></span></div>
                <div class="cal-mini-cell">16<span class="cdot ig"></span><span class="cdot tt"></span></div>
                <div class="cal-mini-cell">17</div>
                <div class="cal-mini-cell">18</div>
                <div class="cal-mini-cell">19</div>
                <div class="cal-mini-cell">20</div>
              </div>
            </div>
            <div style="padding:10px 12px;font-size:9px;color:var(--ink-3);border-top:var(--bdr-m);display:flex;gap:10px">
              <span><span class="cdot ig" style="display:inline-block;margin-right:3px"></span>Instagram</span>
              <span><span class="cdot tt" style="display:inline-block;margin-right:3px"></span>TikTok</span>
              <span><span class="cdot fb" style="display:inline-block;margin-right:3px"></span>Facebook</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature 2: Analytics (reversed) -->
      <div class="feat-row rev" data-reveal="right">
        <div>
          <div class="feat-tag">Analytics &amp; Insight</div>
          <h3 class="feat-h">Data yang kamu butuhkan, bukan data yang bingungkan.</h3>
          <p class="feat-p">Analytics yang mudah dipahami dengan visualisasi jelas. Lacak performa setiap konten, pahami demografis audiensmu, dan temukan waktu terbaik untuk posting.</p>
          <ul class="feat-list">
            <li>Analytics per konten: reach, impressi, like, komentar, share, simpan</li>
            <li>Engagement rate real-time per platform</li>
            <li>Demografis audiens: usia, gender, lokasi (kota/provinsi)</li>
            <li>Best time to post berdasarkan data aktual audiensmu</li>
            <li>Perbandingan performa vs rata-rata akunmu</li>
          </ul>
        </div>
        <div class="feat-visual">
          <div style="background:#EDEBE7;border-bottom:var(--bdr-m);padding:8px 12px;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-3)">Analytics Overview · Juni 2026</div>
          <div class="analytics-preview">
            <div class="ap-kpis">
              <div class="ap-kpi"><div class="ap-kpi-n">142K</div><div class="ap-kpi-d">↑ 18.4%</div><div class="ap-kpi-l">Reach</div></div>
              <div class="ap-kpi"><div class="ap-kpi-n">4.8%</div><div class="ap-kpi-d">↑ 0.9%</div><div class="ap-kpi-l">Eng Rate</div></div>
              <div class="ap-kpi"><div class="ap-kpi-n">+2.1K</div><div class="ap-kpi-d">↑ 12.2%</div><div class="ap-kpi-l">Followers</div></div>
            </div>
            <div class="ap-chart">
              <div class="ap-chart-title">Reach per hari — 7 hari terakhir</div>
              <svg viewBox="0 0 260 60" width="100%" height="60">
                <rect fill="url(#hatch-b)" x="0" y="0" width="260" height="60" opacity=".08" />
                <polyline points="0,45 36,38 73,42 109,22 146,30 182,10 218,18 260,14" fill="none" stroke="#3B6DB5" stroke-width="2" />
                <circle cx="182" cy="10" r="3" fill="#C96442" stroke="#1C1B1A" stroke-width="1.5" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature 3: Compose + AI -->
      <div class="feat-row" data-reveal="left">
        <div>
          <div class="feat-tag">Buat Konten + AI</div>
          <h3 class="feat-h">AI caption yang ngerti konteks konten Indonesia.</h3>
          <p class="feat-p">Generate caption dalam Bahasa Indonesia yang engaging, relevan, dan sesuai tone of voice brandmu — bukan terjemahan kaku dari bahasa Inggris. Satu klik, langsung siap posting.</p>
          <ul class="feat-list">
            <li>Generate caption IG, TikTok, Facebook, X — format berbeda per platform</li>
            <li>Pilihan tone: formal, santai, Gen-Z, profesional</li>
            <li>Hashtag suggester otomatis berdasarkan niche dan tren lokal</li>
            <li>Jadwalkan langsung dari compose ke kalender</li>
            <li>Preview tampilan konten di setiap platform sebelum publish</li>
          </ul>
        </div>
        <div class="feat-visual">
          <div style="background:#EDEBE7;border-bottom:var(--bdr-m);padding:8px 12px;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-3)">Buat Konten · Compose + Preview</div>
          <div class="compose-preview">
            <div class="cp-form">
              <div class="cp-lbl">Caption</div>
              <textarea class="cp-textarea" readonly>Dari nol jadi content creator profesional itu bukan soal gear mahal, tapi soal konsistensi dan strategi yang tepat. Ini 3 hal yang gue pelajari dalam 2 tahun pertama...</textarea>
              <button class="cp-ai-btn">✦ Generate AI Caption</button>
            </div>
            <div class="cp-preview">
              <div class="cp-plt-badge">◎ Instagram</div>
              <div class="cp-preview-img"></div>
              <div class="cp-preview-text">Dari nol jadi content creator profesional itu bukan soal gear mahal... <span style="color:var(--accent-b)">selengkapnya</span><br /><br /><span style="color:var(--ink-3)">#ContentCreator #TipsKonten #CreatorIndonesia</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature 4: Laporan (reversed) -->
      <div class="feat-row rev" style="border-bottom:none;margin-bottom:0;padding-bottom:0" data-reveal="right">
        <div>
          <div class="feat-tag">Laporan Klien</div>
          <h3 class="feat-h">Laporan profesional dengan brandmu sendiri.</h3>
          <p class="feat-p">Buat laporan performa yang bersih dan profesional untuk klien — dengan logo, warna, dan domain brandmu. Export PDF, langsung presentasi. Cocok untuk agensi digital dan freelancer SMM.</p>
          <ul class="feat-list">
            <li>White-label: tambahkan logo dan warna agensimu</li>
            <li>Pilih periode: mingguan, bulanan, atau custom range</li>
            <li>Include semua platform dalam satu laporan PDF</li>
            <li>Template laporan yang bisa dikustomisasi per klien</li>
            <li>Share via link atau download PDF langsung</li>
          </ul>
        </div>
        <div class="feat-visual" style="padding:20px">
          <div style="border:var(--bdr);border-radius:2px;padding:16px;background:#fafaf8">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding-bottom:10px;border-bottom:var(--bdr-m)">
              <div>
                <div style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:16px">[Logo Agensi]</div>
                <div style="font-size:9px;color:var(--ink-3)">Laporan Performa Sosial Media</div>
              </div>
              <div style="text-align:right;font-size:9px;color:var(--ink-3)">Mei 2026<br />PT Brand Indonesia</div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px">
              <div style="border:1px solid rgba(0,0,0,.1);border-radius:2px;padding:8px;text-align:center;background:#fff">
                <div style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:18px;color:var(--accent-r)">284K</div>
                <div style="font-size:8px;color:var(--ink-3)">Total Reach</div>
              </div>
              <div style="border:1px solid rgba(0,0,0,.1);border-radius:2px;padding:8px;text-align:center;background:#fff">
                <div style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:18px;color:var(--accent-b)">5.2%</div>
                <div style="font-size:8px;color:var(--ink-3)">Eng. Rate</div>
              </div>
              <div style="border:1px solid rgba(0,0,0,.1);border-radius:2px;padding:8px;text-align:center;background:#fff">
                <div style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:18px;color:var(--green)">+4.2K</div>
                <div style="font-size:8px;color:var(--ink-3)">New Followers</div>
              </div>
            </div>
            <div style="border:1px solid rgba(0,0,0,.1);border-radius:2px;padding:8px;background:#fff;font-size:9px;color:var(--ink-3);text-align:center">[Chart performa per platform — IG · TT · FB]</div>
            <div style="margin-top:10px;display:flex;justify-content:flex-end">
              <button style="border:var(--bdr);border-radius:2px;padding:4px 12px;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:var(--ink);color:#fff;cursor:pointer">Export PDF →</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Comparison -->
    <section class="section" data-reveal>
      <div class="sec-header">
        <div class="sec-eyebrow">Perbandingan</div>
        <h2 class="sec-title">Publishin vs tools lain.</h2>
      </div>
      <div style="overflow-x:auto">
        <table class="compare-tbl" style="min-width:600px">
          <thead>
            <tr>
              <th style="width:32%">Fitur</th>
              <th style="background:rgba(201,100,66,.06)">Publishin</th>
              <th>Hootsuite</th>
              <th>Buffer</th>
              <th>Spreadsheet</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Bahasa Indonesia native</td><td class="check-y" style="background:rgba(201,100,66,.04)">✓ Ya</td><td class="check-n">— Tidak</td><td class="check-n">— Tidak</td><td class="check-n">— Tidak</td></tr>
            <tr><td>AI Caption Bahasa Indonesia</td><td class="check-y" style="background:rgba(201,100,66,.04)">✓ Ya</td><td class="check-par">~ Add-on</td><td class="check-par">~ Beta</td><td class="check-n">— Tidak</td></tr>
            <tr><td>Analytics per konten</td><td class="check-y" style="background:rgba(201,100,66,.04)">✓ Ya</td><td class="check-y">✓ Ya</td><td class="check-par">~ Terbatas</td><td class="check-n">— Manual</td></tr>
            <tr><td>White-label laporan</td><td class="check-y" style="background:rgba(201,100,66,.04)">✓ Ya</td><td class="check-y">✓ Ya (mahal)</td><td class="check-n">— Tidak</td><td class="check-n">— Manual</td></tr>
            <tr><td>Harga (mulai dari)</td><td style="background:rgba(201,100,66,.04);font-weight:700;color:var(--accent-r)">Rp 99K/bln</td><td style="color:var(--ink-3)">$99/bln</td><td style="color:var(--ink-3)">$15/bln</td><td style="color:var(--ink-3)">Gratis (manual)</td></tr>
            <tr><td>Support Bahasa Indonesia</td><td class="check-y" style="background:rgba(201,100,66,.04)">✓ 24/7</td><td class="check-n">— Inggris saja</td><td class="check-n">— Inggris saja</td><td class="check-n">— Tidak ada</td></tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Pricing -->
    <section class="section" id="harga" data-reveal>
      <div class="sec-header">
        <div class="sec-eyebrow">Harga</div>
        <h2 class="sec-title">Harga yang masuk akal<br />untuk kreator lokal.</h2>
        <p class="sec-sub">Bayar dalam Rupiah. Tidak ada biaya tersembunyi. Cancel kapan saja.</p>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px">
        <span style="font-size:11px;color:var(--ink-3)">Bulanan</span>
        <div class="toggle-btn" :class="{ on: isYearly }" @click="isYearly = !isYearly">
          <span class="toggle-knob"></span>
        </div>
        <span style="font-size:11px;color:var(--ink-3)">Tahunan</span>
        <span style="background:rgba(42,122,75,.1);color:var(--green);border:1px solid var(--green);border-radius:100px;padding:1px 8px;font-size:9px;font-weight:700">Hemat 20%</span>
      </div>
      <div class="pricing-grid">
        <div class="price-card">
          <div class="price-tier">Starter</div>
          <div class="price-val">{{ isYearly ? prices.yearly[0] : prices.monthly[0] }}<span>/bln</span></div>
          <div class="price-period">Untuk solo creator baru</div>
          <ul class="price-features">
            <li>3 akun sosial media</li>
            <li>60 post terjadwalkan/bulan</li>
            <li>Kalender konten visual</li>
            <li>Analytics dasar (7 hari)</li>
            <li>AI Caption (100 generate/bln)</li>
            <li class="no">Laporan white-label</li>
            <li class="no">Multi-user / tim</li>
          </ul>
          <Link href="/waitlist" class="price-btn">Mulai Gratis 14 Hari</Link>
          <p class="price-note">Kartu kredit tidak diperlukan</p>
        </div>
        <div class="price-card pop">
          <div class="price-pop-badge">Paling Populer</div>
          <div class="price-tier">Pro</div>
          <div class="price-val">{{ isYearly ? prices.yearly[1] : prices.monthly[1] }}<span>/bln</span></div>
          <div class="price-period">Untuk creator serius &amp; freelancer SMM</div>
          <ul class="price-features">
            <li>10 akun sosial media</li>
            <li>Unlimited post terjadwalkan</li>
            <li>Kalender + approval workflow</li>
            <li>Analytics lengkap (90 hari)</li>
            <li>AI Caption unlimited</li>
            <li>Laporan white-label (5 klien)</li>
            <li>3 anggota tim</li>
          </ul>
          <Link href="/waitlist" class="price-btn fill">Mulai Gratis 14 Hari</Link>
          <p class="price-note">Kartu kredit tidak diperlukan</p>
        </div>
        <div class="price-card">
          <div class="price-tier">Agency</div>
          <div class="price-val">{{ isYearly ? prices.yearly[2] : prices.monthly[2] }}<span>/bln</span></div>
          <div class="price-period">Untuk agensi digital &amp; tim besar</div>
          <ul class="price-features">
            <li>Unlimited akun sosial media</li>
            <li>Unlimited post terjadwalkan</li>
            <li>Kalender + multi-level approval</li>
            <li>Analytics lengkap (1 tahun)</li>
            <li>AI Caption unlimited</li>
            <li>Laporan white-label unlimited</li>
            <li>Unlimited anggota tim</li>
          </ul>
          <Link href="/waitlist" class="price-btn">Hubungi Sales</Link>
          <p class="price-note">Custom onboarding tersedia</p>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="section" id="testimoni" data-reveal>
      <div class="sec-header">
        <div class="sec-eyebrow">Testimoni</div>
        <h2 class="sec-title">Kata mereka yang sudah pakai Publishin.</h2>
      </div>
      <div class="testi-grid">
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Akhirnya ada tools SMM yang benar-benar ngerti kebutuhan kreator Indonesia. Dashboard-nya bersih, analytics-nya detail, dan AI caption-nya surprisingly bagus untuk konten Bahasa Indonesia.</p>
          <div class="testi-by">
            <div class="testi-avatar">RS</div>
            <div>
              <div style="font-weight:700;color:var(--ink)">Rina Sari</div>
              <div style="font-size:10px;color:var(--ink-3)">Content Creator · 280K followers IG</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Sebelumnya pakai 4 tools berbeda buat manage sosmed 12 klien. Sekarang cukup Publishin. Fitur laporan white-label-nya bikin presentasi ke klien jadi jauh lebih profesional.</p>
          <div class="testi-by">
            <div class="testi-avatar">AP</div>
            <div>
              <div style="font-weight:700;color:var(--ink)">Andi Prasetyo</div>
              <div style="font-size:10px;color:var(--ink-3)">CEO · Agensi Digital Jakarta</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-q">Best time posting recommendation-nya akurat banget. Engagement rate naik 40% sejak pakai Publishin 2 bulan. Seriously game changer buat konten TikTok gue.</p>
          <div class="testi-by">
            <div class="testi-avatar">DK</div>
            <div>
              <div style="font-weight:700;color:var(--ink)">Dika Kusuma</div>
              <div style="font-size:10px;color:var(--ink-3)">TikToker · 1.2M followers</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="section" id="faq" data-reveal>
      <div class="sec-header">
        <div class="sec-eyebrow">FAQ</div>
        <h2 class="sec-title">Pertanyaan yang sering ditanyakan.</h2>
      </div>
      <div class="faq-list">
        <div v-for="(item, i) in faqItems" :key="i" class="faq-item">
          <div class="faq-q" @click="toggleFaq(i)">
            {{ item.q }}
            <span class="faq-toggle" :class="{ open: openFaq === i }">+</span>
          </div>
          <div class="faq-a" v-show="openFaq === i">{{ item.a }}</div>
        </div>
      </div>
    </section>

    <!-- CTA Bottom -->
    <div class="cta-section" data-reveal="scale">
      <div class="ann" style="margin-bottom:20px;display:inline-block">14 hari gratis, tidak perlu kartu kredit</div>
      <h2 class="cta-h">Kelola sosmedmu lebih <em>cerdas</em><br />mulai hari ini.</h2>
      <p class="cta-sub">Bergabung dengan 340+ kreator dan agensi Indonesia yang sudah pakai Publishin untuk menghemat waktu dan meningkatkan performa konten.</p>
      <div v-if="!ctaSubmitted" class="cta-form">
        <input v-model="ctaEmail" class="cta-input" type="email" placeholder="emailkamu@domain.com" @keyup.enter="submitCTA" />
        <button class="btn-pri" @click="submitCTA">Mulai Gratis →</button>
      </div>
      <div v-else style="font-size:14px;color:var(--green);font-weight:600;margin-bottom:16px">✓ Cek email kamu untuk langkah selanjutnya!</div>
      <p class="cta-trust">Tidak perlu kartu kredit · Setup 5 menit · Cancel kapan saja</p>
    </div>

    <!-- Footer -->
    <footer class="footer-main">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="footer-logo">Publishin</div>
          <p>Platform social media management untuk creator dan agensi Indonesia. Jadwalkan, analisis, dan optimalkan kontenmu dari satu dashboard.</p>
          <div style="display:flex;gap:10px;margin-top:14px">
            <span style="border:var(--bdr-m);border-radius:2px;width:28px;height:28px;display:grid;place-items:center;font-size:11px;cursor:pointer">IG</span>
            <span style="border:var(--bdr-m);border-radius:2px;width:28px;height:28px;display:grid;place-items:center;font-size:11px;cursor:pointer">TT</span>
            <span style="border:var(--bdr-m);border-radius:2px;width:28px;height:28px;display:grid;place-items:center;font-size:11px;cursor:pointer">X</span>
          </div>
        </div>
        <div>
          <div class="footer-col-title">Produk</div>
          <ul class="footer-links">
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#harga">Harga</a></li>
            <li><a href="#">Changelog</a></li>
            <li><a href="#">Roadmap</a></li>
            <li><a href="#">API</a></li>
          </ul>
        </div>
        <div>
          <div class="footer-col-title">Perusahaan</div>
          <ul class="footer-links">
            <li><a href="#">Tentang Kami</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Karier</a></li>
            <li><a href="#">Press Kit</a></li>
            <li><a href="#">Mitra</a></li>
          </ul>
        </div>
        <div>
          <div class="footer-col-title">Bantuan</div>
          <ul class="footer-links">
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Status Sistem</a></li>
            <li><a href="#">Hubungi Kami</a></li>
            <li><a href="/privacy">Privasi</a></li>
            <li><a href="/terms">Syarat Layanan</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Publishin · Made with ♥ in Indonesia 🇮🇩</span>
        <span class="footer-spacer"></span>
        <span>Semua harga dalam Rupiah (IDR) · Termasuk PPN 11%</span>
      </div>
    </footer>
  </div>
</template>

<style>
:root {
  --paper: #FAFAF8;
  --ink: #1C1B1A;
  --ink-2: #4A4944;
  --ink-3: #928E89;
  --accent-r: #C96442;
  --accent-b: #3B6DB5;
  --green: #2A7A4B;
  --sticky: #FFF8C5;
  --grid: rgba(170,185,210,.22);
  --f-sans: ui-monospace, 'JetBrains Mono', 'IBM Plex Mono', Menlo, monospace;
  --f-disp: 'Georgia', 'Iowan Old Style', serif;
  --bdr: 1.5px solid var(--ink);
  --bdr-m: 1px solid rgba(28,27,26,.22);
  --r: 2px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  font-family: var(--f-sans);
  font-size: 13px;
  line-height: 1.6;
  color: var(--ink);
  background-color: var(--paper);
  background-image: linear-gradient(var(--grid) 1px, transparent 1px), linear-gradient(90deg, var(--grid) 1px, transparent 1px);
  background-size: 20px 20px;
  min-height: 100vh;
  scroll-behavior: smooth;
}
button { font-family: var(--f-sans); cursor: pointer; }
input, textarea, select { font-family: var(--f-sans); }
a { color: inherit; text-decoration: none; }
ul { list-style: none; }

/* Layout */
.pg { max-width: 1200px; margin: 0 auto; padding: 0 28px 80px; }

/* Nav */
nav {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 0 20px;
  border-bottom: var(--bdr-m);
  flex-wrap: wrap;
  position: sticky;
  top: 0;
  background: rgba(250,250,248,.96);
  backdrop-filter: blur(6px);
  z-index: 100;
}
.nav-logo { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 22px; letter-spacing: -1px; text-decoration: underline; text-decoration-thickness: 1.5px; text-underline-offset: 3px; }
.nav-links { display: flex; gap: 18px; margin-left: auto; flex-wrap: wrap; align-items: center; }
.nav-link { font-size: 11px; letter-spacing: .08em; text-decoration: underline; text-decoration-style: dotted; text-underline-offset: 3px; color: var(--ink-2); cursor: pointer; }
.nav-cta-ghost { border: var(--bdr); padding: 4px 12px; border-radius: 2px; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; background: var(--paper); color: var(--ink); cursor: pointer; }
.nav-cta { border: var(--bdr); padding: 4px 14px; border-radius: 2px; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; background: var(--ink); color: #fff; cursor: pointer; }

/* Hamburger */
.nav-hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; padding: 4px; border: none; background: none; }
.nav-hamburger span { display: block; width: 20px; height: 1.5px; background: var(--ink); transition: transform .2s, opacity .2s; }
.nav-hamburger.open span:nth-child(1) { transform: translateY(5.5px) rotate(45deg); }
.nav-hamburger.open span:nth-child(2) { opacity: 0; }
.nav-hamburger.open span:nth-child(3) { transform: translateY(-5.5px) rotate(-45deg); }

/* Hero */
.hero { padding: 72px 0 56px; text-align: center; position: relative; }
.hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; border: var(--bdr-m); border-radius: 100px; padding: 4px 14px; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--accent-r); margin-bottom: 22px; background: #fff; }
.hero-h1 { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: clamp(48px, 6vw, 88px); line-height: 1.02; letter-spacing: -3px; margin-bottom: 22px; max-width: 860px; margin-left: auto; margin-right: auto; }
.hero-h1 em { color: var(--accent-r); }
.hero-sub { font-size: 16px; line-height: 1.7; color: var(--ink-2); max-width: 580px; margin: 0 auto 32px; }
.hero-btns { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 16px; }
.btn-pri { border: var(--bdr); padding: 10px 24px; border-radius: 2px; font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; background: var(--ink); color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
.btn-ghost { border: var(--bdr); padding: 10px 20px; border-radius: 2px; font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; background: var(--paper); color: var(--ink); cursor: pointer; display: inline-flex; align-items: center; }

/* Mockup */
.mockup-wrap { border: var(--bdr); border-radius: 4px; overflow: hidden; box-shadow: 6px 8px 0 rgba(0,0,0,.12); margin: 0 auto; max-width: 900px; }
.mockup-bar { background: #EDEBE7; border-bottom: var(--bdr); padding: 8px 14px; display: flex; align-items: center; gap: 10px; }
.m-dots { display: flex; gap: 5px; }
.m-dot { width: 10px; height: 10px; border-radius: 50%; border: 1.5px solid var(--ink); }
.m-url { flex: 1; background: #fff; border: 1px solid rgba(28,27,26,.22); border-radius: 100px; padding: 3px 12px; font-size: 11px; color: var(--ink-3); }
.mockup-body { display: flex; height: 560px; }
.m-sidebar { width: 180px; border-right: var(--bdr); background: var(--paper); background-image: linear-gradient(var(--grid) 1px, transparent 1px), linear-gradient(90deg, var(--grid) 1px, transparent 1px); background-size: 20px 20px; padding: 14px 10px; flex-shrink: 0; display: flex; flex-direction: column; }
.m-logo-area { margin-bottom: 16px; padding-bottom: 12px; border-bottom: var(--bdr-m); }
.m-logo-mark { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 18px; letter-spacing: -1px; text-decoration: underline; text-decoration-thickness: 1.5px; text-underline-offset: 3px; color: var(--ink); }
.m-logo-sub { font-size: 8px; color: var(--ink-3); letter-spacing: .1em; text-transform: uppercase; margin-top: 2px; }
.m-nav-item { display: flex; align-items: center; gap: 7px; padding: 6px 8px; border-radius: 2px; font-size: 11px; color: var(--ink-2); border: 1px solid transparent; margin-bottom: 2px; cursor: pointer; }
.m-nav-item.act { background: rgba(201,100,66,.09); border-color: var(--accent-r); color: var(--accent-r); font-weight: 600; }
.m-chk { width: 11px; height: 11px; border: 1.5px solid currentColor; border-radius: 2px; display: grid; place-items: center; flex-shrink: 0; font-size: 7px; }
.m-nav-item.act .m-chk::after { content: '✓'; }
.m-nbadge { margin-left: auto; font-size: 8px; font-weight: 700; background: var(--accent-r); color: #fff; padding: 1px 4px; border-radius: 10px; }
.m-main { flex: 1; padding: 16px; background: #fff; overflow: hidden; background-image: linear-gradient(rgba(170,185,210,0.12) 1px, transparent 1px), linear-gradient(90deg, rgba(170,185,210,0.12) 1px, transparent 1px); background-size: 20px 20px; }
.m-topbar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(0,0,0,.1); }
.m-topbar-title { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 14px; }
.m-btn { display: inline-flex; align-items: center; padding: 3px 9px; border: var(--bdr-m); border-radius: 2px; font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; background: var(--paper); cursor: pointer; }
.m-btn.pri { background: var(--ink); color: #fff; border-color: var(--ink); position: relative; }
.m-kpi-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 7px; margin-bottom: 12px; }
.m-kpi { border: 1.5px solid var(--ink); border-radius: 2px; padding: 8px 10px; background: var(--paper); }
.m-kpi-n { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 22px; color: var(--accent-b); line-height: 1; font-variant-numeric: tabular-nums; }
.m-kpi-n.r { color: var(--accent-r); }
.m-kpi-n.k { color: var(--ink); }
.m-kpi-l { font-size: 7px; color: var(--ink-3); margin-top: 2px; letter-spacing: .08em; text-transform: uppercase; }
.m-kpi-d { font-size: 8px; color: var(--ink-3); margin-top: 2px; font-variant-numeric: tabular-nums; }
.m-kpi-d.up { color: var(--green); }
.m-kpi-d.dn { color: var(--accent-r); }
.m-chart-wrap { border: 1.5px solid var(--ink); border-radius: 2px; padding: 10px; background: var(--paper); margin-bottom: 10px; }
.m-chart-lbl { font-size: 8px; color: var(--ink-3); margin-bottom: 6px; letter-spacing: .08em; text-transform: uppercase; }
.m-tbl { width: 100%; border-collapse: collapse; font-size: 9px; }
.m-tbl th { font-size: 7px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--ink-3); padding: 4px 5px; border-bottom: 1.5px solid var(--ink); text-align: left; }
.m-tbl td { padding: 5px 5px; border-bottom: 1px solid rgba(0,0,0,.06); font-variant-numeric: tabular-nums; color: var(--ink-2); }
.m-plt { display: inline-flex; padding: 1px 4px; border-radius: 2px; font-size: 7px; font-weight: 700; border: 1px solid currentColor; }
.m-plt.ig { color: #C13584; }
.m-plt.tt { color: #111; }
.m-plt.fb { color: #1877F2; }
.m-mstat { display: inline-flex; padding: 1px 5px; border-radius: 2px; font-size: 7px; font-weight: 700; }
.m-mstat.pub { background: rgba(42,122,75,.09); color: var(--green); border: 1px solid var(--green); }
.m-mstat.sch { background: rgba(59,109,181,.09); color: var(--accent-b); border: 1px solid var(--accent-b); }

/* Stats */
.stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 0; border: var(--bdr); border-radius: 4px; overflow: hidden; background: #fff; margin: 48px 0; }
.stat-item { padding: 24px 20px; border-right: var(--bdr-m); text-align: center; }
.stat-item:last-child { border-right: none; }
.stat-num { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 40px; color: var(--accent-r); font-variant-numeric: tabular-nums; line-height: 1; }
.stat-lbl { font-size: 10px; color: var(--ink-3); margin-top: 5px; }

/* Sections */
.section { margin: 64px 0; }
.sec-header { margin-bottom: 36px; }
.sec-eyebrow { font-size: 9px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
.sec-eyebrow::after { content: ''; flex: 1; height: 1px; background: rgba(28,27,26,.15); }
.sec-title { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: clamp(28px, 3.5vw, 48px); line-height: 1.08; letter-spacing: -1.5px; }
.sec-sub { font-size: 13px; color: var(--ink-2); max-width: 540px; margin-top: 10px; line-height: 1.7; }

/* Feature rows */
.feat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; margin-bottom: 48px; padding-bottom: 48px; border-bottom: var(--bdr-m); }
.feat-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.feat-row.rev { direction: rtl; }
.feat-row.rev > * { direction: ltr; }
.feat-visual { border: var(--bdr); border-radius: 4px; overflow: hidden; background: #fff; box-shadow: 4px 5px 0 rgba(0,0,0,.09); }
.feat-tag { display: inline-flex; padding: 2px 8px; border: var(--bdr-m); border-radius: 2px; font-size: 9px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 12px; }
.feat-h { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: clamp(22px, 2.5vw, 34px); line-height: 1.1; letter-spacing: -1px; margin-bottom: 12px; }
.feat-p { font-size: 12px; line-height: 1.7; color: var(--ink-2); margin-bottom: 18px; }
.feat-list { list-style: none; display: flex; flex-direction: column; gap: 6px; }
.feat-list li { display: flex; align-items: flex-start; gap: 8px; font-size: 11px; color: var(--ink-2); }
.feat-list li::before { content: '✓'; color: var(--green); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

/* Calendar mini */
.cal-mini { border: var(--bdr-m); border-radius: 2px; overflow: hidden; }
.cal-mini-grid { display: grid; grid-template-columns: repeat(7,1fr); }
.cal-mini-lbl { font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: 4px; border-bottom: 1px solid rgba(0,0,0,.07); border-right: 1px solid rgba(0,0,0,.06); text-align: center; color: var(--ink-3); background: #fafaf8; }
.cal-mini-lbl:last-child { border-right: none; }
.cal-mini-cell { min-height: 42px; border-bottom: 1px solid rgba(0,0,0,.06); border-right: 1px solid rgba(0,0,0,.06); padding: 3px 4px; font-size: 9px; font-variant-numeric: tabular-nums; }
.cal-mini-cell:nth-child(7n) { border-right: none; }
.cal-mini-cell.today { background: rgba(201,100,66,.07); }
.cdot { display: inline-block; width: 5px; height: 5px; border-radius: 50%; }
.cdot.ig { background: #C13584; }
.cdot.tt { background: #111; }
.cdot.fb { background: #1877F2; }

/* Analytics preview */
.analytics-preview { padding: 14px; }
.ap-kpis { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 12px; }
.ap-kpi { border: 1px solid rgba(0,0,0,.1); border-radius: 2px; padding: 8px; background: #fafaf8; }
.ap-kpi-n { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 22px; color: var(--accent-b); font-variant-numeric: tabular-nums; line-height: 1; }
.ap-kpi-d { font-size: 8px; color: var(--green); font-weight: 700; }
.ap-kpi-l { font-size: 8px; color: var(--ink-3); letter-spacing: .07em; text-transform: uppercase; }
.ap-chart { border: 1px solid rgba(0,0,0,.1); border-radius: 2px; padding: 10px; background: #fafaf8; }
.ap-chart-title { font-size: 8px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }

/* Compose preview */
.compose-preview { padding: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.cp-form { border: 1px solid rgba(0,0,0,.1); border-radius: 2px; padding: 10px; background: #fafaf8; }
.cp-lbl { font-size: 8px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 5px; }
.cp-textarea { border: 1px solid rgba(0,0,0,.15); border-radius: 2px; padding: 6px 8px; font-size: 10px; background: #fff; width: 100%; font-family: var(--f-sans); height: 60px; resize: none; margin-bottom: 8px; display: block; }
.cp-ai-btn { width: 100%; padding: 5px 0; border: 1.5px solid var(--accent-r); border-radius: 2px; font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--accent-r); background: rgba(201,100,66,.06); cursor: pointer; }
.cp-preview { border: 1px solid rgba(0,0,0,.1); border-radius: 2px; padding: 10px; background: #fff; }
.cp-plt-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border: 1px solid #C13584; border-radius: 2px; font-size: 8px; font-weight: 700; color: #C13584; margin-bottom: 8px; }
.cp-preview-img { border: 1px solid rgba(0,0,0,.1); border-radius: 2px; height: 70px; background: #f4f3f0; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: var(--ink-3); }
.cp-preview-text { font-size: 9px; line-height: 1.5; color: var(--ink-2); }

/* Comparison */
.compare-tbl { width: 100%; border-collapse: collapse; border: var(--bdr); border-radius: 4px; overflow: hidden; }
.compare-tbl th { font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: 10px 12px; border-bottom: var(--bdr); border-right: var(--bdr-m); background: #f4f3f0; text-align: left; }
.compare-tbl th:last-child { border-right: none; }
.compare-tbl td { padding: 9px 12px; border-bottom: 1px solid rgba(0,0,0,.06); border-right: var(--bdr-m); font-size: 11px; background: #fff; }
.compare-tbl td:last-child { border-right: none; }
.compare-tbl tr:last-child td { border-bottom: none; }
.compare-tbl tr:hover td { background: #f9f8f6; }
.check-y { color: var(--green); font-weight: 700; }
.check-n { color: var(--ink-3); }
.check-par { font-size: 10px; color: var(--accent-b); }

/* Pricing */
.toggle-btn { width: 36px; height: 20px; border: var(--bdr); border-radius: 100px; display: flex; align-items: center; padding: 2px; cursor: pointer; background: var(--paper); position: relative; }
.toggle-btn.on { background: var(--ink); border-color: var(--ink); }
.toggle-knob { width: 14px; height: 14px; border-radius: 50%; background: var(--ink); transition: transform .2s; }
.toggle-btn.on .toggle-knob { transform: translateX(16px); background: #fff; }

.pricing-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
.price-card { border: var(--bdr); border-radius: 4px; padding: 24px; background: #fff; position: relative; }
.price-card.pop { border-color: var(--accent-r); box-shadow: 4px 5px 0 rgba(201,100,66,.18); }
.price-pop-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--accent-r); color: #fff; font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; padding: 3px 12px; border-radius: 100px; white-space: nowrap; }
.price-tier { font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 10px; }
.price-val { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 38px; line-height: 1; font-variant-numeric: tabular-nums; margin-bottom: 4px; }
.price-val span { font-size: 14px; font-style: normal; color: var(--ink-2); }
.price-period { font-size: 10px; color: var(--ink-3); margin-bottom: 16px; padding-bottom: 16px; border-bottom: var(--bdr-m); }
.price-features { list-style: none; display: flex; flex-direction: column; gap: 7px; margin-bottom: 20px; }
.price-features li { font-size: 11px; color: var(--ink-2); display: flex; align-items: flex-start; gap: 7px; }
.price-features li::before { content: '·'; color: var(--accent-r); font-weight: 700; flex-shrink: 0; }
.price-features li.no::before { content: '·'; color: var(--ink-3); }
.price-features li.no { color: var(--ink-3); }
.price-btn { display: block; text-align: center; width: 100%; padding: 9px; border: var(--bdr); border-radius: var(--r); font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; cursor: pointer; background: var(--paper); color: var(--ink); font-family: var(--f-sans); }
.price-btn.fill { background: var(--ink); color: #fff; }
.price-note { font-size: 9px; color: var(--ink-3); text-align: center; margin-top: 12px; }

/* Testimonials */
.testi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
.testi-card { border: var(--bdr-m); border-radius: var(--r); padding: 18px; background: #fff; box-shadow: 2px 3px 0 rgba(0,0,0,.06); }
.testi-stars { color: var(--accent-r); font-size: 12px; margin-bottom: 8px; }
.testi-q { font-family: var(--f-disp); font-style: italic; font-size: 13px; line-height: 1.55; margin-bottom: 12px; color: var(--ink); }
.testi-q::before { content: '\201C'; font-size: 24px; line-height: 0; vertical-align: -8px; color: var(--accent-r); margin-right: 2px; }
.testi-by { font-size: 10px; color: var(--ink-3); display: flex; align-items: center; gap: 8px; }
.testi-avatar { width: 28px; height: 28px; border: 1.5px solid var(--ink); border-radius: 50%; display: grid; place-items: center; font-size: 9px; font-weight: 700; background: #f4f3f0; flex-shrink: 0; }

/* FAQ */
.faq-list { border: var(--bdr); border-radius: 4px; overflow: hidden; background: #fff; }
.faq-item { border-bottom: var(--bdr-m); }
.faq-item:last-child { border-bottom: none; }
.faq-q { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; cursor: pointer; font-size: 12px; font-weight: 600; user-select: none; }
.faq-q:hover { background: #fafaf8; }
.faq-toggle { font-size: 18px; color: var(--accent-r); font-weight: 300; transition: transform .2s; flex-shrink: 0; }
.faq-toggle.open { transform: rotate(45deg); }
.faq-a { display: block; padding: 0 16px 14px; font-size: 11px; line-height: 1.7; color: var(--ink-2); }

/* CTA */
.cta-section { border: var(--bdr); border-radius: 4px; padding: 56px 40px; background: #fff; text-align: center; box-shadow: 5px 6px 0 rgba(0,0,0,.1); position: relative; overflow: hidden; margin: 64px 0; }
.cta-section::before { content: ''; position: absolute; inset: 0; background-image: repeating-linear-gradient(-45deg, rgba(201,100,66,.03) 0, rgba(201,100,66,.03) 1px, transparent 1px, transparent 10px); pointer-events: none; }
.ann { display: inline-block; background: var(--sticky); border: 1px solid rgba(0,0,0,.12); border-radius: 2px; padding: 4px 9px; font-size: 9px; font-style: italic; line-height: 1.4; box-shadow: 1px 2px 0 rgba(0,0,0,.07); }
.cta-h { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: clamp(30px, 4vw, 56px); line-height: 1.06; letter-spacing: -2px; margin-bottom: 14px; }
.cta-h em { color: var(--accent-r); }
.cta-sub { font-size: 14px; color: var(--ink-2); max-width: 480px; margin: 0 auto 28px; line-height: 1.7; }
.cta-form { display: flex; gap: 8px; max-width: 400px; margin: 0 auto 10px; flex-wrap: wrap; justify-content: center; }
.cta-input { flex: 1; min-width: 200px; border: var(--bdr); border-radius: var(--r); padding: 9px 12px; font-size: 12px; background: var(--paper); color: var(--ink); font-family: var(--f-sans); outline: none; }
.cta-input:focus { background: #fff; }
.cta-trust { font-size: 10px; color: var(--ink-3); }

/* Footer */
.footer-main { border-top: var(--bdr); margin-top: 64px; padding: 40px 0 0; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 32px; margin-bottom: 32px; }
.footer-brand p { font-size: 11px; color: var(--ink-2); line-height: 1.6; max-width: 260px; }
.footer-logo { font-family: var(--f-disp); font-style: italic; font-weight: 700; font-size: 20px; text-decoration: underline; text-decoration-thickness: 1.5px; text-underline-offset: 3px; margin-bottom: 8px; }
.footer-col-title { font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 12px; }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 7px; }
.footer-links a { font-size: 11px; color: var(--ink-2); text-decoration: underline; text-decoration-style: dotted; text-underline-offset: 2px; cursor: pointer; }
.footer-bottom { border-top: var(--bdr-m); padding: 14px 0; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; font-size: 10px; color: var(--ink-3); }
.footer-spacer { flex: 1; }

/* Scroll reveal */
[data-reveal] { opacity: 0; transform: translateY(28px); transition: opacity .55s cubic-bezier(.22,1,.36,1), transform .55s cubic-bezier(.22,1,.36,1); }
[data-reveal].revealed { opacity: 1; transform: translateY(0); }
[data-reveal="left"] { transform: translateX(-28px); }
[data-reveal="left"].revealed { transform: translateX(0); }
[data-reveal="right"] { transform: translateX(28px); }
[data-reveal="right"].revealed { transform: translateX(0); }
[data-reveal="scale"] { transform: scale(.95); }
[data-reveal="scale"].revealed { transform: scale(1); }

/* Responsive */
@media (max-width: 800px) {
  .feat-row, .feat-row.rev { grid-template-columns: 1fr; direction: ltr; }
  .feat-row.rev > * { direction: ltr; }
}
@media (max-width: 760px) {
  .pricing-grid { grid-template-columns: 1fr; }
  .testi-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
  .stats-row { grid-template-columns: repeat(2,1fr); }
  .stat-item:nth-child(2) { border-right: none; }
  .compare-tbl th, .compare-tbl td { padding: 7px 8px; }
  .compose-preview { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .nav-hamburger { display: flex; }
  .nav-links { display: none; position: fixed; top: 0; left: 0; right: 0; background: rgba(250,250,248,.97); backdrop-filter: blur(10px); flex-direction: column; align-items: flex-start; padding: 64px 28px 28px; gap: 14px; z-index: 99; border-bottom: var(--bdr); }
  .nav-links.open { display: flex; }
  .nav-link { font-size: 14px; }
  .pg { padding: 0 16px 60px; }
  .hero { padding: 48px 0 36px; }
  .hero-btns { flex-direction: column; align-items: center; }
  .mockup-body { height: auto; flex-direction: column; }
  .m-sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; gap: 4px; }
}
</style>
