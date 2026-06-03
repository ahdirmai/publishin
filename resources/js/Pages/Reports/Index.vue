<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'
import AppButton from '@/Components/ui/AppButton.vue'

interface Account {
    platform: string
    username: string
}

interface HistoryItem {
    id: number
    name: string
    platforms: string[]
    period: string
    created_at: string
    status: string
    can_download: boolean
}

interface Defaults {
    period_start: string
    period_end: string
    platforms: string[]
}

const props = defineProps<{
    history: HistoryItem[]
    accounts: Account[]
    defaults: Defaults
}>()

// ─── Form state ──────────────────────────────────────────────────────────────

const form = ref({
    period_start:         props.defaults.period_start,
    period_end:           props.defaults.period_end,
    platforms:            [...props.defaults.platforms] as string[],
    include_kpi:          true,
    include_chart:        true,
    include_top_posts:    true,
    include_demographics: false,
    white_label:          false,
})

const generating  = ref(false)
const genMessage  = ref<string | null>(null)
const genError    = ref<string | null>(null)
const preview     = ref<{ month_label: string; reach: string; er: string; posts: string } | null>(null)
const previewLoading = ref(false)

// ─── Platform toggles ─────────────────────────────────────────────────────────

function togglePlatform(p: string) {
    const idx = form.value.platforms.indexOf(p)
    if (idx >= 0) form.value.platforms.splice(idx, 1)
    else form.value.platforms.push(p)
}

const allPlatforms = ['instagram', 'tiktok', 'youtube', 'facebook', 'twitter']
const platformLabel: Record<string, string> = {
    instagram: 'IG', tiktok: 'TikTok', youtube: 'YouTube', facebook: 'Facebook', twitter: 'X/Twitter',
}

// ─── Preview fetch ────────────────────────────────────────────────────────────

async function fetchPreview() {
    previewLoading.value = true
    try {
        const res = await fetch(route('api.reports.preview'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                period_start: form.value.period_start,
                period_end:   form.value.period_end,
                platforms:    form.value.platforms,
            }),
        })
        preview.value = await res.json()
    } catch (_) {
        preview.value = null
    } finally {
        previewLoading.value = false
    }
}

// Initial preview on mount + watch date changes
watch(() => [form.value.period_start, form.value.period_end, form.value.platforms], fetchPreview, { immediate: true, deep: true })

// ─── Period presets ───────────────────────────────────────────────────────────

const now = new Date()

const periods = computed(() => {
    const months = []
    for (let i = 0; i < 4; i++) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
        const end = new Date(d.getFullYear(), d.getMonth() + 1, 0)
        months.push({
            label: d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }),
            start: d.toISOString().split('T')[0],
            end:   end.toISOString().split('T')[0],
        })
    }
    return months
})

function setPeriod(start: string, end: string) {
    form.value.period_start = start
    form.value.period_end   = end
}

// ─── Generate ────────────────────────────────────────────────────────────────

async function generate() {
    generating.value = true
    genMessage.value = null
    genError.value   = null
    try {
        const res = await fetch(route('api.reports.store'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(form.value),
        })
        const data = await res.json()
        if (!res.ok) throw new Error(data.message ?? 'Gagal membuat laporan')
        genMessage.value = data.message
    } catch (e: any) {
        genError.value = e.message
    } finally {
        generating.value = false
    }
}

