<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps<{
    kpi: {
        total_followers: number
        scheduled_posts: number
        engagement_rate: number
        reach_30d: number
    }
}>()
</script>

<template>
    <AppLayout title="Dashboard" screen="dashboard">
        <!-- KPI Cards placeholder — Phase 3 akan inject data real -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div v-for="i in 4" :key="i"
                class="bg-paper border-[1.5px] border-ink p-4"
                style="border-radius: 2px">
                <div class="label-upper text-ink-3 mb-2">KPI</div>
                <div class="kpi-value text-ink">—</div>
            </div>
        </div>
        <div class="text-sm text-ink-3 font-sans">
            Phase 3 — Analytics akan implement dashboard penuh.
        </div>
    </AppLayout>
</template>
