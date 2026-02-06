<script setup lang="ts">
import { computed, useAttrs } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineOptions({
    inheritAttrs: false,
});

const attrs = useAttrs();
const forwardedAttrs = computed(() => {
    const { class: _class, ...rest } = attrs as Record<string, unknown>;
    return rest;
});
</script>

<template>
    <div class="flex items-center gap-3">
        <div
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-transparent p-1"
            :class="attrs.class"
            v-bind="forwardedAttrs"
        >
            <AppLogoIcon class="size-full" />
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-gray-900">Tapwise</span>
            <span class="text-xs text-gray-500 -mt-1">L'art du conseil</span>
        </div>
    </div>
</template>
