<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        src="/assets/logo.svg"
        alt="Logo TapWise"
        :class="['object-contain', className]"
        v-bind="$attrs"
    />
</template>
