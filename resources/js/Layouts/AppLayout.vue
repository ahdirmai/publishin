<script setup lang="ts">
import { ref } from 'vue'
import AppSidebar from '@/Components/layout/AppSidebar.vue'
import AppTopbar from '@/Components/layout/AppTopbar.vue'
import StickyNote from '@/Components/layout/StickyNote.vue'

defineProps<{
    title?: string
    screen?: string
}>()

const sidebarOpen = ref(false)
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
        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-ink/50 z-40 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 lg:static lg:z-auto transition-transform duration-200"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <AppSidebar :screen="screen" @close="sidebarOpen = false" />
        </div>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 overflow-hidden min-w-0">
            <AppTopbar :title="title" @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            <!-- Sticky note annotation -->
            <StickyNote v-if="screen" :screen="screen" />

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
