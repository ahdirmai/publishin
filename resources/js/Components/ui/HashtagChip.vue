<script setup lang="ts">
const props = defineProps<{
    tag: string
    removable?: boolean
    clickable?: boolean
}>()

const emit = defineEmits<{
    click: [tag: string]
    remove: [tag: string]
}>()
</script>

<template>
    <span
        class="inline-flex items-center gap-1 font-sans text-[11px] text-ink-2 border border-[rgba(28,27,26,0.22)] px-2 py-0.5 transition-colors"
        style="border-radius: 9999px"
        :class="{ 'cursor-pointer hover:border-ink hover:text-ink': clickable }"
        @click="clickable ? emit('click', tag) : undefined"
    >
        #{{ tag.startsWith('#') ? tag.slice(1) : tag }}
        <button
            v-if="removable"
            type="button"
            class="text-ink-3 hover:text-ink leading-none"
            style="font-size: 13px; line-height: 1"
            @click.stop="emit('remove', tag)"
        >×</button>
    </span>
</template>
