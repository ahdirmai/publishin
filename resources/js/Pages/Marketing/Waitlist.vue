<template>
  <Head title="Waitlist — Publishin" />

  <!-- SVG Hatch Defs -->
  <svg width="0" height="0" style="position:absolute;overflow:hidden">
    <defs>
      <pattern id="hatch-r" patternUnits="userSpaceOnUse" width="8" height="8" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="rgba(201,100,66,.18)" stroke-width="1.5" />
      </pattern>
      <pattern id="hatch-b" patternUnits="userSpaceOnUse" width="8" height="8" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="rgba(59,109,181,.18)" stroke-width="1.5" />
      </pattern>
      <pattern id="hatch-g" patternUnits="userSpaceOnUse" width="8" height="8" patternTransform="rotate(45)">
        <line x1="0" y1="0" x2="0" y2="8" stroke="rgba(42,122,75,.18)" stroke-width="1.5" />
      </pattern>
    </defs>
  </svg>

  <div class="pg">

    <!-- ============================================================
         NAV
    ============================================================ -->
    <nav>
      <span class="nav-logo">Publishin</span>

      <button
        class="nav-hamburger"
        :class="{ open: navOpen }"
        @click="navOpen = !navOpen"
        aria-label="Toggle menu"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="nav-links" :class="{ open: navOpen }" @click="navOpen = false">
        <a class="nav-link" href="#fitur">Fitur</a>
        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
        <a class="nav-link" href="#testimoni">Testimoni</a>
        <button class="nav-cta" @click="scrollToForm">Daftar Waitlist</button>
      </div>
    </nav>

    <!-- ============================================================
         HERO
    ============================================================ -->
    <section class="hero" id="hero">
      <!-- Left col -->
      <div>
        <div class="hero-eyebrow" data-reveal>Segera Hadir</div>
        <h1 class="hero-h1" data-reveal data-delay="1">
          Kelola semua <em>sosmedmu</em> dari satu tempat.
        </h1>
        <p class="hero-sub" data-reveal data-delay="2">
          Publishin membantu content creator dan agensi Indonesia menjadwalkan, menganalisis, dan mengoptimalkan konten di Instagram, TikTok, Facebook, X, dan YouTube — semuanya dalam satu dashboard.
        </p>
        <div class="count-pill" data-reveal data-delay="3">
          <span class="count-dot"></span>
          <span>{{ count.toLocaleString('id-ID') }} orang sudah bergabung ke waitlist</span>
        </div>
      </div>

      <!-- Right col: Signup card -->
      <div style="position:relative" data-reveal="right">
        <div class="signup-card" id="signup-form">
          <div class="sticky-note">Be the first to know! 🎯</div>

          <div class="sc-title">Bergabung ke Waitlist</div>
          <div class="sc-sub">Dapatkan akses early bird + 3 bulan gratis saat launch</div>

          <form @submit.prevent="submitForm">
            <!-- Name -->
            <label class="flabel" for="wl-name">Nama lengkap</label>
            <input
              id="wl-name"
              v-model="form.name"
              class="finput"
              type="text"
              placeholder="Nama kamu"
              autocomplete="name"
            />

            <!-- Email -->
            <label class="flabel" for="wl-email">Email aktif</label>
            <input
              id="wl-email"
              v-model="form.email"
              class="finput"
              type="email"
              placeholder="email@kamu.com"
              autocomplete="email"
            />

            <!-- Role -->
            <label class="flabel" for="wl-role">Peran kamu</label>
            <select id="wl-role" v-model="form.role" class="finput">
              <option value="" disabled>Pilih peran kamu</option>
              <option value="solo_creator">Solo Content Creator</option>
              <option value="agency">Agensi Digital/Marketing</option>
              <option value="brand">Brand/Perusahaan</option>
              <option value="umkm">UMKM/Bisnis Lokal</option>
              <option value="other">Lainnya</option>
            </select>

            <!-- Platforms -->
            <label class="flabel">Platform yang kamu gunakan</label>
            <div class="platform-check">
              <label
                v-for="platform in platformOptions"
                :key="platform"
                class="pchk"
                :style="form.platforms.includes(platform) ? 'border-color:var(--ink);background:#f0efec;' : ''"
              >
                <input
                  type="checkbox"
                  :value="platform"
                  :checked="form.platforms.includes(platform)"
                  @change="togglePlatform(platform)"
                />
                {{ platform }}
              </label>
            </div>

            <button type="submit" class="btn-submit" :disabled="form.processing">
              {{ form.processing ? 'Mendaftar...' : 'Daftar Sekarang — Gratis' }}
            </button>
          </form>

          <p class="privacy-note">Tidak ada spam. Data kamu aman. Unsubscribe kapan saja.</p>

          <!-- Success message -->
          <div v-show="submitted" class="success-msg">
            <div class="success-icon">✓</div>
            <div style="font-family:var(--f-disp);font-style:italic;font-weight:700;font-size:15px;margin-bottom:4px;">Kamu masuk waitlist!</div>
            <div style="font-size:11px;color:var(--ink-2);">Cek email kamu untuk konfirmasi.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================
         STATS STRIP
    ============================================================ -->
    <div class="stats" data-reveal>
      <div class="stat-item">
        <div class="stat-num">{{ displayCount.toLocaleString('id-ID') }}</div>
        <div class="stat-lbl">Orang di waitlist</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">5</div>
        <div class="stat-lbl">Platform terhubung</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">Q3</div>
        <div class="stat-lbl">Target launch 2026</div>
      </div>
    </div>

    <!-- ============================================================
         APP PREVIEW MOCKUP
    ============================================================ -->
    <section class="section" id="preview" data-reveal>
      <div class="sec-eyebrow">PREVIEW APLIKASI</div>
      <div class="sec-title">Semua yang kamu butuhkan dalam satu tampilan.</div>

      <div class="app-preview-wrap">
        <!-- Browser chrome -->
        <div class="app-preview-bar">
          <div class="ap-dots">
            <div class="ap-dot" style="background:rgba(255,95,86,.6)"></div>
            <div class="ap-dot" style="background:rgba(255,189,46,.6)"></div>
            <div class="ap-dot" style="background:rgba(39,201,63,.6)"></div>
          </div>
          <div class="ap-url">app.publishin.id/dashboard</div>
        </div>

        <!-- App body -->
        <div class="ap-body">
          <!-- Sidebar -->
          <div class="ap-sidebar">
            <div class="ap-logo-area">
              <div class="ap-logo-mark">P·</div>
              <div class="ap-logo-sub">Publishin</div>
            </div>

            <div class="ap-nav-item act">
              <div class="ap-chk"></div>
              Dashboard
            </div>
            <div class="ap-nav-item">
              <div class="ap-chk"></div>
              Kalender
              <span class="ap-nbadge">3</span>
            </div>
            <div class="ap-nav-item">
              <div class="ap-chk"></div>
              Buat Konten
            </div>
            <div class="ap-nav-item">
              <div class="ap-chk"></div>
              Analytics
            </div>
            <div class="ap-nav-item">
              <div class="ap-chk"></div>
              Laporan
            </div>
            <div class="ap-nav-item" style="margin-top:auto">
              <div class="ap-chk"></div>
              Pengaturan
            </div>
          </div>

          <!-- Main area -->
          <div class="ap-main">
            <!-- Topbar -->
            <div class="ap-topbar">
              <div class="ap-topbar-title">Dashboard</div>
              <div style="margin-left:auto;display:flex;gap:6px;align-items:center;">
                <button class="ap-btn pri">+ Buat Konten</button>
                <button class="ap-btn" style="position:relative;">
                  Notifikasi
                  <span style="position:absolute;top:-4px;right:-4px;background:var(--accent-r);color:#fff;font-size:7px;font-weight:700;padding:1px 4px;border-radius:10px;line-height:1.4;">5</span>
                </button>
              </div>
            </div>

            <!-- KPI Cards -->
            <div class="ap-kpi-row">
              <div class="ap-kpi">
                <div class="ap-kpi-n">48.3K</div>
                <div class="ap-kpi-l">Total Followers</div>
                <div class="ap-kpi-d up">↑ +2.4% minggu ini</div>
              </div>
              <div class="ap-kpi">
                <div class="ap-kpi-n r">12</div>
                <div class="ap-kpi-l">Terjadwal</div>
                <div class="ap-kpi-d">postingan bulan ini</div>
              </div>
              <div class="ap-kpi">
                <div class="ap-kpi-n k">4.7%</div>
                <div class="ap-kpi-l">Engagement Rate</div>
                <div class="ap-kpi-d up">↑ +0.3% vs bulan lalu</div>
              </div>
              <div class="ap-kpi">
                <div class="ap-kpi-n">127K</div>
                <div class="ap-kpi-l">Total Reach</div>
                <div class="ap-kpi-d dn">↓ -1.1% vs bulan lalu</div>
              </div>
            </div>

            <!-- 2-col grid: chart + notifications -->
            <div class="ap-g2">
              <!-- Engagement trend -->
              <div class="ap-card">
                <div class="ap-card-lbl">Engagement Trend — 30 Hari</div>
                <svg viewBox="0 0 200 80" style="width:100%;height:64px;">
                  <defs>
                    <linearGradient id="eng-grad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="rgba(59,109,181,.25)" />
                      <stop offset="100%" stop-color="rgba(59,109,181,0)" />
                    </linearGradient>
                  </defs>
                  <polyline
                    points="0,70 20,62 40,55 60,58 80,44 100,38 120,42 140,30 160,24 180,18 200,14"
                    fill="none" stroke="var(--accent-b)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"
                  />
                  <polygon
                    points="0,70 20,62 40,55 60,58 80,44 100,38 120,42 140,30 160,24 180,18 200,14 200,80 0,80"
                    fill="url(#eng-grad)"
                  />
                  <circle cx="200" cy="14" r="3" fill="var(--accent-b)" />
                </svg>
              </div>

              <!-- Notifications -->
              <div class="ap-card">
                <div class="ap-card-lbl">Aktivitas Terkini</div>
                <div class="ap-notif">
                  <div class="ap-ndot"></div>
                  <div><span style="font-weight:600;">Post IG</span> dijadwalkan untuk besok 09.00</div>
                </div>
                <div class="ap-notif">
                  <div class="ap-ndot b"></div>
                  <div><span style="font-weight:600;">Analytics</span> minggu ini siap diunduh</div>
                </div>
                <div class="ap-notif">
                  <div class="ap-ndot g"></div>
                  <div><span style="font-weight:600;">Engagement</span> naik 12% dari baseline</div>
                </div>
              </div>
            </div>

            <!-- Posts table -->
            <div class="ap-card">
              <div class="ap-card-lbl">Postingan Terbaru</div>
              <table class="ap-tbl">
                <thead>
                  <tr>
                    <th>Konten</th>
                    <th>Platform</th>
                    <th>Jadwal</th>
                    <th>Reach</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Tips produktivitas creator...</td>
                    <td><span class="ap-plt ig">IG</span></td>
                    <td>03 Jun, 09:00</td>
                    <td>4.2K</td>
                    <td><span class="ap-stat pub">Terpublish</span></td>
                  </tr>
                  <tr>
                    <td>Behind the scenes studio...</td>
                    <td><span class="ap-plt tt">TT</span></td>
                    <td>04 Jun, 18:00</td>
                    <td>—</td>
                    <td><span class="ap-stat sch">Terjadwal</span></td>
                  </tr>
                  <tr>
                    <td>Review produk skincare...</td>
                    <td><span class="ap-plt fb">FB</span></td>
                    <td>05 Jun, 12:00</td>
                    <td>—</td>
                    <td><span class="ap-stat sch">Terjadwal</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================
         FEATURES GRID
    ============================================================ -->
    <section class="section" id="fitur">
      <div class="sec-eyebrow" data-reveal>FITUR UNGGULAN</div>
      <div class="sec-title" data-reveal data-delay="1">Dibangun untuk creator Indonesia.</div>

      <div class="feat-grid">
        <!-- 01 -->
        <div class="feat-card" data-reveal data-delay="1">
          <div class="feat-num">01</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="1" y="2" width="12" height="11" rx="1" />
              <line x1="1" y1="5" x2="13" y2="5" />
              <line x1="4" y1="1" x2="4" y2="3" />
              <line x1="10" y1="1" x2="10" y2="3" />
            </svg>
          </div>
          <div class="feat-ttl">Kalender Konten</div>
          <div class="feat-desc">Rencanakan dan visualisasikan semua kontenmu dalam satu kalender terintegrasi. Drag-and-drop untuk reschedule dengan mudah.</div>
        </div>

        <!-- 02 -->
        <div class="feat-card" data-reveal data-delay="2">
          <div class="feat-num">02</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <polyline points="1,10 4,6 7,8 10,3 13,5" />
              <line x1="1" y1="13" x2="13" y2="13" />
            </svg>
          </div>
          <div class="feat-ttl">Analytics Mendalam</div>
          <div class="feat-desc">Pantau performa setiap postingan lintas platform: reach, engagement, follower growth, dan lebih banyak lagi dalam satu tampilan.</div>
        </div>

        <!-- 03 -->
        <div class="feat-card" data-reveal data-delay="3">
          <div class="feat-num">03</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <line x1="7" y1="1" x2="7" y2="13" />
              <line x1="1" y1="7" x2="13" y2="7" />
            </svg>
          </div>
          <div class="feat-ttl">AI Caption Generator</div>
          <div class="feat-desc">Buat caption menarik dalam bahasa Indonesia dengan AI. Sesuaikan tone, panjang, dan hashtag otomatis untuk setiap platform.</div>
        </div>

        <!-- 04 -->
        <div class="feat-card" data-reveal data-delay="1">
          <div class="feat-num">04</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="7" cy="7" r="5.5" />
              <line x1="7" y1="7" x2="7" y2="3" />
              <line x1="7" y1="7" x2="10" y2="7" />
            </svg>
          </div>
          <div class="feat-ttl">Best Time Posting</div>
          <div class="feat-desc">Algoritma kami menganalisis pola audiensimu dan merekomendasikan waktu terbaik untuk posting di tiap platform.</div>
        </div>

        <!-- 05 -->
        <div class="feat-card" data-reveal data-delay="2">
          <div class="feat-num">05</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="2" y="8" width="3" height="5" />
              <rect x="5.5" y="5" width="3" height="8" />
              <rect x="9" y="2" width="3" height="11" />
            </svg>
          </div>
          <div class="feat-ttl">Laporan White-Label</div>
          <div class="feat-desc">Buat laporan profesional bermerek untuk klien dalam hitungan menit. PDF siap kirim dengan data real-time semua platform.</div>
        </div>

        <!-- 06 -->
        <div class="feat-card" data-reveal data-delay="3">
          <div class="feat-num">06</div>
          <div class="feat-icon-box">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <polygon points="7,1.5 8.5,5.5 13,5.5 9.5,8 10.8,12 7,9.5 3.2,12 4.5,8 1,5.5 5.5,5.5" />
            </svg>
          </div>
          <div class="feat-ttl">Multi-Akun &amp; Tim</div>
          <div class="feat-desc">Kelola banyak akun sosmed dan undang anggota tim dengan role berbeda. Kolaborasi konten tanpa batas dalam satu workspace.</div>
        </div>
      </div>

      <!-- Platform strip -->
      <div class="plt-strip" data-reveal>
        <span style="font-size:10px;font-weight:700;color:var(--ink-3);letter-spacing:.08em;margin-right:4px;">Platform :</span>
        <span class="plt-badge" style="border-color:#C13584;color:#C13584;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
          Instagram
        </span>
        <span class="plt-badge" style="border-color:#111;color:#111;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.79a4.85 4.85 0 0 1-1.01-.1z"/></svg>
          TikTok
        </span>
        <span class="plt-badge" style="border-color:#1877F2;color:#1877F2;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.41 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.5c-1.5 0-1.96.93-1.96 1.89v2.26h3.32l-.53 3.49h-2.79V24C19.61 23.1 24 18.1 24 12.07"/></svg>
          Facebook
        </span>
        <span class="plt-badge" style="border-color:#000;color:#000;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          X
        </span>
        <span class="plt-badge" style="border-color:#FF0000;color:#FF0000;">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          YouTube
        </span>
      </div>
    </section>

    <!-- ============================================================
         HOW IT WORKS
    ============================================================ -->
    <section class="section" id="cara-kerja">
      <div class="sec-eyebrow" data-reveal>CARA KERJA</div>
      <div class="sec-title" data-reveal data-delay="1">Mulai dalam 4 langkah mudah.</div>

      <div class="hiw-grid">
        <div class="hiw-step" data-reveal data-delay="1" style="position:relative;">
          <div class="hiw-num">01</div>
          <div class="hiw-ttl">Hubungkan Akun</div>
          <div class="hiw-desc">Sambungkan semua akun sosial mediamu ke Publishin dengan aman menggunakan OAuth resmi dari setiap platform.</div>
          <div class="hiw-arr">→</div>
        </div>
        <div class="hiw-step" data-reveal data-delay="2" style="position:relative;">
          <div class="hiw-num">02</div>
          <div class="hiw-ttl">Buat Konten</div>
          <div class="hiw-desc">Gunakan editor konten kami lengkap dengan AI caption generator dan media manager untuk membuat postingan berkualitas.</div>
          <div class="hiw-arr">→</div>
        </div>
        <div class="hiw-step" data-reveal data-delay="3" style="position:relative;">
          <div class="hiw-num">03</div>
          <div class="hiw-ttl">Jadwalkan</div>
          <div class="hiw-desc">Pilih waktu terbaik berdasarkan rekomendasi AI atau atur sendiri di kalender. Publishin akan memposting otomatis.</div>
          <div class="hiw-arr">→</div>
        </div>
        <div class="hiw-step" data-reveal data-delay="4">
          <div class="hiw-num">04</div>
          <div class="hiw-ttl">Analisis &amp; Optimalkan</div>
          <div class="hiw-desc">Pantau performa real-time, baca laporan mendalam, dan optimalkan strategi kontenmu berdasarkan data yang akurat.</div>
        </div>
      </div>
    </section>

    <!-- ============================================================
         TESTIMONIALS
    ============================================================ -->
    <section class="section" id="testimoni">
      <div class="sec-eyebrow" data-reveal>TESTIMONI</div>
      <div class="sec-title" data-reveal data-delay="1">Dipercaya kreator Indonesia.</div>

      <div class="testi-grid">
        <div class="testi-card" data-reveal data-delay="1">
          <div class="testi-q">Publishin benar-benar mengubah cara aku kerja. Dulu harus buka 5 tab berbeda, sekarang semuanya ada di satu tempat. Waktu aku hemat minimal 3 jam per hari!</div>
          <div class="testi-by">
            <div class="testi-avatar">RS</div>
            <div>
              <div style="font-weight:700;color:var(--ink);font-size:11px;">Rina Sari</div>
              <div>Content Creator · 280K IG</div>
            </div>
          </div>
        </div>

        <div class="testi-card" data-reveal data-delay="2">
          <div class="testi-q">Sebagai agensi yang handle 20+ klien, fitur multi-akun dan laporan white-label Publishin sangat membantu. Klien kami terkesan dengan laporan yang profesional.</div>
          <div class="testi-by">
            <div class="testi-avatar">AP</div>
            <div>
              <div style="font-weight:700;color:var(--ink);font-size:11px;">Andi Prasetyo</div>
              <div>CEO Agensi Digital Jakarta</div>
            </div>
          </div>
        </div>

        <div class="testi-card" data-reveal data-delay="3">
          <div class="testi-q">AI caption generator-nya gila sih, bisa nulis caption TikTok yang on-brand dalam hitungan detik. Engagement rate aku naik 40% sejak pakai Publishin.</div>
          <div class="testi-by">
            <div class="testi-avatar">DK</div>
            <div>
              <div style="font-weight:700;color:var(--ink);font-size:11px;">Dika Kusuma</div>
              <div>TikToker · 1.2M Followers</div>
            </div>
          </div>
        </div>

        <div class="testi-card" data-reveal data-delay="4">
          <div class="testi-q">Analytics lintas platform dalam satu dashboard sangat membantu saya membuat keputusan konten berbasis data. Reportingnya juga jauh lebih mudah untuk presentasi ke manajemen.</div>
          <div class="testi-by">
            <div class="testi-avatar">NW</div>
            <div>
              <div style="font-weight:700;color:var(--ink);font-size:11px;">Nadia Wijaya</div>
              <div>Social Media Manager · Brand FMCG</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================
         BOTTOM CTA
    ============================================================ -->
    <div class="cta-bottom" data-reveal>
      <div class="ann" style="margin-bottom:16px;display:inline-block;">Gratis selama beta 🎉</div>
      <h2 class="cta-h">Jangan ketinggalan akses <em>early bird.</em></h2>
      <p class="cta-sub">Daftar sekarang dan dapatkan 3 bulan gratis plan Pro saat Publishin resmi launch Q3 2026.</p>
      <div class="cta-email-row">
        <input
          v-model="ctaEmail"
          class="finput"
          type="email"
          placeholder="email@kamu.com"
        />
        <button class="btn-submit" style="width:auto;white-space:nowrap;padding:10px 18px;" @click="submitBottom">
          Daftar →
        </button>
      </div>
    </div>

    <!-- ============================================================
         FOOTER
    ============================================================ -->
    <footer>
      <span class="logo-f">Publishin</span>
      <span style="flex:1;text-align:center;">© 2026 Publishin · Made in Indonesia 🇮🇩</span>
      <div class="flinks">
        <a class="flink" href="#">Kebijakan Privasi</a>
        <a class="flink" href="#">Syarat &amp; Ketentuan</a>
        <a class="flink" href="#">Hubungi Kami</a>
      </div>
    </footer>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{ count: number }>()

