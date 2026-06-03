<script setup lang="ts">
interface CalendarPost {
  id: number
  title: string | null
  status: string
  date: string | null
  time: string | null
  platforms: string[]
  caption_preview?: string | null
}

defineProps<{
  posts: CalendarPost[]
}>()

const DAY_NAMES = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
const MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des']

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return `${DAY_NAMES[d.getDay()]} ${d.getDate()} ${MONTH_NAMES[d.getMonth()]}`
}

function platformLabel(plt: string): string {
  const map: Record<string, string> = {
    instagram: 'IG',
    facebook: 'FB',
    tiktok: 'TT',
    twitter: 'X',
    youtube: 'YT',
  }
  return map[plt] ?? plt.toUpperCase().slice(0, 2)
}

function statusLabel(status: string): string {
  const map: Record<string, string> = {
    draft: 'Draft',
    scheduled: 'Terjadwal',
    publishing: 'Mengirim',
    published: 'Tayang',
    failed: 'Gagal',
    cancelled: 'Dibatalkan',
  }
  return map[status] ?? status
}
</script>

<template>
  <div>
    <div
      v-if="posts.length === 0"
      style="font-size: 11px; color: var(--ink-3); padding: 12px 0"
    >
      Tidak ada konten minggu ini.
    </div>

    <table v-else class="tbl">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Konten</th>
          <th>Platform</th>
          <th>Waktu</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id">
          <td>{{ formatDate(post.date) }}</td>
          <td>{{ post.title ?? post.caption_preview ?? '—' }}</td>
          <td>
            <span
              v-for="plt in post.platforms"
              :key="plt"
              class="plt"
              :class="plt"
            >{{ platformLabel(plt) }}</span>
          </td>
          <td>{{ post.time ?? '—' }}</td>
          <td>
            <span class="stat" :class="post.status">{{ statusLabel(post.status) }}</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style>
.tbl {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
}
.tbl th {
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--ink-3);
  padding: 5px 6px;
  border-bottom: 1.5px solid var(--ink);
  text-align: left;
}
.tbl td {
  padding: 7px 6px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  color: var(--ink-2);
  font-variant-numeric: tabular-nums;
}
.tbl tr:last-child td {
  border-bottom: none;
}
.plt {
  display: inline-flex;
  padding: 1px 5px;
  border-radius: 2px;
  font-size: 8px;
  font-weight: 700;
  border: 1px solid currentColor;
  margin-right: 3px;
}
.plt.instagram {
  color: #C13584;
}
.plt.facebook {
  color: #1877F2;
}
.plt.tiktok {
  color: #111;
}
.plt.twitter {
  color: #1D9BF0;
}
.plt.youtube {
  color: #FF0000;
}
.stat {
  display: inline-flex;
  padding: 1px 6px;
  border-radius: 2px;
  font-size: 8px;
  font-weight: 700;
}
.stat.published {
  background: rgba(42, 122, 75, 0.09);
  color: var(--green);
  border: 1px solid var(--green);
}
.stat.scheduled {
  background: rgba(59, 109, 181, 0.09);
  color: var(--accent-b);
  border: 1px solid var(--accent-b);
}
.stat.draft {
  background: rgba(0, 0, 0, 0.04);
  color: var(--ink-3);
  border: 1px solid rgba(0, 0, 0, 0.15);
}
.stat.publishing {
  background: rgba(59, 109, 181, 0.06);
  color: var(--accent-b);
  border: 1px dashed var(--accent-b);
}
.stat.failed {
  background: rgba(201, 100, 66, 0.09);
  color: var(--accent-r);
  border: 1px solid var(--accent-r);
}
.stat.cancelled {
  background: rgba(0, 0, 0, 0.04);
  color: var(--ink-3);
  border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
