<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

const lastUpdated = '3 Juni 2026'
</script>

<template>
  <Head title="Syarat Layanan — Publishin" />

  <div style="--paper:#FAFAF8;--ink:#1C1B1A;--ink-2:#4A4845;--ink-3:#8A8784;--accent-b:#3B6DB5;--accent-r:#C96442;min-height:100vh;background:var(--paper);font-family:'DM Sans',sans-serif;color:var(--ink)">

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
        <h1 style="font-family:Georgia,serif;font-style:italic;font-weight:700;font-size:36px;line-height:1.15;margin:0 0 12px">Syarat Layanan</h1>
        <p style="font-size:12px;color:var(--ink-3);margin:0">Terakhir diperbarui: {{ lastUpdated }}</p>
      </div>

      <!-- Intro -->
      <div class="section">
        <p>Syarat layanan ini mengatur penggunaan platform Publishin yang dioperasikan oleh PT Publishin Indonesia ("Publishin", "kami"). Dengan mendaftar atau menggunakan layanan, Anda menyetujui syarat ini.</p>
        <p>Jika tidak setuju, hentikan penggunaan layanan.</p>
      </div>

      <!-- 1 -->
      <div class="section">
        <h2>1. Deskripsi Layanan</h2>
        <p>Publishin adalah platform manajemen sosial media yang memungkinkan pengguna untuk:</p>
        <ul>
          <li>Menjadwalkan dan mempublikasikan konten ke Instagram, TikTok, Facebook, YouTube, dan Twitter/X</li>
          <li>Melihat analitik performa konten dari platform yang terhubung</li>
          <li>Menghasilkan laporan performa untuk klien</li>
          <li>Menggunakan fitur AI untuk saran caption dan hashtag</li>
        </ul>
      </div>

      <!-- 2 -->
      <div class="section">
        <h2>2. Akun Pengguna</h2>
        <ul>
          <li>Anda harus berusia minimal 18 tahun untuk menggunakan layanan</li>
          <li>Anda bertanggung jawab menjaga keamanan kata sandi akun</li>
          <li>Satu akun untuk satu individu atau entitas bisnis — tidak boleh dibagikan</li>
          <li>Beritahu kami segera jika ada akses tidak sah: <a href="mailto:security@publishin.id" style="color:var(--accent-b)">security@publishin.id</a></li>
          <li>Kami berhak menangguhkan akun yang melanggar syarat ini</li>
        </ul>
      </div>

      <!-- 3 -->
      <div class="section">
        <h2>3. Langganan dan Pembayaran</h2>

        <h3>3.1 Plan dan Harga</h3>
        <ul>
          <li>Layanan tersedia dalam beberapa plan berbayar (Starter, Pro, Agency)</li>
          <li>Harga dalam Rupiah (IDR), sudah termasuk PPN 11%</li>
          <li>Harga dapat berubah dengan pemberitahuan 30 hari sebelumnya</li>
        </ul>

        <h3>3.2 Penagihan</h3>
        <ul>
          <li>Langganan ditagih di awal periode (bulanan atau tahunan)</li>
          <li>Pembayaran diproses melalui Midtrans atau Xendit</li>
          <li>Gagal bayar akan menangguhkan akses fitur premium setelah grace period 7 hari</li>
        </ul>

        <h3>3.3 Pembatalan dan Pengembalian Dana</h3>
        <ul>
          <li>Anda dapat membatalkan langganan kapan saja — akses tetap aktif hingga akhir periode</li>
          <li>Tidak ada pengembalian dana untuk periode yang sudah berjalan, kecuali ada kegagalan layanan dari pihak kami</li>
          <li>Klaim pengembalian dana: <a href="mailto:billing@publishin.id" style="color:var(--accent-b)">billing@publishin.id</a> dalam 7 hari setelah tagihan</li>
        </ul>
      </div>

      <!-- 4 -->
      <div class="section">
        <h2>4. Penggunaan yang Diizinkan</h2>
        <p>Anda boleh menggunakan Publishin untuk:</p>
        <ul>
          <li>Menjadwalkan konten orisinal milik Anda atau klien yang Anda kelola</li>
          <li>Menganalisis performa akun sosial media Anda sendiri</li>
          <li>Membuat laporan untuk klien dalam konteks layanan profesional</li>
        </ul>
      </div>

      <!-- 5 -->
      <div class="section">
        <h2>5. Penggunaan yang Dilarang</h2>
        <p>Anda dilarang menggunakan Publishin untuk:</p>
        <ul>
          <li>Mempublikasikan konten yang melanggar hak cipta, merek dagang, atau hak kekayaan intelektual pihak lain</li>
          <li>Menyebarkan spam, konten menyesatkan, atau informasi palsu</li>
          <li>Melanggar kebijakan komunitas atau syarat layanan platform sosial media yang terhubung (Instagram, TikTok, Facebook, YouTube, Twitter/X)</li>
          <li>Mengakses akun platform sosial media yang tidak Anda miliki atau tidak Anda kelola secara sah</li>
          <li>Melakukan reverse engineering, scraping, atau ekstraksi data dari Publishin</li>
          <li>Menggunakan layanan untuk aktivitas ilegal berdasarkan hukum Indonesia</li>
          <li>Menyebarkan konten yang mengandung SARA, kebencian, atau kekerasan</li>
          <li>Mengotomatiskan tindakan yang melanggar rate limit atau kebijakan platform sosial media</li>
        </ul>
        <p>Pelanggaran dapat mengakibatkan penangguhan atau penghapusan akun tanpa pengembalian dana.</p>
      </div>

      <!-- 6 -->
      <div class="section">
        <h2>6. Konten Pengguna</h2>
        <ul>
          <li>Anda memiliki dan bertanggung jawab atas semua konten yang Anda unggah dan publikasikan melalui Publishin</li>
          <li>Dengan menggunakan layanan, Anda memberikan kami lisensi terbatas untuk menyimpan dan memproses konten tersebut semata-mata untuk menjalankan layanan</li>
          <li>Kami tidak mengklaim kepemilikan atas konten Anda</li>
          <li>Publishin tidak bertanggung jawab atas konten yang Anda publikasikan</li>
        </ul>
      </div>

      <!-- 7 -->
      <div class="section">
        <h2>7. Integrasi Platform Pihak Ketiga</h2>
        <p>Publishin terintegrasi dengan platform sosial media melalui API resmi. Dengan menghubungkan akun platform:</p>
        <ul>
          <li>Anda menyetujui syarat layanan platform tersebut (Meta, TikTok, Google, Twitter/X)</li>
          <li>Anda memberikan Publishin izin untuk bertindak atas nama Anda sesuai scope yang disetujui saat OAuth</li>
          <li>Anda dapat mencabut akses kapan saja melalui Pengaturan Publishin</li>
          <li>Publishin tidak bertanggung jawab atas perubahan API, pembatasan, atau penghentian layanan oleh platform pihak ketiga</li>
        </ul>
      </div>

      <!-- 8 -->
      <div class="section">
        <h2>8. Ketersediaan Layanan</h2>
        <ul>
          <li>Kami berupaya menjaga uptime 99.5% per bulan</li>
          <li>Pemeliharaan terjadwal akan diberitahukan minimal 24 jam sebelumnya</li>
          <li>Kami tidak bertanggung jawab atas kegagalan publikasi yang disebabkan oleh pemadaman atau perubahan API platform sosial media</li>
          <li>Notifikasi gangguan tersedia di [status page — akan segera dibuat]</li>
        </ul>
      </div>

      <!-- 9 -->
      <div class="section">
        <h2>9. Batasan Tanggung Jawab</h2>
        <p>Sejauh diizinkan oleh hukum yang berlaku:</p>
        <ul>
          <li>Publishin tidak bertanggung jawab atas kerugian tidak langsung, kehilangan data, atau kerugian bisnis akibat penggunaan layanan</li>
          <li>Total tanggung jawab kami tidak melebihi jumlah yang Anda bayarkan dalam 3 bulan terakhir</li>
          <li>Layanan disediakan "sebagaimana adanya" tanpa jaminan tersirat</li>
        </ul>
      </div>

      <!-- 10 -->
      <div class="section">
        <h2>10. Penghentian Layanan</h2>
        <ul>
          <li><strong>Oleh Anda:</strong> hapus akun kapan saja melalui Pengaturan → Hapus Akun</li>
          <li><strong>Oleh kami:</strong> kami berhak menghentikan akun yang melanggar syarat ini, dengan atau tanpa pemberitahuan tergantung tingkat pelanggaran</li>
          <li>Setelah penghentian: data dihapus permanen dalam 30 hari sesuai Kebijakan Privasi</li>
        </ul>
      </div>

      <!-- 11 -->
      <div class="section">
        <h2>11. Kekayaan Intelektual</h2>
        <ul>
          <li>Platform Publishin, desain, dan kode sumber adalah milik PT Publishin Indonesia</li>
          <li>Anda tidak boleh menyalin, memodifikasi, atau mendistribusikan komponen Publishin tanpa izin tertulis</li>
          <li>Nama dan logo "Publishin" adalah merek dagang kami</li>
        </ul>
      </div>

      <!-- 12 -->
      <div class="section">
        <h2>12. Perubahan Syarat</h2>
        <p>Kami dapat memperbarui syarat ini. Perubahan material akan diberitahukan melalui email dan notifikasi dalam aplikasi minimal 14 hari sebelum berlaku. Penggunaan layanan setelah tanggal efektif berarti Anda menyetujui perubahan.</p>
      </div>

      <!-- 13 -->
      <div class="section">
        <h2>13. Hukum yang Berlaku</h2>
        <p>Syarat ini diatur oleh hukum Republik Indonesia. Sengketa diselesaikan melalui Badan Arbitrase Nasional Indonesia (BANI) atau pengadilan yang berwenang di Indonesia.</p>
      </div>

      <!-- 14 -->
      <div class="section">
        <h2>14. Kontak</h2>
        <p>Pertanyaan terkait syarat layanan:</p>
        <ul>
          <li>Email: <a href="mailto:legal@publishin.id" style="color:var(--accent-b)">legal@publishin.id</a></li>
          <li>Alamat: [Alamat kantor Publishin, Indonesia]</li>
        </ul>
      </div>

      <!-- Back link -->
      <div style="margin-top:64px;padding-top:32px;border-top:1px solid rgba(28,27,26,0.12)">
        <Link href="/" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-3);text-decoration:none">← Kembali ke Beranda</Link>
        <span style="margin:0 12px;color:var(--ink-3)">·</span>
        <Link href="/privacy" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--accent-b);text-decoration:none">Kebijakan Privasi →</Link>
      </div>
    </div>

    <!-- Footer -->
    <footer style="border-top:1px solid rgba(28,27,26,0.12);padding:24px 40px;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--ink-3)">
      <span>© 2026 Publishin · Made with ♥ in Indonesia 🇮🇩</span>
      <div style="display:flex;gap:20px">
        <Link href="/privacy" style="color:var(--ink-3);text-decoration:none">Kebijakan Privasi</Link>
        <Link href="/terms" style="color:var(--accent-b);text-decoration:none">Syarat Layanan</Link>
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
</style>