const navOpen = ref(false)
const displayCount = ref(props.count)
const ctaEmail = ref('')

const platformOptions = ['Instagram', 'TikTok', 'Facebook', 'X (Twitter)', 'YouTube']

const form = useForm({
  name: '',
  email: '',
  role: '',
  platforms: ['Instagram'] as string[],
})

const submitted = ref(false)

function togglePlatform(platform: string) {
  const idx = form.platforms.indexOf(platform)
  if (idx >= 0) form.platforms.splice(idx, 1)
  else form.platforms.push(platform)
}

function submitForm() {
  form.post(route('waitlist.store'), {
    onSuccess: () => {
      submitted.value = true
      displayCount.value++
    },
  })
}

function submitBottom() {
  // If ctaEmail has a value, pre-fill form and scroll to it
  if (ctaEmail.value) {
    form.email = ctaEmail.value
  }
  document.getElementById('signup-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
}

function scrollToForm() {
  document.getElementById('signup-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
}

onMounted(() => {
  const obs = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add('revealed')
          obs.unobserve(e.target)
        }
      })
    },
    { threshold: 0.08 },
  )
  document.querySelectorAll('[data-reveal]').forEach((el) => obs.observe(el))
})
</script>

<style>
/* ================================================================
   BASE
================================================================ */
.pg {
  max-width: 1140px;
  margin: 0 auto;
  padding: 0 28px 80px;
}

