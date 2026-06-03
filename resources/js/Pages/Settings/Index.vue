<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PlatformBadge from '@/Components/ui/PlatformBadge.vue'
import AppButton from '@/Components/ui/AppButton.vue'
import AppToggle from '@/Components/ui/AppToggle.vue'

interface Account {
    id: number
    platform: string
    username: string
    display_name: string
    follower_count: number
    is_active: boolean
    token_expires_at: string | null
}

interface UserData {
    name: string
    email: string
    timezone: string
    initials: string
}

interface NotificationSettings {
    email_weekly_summary: boolean
    push_post_published: boolean
    push_mentions: boolean
    email_monthly_report: boolean
    push_schedule_reminder: boolean
}

interface SubscriptionData {
    plan_name: string
    price: string
    valid_until: string
    features: string[]
}

const page = usePage()
const flashSuccess = computed(() => (page.props.flash as any)?.success)
const flashError   = computed(() => (page.props.errors as any)?.error)

const props = defineProps<{
    user: UserData
    accounts: Account[]
    notifications: NotificationSettings
    subscription: SubscriptionData | null
}>()

// ─── Profile form ─────────────────────────────────────────────────────────────

const profile = reactive({
    name:     props.user.name,
    email:    props.user.email,
    timezone: props.user.timezone,
})

const profileSaving  = ref(false)
const profileMessage = ref<string | null>(null)
const profileError   = ref<string | null>(null)

async function saveProfile() {
    profileSaving.value  = true
    profileMessage.value = null
    profileError.value   = null
    try {
        const res = await fetch(route('api.settings.profile'), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(profile),
        })
        const data = await res.json()
        if (!res.ok) throw new Error(Object.values(data.errors ?? {}).flat()[0] as string ?? data.message)
        profileMessage.value = data.message
    } catch (e: any) {
        profileError.value = e.message
    } finally {
        profileSaving.value = false
    }
}

// ─── Notification toggles ─────────────────────────────────────────────────────

const notif = reactive({ ...props.notifications })
const notifSaving  = ref(false)
const notifMessage = ref<string | null>(null)

