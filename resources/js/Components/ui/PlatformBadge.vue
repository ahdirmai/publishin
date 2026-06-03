<script setup lang="ts">
type Platform = 'instagram' | 'facebook' | 'tiktok' | 'twitter' | 'youtube'

withDefaults(defineProps<{
    platform: Platform
    size?: 'sm' | 'md'
}>(), { size: 'md' })

const labels: Record<Platform, string> = {
    instagram: 'IG',
    facebook: 'FB',
    tiktok: 'TT',
    twitter: 'X',
    youtube: 'YT',
}

const colors: Record<Platform, string> = {
    instagram: 'background: linear-gradient(135deg, #E1306C, #833AB4); color: white',
    facebook: 'background: #1877F2; color: white',
    tiktok: 'background: #000; color: white',
    twitter: 'background: #000; color: white',
    youtube: 'background: #FF0000; color: white',
}
</script>

<template>
    <span
        class="inline-flex items-center justify-center font-sans font-bold uppercase"
        style="border-radius: 2px"
        :style="[
            colors[platform],
            size === 'sm' ? 'font-size: 8px; padding: 1px 4px;' : 'font-size: 9px; padding: 2px 5px;',
        ]"
    >
        {{ labels[platform] }}
    </span>
</template>