html {
  scroll-behavior: smooth;
}

/* ================================================================
   NAV
================================================================ */
nav {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 18px 0 22px;
  border-bottom: var(--bdr-m);
  flex-wrap: wrap;
  position: sticky;
  top: 0;
  background: rgba(250, 250, 248, 0.96);
  backdrop-filter: blur(6px);
  z-index: 100;
}

.nav-logo {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 22px;
  letter-spacing: -1px;
  text-decoration: underline;
  text-decoration-thickness: 1.5px;
  text-underline-offset: 3px;
  color: var(--ink);
}

.nav-links {
  display: flex;
  gap: 18px;
  margin-left: auto;
  flex-wrap: wrap;
  align-items: center;
}

.nav-link {
  font-size: 11px;
  letter-spacing: 0.08em;
  text-decoration: underline;
  text-decoration-style: dotted;
  text-underline-offset: 3px;
  color: var(--ink-2);
  cursor: pointer;
}

.nav-cta {
  border: var(--bdr);
  padding: 4px 14px;
  border-radius: 2px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  background: var(--ink);
  color: #fff;
  cursor: pointer;
}

.nav-hamburger {
  display: none;
  flex-direction: column;
  gap: 4px;
  cursor: pointer;
  padding: 4px;
  border: none;
  background: none;
}

