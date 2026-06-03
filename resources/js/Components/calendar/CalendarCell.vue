<script setup lang="ts">
defineProps<{
  day: number
  isCurrentMonth: boolean
  isToday: boolean
  date: string
  posts: Array<{ id: number; title: string | null; status: string; platforms: string[] }>
}>()

function statusDotClass(status: string): string {
  if (status === 'published') return 'pub'
  if (status === 'scheduled' || status === 'publishing') return 'sched'
  return 'draft'
}
</script>

<template>
  <div
    class="cal-cell"
    :class="{
      'other-month': !isCurrentMonth,
      'today': isToday,
    }"
  >
    <div class="cell-day">{{ day }}</div>
    <div class="cell-dots">
      <span
        v-for="post in posts.slice(0, 3)"
        :key="post.id"
        class="cdot"
        :class="statusDotClass(post.status)"
        :title="post.title ?? ''"
      ></span>
      <span v-if="posts.length > 3" class="cell-more">+{{ posts.length - 3 }}</span>
    </div>
    <div v-if="posts.length > 0" class="cell-label">
      {{ (posts[0].title ?? posts[0].platforms[0] ?? '').slice(0, 14) }}
    </div>
  </div>
</template>

<style>
.cal-cell {
  min-height: 64px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  border-right: 1px solid rgba(0, 0, 0, 0.06);
  padding: 4px 5px;
  font-size: 10px;
  font-variant-numeric: tabular-nums;
}
.cal-cell:nth-child(7n) {
  border-right: none;
}
.cal-cell.other-month {
  background: #fafaf8;
}
.cal-cell.other-month .cell-day {
  color: var(--ink-3);
}
.cal-cell.today {
  background: rgba(201, 100, 66, 0.06);
}
.cal-cell.today .cell-day {
  color: var(--accent-r);
  font-weight: 700;
  border: 1.5px solid var(--accent-r);
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.cell-day {
  font-size: 10px;
  margin-bottom: 3px;
}
.cell-dots {
  display: flex;
  gap: 2px;
  flex-wrap: wrap;
  margin-bottom: 2px;
}
.cdot {
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  border: 1.5px solid;
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
.cell-more {
  font-size: 8px;
  color: var(--ink-3);
}
.cell-label {
  font-size: 9px;
  color: var(--ink-2);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
</style>
