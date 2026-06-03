<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'
import StatusBadge from '@/Components/ui/StatusBadge.vue'

function csrfToken(): string {
    return (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? ''
}

interface PostItem {
    id: number
    post_version_id: number
    title: string
    platform: string
    content_type: string
    published_at: string
    reach: number
    impressions: number
    likes: number
    comments: number
    shares: number
    saves: number
    engagement_rate: number
    status: string
    thumbnail_url: string | null
}

interface PaginatedData {
    data: PostItem[]
    total: number
    current_page: number
    last_page: number
    per_page: number
}

const props = defineProps<{
    data: PaginatedData
    filters: { sort?: string; type?: string; platform?: string; page?: number }
}>()

// ─── Expand state ─────────────────────────────────────────────────────────────

const expandedIds = ref<Set<number>>(new Set())
function toggleExpand(id: number) {
    if (expandedIds.value.has(id)) expandedIds.value.delete(id)
    else expandedIds.value.add(id)
}

// ─── Filter helpers ───────────────────────────────────────────────────────────

function applyFilter(overrides: Record<string, string | number>) {
    router.get(route('analytics.per-konten'), {
        ...props.filters,
        ...overrides,
        page: 1,
    }, { preserveState: true, replace: true })
}

function goPage(page: number) {
    router.get(route('analytics.per-konten'), { ...props.filters, page }, { preserveState: true })
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function fmtNum(n: number): string {
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
    if (n >= 1_000) return (n / 1_000).toFixed(1) + 'K'
    return n.toString()
}

function erClass(er: number): string {
    if (er >= 7) return 'text-forest font-bold'
    if (er >= 4) return 'text-accent-b font-bold'
    return 'text-ink-3'
}

function contentTypeLabel(t: string): string {
    const map: Record<string, string> = {
        carousel: 'Carousel', reels: 'Reels', video: 'Video',
        thread: 'Thread', photo: 'Foto', static: 'Foto',
    }
    return map[t.toLowerCase()] ?? t
}

// ─── Sync ─────────────────────────────────────────────────────────────────────

const syncingAll  = ref(false)
const syncMsg     = ref<string | null>(null)
const syncingRows = ref<Set<number>>(new Set())

async function syncAll() {
    syncingAll.value = true
    syncMsg.value    = null
    try {
        const res  = await fetch(route('api.analytics.sync'), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
        })
        const data = await res.json()
        syncMsg.value = data.message
        router.reload({ only: ['data'] })
    } catch {
        syncMsg.value = 'Sinkronisasi gagal.'
    } finally {
        syncingAll.value = false
    }
}

async function syncOne(postVersionId: number) {
    syncingRows.value.add(postVersionId)
    try {
        const res  = await fetch(route('api.analytics.sync-one', postVersionId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
        })
        const data = await res.json()
        syncMsg.value = data.message
        router.reload({ only: ['data'] })
    } catch {
        syncMsg.value = 'Sinkronisasi gagal.'
    } finally {
        syncingRows.value.delete(postVersionId)
    }
}

const sortOptions = [
    { value: 'reach', label: 'Reach ↓' },
    { value: 'engagement_rate', label: 'Eng. Rate ↓' },
    { value: 'impressions', label: 'Impresi ↓' },
    { value: 'published_at', label: 'Terbaru' },
]

const typeOptions = [
    { value: '', label: 'Semua Tipe' },
    { value: 'carousel', label: 'Carousel' },
    { value: 'reels', label: 'Reels' },
    { value: 'video', label: 'Video' },
    { value: 'thread', label: 'Thread' },
    { value: 'photo', label: 'Foto' },
]
</script>

<template>
    <AppLayout title="Analytics" screen="analytics">

        <!-- Sub-tabs -->
        <nav class="flex gap-0 mb-4 border-b border-ink-3/30">
            <button
                class="px-4 py-2 text-xs font-sans font-semibold border-b-2 border-transparent text-ink-3 hover:text-ink transition-colors"
                @click="$inertia.visit(route('analytics'))">
                Overview
            </button>
            <button class="px-4 py-2 text-xs font-sans font-semibold border-b-2 border-accent-r text-accent-r">
                Per Konten
            </button>
        </nav>

        <!-- Sync feedback -->
        <div v-if="syncMsg"
            class="mb-3 px-3 py-2 text-[10px] font-sans border border-ink-3/30 bg-ink/[0.02]"
            style="border-radius:2px">
            {{ syncMsg }}
        </div>

        <!-- Filters bar -->
        <div class="flex items-center gap-2 mb-4 flex-wrap">
            <select
                class="font-sans text-[11px] bg-paper border border-ink-3/40 px-2.5 py-1.5"
                style="border-radius:2px;outline:none"
                :value="filters.sort ?? 'reach'"
                @change="applyFilter({ sort: ($event.target as HTMLSelectElement).value })">
                <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
                    Urutkan: {{ opt.label }}
                </option>
            </select>
            <select
                class="font-sans text-[11px] bg-paper border border-ink-3/40 px-2.5 py-1.5"
                style="border-radius:2px;outline:none"
                :value="filters.type ?? ''"
                @change="applyFilter({ type: ($event.target as HTMLSelectElement).value })">
                <option v-for="opt in typeOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
            <div class="flex-1"></div>
            <span class="text-[10px] font-sans text-ink-3">{{ data.total }} postingan</span>
            <button
                class="flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-sans border border-ink-3/40 hover:border-ink transition-colors disabled:opacity-50"
                style="border-radius:2px"
                :disabled="syncingAll"
                @click="syncAll">
                <span :class="syncingAll ? 'animate-spin' : ''">↻</span>
                {{ syncingAll ? 'Menyinkron...' : 'Sync Data' }}
            </button>
        </div>

        <!-- Table -->
        <div class="bg-paper border-[1.5px] border-ink" style="border-radius:2px;overflow-x:auto">
            <table class="w-full" style="border-collapse:collapse;min-width:800px">
                <thead>
                    <tr class="border-b border-ink-3/30">
                        <th class="w-8 py-2 px-3"></th>
                        <th class="text-left label-upper py-2 px-3 font-normal">Konten</th>
                        <th class="text-left label-upper py-2 px-3 font-normal">Platform</th>
                        <th class="text-left label-upper py-2 px-3 font-normal">Tipe</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Reach</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Impresi</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Like</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Komen</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Share</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Save</th>
                        <th class="text-right label-upper py-2 px-3 font-normal">Eng.%</th>
                        <th class="w-8 py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="post in data.data" :key="post.id">
                        <!-- Main row -->
                        <tr class="border-b border-ink-3/10 hover:bg-ink/[0.02] transition-colors">
                            <td class="py-2.5 px-3">
                                <div v-if="post.thumbnail_url"
                                    class="w-8 h-8 border border-ink-3/20 overflow-hidden flex-shrink-0"
                                    style="border-radius:2px">
                                    <img :src="post.thumbnail_url" class="w-full h-full object-cover"/>
                                </div>
                                <div v-else
                                    class="w-8 h-8 border border-ink-3/20 bg-ink/5 flex-shrink-0"
                                    style="border-radius:2px"></div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="text-xs font-sans truncate max-w-[200px]">{{ post.title || '(tanpa judul)' }}</div>
                                <div class="text-[9px] font-sans text-ink-3 mt-0.5">{{ post.published_at }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <PlatformBadge :platform="post.platform as any" size="sm"/>
                            </td>
                            <td class="py-2.5 px-3 text-xs font-sans text-ink-3">
                                {{ contentTypeLabel(post.content_type) }}
                            </td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right">{{ fmtNum(post.reach) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.impressions) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.likes) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.comments) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.shares) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.saves) }}</td>
                            <td class="py-2.5 px-3 text-xs font-sans text-right" :class="erClass(post.engagement_rate)">
                                {{ post.engagement_rate.toFixed(1) }}%
                            </td>
                            <td class="py-2.5 px-3">
                                <button
                                    class="w-6 h-6 border border-ink-3/30 flex items-center justify-center text-xs text-ink-3 hover:border-ink hover:text-ink transition-colors"
                                    style="border-radius:2px;font-family:monospace"
                                    @click="toggleExpand(post.id)">
                                    {{ expandedIds.has(post.id) ? '×' : '+' }}
                                </button>
                            </td>
                        </tr>

                        <!-- Expand panel -->
                        <tr v-if="expandedIds.has(post.id)" class="border-b border-ink-3/20">
                            <td colspan="12" class="px-4 py-4 bg-ink/[0.02]">
                                <!-- 6 metric cards -->
                                <div class="grid grid-cols-6 gap-2 mb-4">
                                    <div v-for="(metric, i) in [
                                        { label: 'Reach', val: fmtNum(post.reach) },
                                        { label: 'Impresi', val: fmtNum(post.impressions) },
                                        { label: 'Like', val: fmtNum(post.likes) },
                                        { label: 'Komentar', val: fmtNum(post.comments) },
                                        { label: 'Share', val: fmtNum(post.shares) },
                                        { label: 'Simpan', val: fmtNum(post.saves) },
                                    ]" :key="i"
                                    class="bg-paper border border-ink-3/20 px-3 py-2 text-center"
                                    style="border-radius:2px">
                                        <div class="label-upper text-ink-3 mb-1">{{ metric.label }}</div>
                                        <div class="text-sm font-display italic font-bold text-ink">{{ metric.val }}</div>
                                    </div>
                                </div>

                                <!-- Engagement distribution bars -->
                                <div class="mb-4">
                                    <div class="label-upper text-ink-3 mb-2">Distribusi Engagement</div>
                                    <div class="flex gap-3">
                                        <template v-for="(seg, i) in [
                                            { label: 'Like', val: post.likes, color: '#C96442' },
                                            { label: 'Komentar', val: post.comments, color: '#3B6DB5' },
                                            { label: 'Share', val: post.shares, color: '#2A7A4B' },
                                            { label: 'Simpan', val: post.saves, color: '#928E89' },
                                        ]" :key="i">
                                            <div class="flex flex-col items-center gap-1 flex-1">
                                                <div class="w-full bg-ink/5" style="border-radius:1px;height:60px;display:flex;align-items:flex-end">
                                                    <div
                                                        :style="{
                                                            width: '100%',
                                                            height: Math.max(4, (seg.val / Math.max(post.likes, post.comments, post.shares, post.saves, 1)) * 56) + 'px',
                                                            background: seg.color,
                                                            opacity: '0.7',
                                                            borderRadius: '1px',
                                                        }"></div>
                                                </div>
                                                <div class="text-[9px] font-sans text-ink-3">{{ seg.label }}</div>
                                                <div class="text-[10px] font-sans font-semibold" :style="{ color: seg.color }">
                                                    {{ fmtNum(seg.val) }}
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Metadata + actions -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-[9px] font-sans text-ink-3">
                                        <span>Dipublikasi: <strong class="text-ink">{{ post.published_at }}</strong></span>
                                        <span>Tipe: <strong class="text-ink">{{ contentTypeLabel(post.content_type) }}</strong></span>
                                        <PlatformBadge :platform="post.platform as any" size="sm"/>
                                        <span>ER: <strong :class="erClass(post.engagement_rate)">{{ post.engagement_rate.toFixed(1) }}%</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="flex items-center gap-1 px-2.5 py-1 text-[10px] font-sans border border-ink-3/30 hover:border-ink transition-colors disabled:opacity-50"
                                            style="border-radius:2px"
                                            :disabled="syncingRows.has(post.post_version_id)"
                                            @click="syncOne(post.post_version_id)">
                                            <span :class="syncingRows.has(post.post_version_id) ? 'animate-spin' : ''">↻</span>
                                            {{ syncingRows.has(post.post_version_id) ? 'Syncing...' : 'Sync' }}
                                        </button>
                                        <Link
                                            :href="route('analytics.content-detail', post.post_version_id)"
                                            class="text-[10px] font-sans text-accent-b hover:underline">
                                            Lihat Detail →
                                        </Link>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr v-if="!data.data.length">
                        <td colspan="12" class="py-10 text-center text-xs text-ink-3 font-sans">
                            Belum ada data postingan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="data.last_page > 1" class="flex items-center justify-between mt-4">
            <span class="text-[10px] font-sans text-ink-3">
                Halaman {{ data.current_page }} dari {{ data.last_page }}
            </span>
            <div class="flex gap-1">
                <button
                    v-for="page in data.last_page" :key="page"
                    class="w-7 h-7 text-xs font-sans border transition-colors"
                    style="border-radius:2px"
                    :class="page === data.current_page
                        ? 'bg-ink text-paper border-ink'
                        : 'bg-paper text-ink-3 border-ink-3/30 hover:border-ink hover:text-ink'"
                    @click="goPage(page)">
                    {{ page }}
                </button>
            </div>
        </div>

    </AppLayout>
</template>
