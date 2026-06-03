<script setup lang="ts">
import { useStickyNote } from '@/composables/useStickyNote'

const props = defineProps<{ screen: string }>()
const note = useStickyNote(props.screen)
</script>

<template>
    <div v-if="note"
        class="absolute bottom-8 right-8 bg-sticky border border-[rgba(28,27,26,0.15)] px-3 py-2 text-[11px] text-ink-2 font-sans shadow-sm pointer-events-none z-10"
        style="border-radius: 2px; transform: rotate(-1.2deg); max-width: 160px">
        {{ note }}
    </div>
</template>
