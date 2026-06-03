<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post(route('register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
                <h1 class="font-serif italic font-bold text-xl text-ink mb-6">Buat Akun</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Nama Lengkap</label>
                        <input v-model="form.name" type="text" autocomplete="name"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px"
                            :class="{ 'border-accent-r': form.errors.name }" />
                        <p v-if="form.errors.name" class="text-xs text-accent-r mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Email</label>
                        <input v-model="form.email" type="email" autocomplete="email"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px"
                            :class="{ 'border-accent-r': form.errors.email }" />
                        <p v-if="form.errors.email" class="text-xs text-accent-r mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Password</label>
                        <input v-model="form.password" type="password" autocomplete="new-password"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px" />
                        <p v-if="form.errors.password" class="text-xs text-accent-r mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="label-upper text-ink-2 block mb-1">Konfirmasi Password</label>
                        <input v-model="form.password_confirmation" type="password" autocomplete="new-password"
                            class="w-full bg-paper border border-[rgba(28,27,26,0.22)] px-3 py-2 text-sm text-ink font-sans focus:outline-none focus:border-ink"
                            style="border-radius: 2px" />
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-ink text-paper font-sans font-semibold uppercase tracking-wide text-xs py-2.5 border-[1.5px] border-ink hover:bg-accent-r hover:border-accent-r transition-colors"
                        style="border-radius: 2px">
                        {{ form.processing ? 'Mendaftar…' : 'Daftar Gratis' }}
                    </button>
                </form>

                <p class="text-xs text-ink-3 text-center mt-4">
                    Sudah punya akun?
                    <a :href="route('login')" class="text-accent-b hover:underline">Masuk</a>
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