.nav-hamburger span {
  display: block;
  width: 20px;
  height: 1.5px;
  background: var(--ink);
  transition: transform 0.2s, opacity 0.2s;
}

.nav-hamburger.open span:nth-child(1) {
  transform: translateY(5.5px) rotate(45deg);
}

.nav-hamburger.open span:nth-child(2) {
  opacity: 0;
}

.nav-hamburger.open span:nth-child(3) {
  transform: translateY(-5.5px) rotate(-45deg);
}

/* ================================================================
   HERO
================================================================ */
.hero {
  padding: 72px 0 60px;
  display: grid;
  grid-template-columns: 1fr 480px;
  gap: 48px;
  align-items: center;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--accent-r);
  margin-bottom: 18px;
}

.hero-eyebrow::before {
  content: '';
  display: inline-block;
  width: 22px;
  height: 1.5px;
  background: var(--accent-r);
}

.hero-h1 {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: clamp(42px, 5vw, 72px);
  line-height: 1.06;
  letter-spacing: -2px;
  margin-bottom: 20px;
}

.hero-h1 em {
  color: var(--accent-r);
  font-style: italic;
}

.hero-sub {
  font-size: 14px;
  line-height: 1.7;
  color: var(--ink-2);
  max-width: 460px;
  margin-bottom: 28px;
}

