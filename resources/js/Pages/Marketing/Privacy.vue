<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

const lastUpdated = '3 Juni 2026'
</script>

<template>
  <Head title="Kebijakan Privasi — Publishin" />

  <div class="pg" style="--paper:#FAFAF8;--ink:#1C1B1A;--ink-2:#4A4845;--ink-3:#8A8784;--accent-b:#3B6DB5;--accent-r:#C96442;--forest:#2A7A4B;min-height:100vh;background:var(--paper);font-family:'DM Sans',sans-serif;color:var(--ink)">

    <!-- Nav -->
    <nav style="display:flex;align-items:center;justify-content:space-between;padding:16px 40px;border-bottom:1px solid rgba(28,27,26,0.12);position:sticky;top:0;background:var(--paper);z-index:100">
      <Link href="/" style="font-family:Georgia,serif;font-style:italic;font-weight:700;font-size:20px;color:var(--ink);text-decoration:none">Publishin</Link>
      <div style="display:flex;gap:12px;align-items:center">
        <Link href="/login" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-2);text-decoration:none;padding:6px 14px;border:1px solid rgba(28,27,26,0.22);border-radius:2px">Masuk</Link>
        <Link href="/waitlist" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:600;color:#FAFAF8;background:var(--ink);padding:7px 16px;border-radius:2px;text-decoration:none">Coba Gratis</Link>
      </div>
    </nav>

    <!-- Content -->
    <div style="max-width:720px;margin:0 auto;padding:64px 24px 120px">

      <!-- Header -->
      <div style="margin-bottom:48px">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--ink-3);margin-bottom:12px">Legal</div>
        <h1 style="font-family:Georgia,serif;font-style:italic;font-weight:700;font-size:36px;line-height:1.15;margin:0 0 12px">Kebijakan Privasi</h1>
        <p style="font-size:12px;color:var(--ink-3);margin:0">Terakhir diperbarui: {{ lastUpdated }}</p>
      </div>

      <!-- Intro -->
      <div class="section">
        <p>Publishin ("kami", "kita", "layanan") berkomitmen melindungi privasi pengguna. Kebijakan ini menjelaskan data apa yang kami kumpulkan, bagaimana kami menggunakannya, dan hak-hak Anda sebagai pengguna.</p>
        <p>Dengan menggunakan Publishin, Anda menyetujui kebijakan ini. Jika tidak setuju, jangan gunakan layanan kami.</p>
      </div>

      <!-- 1 -->
      <div class="section">
        <h2>1. Data yang Kami Kumpulkan</h2>

        <h3>1.1 Data Akun</h3>
        <ul>
          <li>Nama, alamat email, dan kata sandi (dienkripsi)</li>
          <li>Zona waktu dan preferensi notifikasi</li>
          <li>Informasi langganan dan riwayat pembayaran</li>
        </ul>

        <h3>1.2 Data Platform Sosial Media</h3>
        <p>Saat Anda menghubungkan akun sosial media, kami mengakses dan menyimpan:</p>
        <ul>
          <li><strong>Instagram & Facebook (Meta):</strong> nama pengguna, foto profil, jumlah follower, access token OAuth, statistik post (reach, impressions, engagement), data halaman Facebook yang dikelola</li>
          <li><strong>TikTok:</strong> nama pengguna, foto profil, jumlah follower, access token OAuth, statistik video (views, likes, komentar, share), data akun bisnis</li>
          <li><strong>YouTube:</strong> nama channel, jumlah subscriber, access token OAuth, statistik video (views, likes, komentar)</li>
          <li><strong>Twitter/X:</strong> nama pengguna, jumlah follower, access token OAuth, statistik tweet</li>
        </ul>
        <p>Kami <strong>tidak pernah</strong> menyimpan kata sandi platform sosial media Anda. Akses dilakukan melalui OAuth resmi masing-masing platform.</p>

        <h3>1.3 Konten yang Anda Buat</h3>
        <ul>
          <li>Teks caption, hashtag, dan jadwal posting</li>
          <li>File media (foto, video) yang diunggah untuk dijadwalkan</li>
          <li>Draft dan riwayat post</li>
        </ul>

        <h3>1.4 Data Penggunaan</h3>
        <ul>
          <li>Log aktivitas (waktu login, halaman yang dikunjungi)</li>
          <li>Alamat IP dan informasi browser/perangkat</li>
          <li>Data performa fitur untuk peningkatan layanan</li>
        </ul>
      </div>

      <!-- 2 -->
      <div class="section">
        <h2>2. Bagaimana Kami Menggunakan Data</h2>
        <ul>
          <li><strong>Menjalankan layanan:</strong> menjadwalkan dan mempublikasikan konten ke platform yang Anda hubungkan</li>
          <li><strong>Analitik:</strong> menampilkan statistik performa konten dari platform sosial media Anda</li>
          <li><strong>Laporan:</strong> menghasilkan laporan performa yang Anda minta</li>
          <li><strong>Notifikasi:</strong> memberitahu Anda saat konten berhasil dipublikasikan atau gagal</li>
          <li><strong>AI:</strong> menggunakan caption dan konteks konten untuk menghasilkan saran AI (diproses oleh Anthropic Claude API — data tidak digunakan untuk melatih model)</li>
          <li><strong>Keamanan:</strong> mendeteksi dan mencegah akses tidak sah</li>
          <li><strong>Peningkatan produk:</strong> menganalisis pola penggunaan untuk memperbaiki fitur</li>
        </ul>
        <p>Kami <strong>tidak menjual</strong> data Anda kepada pihak ketiga.</p>
      </div>

      <!-- 3 -->
      <div class="section">
        <h2>3. Data Platform TikTok</h2>
        <p>Publishin menggunakan TikTok Login Kit dan Content Posting API. Secara spesifik:</p>
        <ul>
          <li><strong>Data yang diakses:</strong> profil dasar (<code>user.info.basic</code>), statistik akun (<code>user.info.stats</code>), daftar video (<code>video.list</code>), kemampuan upload dan publish video (<code>video.upload</code>, <code>video.publish</code>)</li>
          <li><strong>Tujuan akses:</strong> menjadwalkan dan mempublikasikan video TikTok atas nama Anda pada waktu yang telah ditentukan</li>
          <li><strong>Penyimpanan token:</strong> access token TikTok disimpan terenkripsi di database kami dan hanya digunakan untuk API call ke TikTok</li>
          <li><strong>File video:</strong> disimpan sementara di server kami selama proses upload ke TikTok, kemudian dihapus</li>
          <li><strong>Berbagi data:</strong> kami tidak berbagi data TikTok Anda dengan pihak ketiga manapun</li>
          <li><strong>Pencabutan akses:</strong> Anda dapat mencabut akses kapan saja melalui Pengaturan → Platform Terhubung, atau langsung di pengaturan privasi akun TikTok Anda</li>
        </ul>
      </div>

      <!-- 4 -->
      <div class="section">
        <h2>4. Data Platform Meta (Instagram & Facebook)</h2>
        <ul>
          <li><strong>Data yang diakses:</strong> profil Instagram, halaman Facebook yang dikelola, statistik post, kemampuan mempublikasikan konten</li>
          <li><strong>Tujuan:</strong> menjadwalkan dan mempublikasikan konten atas nama Anda</li>
          <li><strong>Token:</strong> disimpan terenkripsi, diperbarui otomatis sebelum kedaluwarsa</li>
          <li><strong>Pencabutan:</strong> melalui Pengaturan Publishin atau Meta Business Suite → Apps</li>
        </ul>
      </div>

      <!-- 5 -->
      <div class="section">
        <h2>5. Penyimpanan dan Keamanan Data</h2>
        <ul>
          <li>Data disimpan di server yang berlokasi di Indonesia</li>
          <li>Koneksi dienkripsi dengan TLS/HTTPS</li>
          <li>Kata sandi dienkripsi menggunakan bcrypt</li>
          <li>Access token platform dienkripsi sebelum disimpan di database</li>
          <li>File media dihapus dari server setelah berhasil dipublikasikan</li>
          <li>Akses ke data produksi dibatasi hanya pada tim yang memerlukannya</li>
        </ul>
      </div>

      <!-- 6 -->
      <div class="section">
        <h2>6. Berbagi Data dengan Pihak Ketiga</h2>
        <p>Kami hanya berbagi data dengan pihak ketiga dalam kondisi berikut:</p>
        <ul>
          <li><strong>Platform sosial media:</strong> data dikirim ke Instagram, TikTok, Facebook, YouTube, atau Twitter saat Anda mempublikasikan konten</li>
          <li><strong>Anthropic (Claude AI):</strong> teks caption dikirim untuk menghasilkan saran AI — Anthropic tidak menyimpan data ini untuk pelatihan model</li>
          <li><strong>Payment gateway:</strong> informasi pembayaran diproses oleh Midtrans/Xendit — kami tidak menyimpan data kartu</li>
          <li><strong>Hukum:</strong> jika diwajibkan oleh peraturan perundang-undangan Indonesia yang berlaku</li>
        </ul>
      </div>

      <!-- 7 -->
      <div class="section">
        <h2>7. Hak Pengguna</h2>
        <p>Anda berhak untuk:</p>
        <ul>
          <li><strong>Mengakses:</strong> meminta salinan data pribadi yang kami simpan</li>
          <li><strong>Memperbaiki:</strong> memperbarui data yang tidak akurat melalui Pengaturan → Profil</li>
          <li><strong>Menghapus:</strong> meminta penghapusan akun dan seluruh data terkait</li>
          <li><strong>Mencabut akses platform:</strong> memutuskan koneksi akun sosial media kapan saja</li>
          <li><strong>Portabilitas:</strong> meminta ekspor data dalam format yang dapat dibaca mesin</li>
        </ul>
        <p>Kirim permintaan ke <a href="mailto:privacy@publishin.id" style="color:var(--accent-b)">privacy@publishin.id</a>.</p>
      </div>

      <!-- 8 -->
      <div class="section">
        <h2>8. Cookies dan Penyimpanan Lokal</h2>
        <ul>
          <li>Kami menggunakan session cookie untuk autentikasi — wajib untuk layanan berfungsi</li>
          <li>Tidak ada cookie pelacakan pihak ketiga (Google Analytics, Facebook Pixel, dll.)</li>
          <li>Penyimpanan lokal browser digunakan untuk preferensi UI</li>
        </ul>
      </div>

      <!-- 9 -->
      <div class="section">
        <h2>9. Retensi Data</h2>
        <ul>
          <li>Data akun disimpan selama akun aktif</li>
          <li>Setelah penghapusan akun: data dihapus permanen dalam 30 hari</li>
          <li>Log sistem disimpan maksimal 90 hari</li>
          <li>Data analitik agregat (tanpa identitas) dapat disimpan lebih lama untuk peningkatan produk</li>
        </ul>
      </div>

      <!-- 10 -->
      <div class="section">
        <h2>10. Perubahan Kebijakan</h2>
        <p>Kami dapat memperbarui kebijakan ini sewaktu-waktu. Perubahan material akan diberitahukan melalui email dan notifikasi dalam aplikasi minimal 7 hari sebelum berlaku. Penggunaan layanan setelah tanggal efektif berarti Anda menyetujui perubahan tersebut.</p>
      </div>

      <!-- 11 -->
      <div class="section">
        <h2>11. Kontak</h2>
        <p>Pertanyaan atau keluhan terkait privasi:</p>
        <ul>
          <li>Email: <a href="mailto:privacy@publishin.id" style="color:var(--accent-b)">privacy@publishin.id</a></li>
          <li>Alamat: [Alamat kantor Publishin, Indonesia]</li>
        </ul>
      </div>

      <!-- Back link -->
      <div style="margin-top:64px;padding-top:32px;border-top:1px solid rgba(28,27,26,0.12)">
        <Link href="/" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-3);text-decoration:none">← Kembali ke Beranda</Link>
        <span style="margin:0 12px;color:var(--ink-3)">·</span>
        <Link href="/terms" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--accent-b);text-decoration:none">Syarat Layanan →</Link>
      </div>
    </div>

    <!-- Footer -->
    <footer style="border-top:1px solid rgba(28,27,26,0.12);padding:24px 40px;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--ink-3)">
      <span>© 2026 Publishin · Made with ♥ in Indonesia 🇮🇩</span>
      <div style="display:flex;gap:20px">
        <Link href="/privacy" style="color:var(--accent-b);text-decoration:none">Kebijakan Privasi</Link>
        <Link href="/terms" style="color:var(--ink-3);text-decoration:none">Syarat Layanan</Link>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.section {
  margin-bottom: 40px;
}
.section h2 {
  font-family: Georgia, serif;
  font-style: italic;
  font-weight: 700;
  font-size: 20px;
  margin: 0 0 16px;
  color: #1C1B1A;
}
.section h3 {
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .05em;
  margin: 20px 0 8px;
  color: #4A4845;
}
.section p {
  font-size: 14px;
  line-height: 1.7;
  color: #4A4845;
  margin: 0 0 12px;
}
.section ul {
  font-size: 14px;
  line-height: 1.7;
  color: #4A4845;
  margin: 0 0 12px;
  padding-left: 20px;
}
.section li {
  margin-bottom: 6px;
}
code {
  font-family: monospace;
  font-size: 12px;
  background: rgba(28,27,26,0.06);
  padding: 1px 5px;
  border-radius: 2px;
}
</style>
