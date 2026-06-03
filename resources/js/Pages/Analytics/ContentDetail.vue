<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'
import StatusBadge from '@/Components/ui/StatusBadge.vue'

interface KpiCol {
    label: string
    value: string
    delta: string | null
    delta_positive: boolean | null
}

interface DailyReach {
    day: number
    reach: number
    date: string
}

interface VsAvg {
    metric: string
    post_value: number
    avg_value: number
    unit: string
}

interface Insight {
    icon: string
    title: string
    body: string
}

interface PostDetailData {
    id: number
    title: string
    platform: string
    platform_username: string | null
    platform_post_id: string | null
    content_type: string
    published_at: string
    status: string
    thumbnail_url: string | null
    preview_url: string | null
    post_url: string | null
    caption: string | null
    kpis: KpiCol[]
    daily_7: DailyReach[]
    vs_avg: VsAvg[]
    insights: Insight[]
    engagement_distribution: { label: string; value: number; color: string }[]
}

const props = defineProps<{
    data: PostDetailData
}>()

// ─── Daily reach SVG ─────────────────────────────────────────────────────────

const DR_W = 560
const DR_H = 100
const DR_PAD_L = 28
const DR_PAD_B = 20
const DR_PAD_T = 8

const dailyBars = computed(() => {
    const pts = props.data.daily_7
    if (!pts.length) return []
    const maxReach = Math.max(...pts.map(p => p.reach), 1)
    const innerH = DR_H - DR_PAD_T - DR_PAD_B
    const innerW = DR_W - DR_PAD_L - 5
    const barW = Math.min(40, innerW / pts.length * 0.6)
    const spacing = innerW / pts.length

    return pts.map((p, i) => {
        const barH = (p.reach / maxReach) * innerH
        const x = DR_PAD_L + spacing * i + (spacing - barW) / 2
        const y = DR_PAD_T + innerH - barH
        const isPeak = p.reach === maxReach
        return { x, y, w: barW, h: barH, reach: fmtNum(p.reach), day: 'H' + p.day, date: p.date, isPeak }
    })
})

const peakDay = computed(() => {
    return props.data.daily_7.reduce((best, p) => p.reach > best.reach ? p : best, props.data.daily_7[0] ?? { reach: 0, day: 1 })
})

const total7 = computed(() => props.data.daily_7.reduce((s, p) => s + p.reach, 0))

// ─── Helpers ─────────────────────────────────────────────────────────────────

function fmtNum(n: number): string {
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
    if (n >= 1_000) return (n / 1_000).toFixed(1) + 'K'
    return n.toString()
}

const insightIcons: Record<string, string> = {
    '★': 'text-accent-r',
    '↗': 'text-forest',
    '◈': 'text-accent-b',
    '▶': 'text-ink',
    '◎': 'text-accent-b',
}
</script>

