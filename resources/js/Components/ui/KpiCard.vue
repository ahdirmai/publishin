<script setup lang="ts">
withDefaults(defineProps<{
    label: string
    value: string | number
    delta?: string          // e.g. "↑ +1.2K" or "↓ −6%"
    deltaPositive?: boolean // true = green, false = red, undefined = neutral
    subtext?: string        // e.g. "industri avg: 2.3%"
}>(), {})
</script>

<template>
    <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius: 2px">
        <div class="label-upper text-ink-3 mb-2">{{ label }}</div>
        <div class="kpi-value text-ink leading-none mb-1">{{ value }}</div>
        <div v-if="delta || subtext" class="flex items-center gap-2 mt-1">
            <span
                v-if="delta"
                class="text-[10px] font-sans font-semibold"
                :class="{
                    'text-forest': deltaPositive === true,
                    'text-accent-r': deltaPositive === false,
                    'text-ink-3': deltaPositive === undefined,
                }"
            >{{ delta }}</span>
            <span v-if="subtext" class="text-[9px] text-ink-3">{{ subtext }}</span>
        </div>
    </div>
</template>
