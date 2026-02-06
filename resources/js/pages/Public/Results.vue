<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Beer {
    id: number;
    name: string;
    brewery: string | null;
    style: string;
    abv_x10: number;
    ibu?: number;
    description?: string;
    price?: number;
    color?: string;
}

interface Recommendation {
    beer: Beer;
    explanation: string;
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        logo_path?: string;
        brand_background_color?: string;
        brand_primary_color?: string;
    };
    recommendations: Recommendation[];
    preferences: {
        bitterness?: string;
        color?: string[];
        aromas?: string[];
        max_abv?: number;
        format?: string;
        style?: string;
        brewery?: string;
        max_price?: number;
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

const formatPrice = (price?: number) => {
    if (!price) return null;
    return (price / 100).toFixed(2) + '€';
};

const getAbv = (abvX10: number) => {
    return (abvX10 / 10).toFixed(1);
};
</script>

<template>
    <Head :title="`${bar.name} - Recommandations`" />

    <div :style="{ backgroundColor }" class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <!-- Logo -->
            <div v-if="logoSrc" class="mb-8 text-center">
                <img :src="logoSrc" :alt="bar.name" class="mx-auto h-24" />
            </div>
            <div v-else class="mb-8 text-center">
                <h1 class="text-3xl font-bold" :style="{ color: primaryColor }">{{ bar.name }}</h1>
            </div>

            <h2 class="mb-8 text-center text-3xl font-bold">Nos recommandations pour vous</h2>

            <div v-if="recommendations.length === 0" class="rounded-lg bg-white p-8 text-center shadow-lg">
                <p class="text-lg text-gray-600">
                    Aucune bière ne correspond à vos critères pour le moment.
                </p>
            </div>

            <div v-else class="space-y-6">
                <div
                    v-for="(recommendation, index) in recommendations"
                    :key="recommendation.beer.id"
                    class="rounded-lg bg-white p-6 shadow-lg"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white"
                                    :style="{ backgroundColor: primaryColor }"
                                >
                                    {{ index + 1 }}
                                </span>
                                <h3 class="text-2xl font-bold">{{ recommendation.beer.name }}</h3>
                            </div>
                            <p v-if="recommendation.beer.brewery" class="text-lg text-gray-600">
                                {{ recommendation.beer.brewery }}
                            </p>
                            <p class="text-sm text-gray-500">{{ recommendation.beer.style }}</p>

                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                <span>
                                    <strong>ABV:</strong> {{ getAbv(recommendation.beer.abv_x10) }}%
                                </span>
                                <span v-if="recommendation.beer.ibu">
                                    <strong>IBU:</strong> {{ recommendation.beer.ibu }}
                                </span>
                                <span v-if="recommendation.beer.price">
                                    <strong>Prix:</strong> {{ formatPrice(recommendation.beer.price) }}
                                </span>
                            </div>

                            <p v-if="recommendation.beer.description" class="mt-4 text-gray-700">
                                {{ recommendation.beer.description }}
                            </p>

                            <p class="mt-4 text-sm italic text-gray-600">
                                {{ recommendation.explanation }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a
                    :href="`/b/${bar.slug}`"
                    class="inline-block rounded-md px-6 py-3 text-white"
                    :style="{ backgroundColor: primaryColor }"
                >
                    Recommencer
                </a>
            </div>
        </div>
    </div>
</template>