<template>
    <AppLayout :title="data.title" screen="analytics">

        <!-- Back nav -->
        <div class="flex items-center gap-3 mb-4">
            <Link
                :href="route('analytics.per-konten')"
                class="text-xs font-sans text-ink-3 hover:text-ink flex items-center gap-1">
                ← Kembali ke Analitik
            </Link>
            <div class="flex-1"></div>
            <PlatformBadge :platform="data.platform as any"/>
        </div>

        <!-- Header card -->
        <div class="bg-paper border-[1.5px] border-ink p-4 mb-4 flex items-start gap-4" style="border-radius:2px">
            <div v-if="data.thumbnail_url"
                class="w-16 h-16 border border-ink-3/20 overflow-hidden flex-shrink-0"
                style="border-radius:2px">
                <img :src="data.thumbnail_url" class="w-full h-full object-cover"/>
            </div>
            <div v-else
                class="w-16 h-16 border border-ink-3/20 bg-ink/5 flex-shrink-0"
                style="border-radius:2px"></div>

            <div class="flex-1 min-w-0">
                <h1 class="font-display italic font-bold text-lg text-ink leading-snug mb-2">
                    {{ data.title || '(tanpa judul)' }}
                </h1>
                <div class="flex items-center gap-2 flex-wrap">
                    <PlatformBadge :platform="data.platform as any" size="sm"/>
                    <span class="text-[9px] font-sans text-ink-3 border border-ink-3/30 px-1.5 py-0.5"
                        style="border-radius:2px">
                        {{ data.content_type }}
                    </span>
                    <span class="text-[9px] font-sans text-ink-3">{{ data.published_at }}</span>
                    <StatusBadge :status="data.status as any"/>
                </div>
            </div>
        </div>

        <!-- Preview row: post preview + video player -->
        <div class="grid gap-3 mb-4" :class="data.content_type === 'video' ? 'grid-cols-2' : 'grid-cols-1'">

            <!-- Post preview card -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Preview Post</div>

                <!-- Platform mock frame -->
                <div class="border border-ink-3/20 overflow-hidden" style="border-radius:4px;max-width:420px;margin:0 auto">
                    <!-- Header -->
                    <div class="flex items-center gap-2 px-3 py-2 border-b border-ink-3/10">
                        <div class="w-7 h-7 rounded-full bg-ink/10 flex items-center justify-center text-[10px] font-bold text-ink-3">
                            {{ (data.platform_username ?? data.platform).charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div class="text-[10px] font-sans font-semibold text-ink">
                                @{{ data.platform_username ?? data.platform }}
                            </div>
                            <div class="text-[8px] font-sans text-ink-3">{{ data.published_at }}</div>
                        </div>
                        <div class="ml-auto">
                            <PlatformBadge :platform="data.platform as any" size="sm"/>
                        </div>
                    </div>

                    <!-- Thumbnail / image -->
                    <div v-if="data.preview_url || data.thumbnail_url"
                        class="w-full bg-ink/5 flex items-center justify-center overflow-hidden"
                        style="aspect-ratio:1/1;max-height:300px">
                        <img
                            :src="data.preview_url || data.thumbnail_url!"
                            class="w-full h-full object-cover"/>
                    </div>
                    <div v-else
                        class="w-full bg-ink/5 flex items-center justify-center text-ink-3 text-xs font-sans"
                        style="aspect-ratio:1/1;max-height:200px">
                        No image
                    </div>

                    <!-- Caption -->
                    <div class="px-3 py-2.5">
                        <p class="text-[10px] font-sans text-ink leading-relaxed whitespace-pre-line line-clamp-4">
                            {{ data.caption || '(tanpa caption)' }}
                        </p>
                        <a v-if="data.post_url"
                            :href="data.post_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-block mt-2 text-[9px] font-sans text-accent-b hover:underline">
                            Buka di {{ { tiktok: 'TikTok', instagram: 'Instagram', facebook: 'Facebook' }[data.platform] ?? data.platform }} ↗
                        </a>
                    </div>
                </div>
            </div>

            <!-- Video player (only when content_type === video) -->
            <div v-if="data.content_type === 'video'" class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Preview Video</div>

                <!-- TikTok embed -->
                <div v-if="data.platform === 'tiktok' && data.platform_post_id"
                    class="flex flex-col items-center gap-3">
                    <iframe
                        :src="`https://www.tiktok.com/embed/v2/${data.platform_post_id}`"
                        style="border:none;width:100%;max-width:340px;height:600px;border-radius:4px"
                        allow="encrypted-media"
                        allowfullscreen>
                    </iframe>
                </div>

                <!-- Generic video (if preview_url is a video file) -->
                <div v-else-if="data.preview_url" class="flex justify-center">
                    <video
                        :src="data.preview_url"
                        controls
                        style="max-width:100%;border-radius:4px;max-height:480px">
                    </video>
                </div>

                <!-- Fallback: thumbnail + link -->
                <div v-else class="flex flex-col items-center gap-3">
                    <div v-if="data.thumbnail_url"
                        class="relative overflow-hidden"
                        style="border-radius:4px;max-width:320px;width:100%">
                        <img :src="data.thumbnail_url" class="w-full object-cover"/>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                            <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center text-xl">▶</div>
                        </div>
                    </div>
                    <a v-if="data.post_url"
                        :href="data.post_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-[11px] font-sans text-accent-b hover:underline">
                        Tonton video ↗
                    </a>
                    <p v-else class="text-[10px] font-sans text-ink-3">Embed tidak tersedia</p>
                </div>
            </div>
        </div>

        <!-- KPI row (7 cols) -->
        <div class="grid grid-cols-7 gap-2 mb-4">
            <div v-for="(kpi, i) in data.kpis" :key="i"
                class="bg-paper border border-ink-3/20 p-3 text-center"
                style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-1">{{ kpi.label }}</div>
                <div class="font-display italic font-bold text-base text-ink leading-none">{{ kpi.value }}</div>
                <div v-if="kpi.delta"
                    class="text-[9px] font-sans mt-1"
                    :class="kpi.delta_positive === true ? 'text-forest' : kpi.delta_positive === false ? 'text-accent-r' : 'text-ink-3'">
                    {{ kpi.delta }}
                </div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-3 gap-3 mb-4">

            <!-- Engagement distribution -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Distribusi Engagement</div>
                <div class="flex items-end gap-3 h-24">
                    <div v-for="(seg, i) in data.engagement_distribution" :key="i"
                        class="flex flex-col items-center gap-1 flex-1">
                        <div class="w-full flex items-end" style="height:64px">
                            <div
                                :style="{
                                    width: '100%',
                                    height: Math.max(4, (seg.value / Math.max(...data.engagement_distribution.map(s => s.value), 1)) * 60) + 'px',
                                    background: seg.color,
                                    opacity: '0.75',
                                    borderRadius: '1px',
                                }"></div>
                        </div>
                        <div class="text-[8px] font-sans text-ink-3">{{ seg.label }}</div>
                        <div class="text-[9px] font-sans font-semibold" :style="{ color: seg.color }">
                            {{ fmtNum(seg.value) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proporsi Aksi (horizontal bars) -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Proporsi Aksi</div>
                <div v-if="data.engagement_distribution.length">
                    <div v-for="(seg, i) in data.engagement_distribution" :key="i"
                        class="flex items-center gap-2 mb-2">
                        <span class="text-[9px] font-sans text-ink-3 min-w-[44px]">{{ seg.label }}</span>
                        <div class="flex-1 h-2 bg-ink/5" style="border-radius:1px">
                            <div
                                :style="{
                                    width: Math.max(2, (seg.value / Math.max(...data.engagement_distribution.map(s => s.value), 1)) * 100) + '%',
                                    height: '100%',
                                    background: seg.color,
                                    opacity: '0.7',
                                    borderRadius: '1px',
                                    transition: 'width .3s',
                                }"></div>
                        </div>
                        <span class="text-[9px] font-sans text-ink-3 min-w-[28px] text-right">
                            {{ fmtNum(seg.value) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- vs Rata-rata Akun -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">vs Rata-rata Akun</div>
                <div v-for="(row, i) in data.vs_avg" :key="i" class="mb-3">
                    <div class="flex justify-between text-[9px] font-sans text-ink-3 mb-1">
                        <span>{{ row.metric }}</span>
                        <span>
                            <strong class="text-ink">{{ fmtNum(row.post_value) }}</strong>
                            {{ row.unit }} vs avg {{ fmtNum(row.avg_value) }}{{ row.unit }}
                        </span>
                    </div>
                    <div class="relative h-1.5 bg-ink/10" style="border-radius:1px">
                        <div class="absolute h-full bg-ink-2" style="border-radius:1px;left:0;width:50%"></div>
                        <div
                            class="absolute h-full"
                            style="border-radius:1px;left:0;background:#C96442;opacity:.8"
                            :style="{
                                width: Math.min(100, row.avg_value > 0 ? (row.post_value / row.avg_value) * 50 : 0) + '%'
                            }"></div>
                    </div>
                </div>
                <div v-if="!data.vs_avg.length" class="text-xs text-ink-3 font-sans">
                    Belum ada data rata-rata
                </div>
            </div>
        </div>

        <!-- Reach Harian 7 Hari Pertama -->
        <div class="bg-paper border-[1.5px] border-ink p-4 mb-4" style="border-radius:2px">
            <div class="flex items-center justify-between mb-3">
                <div class="label-upper text-ink-3">Reach Harian (7 Hari Pertama)</div>
                <div class="text-[10px] font-sans text-ink-3">
                    Peak H{{ peakDay?.day }} · Total 7 hari:
                    <strong class="text-ink">{{ fmtNum(total7) }}</strong>
                </div>
            </div>
            <svg :viewBox="`0 0 ${DR_W} ${DR_H}`" width="100%" :height="DR_H">
                <line :x1="DR_PAD_L" :y1="DR_PAD_T" :x2="DR_PAD_L" :y2="DR_H - DR_PAD_B"
                    stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                <line :x1="DR_PAD_L" :y1="DR_H - DR_PAD_B" :x2="DR_W - 5" :y2="DR_H - DR_PAD_B"
                    stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                <template v-for="(bar, i) in dailyBars" :key="i">
                    <rect :x="bar.x" :y="bar.y" :width="bar.w" :height="bar.h"
                        :fill="bar.isPeak ? '#C96442' : 'url(#h-r)'"
                        :stroke="bar.isPeak ? '#C96442' : '#C96442'"
                        :opacity="bar.isPeak ? '1' : '0.6'"
                        stroke-width="1.5"/>
                    <text :x="bar.x + bar.w / 2" :y="DR_H - 5"
                        font-size="8" fill="rgba(0,0,0,.45)" text-anchor="middle" font-family="monospace">
                        {{ bar.day }}
                    </text>
                    <text v-if="bar.isPeak" :x="bar.x + bar.w / 2" :y="bar.y - 3"
                        font-size="8" fill="#C96442" text-anchor="middle" font-family="monospace" font-weight="bold">
                        {{ bar.reach }}
                    </text>
                </template>
                <text v-if="!dailyBars.length" :x="DR_W / 2" :y="DR_H / 2"
                    font-size="10" fill="rgba(0,0,0,.3)" text-anchor="middle" font-family="monospace">
                    Data belum tersedia
                </text>
            </svg>
        </div>

        <!-- AI Insights -->
        <div v-if="data.insights.length" class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
            <div class="label-upper text-ink-3 mb-3">Insight Konten</div>
            <div class="grid grid-cols-1 gap-2">
                <div v-for="(insight, i) in data.insights" :key="i"
                    class="flex items-start gap-3 p-3 border border-ink-3/20"
                    style="border-radius:2px">
                    <span class="text-base flex-shrink-0" :class="insightIcons[insight.icon] ?? 'text-ink'">
                        {{ insight.icon }}
                    </span>
                    <div>
                        <div class="text-xs font-sans font-semibold text-ink mb-0.5">{{ insight.title }}</div>
                        <div class="text-[10px] font-sans text-ink-3 leading-relaxed">{{ insight.body }}</div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
