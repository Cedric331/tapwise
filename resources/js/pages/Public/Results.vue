<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

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

interface Wine {
    id: number;
    name: string;
    color: string;
    grape?: string | null;
    region?: string | null;
    abv_x10: number;
    description?: string | null;
    price?: number | null;
}

interface Recommendation {
    beer?: Beer;
    wine?: Wine;
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
    drinkType: 'beer' | 'wine';
    recommendations: Recommendation[];
    preferences: {
        bitterness?: string;
        color?: string[];
        aromas?: string[];
        max_abv?: number;
        format?: string;
        style?: string;
        brewery?: string;
        grape?: string;
        region?: string;
        max_price?: number;
    };
}

const props = defineProps<Props>();
const isWine = computed(() => props.drinkType === 'wine');

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
                    {{ isWine ? 'Aucun vin ne correspond à vos critères pour le moment.' : 'Aucune bière ne correspond à vos critères pour le moment.' }}
                </p>
            </div>

            <div v-else class="space-y-6">
                <div
                    v-for="(recommendation, index) in recommendations"
                    :key="isWine ? (recommendation.wine?.id ?? index) : (recommendation.beer?.id ?? index)"
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
                                <h3 class="text-2xl font-bold">
                                    {{ isWine ? recommendation.wine?.name : recommendation.beer?.name }}
                                </h3>
                            </div>
                            <p v-if="!isWine && recommendation.beer?.brewery" class="text-lg text-gray-600">
                                {{ recommendation.beer.brewery }}
                            </p>
                            <p v-if="!isWine" class="text-sm text-gray-500">{{ recommendation.beer?.style }}</p>
                            <p v-if="isWine && recommendation.wine?.grape" class="text-sm text-gray-500">
                                Cépage : {{ recommendation.wine.grape }}
                            </p>
                            <p v-if="isWine && recommendation.wine?.region" class="text-sm text-gray-500">
                                Région : {{ recommendation.wine.region }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                <span>
                                    <strong>ABV:</strong>
                                    {{ getAbv(isWine ? recommendation.wine?.abv_x10 ?? 0 : recommendation.beer?.abv_x10 ?? 0) }}%
                                </span>
                                <span v-if="!isWine && recommendation.beer?.ibu">
                                    <strong>IBU:</strong> {{ recommendation.beer.ibu }}
                                </span>
                                <span v-if="(isWine ? recommendation.wine?.price : recommendation.beer?.price)">
                                    <strong>Prix:</strong>
                                    {{ formatPrice(isWine ? recommendation.wine?.price : recommendation.beer?.price) }}
                                </span>
                            </div>

                            <p v-if="isWine ? recommendation.wine?.description : recommendation.beer?.description" class="mt-4 text-gray-700">
                                {{ isWine ? recommendation.wine?.description : recommendation.beer?.description }}
                            </p>

                            <p class="mt-4 text-sm italic text-gray-600">
                                {{ recommendation.explanation }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a
                        :href="`/b/${bar.slug}`"
                        class="inline-block rounded-md px-6 py-3 text-white"
                        :style="{ backgroundColor: primaryColor }"
                    >
                        Recommencer
                    </a>
                    <a
                        :href="`/b/${bar.slug}/menu`"
                        class="inline-block rounded-md border border-gray-200 bg-white px-6 py-3 text-gray-700 transition hover:border-gray-300 hover:bg-gray-50"
                    >
                        Voir la carte
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

