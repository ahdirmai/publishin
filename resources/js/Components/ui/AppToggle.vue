<script setup lang="ts">
const props = withDefaults(defineProps<{
    modelValue: boolean
    disabled?: boolean
    label?: string
}>(), { disabled: false })

const emit = defineEmits<{
    'update:modelValue': [value: boolean]
}>()

function toggle() {
    if (!props.disabled) emit('update:modelValue', !props.modelValue)
}
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="modelValue"
        :disabled="disabled"
        class="relative inline-flex items-center shrink-0 cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        style="width: 30px; height: 17px; border-radius: 9999px; border: 1.5px solid #1C1B1A;"
        :style="{
            backgroundColor: modelValue ? '#1C1B1A' : 'transparent',
        }"
        @click="toggle"
    >
        <span
            class="inline-block transition-transform bg-paper"
            style="width: 11px; height: 11px; border-radius: 9999px; border: 1.5px solid #1C1B1A;"
            :style="{
                transform: modelValue ? 'translateX(14px)' : 'translateX(1px)',
                borderColor: modelValue ? '#FAFAF8' : '#1C1B1A',
            }"
        />
    </button>
</template>
