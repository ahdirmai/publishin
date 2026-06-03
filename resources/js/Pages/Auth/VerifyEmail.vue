<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const form = useForm({})

function resend() {
    form.post(route('verification.send'))
}
</script>

<template>
    <div class="min-h-screen grid-paper flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="font-serif italic font-bold text-3xl text-ink">P·</span>
            </div>
            <div class="bg-paper border-[1.5px] border-ink p-6" style="border-radius: 2px">
                <h1 class="font-serif italic font-bold text-xl text-ink mb-2">Verifikasi Email</h1>
                <p class="text-xs text-ink-3 mb-6">
                    Link verifikasi sudah dikirim ke email kamu. Cek inbox (atau folder spam).
                </p>

                <div v-if="(page.props as any).flash?.status === 'verification-link-sent'"
                    class="text-xs text-forest bg-forest/10 border border-forest/30 px-3 py-2 mb-4"
                    style="border-radius: 2px">
                    Link verifikasi baru sudah dikirim ulang.
                </div>

                <form @submit.prevent="resend">
                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-ink text-paper font-sans font-semibold uppercase tracking-wide text-xs py-2.5 border-[1.5px] border-ink hover:bg-accent-r hover:border-accent-r transition-colors"
                        style="border-radius: 2px">
                        {{ form.processing ? 'Mengirim…' : 'Kirim Ulang Link' }}
                    </button>
                </form>

                <form method="POST" :action="route('logout')" class="mt-3">
                    <input type="hidden" name="_token" :value="(page.props as any)._token">
                    <button type="submit" class="w-full text-xs text-ink-3 hover:text-ink py-1.5">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
