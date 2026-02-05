<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
    bar: {
        id: number;
        name: string;
        logo_path?: string;
        brand_background_color?: string;
        brand_primary_color?: string;
    };
}

const props = defineProps<Props>();

const backgroundColor = props.bar.brand_background_color || '#ffffff';
const primaryColor = props.bar.brand_primary_color || '#4f46e5';
const logoSrc = (() => {
    if (!props.bar.logo_path) return null;
    return props.bar.logo_path.startsWith('assets/')
        ? `/${props.bar.logo_path}`
        : `/storage/${props.bar.logo_path}`;
})();
</script>

<template>
    <Head :title="`${bar.name} - Indisponible`" />

    <div :style="{ backgroundColor }" class="flex min-h-screen items-center justify-center px-4">
        <div class="mx-auto max-w-md text-center">
            <div v-if="logoSrc" class="mb-8">
                <img :src="logoSrc" :alt="bar.name" class="mx-auto h-24" />
            </div>
            <div v-else class="mb-8">
                <h1 class="text-3xl font-bold" :style="{ color: primaryColor }">{{ bar.name }}</h1>
            </div>

            <div class="rounded-lg bg-white p-8 shadow-lg">
                <p class="text-lg text-gray-700">
                    Ce QR code est temporairement désactivé par l'établissement.
                </p>
            </div>
        </div>
    </div>
</template>

