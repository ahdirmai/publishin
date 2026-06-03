<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CalendarGrid from '@/Components/calendar/CalendarGrid.vue'
import WeeklyTable from '@/Components/calendar/WeeklyTable.vue'

interface CalendarPost {
  id: number
  title: string | null
  status: 'draft' | 'scheduled' | 'publishing' | 'published' | 'failed' | 'cancelled'
  scheduled_at: string | null
  published_at: string | null
  date: string | null
  time: string | null
  platforms: string[]
  caption_preview: string | null
}

const props = defineProps<{
  year: number
  month: number
  posts: CalendarPost[]
}>()

const currentYear = ref(props.year)
const currentMonth = ref(props.month)

const monthName = computed(() => {
  const d = new Date(currentYear.value, currentMonth.value - 1, 1)
  return d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })
})

function navigate(direction: 'prev' | 'next') {
  let y = currentYear.value
  let m = currentMonth.value
  if (direction === 'prev') {
    m--
    if (m < 1) { m = 12; y-- }
  } else {
    m++
    if (m > 12) { m = 1; y++ }
  }
  router.get(route('calendar'), { year: y, month: m }, { preserveScroll: true })
}

const weeklyPosts = computed(() => {
  const today = new Date()
  const dayOfWeek = today.getDay()
  const monday = new Date(today)
  monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1))
  const sunday = new Date(monday)
  sunday.setDate(monday.getDate() + 6)

  return props.posts.filter(p => {
    if (!p.date) return false
    const d = new Date(p.date)
    return d >= monday && d <= sunday
  }).sort((a, b) => (a.scheduled_at ?? '').localeCompare(b.scheduled_at ?? ''))
})

const weekLabel = computed(() => {
  const today = new Date()
  const dayOfWeek = today.getDay()
  const monday = new Date(today)
  monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1))
  const sunday = new Date(monday)
  sunday.setDate(monday.getDate() + 6)
  const fmt = (d: Date) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
  return `${fmt(monday)} – ${fmt(sunday)} ${today.getFullYear()}`
})
</script>

<template>
  <AppLayout title="Kalender" screen="calendar">
    <!-- Topbar row -->
    <div class="cal-topbar">
      <button class="btn" @click="navigate('prev')">← Prev</button>
      <div class="cal-month-title">{{ monthName }}</div>
      <button class="btn" @click="navigate('next')">Next →</button>
      <div style="flex: 1"></div>
      <Link :href="route('compose')" class="btn pri">+ Jadwalkan Post</Link>
    </div>

    <!-- Legend -->
    <div class="cal-legend">
      <span><span class="cdot pub"></span> Published</span>
      <span><span class="cdot sched"></span> Scheduled</span>
      <span><span class="cdot draft"></span> Draft</span>
    </div>

    <!-- Calendar grid -->
    <CalendarGrid :year="currentYear" :month="currentMonth" :posts="posts" />

    <!-- Weekly table -->
    <div class="card" style="margin-top: 14px">
      <div class="slabel" style="margin-bottom: 8px">Minggu Ini · {{ weekLabel }}</div>
      <WeeklyTable :posts="weeklyPosts" />
    </div>
  </AppLayout>
</template>

<style>
.cal-topbar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.cal-month-title {
  font-family: var(--f-disp);
  font-style: italic;
  font-weight: 700;
  font-size: 18px;
  letter-spacing: -0.5px;
}
.btn {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border: var(--bdr-m);
  border-radius: 2px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  background: var(--paper);
  cursor: pointer;
  font-family: var(--f-sans);
  text-decoration: none;
  color: var(--ink);
}
.btn.pri {
  background: var(--ink);
  color: #fff;
  border-color: var(--ink);
}
.cal-legend {
  display: flex;
  gap: 14px;
  margin-bottom: 8px;
  font-size: 10px;
  flex-wrap: wrap;
}
.cdot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: 1.5px solid;
  margin-right: 3px;
  vertical-align: middle;
}
.cdot.pub {
  background: var(--green);
  border-color: var(--green);
}
.cdot.sched {
  background: var(--accent-b);
  border-color: var(--accent-b);
}
.cdot.draft {
  background: transparent;
  border-color: var(--ink-3);
  border-style: dashed;
}
.card {
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  padding: 16px;
  background: #fff;
}
.slabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--ink-3);
}
</style>