.count-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: var(--bdr-m);
  border-radius: 100px;
  padding: 5px 14px;
  font-size: 11px;
  color: var(--ink-2);
  background: #fff;
  margin-bottom: 20px;
}

.count-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--green);
  border: 1.5px solid var(--green);
  flex-shrink: 0;
}

/* ================================================================
   SIGNUP CARD
================================================================ */
.signup-card {
  border: var(--bdr);
  border-radius: 4px;
  background: #fff;
  box-shadow: 4px 5px 0 rgba(0, 0, 0, 0.11);
  padding: 28px;
  position: relative;
}

.signup-card .sticky-note {
  position: absolute;
  top: -22px;
  right: 18px;
  background: var(--sticky, #fff8c5);
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 2px;
  padding: 6px 10px;
  font-size: 10px;
  font-style: italic;
  line-height: 1.4;
  box-shadow: 2px 3px 0 rgba(0, 0, 0, 0.08);
  transform: rotate(1.4deg);
}

.sc-title {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 20px;
  margin-bottom: 4px;
}

.sc-sub {
  font-size: 11px;
  color: var(--ink-3);
  margin-bottom: 20px;
}

.flabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 4px;
  display: block;
}

.finput {
  width: 100%;
  border: var(--bdr-m);
  border-radius: 2px;
  padding: 8px 10px;
  font-size: 12px;
  background: #fafaf8;
  color: var(--ink);
  outline: none;
  font-family: var(--f-sans);
  transition: border-color 0.15s;
  margin-bottom: 12px;
  box-sizing: border-box;
}

