<script setup lang="ts">
withDefaults(defineProps<{
    variant?: 'default' | 'primary' | 'danger' | 'ghost'
    size?: 'sm' | 'md' | 'lg'
    disabled?: boolean
    type?: 'button' | 'submit' | 'reset'
}>(), {
    variant: 'default',
    size: 'md',
    disabled: false,
    type: 'button',
})
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center justify-center font-sans font-semibold uppercase tracking-wide transition-colors focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
        style="border-radius: 2px"
        :class="{
            // Sizes
            'text-[10px] px-2.5 py-1': size === 'sm',
            'text-xs px-3 py-1.5': size === 'md',
            'text-xs px-4 py-2': size === 'lg',
            // Variants
            'bg-ink text-paper border-[1.5px] border-ink hover:bg-accent-r hover:border-accent-r': variant === 'default',
            'bg-accent-r text-paper border-[1.5px] border-accent-r hover:bg-[#a8512f] hover:border-[#a8512f]': variant === 'primary',
            'bg-transparent text-accent-r border-[1.5px] border-accent-r hover:bg-accent-r hover:text-paper': variant === 'danger',
            'bg-transparent text-ink-2 border border-[rgba(28,27,26,0.22)] hover:border-ink hover:text-ink': variant === 'ghost',
        }"
    >
        <slot />
    </button>
</template>
