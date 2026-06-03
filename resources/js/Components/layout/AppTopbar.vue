<script setup lang="ts">
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

defineProps<{ title?: string }>()

const page = usePage()
const notifCount = computed(() => (page.props.notif_count as number) ?? 0)

function logout() {
    router.post(route('logout'))
}
</script>

<template>
    <header class="flex items-center justify-between px-6 py-3 border-b border-[rgba(28,27,26,0.22)] bg-paper shrink-0">
        <h1 class="font-serif italic font-bold text-lg text-ink">{{ title }}</h1>
        <div class="flex items-center gap-3">
            <button
                class="text-[10px] uppercase tracking-wide text-ink-2 border border-[rgba(28,27,26,0.22)] px-2.5 py-1 hover:border-ink transition-colors"
                style="border-radius: 2px">
                🔔
                <span v-if="notifCount > 0" class="ml-1 text-accent-r font-bold">{{ notifCount }}</span>
            </button>
            <a href="/compose"
                class="text-[10px] uppercase tracking-wide font-semibold bg-ink text-paper border-[1.5px] border-ink px-3 py-1.5 hover:bg-accent-r hover:border-accent-r transition-colors"
                style="border-radius: 2px">
                + Buat Konten
            </a>
        </div>
    </header>
</template>
