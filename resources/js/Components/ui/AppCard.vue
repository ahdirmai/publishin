<script setup lang="ts">
withDefaults(defineProps<{
    size?: 'default' | 'sm'
    padding?: boolean
}>(), {
    size: 'default',
    padding: true,
})
</script>

<template>
    <div
        class="bg-paper"
        style="border-radius: 2px"
        :class="{
            'border-[1.5px] border-ink': size === 'default',
            'border border-[rgba(28,27,26,0.22)]': size === 'sm',
            'p-4': padding,
        }"
    >
        <slot />
    </div>
</template>
