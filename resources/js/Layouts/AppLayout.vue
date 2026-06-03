<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppSidebar from '@/Components/layout/AppSidebar.vue'
import AppTopbar from '@/Components/layout/AppTopbar.vue'
import StickyNote from '@/Components/layout/StickyNote.vue'
import { useStickyNote } from '@/composables/useStickyNote'

defineProps<{
    title?: string
    screen?: string
}>()

const page = usePage()
</script>

<template>
    <!-- SVG Hatch Pattern Defs — global, used by all charts -->
    <svg width="0" height="0" style="position:absolute">
        <defs>
            <pattern id="h-r" x="0" y="0" width="4" height="4" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="4" stroke="#C96442" stroke-width="1.5"/>
            </pattern>
            <pattern id="h-b" x="0" y="0" width="4" height="4" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="4" stroke="#3B6DB5" stroke-width="1.5"/>
            </pattern>
            <pattern id="h-g" x="0" y="0" width="4" height="4" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="4" stroke="#2A7A4B" stroke-width="1.5"/>
            </pattern>
            <pattern id="h-k" x="0" y="0" width="4" height="4" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="4" stroke="#1C1B1A" stroke-width="1.5"/>
            </pattern>
        </defs>
    </svg>

    <div class="flex h-screen overflow-hidden grid-paper">
        <!-- Sidebar -->
        <AppSidebar :screen="screen" />

        <!-- Main content area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <AppTopbar :title="title" />

            <!-- Sticky note annotation -->
            <StickyNote v-if="screen" :screen="screen" />

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
