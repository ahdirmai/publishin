<script setup lang="ts">
import { computed } from 'vue'
import CalendarCell from '@/Components/calendar/CalendarCell.vue'

interface CalendarPost {
  id: number
  title: string | null
  status: string
  date: string | null
  time: string | null
  platforms: string[]
}

const props = defineProps<{
  year: number
  month: number
  posts: CalendarPost[]
}>()

interface Cell {
  day: number
  isCurrentMonth: boolean
  date: string
  isToday: boolean
}

const cells = computed<Cell[]>(() => {
  const firstDay = new Date(props.year, props.month - 1, 1)
  const startDow = firstDay.getDay() // 0=Sun
  const offset = (startDow + 6) % 7  // Monday-based offset

  const daysInM = new Date(props.year, props.month, 0).getDate()
  const prevMonthDays = new Date(props.year, props.month - 1, 0).getDate()

  const todayStr = new Date().toISOString().slice(0, 10)

  const result: Cell[] = []

  // Previous month trailing days
  for (let i = offset - 1; i >= 0; i--) {
    const day = prevMonthDays - i
    let y = props.year
    let m = props.month - 1
    if (m < 1) { m = 12; y-- }
    const date = `${y}-${String(m).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    result.push({ day, isCurrentMonth: false, date, isToday: date === todayStr })
  }

  // Current month days
  for (let day = 1; day <= daysInM; day++) {
    const date = `${props.year}-${String(props.month).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    result.push({ day, isCurrentMonth: true, date, isToday: date === todayStr })
  }

  // Next month leading days to fill 42 cells
  let nextDay = 1
  let y = props.year
  let m = props.month + 1
  if (m > 12) { m = 1; y++ }
  while (result.length < 42) {
    const date = `${y}-${String(m).padStart(2, '0')}-${String(nextDay).padStart(2, '0')}`
    result.push({ day: nextDay, isCurrentMonth: false, date, isToday: date === todayStr })
    nextDay++
  }

  return result
})

const postsByDate = computed<Record<string, CalendarPost[]>>(() => {
  const map: Record<string, CalendarPost[]> = {}
  for (const post of props.posts) {
    if (!post.date) continue
    if (!map[post.date]) map[post.date] = []
    map[post.date].push(post)
  }
  return map
})
</script>

<template>
  <div class="cal-wrap">
    <div class="cal-grid">
      <!-- Day-of-week headers -->
      <div
        v-for="dow in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']"
        :key="dow"
        class="cal-dow"
      >
        {{ dow }}
      </div>

      <!-- 42 day cells -->
      <CalendarCell
        v-for="cell in cells"
        :key="cell.date"
        :day="cell.day"
        :is-current-month="cell.isCurrentMonth"
        :is-today="cell.isToday"
        :date="cell.date"
        :posts="postsByDate[cell.date] ?? []"
      />
    </div>
  </div>
</template>

<style>
.cal-wrap {
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  overflow: hidden;
  background: #fff;
}
.cal-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}
.cal-dow {
  background: #f4f3f0;
  border-bottom: var(--bdr);
  border-right: var(--bdr-m);
  padding: 5px 6px;
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--ink-3);
  text-align: center;
}
.cal-dow:last-child {
  border-right: none;
}
</style>
