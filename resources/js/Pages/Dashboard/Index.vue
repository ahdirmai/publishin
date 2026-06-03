<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import KpiCard from '@/Components/ui/KpiCard.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'
import StatusBadge from '@/Components/ui/StatusBadge.vue'

interface KpiData {
    total_followers: number
    scheduled_posts: number
    engagement_rate: number
    reach_30d: number
    followers_delta: number
    engagement_delta: number
    reach_delta: number
}

interface RecentPost {
    id: number
    title: string
    platform: string
    reach: number | null
    engagement_rate: number | null
    status: string
}

interface Notification {
    id: number
    color: 'green' | 'blue' | 'neutral' | 'red'
    message: string
    time: string
}

interface BestTime {
    platform: string
    label: string
    time: string
    days: string
}

interface EngagementPoint {
    date: string
    rate: number
}

interface PlatformPost {
    platform: string
    count: number
}

const props = defineProps<{
    kpi: KpiData
    recentPosts: RecentPost[]
    notifications: Notification[]
    bestTimes: BestTime[]
    engagementTrend: EngagementPoint[]
    postsPerPlatform: PlatformPost[]
}>()

// ─── SVG Engagement Trend ────────────────────────────────────────────────────

const CHART_W = 370
const CHART_H = 130
const PAD_L = 28
const PAD_B = 18
const PAD_T = 10

const trendPath = computed(() => {
    const pts = props.engagementTrend
    if (!pts.length) return ''
    const maxRate = Math.max(...pts.map(p => p.rate), 0.01)
    const innerW = CHART_W - PAD_L - 5
    const innerH = CHART_H - PAD_T - PAD_B

    const mapped = pts.map((p, i) => {
        const x = PAD_L + (i / (pts.length - 1)) * innerW
        const y = PAD_T + innerH - (p.rate / maxRate) * innerH
        return { x, y, p }
    })

    if (mapped.length === 1) return `M${mapped[0].x},${mapped[0].y}`

    let d = `M${mapped[0].x},${mapped[0].y}`
    for (let i = 1; i < mapped.length; i++) {
        const prev = mapped[i - 1]
        const curr = mapped[i]
        const cpx = (prev.x + curr.x) / 2
        d += ` C${cpx},${prev.y} ${cpx},${curr.y} ${curr.x},${curr.y}`
    }
    return d
})

const trendDots = computed(() => {
    const pts = props.engagementTrend
    if (!pts.length) return []
    const maxRate = Math.max(...pts.map(p => p.rate), 0.01)
    const innerW = CHART_W - PAD_L - 5
    const innerH = CHART_H - PAD_T - PAD_B
    return pts.map((p, i) => ({
        x: PAD_L + (i / (pts.length - 1)) * innerW,
        y: PAD_T + innerH - (p.rate / maxRate) * innerH,
        last: i === pts.length - 1,
        date: p.date,
    }))
})

// ─── SVG Post per Platform bars ──────────────────────────────────────────────

const BAR_W = 280
const BAR_H = 130
const BAR_PAD_L = 28
const BAR_PAD_B = 22
const BAR_PAD_T = 10

const platformColors: Record<string, { stroke: string; fill: string; label: string }> = {
    instagram: { stroke: '#C96442', fill: 'url(#h-r)', label: 'IG' },
    tiktok:    { stroke: 'rgba(0,0,0,.5)', fill: 'url(#h-k)', label: 'TT' },
    facebook:  { stroke: '#3B6DB5', fill: 'url(#h-b)', label: 'FB' },
    twitter:   { stroke: '#2A7A4B', fill: 'url(#h-g)', label: 'X' },
    youtube:   { stroke: '#C96442', fill: 'url(#h-r)', label: 'YT' },
}

const barRects = computed(() => {
    const posts = props.postsPerPlatform
    if (!posts.length) return []
    const maxCount = Math.max(...posts.map(p => p.count), 1)
    const innerH = BAR_H - BAR_PAD_T - BAR_PAD_B
    const spacing = (BAR_W - BAR_PAD_L) / posts.length
    const barW = Math.min(32, spacing * 0.7)

    return posts.map((p, i) => {
        const barH = (p.count / maxCount) * innerH
        const x = BAR_PAD_L + spacing * i + (spacing - barW) / 2
        const y = BAR_PAD_T + innerH - barH
        const cfg = platformColors[p.platform] ?? { stroke: '#928E89', fill: 'url(#h-k)', label: p.platform.toUpperCase().slice(0, 2) }
        return { x, y, w: barW, h: barH, count: p.count, label: cfg.label, stroke: cfg.stroke, fill: cfg.fill }
    })
})

// ─── Formatters ──────────────────────────────────────────────────────────────

