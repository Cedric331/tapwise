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

    <div :style="{ backgroundColor }" class="min-h-screen px-4 py-16">
        <div class="mx-auto max-w-4xl">
            <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <div class="mb-6 inline-flex items-center gap-3 rounded-full border border-amber-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-800">
                        Tapwise
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Lien public
                    </div>

                    <div v-if="logoSrc" class="mb-6">
                        <img :src="logoSrc" :alt="bar.name" class="h-16 w-auto" />
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl" :style="{ color: primaryColor }">
                        {{ bar.name }}
                    </h1>
                    <p class="mt-4 text-lg text-gray-700">
                        Ce lien n'est pas disponible pour le moment.
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        Revenez un peu plus tard ou contactez l'etablissement si besoin.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm">
                            Service temporairement indisponible
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-800 shadow-sm">
                            Merci de votre comprehension
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -left-6 -top-6 h-40 w-40 rounded-full bg-amber-100/70 blur-3xl"></div>
                    <div class="relative overflow-hidden rounded-3xl border border-amber-100 bg-white p-8 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Acces indisponible</p>
                                <p class="mt-2 text-sm text-gray-600">
                                    Le lien est desactive pour le moment.
                                </p>
                            </div>
                            <div class="rounded-full bg-amber-50 p-3 text-amber-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-between gap-4">
                            <img src="/assets/illustration-bartender.png" alt="Illustration barman" class="h-24 w-auto" />
                            <img src="/assets/illustration-beer-glass.png" alt="Illustration verre" class="h-20 w-auto" />
                        </div>
                    </div>
                    <div class="absolute -bottom-10 right-6 h-32 w-32 rounded-full bg-amber-100/70 blur-3xl"></div>
                </div>
            </div>
        </div>
    </div>
</template>