.finput:focus {
  border-color: var(--accent-r);
  background: #fff;
}

.platform-check {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 16px;
}

.pchk {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 9px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 2px;
  font-size: 10px;
  cursor: pointer;
  user-select: none;
  background: #fafaf8;
}

.pchk input {
  width: 10px;
  height: 10px;
  cursor: pointer;
}

.btn-submit {
  width: 100%;
  padding: 10px;
  border: var(--bdr);
  border-radius: 2px;
  background: var(--ink);
  color: #fff;
  font-family: var(--f-sans);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  cursor: pointer;
  transition: opacity 0.15s;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.privacy-note {
  font-size: 9px;
  color: var(--ink-3);
  text-align: center;
  margin-top: 8px;
}

.success-msg {
  text-align: center;
  padding: 20px;
  border: 1.5px dashed var(--green);
  border-radius: 2px;
  background: rgba(42, 122, 75, 0.05);
  margin-top: 12px;
}

.success-icon {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 28px;
  color: var(--green);
  margin-bottom: 6px;
}

/* ================================================================
   STATS
================================================================ */
.stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0;
  border: var(--bdr);
  border-radius: 4px;
  overflow: hidden;
  margin: 44px 0;
  background: #fff;
}

.stat-item {
  padding: 22px 20px;
  border-right: var(--bdr-m);
  text-align: center;
}

.stat-item:last-child {
  border-right: none;
}

.stat-num {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 38px;
  color: var(--accent-r);
  font-variant-numeric: tabular-nums;
  line-height: 1;
}

.stat-lbl {
  font-size: 10px;
  color: var(--ink-3);
  margin-top: 4px;
  letter-spacing: 0.07em;
}

/* ================================================================
   SECTION HEADERS
================================================================ */
.section {
  margin: 56px 0;
}

.sec-eyebrow {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.sec-eyebrow::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(28, 27, 26, 0.12);
}

.sec-title {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: clamp(26px, 3vw, 40px);
  line-height: 1.1;
  letter-spacing: -1px;
  margin-bottom: 28px;
}

/* ================================================================
   APP PREVIEW MOCKUP
================================================================ */
.app-preview-wrap {
  border: var(--bdr);
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 6px 8px 0 rgba(0, 0, 0, 0.12);
  margin: 0 auto;
}

.app-preview-bar {
  background: #edebe7;
  border-bottom: var(--bdr);
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.ap-dots {
  display: flex;
  gap: 5px;
}

.ap-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 1.5px solid var(--ink);
}

.ap-url {
  flex: 1;
  background: #fff;
  border: 1px solid rgba(28, 27, 26, 0.22);
  border-radius: 100px;
  padding: 3px 12px;
  font-size: 11px;
  color: var(--ink-3);
}

.ap-body {
  display: flex;
  height: 500px;
}

.ap-sidebar {
  width: 180px;
  border-right: var(--bdr);
  background: var(--paper);
  background-image: linear-gradient(rgba(170, 185, 210, 0.22) 1px, transparent 1px),
    linear-gradient(90deg, rgba(170, 185, 210, 0.22) 1px, transparent 1px);
  background-size: 20px 20px;
  padding: 14px 10px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
}

.ap-logo-area {
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: var(--bdr-m);
}

.ap-logo-mark {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 18px;
  letter-spacing: -1px;
  text-decoration: underline;
  text-decoration-thickness: 1.5px;
  text-underline-offset: 3px;
}

.ap-logo-sub {
  font-size: 8px;
  color: var(--ink-3);
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-top: 2px;
}

.ap-nav-item {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 6px 8px;
  border-radius: 2px;
  font-size: 11px;
  color: var(--ink-2);
  border: 1px solid transparent;
  margin-bottom: 2px;
}

.ap-nav-item.act {
  background: rgba(201, 100, 66, 0.09);
  border-color: var(--accent-r);
  color: var(--accent-r);
  font-weight: 600;
}

.ap-chk {
  width: 11px;
  height: 11px;
  border: 1.5px solid currentColor;
  border-radius: 2px;
  display: grid;
  place-items: center;
  flex-shrink: 0;
  font-size: 7px;
}

.ap-nav-item.act .ap-chk::after {
  content: '✓';
}

