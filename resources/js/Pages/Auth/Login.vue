<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="min-h-screen grid-paper flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <span class="font-serif italic font-bold text-3xl text-ink">P·</span>
                <div class="text-[10px] uppercase tracking-widest text-ink-3 mt-1">Publishin</div>
            </div>

            <!-- Card -->
            <div class="bg-paper border-[1.5px] border-ink p-6" style="border-radius: 2px">
                <h1 class="font-serif italic font-bold text-xl text-ink mb-6">Masuk</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px"
                            :class="{ 'border-accent-r': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="text-xs text-accent-r mt-1">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px"
                        />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs text-ink-2 cursor-pointer">
                            <input v-model="form.remember" type="checkbox" class="accent-accent-r" />
                            Ingat saya
                        </label>
                        <a :href="route('password.request')" class="text-xs text-accent-b hover:underline">
                            Lupa password?
                        </a>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-ink text-paper font-sans font-semibold uppercase tracking-wide text-xs py-2.5 border-[1.5px] border-ink hover:bg-accent-r hover:border-accent-r transition-colors"
                        style="border-radius: 2px"
                    >
                        {{ form.processing ? 'Memproses…' : 'Masuk' }}
                    </button>
                </form>

                <p class="text-xs text-ink-3 text-center mt-4">
                    Belum punya akun?
                    <a :href="route('register')" class="text-accent-b hover:underline">Daftar gratis</a>
                </p>
            </div>

            <div class="mt-4 text-center">
                <a :href="route('home')" class="text-[10px] font-sans text-ink-3 hover:text-ink">
                    ← Kembali ke beranda
                </a>
            </div>
        </div>
    </div>
</template>
