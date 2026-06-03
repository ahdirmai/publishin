<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import KpiCard from '@/Components/ui/KpiCard.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'

interface KpiItem {
    label: string
    value: string
    delta: string
    delta_positive: boolean | null
    subtext?: string
}

interface PlatformReach {
    platform: string
    reach: number
}

interface FollowerPoint {
    date: string
    instagram: number
    tiktok: number
}

interface TopPost {
    id: number
    title: string
    platform: string
    reach: number
    engagement_rate: number
}

interface AudienceData {
    age: { label: string; pct: number }[]
    gender: { female: number; male: number }
    cities: string
}

const props = defineProps<{
    data: {
        kpis: KpiItem[]
        reachByPlatform: PlatformReach[]
        followerGrowthChart: FollowerPoint[]
        topPosts: TopPost[]
        audience: AudienceData | null
    }
    filters: { days: number; platform: string | null }
    available_days: number[]
    activeTab?: string
}>()

// ─── Tab state ────────────────────────────────────────────────────────────────

const activeTab = ref(props.activeTab ?? 'overview')

// ─── Filters ─────────────────────────────────────────────────────────────────

function applyFilter(days?: number, platform?: string) {
    router.get(route('analytics'), {
        days: days ?? props.filters.days,
        platform: platform !== undefined ? platform : (props.filters.platform ?? ''),
    }, { preserveState: true, replace: true })
}

// ─── Reach per Platform SVG ──────────────────────────────────────────────────

const R_W = 300
const R_H = 140
const R_PAD_L = 28
const R_PAD_B = 20
const R_PAD_T = 10

const platformBarCfg: Record<string, { stroke: string; fill: string; textColor: string }> = {
    instagram: { stroke: '#C96442', fill: 'url(#h-r)', textColor: '#C96442' },
    tiktok:    { stroke: 'rgba(0,0,0,.5)', fill: 'url(#h-k)', textColor: 'rgba(0,0,0,.6)' },
    facebook:  { stroke: '#3B6DB5', fill: 'url(#h-b)', textColor: '#3B6DB5' },
    twitter:   { stroke: '#2A7A4B', fill: 'url(#h-g)', textColor: '#2A7A4B' },
    youtube:   { stroke: '#2A7A4B', fill: 'url(#h-g)', textColor: '#2A7A4B' },
}

const platformLabel: Record<string, string> = {
    instagram: 'IG', tiktok: 'TT', facebook: 'FB', twitter: 'X', youtube: 'YT',
}

const reachBars = computed(() => {
    const items = props.data.reachByPlatform
    if (!items.length) return []
    const maxReach = Math.max(...items.map(p => p.reach), 1)
    const innerH = R_H - R_PAD_T - R_PAD_B
    const spacing = (R_W - R_PAD_L) / items.length
    const barW = Math.min(38, spacing * 0.7)

    return items.map((p, i) => {
        const barH = (p.reach / maxReach) * innerH
        const x = R_PAD_L + spacing * i + (spacing - barW) / 2
        const y = R_PAD_T + innerH - barH
        const cfg = platformBarCfg[p.platform] ?? { stroke: '#928E89', fill: 'url(#h-k)', textColor: '#928E89' }
        return {
            x, y, w: barW, h: barH,
            label: platformLabel[p.platform] ?? p.platform.toUpperCase().slice(0, 2),
            reach: fmtNum(p.reach),
            ...cfg,
        }
    })
})

// ─── Follower Growth SVG ─────────────────────────────────────────────────────

const FG_W = 300
const FG_H = 140
const FG_PAD_L = 28
const FG_PAD_B = 20
const FG_PAD_T = 10

const followerPaths = computed(() => {
    const pts = props.data.followerGrowthChart
    if (!pts.length) return { ig: '', tt: '' }

    const maxIg = Math.max(...pts.map(p => p.instagram), 1)
    const maxTt = Math.max(...pts.map(p => p.tiktok), 1)
    const maxVal = Math.max(maxIg, maxTt)
    const innerW = FG_W - FG_PAD_L - 5
    const innerH = FG_H - FG_PAD_T - FG_PAD_B

    function toPath(vals: number[]) {
        const mapped = vals.map((v, i) => ({
            x: FG_PAD_L + (i / (vals.length - 1)) * innerW,
            y: FG_PAD_T + innerH - (v / maxVal) * innerH,
        }))
        if (mapped.length === 1) return `M${mapped[0].x},${mapped[0].y}`
        let d = `M${mapped[0].x},${mapped[0].y}`
        for (let i = 1; i < mapped.length; i++) {
            const cpx = (mapped[i - 1].x + mapped[i].x) / 2
            d += ` C${cpx},${mapped[i - 1].y} ${cpx},${mapped[i].y} ${mapped[i].x},${mapped[i].y}`
        }
        return d
    }

    return {
        ig: toPath(pts.map(p => p.instagram)),
        tt: toPath(pts.map(p => p.tiktok)),
    }
})

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
</script>

