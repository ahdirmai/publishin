<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{ status?: string }>()

const form = useForm({ email: '' })
function submit() { form.post(route('password.email')) }
</script>

<template>
    <div class="min-h-screen grid-paper flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="font-serif italic font-bold text-3xl text-ink">P·</span>
            </div>
            <div class="bg-paper border-[1.5px] border-ink p-6" style="border-radius: 2px">
                <h1 class="font-serif italic font-bold text-xl text-ink mb-2">Lupa Password</h1>
                <p class="text-xs text-ink-3 mb-6">Masukkan email kamu, kami akan kirim link reset.</p>

                <div v-if="status" class="text-xs text-forest bg-forest/10 border border-forest/30 px-3 py-2 mb-4" style="border-radius: 2px">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Email</label>
                        <input v-model="form.email" type="email"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px" />
                        <p v-if="form.errors.email" class="text-xs text-accent-r mt-1">{{ form.errors.email }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-ink text-paper font-sans font-semibold uppercase tracking-wide text-xs py-2.5 border-[1.5px] border-ink hover:bg-accent-r hover:border-accent-r transition-colors"
                        style="border-radius: 2px">
                        {{ form.processing ? 'Mengirim…' : 'Kirim Link Reset' }}
                    </button>
                </form>
                <p class="text-xs text-ink-3 text-center mt-4">
                    <a :href="route('login')" class="text-accent-b hover:underline">← Kembali ke Login</a>
                </p>
            </div>
        </div>
    </div>
</template>
