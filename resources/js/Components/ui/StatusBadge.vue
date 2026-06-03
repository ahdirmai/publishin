<script setup lang="ts">
type Status = 'published' | 'scheduled' | 'draft' | 'failed' | 'publishing' | 'cancelled'

withDefaults(defineProps<{
    status: Status
}>(), {})

const config: Record<Status, { label: string; style: string }> = {
    published:  { label: 'Dipublikasi',  style: 'color: #2A7A4B; border-color: #2A7A4B; background: rgba(42,122,75,0.08)' },
    scheduled:  { label: 'Terjadwal',    style: 'color: #3B6DB5; border-color: #3B6DB5; background: rgba(59,109,181,0.08)' },
    draft:      { label: 'Draft',        style: 'color: #928E89; border-color: rgba(28,27,26,0.22); background: transparent' },
    failed:     { label: 'Gagal',        style: 'color: #C96442; border-color: #C96442; background: rgba(201,100,66,0.08)' },
    publishing: { label: 'Publishing…', style: 'color: #3B6DB5; border-color: #3B6DB5; background: rgba(59,109,181,0.08)' },
    cancelled:  { label: 'Dibatalkan',  style: 'color: #928E89; border-color: rgba(28,27,26,0.22); background: transparent' },
}
</script>

<template>
    <span
        class="inline-block font-sans font-semibold uppercase"
        style="font-size: 8px; letter-spacing: 0.06em; padding: 2px 6px; border-width: 1px; border-style: solid; border-radius: 2px;"
        :style="config[status].style"
    >
        {{ config[status].label }}
    </span>
</template>