.ap-nbadge {
  margin-left: auto;
  font-size: 8px;
  font-weight: 700;
  background: var(--accent-r);
  color: #fff;
  padding: 1px 4px;
  border-radius: 10px;
}

.ap-main {
  flex: 1;
  padding: 16px;
  background: #fff;
  overflow: hidden;
  background-image: linear-gradient(rgba(170, 185, 210, 0.12) 1px, transparent 1px),
    linear-gradient(90deg, rgba(170, 185, 210, 0.12) 1px, transparent 1px);
  background-size: 20px 20px;
}

.ap-topbar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.ap-topbar-title {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 14px;
}

.ap-btn {
  display: inline-flex;
  align-items: center;
  padding: 3px 9px;
  border: var(--bdr-m);
  border-radius: 2px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  background: var(--paper);
  cursor: pointer;
}

.ap-btn.pri {
  background: var(--ink);
  color: #fff;
  border-color: var(--ink);
  position: relative;
}

.ap-kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 7px;
  margin-bottom: 12px;
}

.ap-kpi {
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  padding: 8px 10px;
  background: var(--paper);
}

.ap-kpi-n {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 22px;
  color: var(--accent-b);
  line-height: 1;
  font-variant-numeric: tabular-nums;
}

.ap-kpi-n.r {
  color: var(--accent-r);
}

.ap-kpi-n.k {
  color: var(--ink);
}

.ap-kpi-l {
  font-size: 7px;
  color: var(--ink-3);
  margin-top: 2px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.ap-kpi-d {
  font-size: 8px;
  color: var(--ink-3);
  margin-top: 2px;
  font-variant-numeric: tabular-nums;
}

.ap-kpi-d.up {
  color: var(--green);
}

.ap-kpi-d.dn {
  color: var(--accent-r);
}

.ap-g2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 10px;
}

.ap-card {
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  padding: 10px;
  background: var(--paper);
}

.ap-card-lbl {
  font-size: 8px;
  color: var(--ink-3);
  margin-bottom: 6px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.ap-tbl {
  width: 100%;
  border-collapse: collapse;
  font-size: 9px;
}

.ap-tbl th {
  font-size: 7px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--ink-3);
  padding: 4px 5px;
  border-bottom: 1.5px solid var(--ink);
  text-align: left;
}

.ap-tbl td {
  padding: 5px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  font-variant-numeric: tabular-nums;
  color: var(--ink-2);
}

.ap-plt {
  display: inline-flex;
  padding: 1px 4px;
  border-radius: 2px;
  font-size: 7px;
  font-weight: 700;
  border: 1px solid currentColor;
}

.ap-plt.ig {
  color: #c13584;
}

.ap-plt.tt {
  color: #111;
}

.ap-plt.fb {
  color: #1877f2;
}

.ap-stat {
  display: inline-flex;
  padding: 1px 5px;
  border-radius: 2px;
  font-size: 7px;
  font-weight: 700;
}

.ap-stat.pub {
  background: rgba(42, 122, 75, 0.09);
  color: var(--green);
  border: 1px solid var(--green);
}

.ap-stat.sch {
  background: rgba(59, 109, 181, 0.09);
  color: var(--accent-b);
  border: 1px solid var(--accent-b);
}

.ap-notif {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  padding: 6px 8px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  border-radius: 2px;
  margin-bottom: 4px;
  font-size: 9px;
  background: #fff;
}

.ap-ndot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  border: 1.5px solid var(--accent-r);
  margin-top: 2px;
  flex-shrink: 0;
}

.ap-ndot.b {
  border-color: var(--accent-b);
}

.ap-ndot.g {
  border-color: var(--green);
}

/* ================================================================
   FEATURE GRID
================================================================ */
.feat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.feat-card {
  border: var(--bdr);
  border-radius: 2px;
  padding: 18px;
  background: #fff;
  box-shadow: 2px 3px 0 rgba(0, 0, 0, 0.07);
  position: relative;
}

.feat-num {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 36px;
  color: rgba(28, 27, 26, 0.08);
  position: absolute;
  top: 10px;
  right: 14px;
  line-height: 1;
}

.feat-icon-box {
  width: 36px;
  height: 36px;
  border: var(--bdr);
  border-radius: 2px;
  display: grid;
  place-items: center;
  margin-bottom: 12px;
  font-size: 14px;
}

.feat-ttl {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 6px;
}

.feat-desc {
  font-size: 11px;
  line-height: 1.65;
  color: var(--ink-2);
}

.plt-strip {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding: 20px;
  border: var(--bdr-m);
  border-radius: 2px;
  background: #fff;
  margin-top: 28px;
  align-items: center;
}

.plt-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.07em;
}

/* ================================================================
   HOW IT WORKS
================================================================ */
.hiw-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.hiw-step {
  border: var(--bdr);
  border-radius: 2px;
  padding: 16px;
  background: #fff;
  text-align: center;
  position: relative;
}

.hiw-num {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 42px;
  color: rgba(201, 100, 66, 0.15);
  line-height: 1;
  margin-bottom: 8px;
}

.hiw-ttl {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 5px;
}

.hiw-desc {
  font-size: 10px;
  line-height: 1.6;
  color: var(--ink-2);
}

.hiw-arr {
  display: none;
}

@media (min-width: 740px) {
  .hiw-arr {
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    right: -16px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    font-size: 18px;
    color: var(--ink-3);
  }
}