function csrfToken(): string {
    return (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? ''
}

function downloadUrl(id: number): string {
    return route('api.reports.download', id)
}

function statusLabel(s: string): { text: string; color: string } {
    const map: Record<string, { text: string; color: string }> = {
        pending:    { text: 'Antrian',   color: 'text-ink-3' },
        generating: { text: 'Proses…',   color: 'text-accent-b' },
        done:       { text: 'Selesai',   color: 'text-forest' },
        failed:     { text: 'Gagal',     color: 'text-accent-r' },
    }
    return map[s] ?? { text: s, color: 'text-ink-3' }
}
</script>

<template>
    <AppLayout title="Laporan" screen="reports">

        <!-- Topbar action -->
        <div class="topbar-actions">
            <AppButton variant="primary" size="sm" :disabled="generating" @click="generate">
                <span v-if="generating">Membuat…</span>
                <span v-else>↓ Export PDF</span>
            </AppButton>
        </div>

        <!-- Success / error banner -->
        <div v-if="genMessage"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-forest/40 bg-forest/5 text-forest"
            style="border-radius:2px">
            {{ genMessage }}
        </div>
        <div v-if="genError"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-accent-r/40 bg-accent-r/5 text-accent-r"
            style="border-radius:2px">
            {{ genError }}
        </div>

        <!-- Config + Preview row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">

            <!-- Config -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-4">Konfigurasi Laporan</div>

                <!-- Period -->
                <div class="mb-4">
                    <div class="label-upper text-ink-3 mb-2">Periode</div>
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <button
                            v-for="p in periods" :key="p.start"
                            class="px-2.5 py-1 text-[10px] font-sans border transition-colors"
                            style="border-radius:2px"
                            :class="form.period_start === p.start
                                ? 'bg-ink text-paper border-ink'
                                : 'bg-paper text-ink-3 border-ink-3/30 hover:border-ink hover:text-ink'"
                            @click="setPeriod(p.start, p.end)">
                            {{ p.label }}
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label-upper text-ink-3 mb-1 block">Dari</label>
                            <input type="date" v-model="form.period_start"
                                class="w-full font-sans text-[11px] bg-paper border border-ink-3/40 px-2 py-1.5"
                                style="border-radius:2px;outline:none"/>
                        </div>
                        <div>
                            <label class="label-upper text-ink-3 mb-1 block">Sampai</label>
                            <input type="date" v-model="form.period_end"
                                class="w-full font-sans text-[11px] bg-paper border border-ink-3/40 px-2 py-1.5"
                                style="border-radius:2px;outline:none"/>
                        </div>
                    </div>
                </div>

                <!-- Platform -->
                <div class="mb-4">
                    <div class="label-upper text-ink-3 mb-2">Platform</div>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="p in allPlatforms" :key="p"
                            class="flex items-center gap-1.5 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox"
                                :checked="form.platforms.includes(p)"
                                @change="togglePlatform(p)"
                                style="accent-color:#1C1B1A"/>
                            {{ platformLabel[p] }}
                        </label>
                    </div>
                </div>

                <!-- Include -->
                <div class="mb-3">
                    <div class="label-upper text-ink-3 mb-2">Sertakan</div>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-2 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox" v-model="form.include_kpi" style="accent-color:#1C1B1A"/>
                            KPI Summary
                        </label>
                        <label class="flex items-center gap-2 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox" v-model="form.include_chart" style="accent-color:#1C1B1A"/>
                            Grafik Engagement
                        </label>
                        <label class="flex items-center gap-2 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox" v-model="form.include_top_posts" style="accent-color:#1C1B1A"/>
                            Top 10 Posts
                        </label>
                        <label class="flex items-center gap-2 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox" v-model="form.include_demographics" style="accent-color:#1C1B1A"/>
                            Demografi Audiens
                        </label>
                        <label class="flex items-center gap-2 text-[11px] font-sans cursor-pointer">
                            <input type="checkbox" v-model="form.white_label" style="accent-color:#1C1B1A"/>
                            White-label (hapus logo Publishin)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Preview</div>

                <div class="border border-ink-3/20 p-4" style="border-radius:2px;background:#f8f8f6">
                    <template v-if="preview && !previewLoading">
                        <div class="font-display italic font-bold text-sm text-ink mb-1">
                            Laporan Performa · {{ preview.month_label }}
                        </div>
                        <div class="text-[9px] font-sans text-ink-3 mb-3">
                            Dibuat {{ new Date().toLocaleDateString('id-ID') }}
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div v-for="kpi in [
                                { l: 'REACH', v: preview.reach, c: '#3B6DB5' },
                                { l: 'ENG.', v: preview.er, c: '#C96442' },
                                { l: 'POSTS', v: preview.posts, c: '#1C1B1A' },
                            ]" :key="kpi.l"
                            class="border border-ink-3/20 p-2 text-center" style="border-radius:2px">
                                <div class="text-[8px] font-sans text-ink-3">{{ kpi.l }}</div>
                                <div class="font-display italic font-bold text-lg" :style="{ color: kpi.c }">{{ kpi.v }}</div>
                            </div>
                        </div>
                        <div class="bg-ink/5 flex items-center justify-center text-[9px] font-sans text-ink-3"
                            style="height:55px;border-radius:2px;letter-spacing:.1em;text-transform:uppercase">
                            [ CHART ENGAGEMENT ]
                        </div>
                        <div class="text-[9px] font-sans text-ink-3 mt-2">…dan lebih banyak data</div>
                    </template>
                    <div v-else-if="previewLoading" class="text-xs font-sans text-ink-3 py-8 text-center">
                        Memuat preview…
                    </div>
                    <div v-else class="text-xs font-sans text-ink-3 py-8 text-center">
                        Pilih periode untuk melihat preview
                    </div>
                </div>

                <div class="mt-4">
                    <AppButton variant="primary" size="sm" :disabled="generating" @click="generate" class="w-full justify-center">
                        <span v-if="generating">Membuat laporan…</span>
                        <span v-else>↓ Buat & Export PDF</span>
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- History table -->
        <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
            <div class="label-upper text-ink-3 mb-3">Riwayat Laporan</div>
            <table class="w-full" style="border-collapse:collapse">
                <thead>
                    <tr class="border-b border-ink-3/30">
                        <th class="text-left label-upper py-2 pr-4 font-normal">Nama</th>
                        <th class="text-left label-upper py-2 pr-4 font-normal">Platform</th>
                        <th class="text-left label-upper py-2 pr-4 font-normal">Periode</th>
                        <th class="text-left label-upper py-2 pr-4 font-normal">Dibuat</th>
                        <th class="text-left label-upper py-2 pr-4 font-normal">Status</th>
                        <th class="label-upper py-2 font-normal"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in history" :key="item.id"
                        class="border-b border-ink-3/10 hover:bg-ink/[0.02]">
                        <td class="py-2.5 pr-4 text-xs font-sans">{{ item.name }}</td>
                        <td class="py-2.5 pr-4">
                            <div class="flex items-center gap-1 flex-wrap">
                                <PlatformBadge
                                    v-for="p in item.platforms" :key="p"
                                    :platform="p as any" size="sm"/>
                                <span v-if="!item.platforms.length" class="text-[9px] text-ink-3">Semua</span>
                            </div>
                        </td>
                        <td class="py-2.5 pr-4 text-xs font-sans text-ink-3">{{ item.period }}</td>
                        <td class="py-2.5 pr-4 text-xs font-sans text-ink-3">{{ item.created_at }}</td>
                        <td class="py-2.5 pr-4 text-xs font-sans" :class="statusLabel(item.status).color">
                            {{ statusLabel(item.status).text }}
                        </td>
                        <td class="py-2.5">
                            <a v-if="item.can_download"
                                :href="downloadUrl(item.id)"
                                class="text-[10px] font-sans text-accent-b hover:underline">
                                ↓ Download
                            </a>
                        </td>
                    </tr>
                    <tr v-if="!history.length">
                        <td colspan="6" class="py-8 text-center text-xs text-ink-3 font-sans">
                            Belum ada laporan yang dibuat
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>
