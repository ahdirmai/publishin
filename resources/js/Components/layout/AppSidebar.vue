<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{ screen?: string }>()
defineEmits<{ (e: 'close'): void }>()

const navItems = [
    { name: 'Dashboard',   href: '/dashboard',       screen: 'dashboard',  badge: null },
    { name: 'Kalender',    href: '/calendar',         screen: 'calendar',   badge: '3' },
    { name: 'Buat Konten', href: '/compose',          screen: 'compose',    badge: null },
    { name: 'Analytics',   href: '/analytics',        screen: 'analytics',  badge: null },
    { name: 'Laporan',     href: '/reports',          screen: 'reports',    badge: null },
    { name: 'Pengaturan',  href: '/settings',         screen: 'settings',   badge: null },
]
</script>

<template>
    <aside class="w-48 h-full flex-shrink-0 border-r border-[rgba(28,27,26,0.22)] bg-paper flex flex-col">
        <!-- Logo + close button -->
        <div class="px-4 pt-5 pb-3 border-b border-[rgba(28,27,26,0.22)] flex items-start justify-between">
            <div>
                <div class="font-serif italic font-bold text-2xl text-ink leading-none">P·</div>
                <div class="text-[9px] uppercase tracking-widest text-ink-3 mt-0.5">Pro Plan</div>
            </div>
            <button
                class="lg:hidden text-ink-3 hover:text-ink p-0.5 mt-0.5"
                @click="$emit('close')"
                aria-label="Tutup menu"
            >✕</button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 py-3 space-y-0.5 px-2">
            <Link
                v-for="item in navItems"
                :key="item.screen"
                :href="item.href"
                class="flex items-center gap-2 px-2 py-1.5 text-xs font-sans transition-colors"
                :class="screen === item.screen
                    ? 'bg-accent-r/10 border-l-2 border-accent-r text-accent-r font-semibold'
                    : 'text-ink-2 hover:text-ink hover:bg-ink/5'"
                style="border-radius: 2px"
                @click="$emit('close')"
            >
                <span class="text-[10px] w-3 shrink-0">
                    {{ screen === item.screen ? '✓' : '○' }}
                </span>
                <span class="truncate">{{ item.name }}</span>
                <span v-if="item.badge"
                    class="ml-auto text-[9px] bg-ink text-paper rounded-full px-1.5 py-0.5 font-bold">
                    {{ item.badge }}
                </span>
            </Link>
        </nav>

        <!-- Bottom user info -->
        <div class="px-4 py-3 border-t border-[rgba(28,27,26,0.22)]">
            <div class="text-[10px] text-ink-3 truncate">{{ $page.props.auth.user?.name }}</div>
        </div>
    </aside>
</template>