/* ================================================================
   TESTIMONIALS
================================================================ */
.testi-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.testi-card {
  border: var(--bdr-m);
  border-radius: 2px;
  padding: 16px;
  background: #fff;
  box-shadow: 2px 3px 0 rgba(0, 0, 0, 0.06);
}

.testi-q {
  font-family: var(--f-disp);
  font-style: italic;
  font-size: 14px;
  line-height: 1.55;
  margin-bottom: 12px;
  color: var(--ink);
}

.testi-q::before {
  content: '\201C';
  font-size: 28px;
  line-height: 0;
  vertical-align: -10px;
  color: var(--accent-r);
  margin-right: 2px;
}

.testi-by {
  font-size: 10px;
  color: var(--ink-3);
  display: flex;
  align-items: center;
  gap: 8px;
}

.testi-avatar {
  width: 26px;
  height: 26px;
  border: 1.5px solid var(--ink);
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 9px;
  font-weight: 700;
  background: #f4f3f0;
  flex-shrink: 0;
}

/* ================================================================
   CTA BOTTOM
================================================================ */
.cta-bottom {
  border: var(--bdr);
  border-radius: 4px;
  padding: 48px 40px;
  background: #fff;
  text-align: center;
  box-shadow: 4px 5px 0 rgba(0, 0, 0, 0.1);
  margin-top: 56px;
  position: relative;
  overflow: hidden;
}

.cta-bottom::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: repeating-linear-gradient(
    -45deg,
    rgba(201, 100, 66, 0.04) 0,
    rgba(201, 100, 66, 0.04) 1px,
    transparent 1px,
    transparent 9px
  );
  pointer-events: none;
}

.cta-h {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: clamp(28px, 4vw, 52px);
  line-height: 1.08;
  letter-spacing: -1.5px;
  margin-bottom: 14px;
}

.cta-h em {
  color: var(--accent-r);
  font-style: italic;
}

.cta-sub {
  font-size: 13px;
  color: var(--ink-2);
  max-width: 420px;
  margin: 0 auto 24px;
}

.cta-email-row {
  display: flex;
  gap: 8px;
  max-width: 380px;
  margin: 0 auto;
}

.cta-email-row .finput {
  margin-bottom: 0;
  flex: 1;
}

/* ================================================================
   ANN STICKY NOTE
================================================================ */
.ann {
  display: inline-block;
  background: #fff8c5;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 2px;
  padding: 4px 9px;
  font-size: 9px;
  font-style: italic;
  line-height: 1.4;
  box-shadow: 1px 2px 0 rgba(0, 0, 0, 0.07);
}

/* ================================================================
   FOOTER
================================================================ */
footer {
  border-top: var(--bdr-m);
  margin-top: 56px;
  padding-top: 20px;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  font-size: 10px;
  color: var(--ink-3);
}

.logo-f {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 14px;
  color: var(--ink);
}

.flinks {
  display: flex;
  gap: 14px;
  margin-left: auto;
}

.flink {
  text-decoration: underline;
  text-decoration-style: dotted;
  text-underline-offset: 2px;
  color: var(--ink-3);
  cursor: pointer;
}

/* ================================================================
   SCROLL REVEAL
================================================================ */
[data-reveal] {
  opacity: 0;
  transform: translateY(28px);
  transition:
    opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

[data-reveal].revealed {
  opacity: 1;
  transform: translateY(0);
}

[data-reveal='left'] {
  transform: translateX(-28px);
}

[data-reveal='left'].revealed {
  transform: translateX(0);
}

[data-reveal='right'] {
  transform: translateX(28px);
}

[data-reveal='right'].revealed {
  transform: translateX(0);
}

[data-reveal='scale'] {
  transform: scale(0.95);
}

[data-reveal='scale'].revealed {
  transform: scale(1);
}

[data-delay='1'] {
  transition-delay: 0.1s;
}

[data-delay='2'] {
  transition-delay: 0.2s;
}

[data-delay='3'] {
  transition-delay: 0.3s;
}

[data-delay='4'] {
  transition-delay: 0.4s;
}

/* ================================================================
   RESPONSIVE
================================================================ */
@media (max-width: 860px) {
  .hero {
    grid-template-columns: 1fr;
    padding: 48px 0 36px;
  }
}

@media (max-width: 740px) {
  .feat-grid {
    grid-template-columns: 1fr;
  }

  .hiw-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .testi-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .nav-hamburger {
    display: flex;
  }

  .nav-links {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(250, 250, 248, 0.97);
    backdrop-filter: blur(10px);
    flex-direction: column;
    align-items: flex-start;
    padding: 64px 28px 28px;
    gap: 14px;
    z-index: 99;
    border-bottom: var(--bdr);
  }

  .nav-links.open {
    display: flex;
  }

  .pg {
    padding: 0 16px 60px;
  }

  .stats {
    grid-template-columns: 1fr;
  }

  .stat-item {
    border-right: none;
    border-bottom: var(--bdr-m);
  }

  .stat-item:last-child {
    border-bottom: none;
  }

  .app-preview-wrap {
    overflow-x: auto;
  }

  .ap-body {
    min-width: 600px;
  }

  .cta-bottom {
    padding: 32px 20px;
  }

  .cta-email-row {
    flex-direction: column;
  }

  .cta-email-row .btn-submit {
    width: 100%;
  }
}
</style>