<template>
    <AppLayout title="Analytics" screen="analytics">

        <!-- Topbar filters -->
        <div class="flex items-center gap-2 mb-4">
            <select
                class="font-sans text-[11px] bg-paper border border-ink-3/40 px-2.5 py-1.5"
                style="border-radius:2px;outline:none"
                :value="filters.days"
                @change="applyFilter(Number(($event.target as HTMLSelectElement).value))">
                <option v-for="d in available_days" :key="d" :value="d">
                    {{ d }} Hari Terakhir
                </option>
            </select>
            <select
                class="font-sans text-[11px] bg-paper border border-ink-3/40 px-2.5 py-1.5"
                style="border-radius:2px;outline:none"
                :value="filters.platform ?? ''"
                @change="applyFilter(undefined, ($event.target as HTMLSelectElement).value)">
                <option value="">Semua Platform</option>
                <option value="instagram">Instagram</option>
                <option value="tiktok">TikTok</option>
                <option value="facebook">Facebook</option>
                <option value="twitter">X / Twitter</option>
                <option value="youtube">YouTube</option>
            </select>
        </div>

        <!-- Sub-tabs -->
        <nav class="flex gap-0 mb-4 border-b border-ink-3/30">
            <button
                class="px-4 py-2 text-xs font-sans font-semibold border-b-2 transition-colors"
                :class="activeTab === 'overview'
                    ? 'border-accent-r text-accent-r'
                    : 'border-transparent text-ink-3 hover:text-ink'"
                @click="activeTab = 'overview'">
                Overview
            </button>
            <button
                class="px-4 py-2 text-xs font-sans font-semibold border-b-2 transition-colors"
                :class="activeTab === 'perkonten'
                    ? 'border-accent-r text-accent-r'
                    : 'border-transparent text-ink-3 hover:text-ink'"
                @click="$inertia.visit(route('analytics.per-konten'))">
                Per Konten
            </button>
        </nav>

        <!-- Overview panel -->
        <div v-if="activeTab === 'overview'">

            <!-- Empty state: no synced data -->
            <div
                v-if="!data.reachByPlatform?.length && !data.topPosts?.length"
                class="mb-4 flex items-center justify-between px-4 py-3 border border-dashed border-ink-3/25 text-xs font-sans text-ink-3"
                style="border-radius:2px">
                <span>Belum ada data analitik — hubungkan platform dan klik Sync Data.</span>
                <a :href="route('settings.index')" class="text-accent-b hover:underline ml-4 shrink-0">Hubungkan Platform →</a>
            </div>

            <!-- KPI row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <KpiCard
                    v-for="(kpi, i) in data.kpis" :key="i"
                    :label="kpi.label"
                    :value="kpi.value"
                    :delta="kpi.delta"
                    :delta-positive="kpi.delta_positive ?? undefined"
                    :subtext="kpi.subtext"
                />
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">

                <!-- Reach per Platform -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-3">Reach per Platform</div>
                    <svg :viewBox="`0 0 ${R_W} ${R_H}`" width="100%" :height="R_H">
                        <line :x1="R_PAD_L" :y1="R_PAD_T" :x2="R_PAD_L" :y2="R_H - R_PAD_B"
                            stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                        <line :x1="R_PAD_L" :y1="R_H - R_PAD_B" :x2="R_W - 5" :y2="R_H - R_PAD_B"
                            stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                        <template v-for="(bar, i) in reachBars" :key="i">
                            <rect :x="bar.x" :y="bar.y" :width="bar.w" :height="bar.h"
                                :fill="bar.fill" :stroke="bar.stroke" stroke-width="1.5"/>
                            <text :x="bar.x + bar.w / 2" :y="R_H - 5"
                                font-size="8" fill="rgba(0,0,0,.45)" text-anchor="middle" font-family="monospace">
                                {{ bar.label }}
                            </text>
                            <text :x="bar.x + bar.w / 2" :y="bar.y - 3"
                                font-size="9" :fill="bar.textColor" text-anchor="middle"
                                font-family="monospace" font-weight="bold">
                                {{ bar.reach }}
                            </text>
                        </template>
                        <text v-if="!reachBars.length" x="150" y="70"
                            font-size="10" fill="rgba(0,0,0,.3)" text-anchor="middle" font-family="monospace">
                            Belum ada data
                        </text>
                    </svg>
                </div>

                <!-- Follower Growth -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-3">Pertumbuhan Followers</div>
                    <svg :viewBox="`0 0 ${FG_W} ${FG_H}`" width="100%" :height="FG_H">
                        <line :x1="FG_PAD_L" :y1="FG_PAD_T" :x2="FG_PAD_L" :y2="FG_H - FG_PAD_B"
                            stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                        <line :x1="FG_PAD_L" :y1="FG_H - FG_PAD_B" :x2="FG_W - 5" :y2="FG_H - FG_PAD_B"
                            stroke="rgba(0,0,0,.1)" stroke-width="1"/>
                        <line :x1="FG_PAD_L" :y1="(FG_H - FG_PAD_B - FG_PAD_T) * 0.5 + FG_PAD_T"
                            :x2="FG_W - 5" :y2="(FG_H - FG_PAD_B - FG_PAD_T) * 0.5 + FG_PAD_T"
                            stroke="rgba(0,0,0,.06)" stroke-width="1" stroke-dasharray="3,3"/>
                        <path v-if="followerPaths.ig" :d="followerPaths.ig"
                            fill="none" stroke="#C96442" stroke-width="2" stroke-linecap="round"/>
                        <path v-if="followerPaths.tt" :d="followerPaths.tt"
                            fill="none" stroke="rgba(0,0,0,.45)" stroke-width="1.5" stroke-dasharray="4,2"/>
                        <!-- Legend -->
                        <line x1="34" :y1="FG_H - 3" x2="52" :y2="FG_H - 3"
                            stroke="#C96442" stroke-width="1.5"/>
                        <text x="55" :y="FG_H - 1" font-size="8" fill="rgba(0,0,0,.45)" font-family="monospace">Instagram</text>
                        <line x1="120" :y1="FG_H - 3" x2="138" :y2="FG_H - 3"
                            stroke="rgba(0,0,0,.45)" stroke-width="1.5" stroke-dasharray="4,2"/>
                        <text x="141" :y="FG_H - 1" font-size="8" fill="rgba(0,0,0,.45)" font-family="monospace">TikTok</text>
                    </svg>
                </div>
            </div>

            <!-- Top posts + Audience -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                <!-- Top 5 posts -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-3">Top 5 Postingan</div>
                    <table class="w-full" style="border-collapse:collapse">
                        <thead>
                            <tr class="border-b border-ink-3/30">
                                <th class="text-left label-upper py-1.5 pr-3 font-normal">Konten</th>
                                <th class="text-right label-upper py-1.5 pr-3 font-normal">Reach</th>
                                <th class="text-right label-upper py-1.5 pr-3 font-normal">Eng.%</th>
                                <th class="text-left label-upper py-1.5 font-normal">Platform</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="post in data.topPosts" :key="post.id"
                                class="border-b border-ink-3/10">
                                <td class="py-2 pr-3 text-xs font-sans truncate max-w-[160px]">{{ post.title }}</td>
                                <td class="py-2 pr-3 text-xs font-sans text-right text-ink-3">{{ fmtNum(post.reach) }}</td>
                                <td class="py-2 pr-3 text-xs font-sans text-right" :class="erClass(post.engagement_rate)">
                                    {{ post.engagement_rate.toFixed(1) }}%
                                </td>
                                <td class="py-2">
                                    <PlatformBadge :platform="post.platform as any" size="sm"/>
                                </td>
                            </tr>
                            <tr v-if="!data.topPosts.length">
                                <td colspan="4" class="py-6 text-center text-xs text-ink-3 font-sans">Belum ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Audience Demographics -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-3">Demografi Audiens · IG</div>
                    <template v-if="data.audience">
                        <div class="label-upper text-ink-3 mb-2">USIA</div>
                        <div v-for="age in data.audience.age" :key="age.label"
                            class="flex items-center gap-2 py-1">
                            <span class="text-[10px] font-sans min-w-[44px]">{{ age.label }}</span>
                            <div class="flex-1 h-1.5 bg-ink-3/10" style="border-radius:1px">
                                <div class="h-full bg-ink-2" style="border-radius:1px;transition:width .3s"
                                    :style="{ width: age.pct + '%' }"></div>
                            </div>
                            <span class="text-[10px] font-sans text-ink-3 min-w-[30px] text-right">{{ age.pct }}%</span>
                        </div>
                        <div class="label-upper text-ink-3 mt-3 mb-2">GENDER</div>
                        <div class="flex h-3 overflow-hidden" style="border-radius:1px">
                            <div :style="{ width: data.audience.gender.female + '%', background: '#C96442', opacity: '.65' }"></div>
                            <div :style="{ width: data.audience.gender.male + '%', background: '#3B6DB5', opacity: '.65' }"></div>
                        </div>
                        <div class="flex justify-between text-[9px] font-sans text-ink-3 mt-1">
                            <span>Perempuan {{ data.audience.gender.female }}%</span>
                            <span>Laki-laki {{ data.audience.gender.male }}%</span>
                        </div>
                        <div class="label-upper text-ink-3 mt-3 mb-1">LOKASI TOP 3</div>
                        <div class="text-[10px] font-sans text-ink-2">{{ data.audience.cities }}</div>
                    </template>
                    <div v-else class="text-xs text-ink-3 font-sans py-4">
                        Data demografi belum tersedia. Hubungkan akun Instagram Business.
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