function fmtNum(n: number | null): string {
    if (n === null || n === undefined) return '—'
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
    if (n >= 1_000) return (n / 1_000).toFixed(1) + 'K'
    return n.toString()
}

function fmtDelta(n: number): string {
    return n >= 0 ? `↑ +${fmtNum(Math.abs(n))}` : `↓ −${fmtNum(Math.abs(n))}`
}

function erColor(er: number | null): string {
    if (er === null) return 'text-ink-3'
    if (er >= 7) return 'text-forest font-bold'
    if (er >= 4) return 'text-accent-b font-bold'
    return 'text-ink-3'
}

const notifDot: Record<string, string> = {
    green:   'bg-forest',
    blue:    'bg-accent-b',
    red:     'bg-accent-r',
    neutral: 'bg-ink-2',
}

const platformOrder = ['instagram', 'tiktok', 'facebook', 'twitter', 'youtube'] as const
</script>

<template>
    <AppLayout title="Dashboard" screen="dashboard">

        <!-- KPI row -->
        <div class="grid grid-cols-4 gap-3 mb-4">
            <KpiCard
                label="Total Followers"
                :value="fmtNum(kpi.total_followers)"
                :delta="fmtDelta(kpi.followers_delta) + ' · bulan ini'"
                :delta-positive="kpi.followers_delta >= 0"
            />
            <KpiCard
                label="Post Terjadwal"
                :value="kpi.scheduled_posts.toString()"
                delta="minggu ini"
            />
            <KpiCard
                label="Avg. Engagement"
                :value="kpi.engagement_rate.toFixed(1) + '%'"
                :delta="fmtDelta(kpi.engagement_delta) + '% vs bln lalu'"
                :delta-positive="kpi.engagement_delta >= 0"
            />
            <KpiCard
                label="Reach (30 hari)"
                :value="fmtNum(kpi.reach_30d)"
                :delta="fmtDelta(kpi.reach_delta) + ' vs bln lalu'"
                :delta-positive="kpi.reach_delta >= 0"
            />
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-2 gap-3 mb-4">

            <!-- Engagement trend -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Engagement Trend (30 hari)</div>
                <svg :viewBox="`0 0 ${CHART_W} ${CHART_H}`" width="100%" :height="CHART_H">
                    <line :x1="PAD_L" :y1="PAD_T" :x2="PAD_L" :y2="CHART_H - PAD_B"
                        stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                    <line :x1="PAD_L" :y1="CHART_H - PAD_B" :x2="CHART_W - 5" :y2="CHART_H - PAD_B"
                        stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                    <line :x1="PAD_L" :y1="(CHART_H - PAD_B - PAD_T) * 0.5 + PAD_T"
                        :x2="CHART_W - 5" :y2="(CHART_H - PAD_B - PAD_T) * 0.5 + PAD_T"
                        stroke="rgba(0,0,0,.06)" stroke-width="1" stroke-dasharray="3,3"/>
                    <path v-if="trendPath" :d="trendPath" fill="none" stroke="#C96442"
                        stroke-width="2" stroke-linecap="round"/>
                    <template v-for="(dot, i) in trendDots" :key="i">
                        <circle v-if="dot.last" :cx="dot.x" :cy="dot.y" r="4"
                            fill="#C96442" stroke="#C96442" stroke-width="1.5"/>
                        <circle v-else-if="i % Math.max(1, Math.floor(trendDots.length / 4)) === 0"
                            :cx="dot.x" :cy="dot.y" r="3.5"
                            fill="#fff" stroke="#C96442" stroke-width="1.5"/>
                    </template>
                    <text v-if="trendDots.length"
                        :x="trendDots[0].x" :y="CHART_H - 3"
                        font-size="8" fill="rgba(0,0,0,.38)" text-anchor="middle" font-family="monospace">
                        {{ engagementTrend[0]?.date }}
                    </text>
                    <text v-if="trendDots.length"
                        :x="trendDots[trendDots.length - 1].x" :y="CHART_H - 3"
                        font-size="8" fill="rgba(0,0,0,.38)" text-anchor="middle" font-family="monospace">
                        {{ engagementTrend[engagementTrend.length - 1]?.date }}
                    </text>
                </svg>
            </div>

            <!-- Post per Platform -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="label-upper text-ink-3 mb-3">Post per Platform</div>
                <svg :viewBox="`0 0 ${BAR_W} ${BAR_H}`" width="100%" :height="BAR_H">
                    <line :x1="BAR_PAD_L" :y1="BAR_PAD_T" :x2="BAR_PAD_L" :y2="BAR_H - BAR_PAD_B"
                        stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                    <line :x1="BAR_PAD_L" :y1="BAR_H - BAR_PAD_B" :x2="BAR_W - 5" :y2="BAR_H - BAR_PAD_B"
                        stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                    <template v-for="(bar, i) in barRects" :key="i">
                        <rect :x="bar.x" :y="bar.y" :width="bar.w" :height="bar.h"
                            :fill="bar.fill" :stroke="bar.stroke" stroke-width="1.5"/>
                        <text :x="bar.x + bar.w / 2" :y="BAR_H - 5"
                            font-size="8" fill="rgba(0,0,0,.45)" text-anchor="middle" font-family="monospace">
                            {{ bar.label }}
                        </text>
                        <text :x="bar.x + bar.w / 2" :y="bar.y - 3"
                            font-size="9" :fill="bar.stroke" text-anchor="middle"
                            font-family="monospace" font-weight="bold">
                            {{ bar.count }}
                        </text>
                    </template>
                    <text v-if="!barRects.length"
                        x="140" y="65" font-size="10" fill="rgba(0,0,0,.3)"
                        text-anchor="middle" font-family="monospace">
                        Belum ada data
                    </text>
                </svg>
            </div>
        </div>

        <!-- Recent posts + sidebar -->
        <div class="grid grid-cols-2 gap-3">

            <!-- Recent posts table -->
            <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                <div class="flex items-center justify-between mb-3">
                    <div class="label-upper text-ink-3">Postingan Terbaru</div>
                    <Link :href="route('analytics')"
                        class="text-[10px] font-sans text-ink-3 hover:text-ink border-b border-current">
                        Lihat Semua →
                    </Link>
                </div>
                <table class="w-full" style="border-collapse:collapse">
                    <thead>
                        <tr class="border-b border-ink-3/30">
                            <th class="text-left label-upper py-1.5 pr-3 font-normal">Konten</th>
                            <th class="text-left label-upper py-1.5 pr-3 font-normal">Platform</th>
                            <th class="text-right label-upper py-1.5 pr-3 font-normal">Reach</th>
                            <th class="text-right label-upper py-1.5 pr-3 font-normal">Eng.</th>
                            <th class="text-left label-upper py-1.5 font-normal">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in recentPosts" :key="post.id"
                            class="border-b border-ink-3/10 hover:bg-ink/[0.02]">
                            <td class="py-2 pr-3 text-xs font-sans truncate max-w-[180px]">
                                {{ post.title || '(tanpa judul)' }}
                            </td>
                            <td class="py-2 pr-3">
                                <PlatformBadge :platform="post.platform as any" size="sm"/>
                            </td>
                            <td class="py-2 pr-3 text-xs font-sans text-right text-ink-3">
                                {{ fmtNum(post.reach) }}
                            </td>
                            <td class="py-2 pr-3 text-xs font-sans text-right"
                                :class="erColor(post.engagement_rate)">
                                {{ post.engagement_rate !== null ? post.engagement_rate.toFixed(1) + '%' : '—' }}
                            </td>
                            <td class="py-2">
                                <StatusBadge :status="post.status as any"/>
                            </td>
                        </tr>
                        <tr v-if="!recentPosts.length">
                            <td colspan="5" class="py-6 text-center text-xs text-ink-3 font-sans">
                                Belum ada postingan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notifikasi + Waktu Terbaik -->
            <div class="flex flex-col gap-3">

                <!-- Notifikasi -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="flex items-center justify-between mb-3">
                        <div class="label-upper text-ink-3">Notifikasi</div>
                        <span class="text-[9px] font-sans text-ink-3">{{ notifications.length }} baru</span>
                    </div>
                    <div v-if="!notifications.length" class="text-xs text-ink-3 font-sans">
                        Tidak ada notifikasi
                    </div>
                    <div v-for="notif in notifications" :key="notif.id"
                        class="flex items-start gap-2.5 py-2 border-b border-ink-3/10 last:border-0">
                        <div class="w-2 h-2 rounded-full mt-1 flex-shrink-0"
                            :class="notifDot[notif.color] ?? 'bg-ink-2'"></div>
                        <div class="text-xs font-sans leading-relaxed flex-1"
                            v-html="notif.message"></div>
                    </div>
                </div>

                <!-- Waktu Terbaik -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-3">Waktu Terbaik Posting</div>
                    <div v-for="bt in bestTimes" :key="bt.platform"
                        class="flex items-center justify-between py-2 border-b border-ink-3/10 last:border-0">
                        <span class="flex items-center gap-2 text-xs font-sans">
                            <PlatformBadge :platform="bt.platform as any" size="sm"/>
                            {{ bt.days }}
                        </span>
                        <span class="text-xs font-sans font-semibold text-ink">{{ bt.time }}</span>
                    </div>
                    <div v-if="!bestTimes.length" class="text-xs text-ink-3 font-sans">
                        Hubungkan platform untuk rekomendasi
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