async function saveNotifications() {
    notifSaving.value = true
    notifMessage.value = null
    try {
        const res = await fetch(route('api.settings.notifications'), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(notif),
        })
        const data = await res.json()
        notifMessage.value = data.message
    } catch (_) {
        // silent
    } finally {
        notifSaving.value = false
    }
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function csrfToken(): string {
    return (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? ''
}

function fmtFollowers(n: number): string {
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
    if (n >= 1_000) return (n / 1_000).toFixed(1) + 'K'
    return n.toString()
}

const timezones = [
    { value: 'Asia/Jakarta',  label: 'Asia/Jakarta (WIB UTC+7)' },
    { value: 'Asia/Makassar', label: 'Asia/Makassar (WITA UTC+8)' },
    { value: 'Asia/Jayapura', label: 'Asia/Jayapura (WIT UTC+9)' },
]

const notifItems = [
    { key: 'email_weekly_summary',   label: 'Email: ringkasan mingguan' },
    { key: 'push_post_published',    label: 'Push: konten berhasil dipublikasi' },
    { key: 'push_mentions',          label: 'Push: mention & komentar baru' },
    { key: 'email_monthly_report',   label: 'Email: laporan bulanan otomatis' },
    { key: 'push_schedule_reminder', label: 'Push: pengingat jadwal posting' },
] as const
</script>

<template>
    <AppLayout title="Pengaturan" screen="settings">

        <!-- Topbar actions -->
        <div class="topbar-actions">
            <AppButton variant="primary" size="sm" :disabled="profileSaving" @click="saveProfile">
                Simpan Perubahan
            </AppButton>
        </div>

        <!-- Flash messages from OAuth callbacks -->
        <div v-if="flashSuccess"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-forest/40 bg-forest/5 text-forest"
            style="border-radius:2px">
            ✓ {{ flashSuccess }}
        </div>
        <div v-if="flashError"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-accent-r/40 bg-accent-r/5 text-accent-r"
            style="border-radius:2px">
            {{ flashError }}
        </div>

        <!-- Save feedback -->
        <div v-if="profileMessage"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-forest/40 bg-forest/5 text-forest"
            style="border-radius:2px">
            ✓ {{ profileMessage }}
        </div>
        <div v-if="profileError"
            class="mb-4 px-4 py-2.5 text-xs font-sans border border-accent-r/40 bg-accent-r/5 text-accent-r"
            style="border-radius:2px">
            {{ profileError }}
        </div>

        <div class="grid grid-cols-2 gap-3">

            <!-- Left column: Profile + Platform connections -->
            <div class="flex flex-col gap-3">

                <!-- Profile -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-4">Profil Akun</div>

                    <!-- Avatar row -->
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 rounded-full border-[1.5px] border-ink flex items-center justify-center font-display italic font-bold text-lg text-ink flex-shrink-0">
                            {{ user.initials }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold font-sans text-ink">{{ user.name }}</div>
                            <div class="text-[10px] font-sans text-ink-3">{{ user.email }}</div>
                            <div v-if="subscription" class="text-[9px] font-sans text-accent-b mt-0.5">
                                {{ subscription.plan_name }} · aktif {{ subscription.valid_until }}
                            </div>
                        </div>
                    </div>

                    <!-- Form fields -->
                    <div class="flex flex-col gap-3">
                        <div>
                            <label class="label-upper text-ink-3 mb-1 block">Nama</label>
                            <input
                                v-model="profile.name"
                                type="text"
                                class="w-full font-sans text-[11px] bg-paper border border-ink-3/40 px-3 py-2"
                                style="border-radius:2px;outline:none"/>
                        </div>
                        <div>
                            <label class="label-upper text-ink-3 mb-1 block">Email</label>
                            <input
                                v-model="profile.email"
                                type="email"
                                class="w-full font-sans text-[11px] bg-paper border border-ink-3/40 px-3 py-2"
                                style="border-radius:2px;outline:none"/>
                        </div>
                        <div>
                            <label class="label-upper text-ink-3 mb-1 block">Zona Waktu</label>
                            <select
                                v-model="profile.timezone"
                                class="w-full font-sans text-[11px] bg-paper border border-ink-3/40 px-3 py-2"
                                style="border-radius:2px;outline:none">
                                <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
                                    {{ tz.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Platform connections -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-4">Platform Terhubung</div>
                    <div class="flex flex-col gap-2">
                        <!-- Connected accounts -->
                        <template v-for="account in accounts" :key="account.id">
                            <div class="flex items-center justify-between px-3 py-2 border border-ink-3/10"
                                style="border-radius:2px">
                                <span class="flex items-center gap-2 text-xs font-sans">
                                    <PlatformBadge :platform="account.platform as any" size="sm"/>
                                    @{{ account.username }} · {{ fmtFollowers(account.follower_count) }}
                                </span>
                                <span class="text-[9px] font-sans font-bold text-forest">✓ Terhubung</span>
                            </div>
                        </template>

                        <!-- Unconnected platforms -->
                        <template v-for="p in ['instagram','tiktok','threads','twitter','youtube']" :key="p">
                            <div
                                v-if="!accounts.find(a => a.platform === p)"
                                class="flex flex-col gap-1 px-3 py-2 border border-dashed border-ink-3/25"
                                style="border-radius:2px">
                                <div class="flex items-center justify-between">
                                    <span class="flex items-center gap-2 text-xs font-sans text-ink-3">
                                        <PlatformBadge :platform="p as any" size="sm"/>
                                        {{ { instagram: 'Instagram', tiktok: 'TikTok', threads: 'Threads', twitter: 'X/Twitter', youtube: 'YouTube' }[p] }}
                                    </span>
                                    <template v-if="['instagram','tiktok','threads'].includes(p)">
                                        <a
                                            :href="route('social.redirect', p)"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-[10px] font-sans text-accent-b hover:underline">
                                            + Hubungkan ↗
                                        </a>
                                    </template>
                                    <span v-else class="text-[9px] font-sans text-ink-3 italic">Segera hadir</span>
                                </div>
                                <p v-if="p === 'instagram'" class="text-[9px] font-sans text-ink-3 leading-relaxed">
                                    Login langsung dengan akun Instagram Business atau Creator.
                                </p>
                                <p v-if="p === 'threads'" class="text-[9px] font-sans text-ink-3 leading-relaxed">
                                    Hubungkan akun Threads untuk publish dan lihat analitik.
                                </p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right column: Notifications + Billing -->
            <div class="flex flex-col gap-3">

                <!-- Notifications -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="flex items-center justify-between mb-4">
                        <div class="label-upper text-ink-3">Notifikasi</div>
                        <span v-if="notifMessage" class="text-[9px] font-sans text-forest">✓ Tersimpan</span>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div
                            v-for="item in notifItems" :key="item.key"
                            class="flex items-center justify-between">
                            <span class="text-xs font-sans">{{ item.label }}</span>
                            <AppToggle
                                v-model="notif[item.key]"
                                @update:model-value="saveNotifications"/>
                        </div>
                    </div>
                </div>

                <!-- Plan & Billing -->
                <div class="bg-paper border-[1.5px] border-ink p-4" style="border-radius:2px">
                    <div class="label-upper text-ink-3 mb-4">Plan &amp; Billing</div>

                    <template v-if="subscription">
                        <div class="border-[1.5px] border-ink p-3 mb-3" style="border-radius:2px;background:#fafaf8">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-display italic font-bold text-sm text-ink">{{ subscription.plan_name }}</span>
                                <span class="text-[10px] font-sans text-accent-b font-semibold">{{ subscription.price }}</span>
                            </div>
                            <div class="text-[10px] font-sans text-ink-3 mb-3">
                                Aktif hingga {{ subscription.valid_until }}
                            </div>
                            <div class="flex flex-col gap-1">
                                <span v-for="feat in subscription.features" :key="feat"
                                    class="text-[10px] font-sans text-ink">
                                    ✓ {{ feat }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <AppButton variant="default" size="sm" class="flex-1 justify-center">
                                Upgrade ke Enterprise
                            </AppButton>
                            <AppButton variant="danger" size="sm">Batalkan</AppButton>
                        </div>
                    </template>

                    <div v-else class="text-xs font-sans text-ink-3 py-4">
                        Tidak ada langganan aktif.
                        <a href="#" class="text-accent-b hover:underline">Pilih plan →</a>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>
