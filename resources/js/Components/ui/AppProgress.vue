<script setup lang="ts">
withDefaults(defineProps<{
    value: number        // 0–100
    color?: 'ink' | 'accent-r' | 'accent-b' | 'forest'
    showLabel?: boolean
}>(), {
    color: 'ink',
    showLabel: false,
})

const colorMap: Record<string, string> = {
    ink: '#1C1B1A',
    'accent-r': '#C96442',
    'accent-b': '#3B6DB5',
    forest: '#2A7A4B',
}
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex-1 bg-[#EEEDE9]" style="height: 5px; border-radius: 2px; overflow: hidden;">
            <div
                class="h-full transition-all"
                :style="{
                    width: `${Math.min(100, Math.max(0, value))}%`,
                    backgroundColor: colorMap[color] ?? colorMap.ink,
                    borderRadius: '2px',
                }"
            />
        </div>
        <span v-if="showLabel" class="label-upper text-ink-3 shrink-0">{{ value }}%</span>
    </div>
</template>
