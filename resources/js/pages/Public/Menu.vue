<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface BeerTag {
    id: number;
    name: string;
}

interface WineTag {
    id: number;
    name: string;
}

interface Beer {
    id: number;
    name: string;
    brewery: string | null;
    style: string;
    abv_x10: number;
    ibu?: number;
    description?: string;
    price?: number;
    is_on_tap: boolean;
    popularity?: number;
    tags: BeerTag[];
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
    popularity?: number;
    tags: WineTag[];
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        logo_path?: string;
        brand_background_color?: string;
        brand_primary_color?: string;
        offers_beer: boolean;
        offers_wine: boolean;
    };
    beers: Beer[];
    wines: Wine[];
}

const props = defineProps<Props>();

const backgroundColor = props.bar.brand_background_color || '#ffffff';
const primaryColor = props.bar.brand_primary_color || '#4f46e5';
const logoSrc = computed(() => {
    if (!props.bar.logo_path) return null;
    return props.bar.logo_path.startsWith('assets/')
        ? `/${props.bar.logo_path}`
        : `/storage/${props.bar.logo_path}`;
});

const formatPrice = (price?: number | null) => {
    if (!price && price !== 0) return null;
    return (price / 100).toFixed(2) + '€';
};

const getAbv = (abvX10: number) => {
    return (abvX10 / 10).toFixed(1);
};

const beerList = computed(() => (Array.isArray(props.beers) ? props.beers : []));
const wineList = computed(() => (Array.isArray(props.wines) ? props.wines : []));
const hasBeers = computed(() => props.bar.offers_beer && beerList.value.length > 0);
const hasWines = computed(() => props.bar.offers_wine && wineList.value.length > 0);

const activeType = ref<'all' | 'beer' | 'wine'>('all');
const sortBy = ref<'popular' | 'price' | 'abv' | 'name'>('popular');

const sortedBeers = computed(() => {
    const items = [...beerList.value];
    switch (sortBy.value) {
        case 'price':
            return items.sort((a, b) => (a.price ?? Number.MAX_SAFE_INTEGER) - (b.price ?? Number.MAX_SAFE_INTEGER));
        case 'abv':
            return items.sort((a, b) => a.abv_x10 - b.abv_x10);
        case 'name':
            return items.sort((a, b) => a.name.localeCompare(b.name));
        case 'popular':
        default:
            return items.sort((a, b) => (b.popularity ?? 0) - (a.popularity ?? 0));
    }
});

const sortedWines = computed(() => {
    const items = [...wineList.value];
    switch (sortBy.value) {
        case 'price':
            return items.sort((a, b) => (a.price ?? Number.MAX_SAFE_INTEGER) - (b.price ?? Number.MAX_SAFE_INTEGER));
        case 'abv':
            return items.sort((a, b) => a.abv_x10 - b.abv_x10);
        case 'name':
            return items.sort((a, b) => a.name.localeCompare(b.name));
        case 'popular':
        default:
            return items.sort((a, b) => (b.popularity ?? 0) - (a.popularity ?? 0));
    }
});
</script>

<template>
    <Head :title="`${bar.name} - Carte`" />

    <div :style="{ backgroundColor }" class="min-h-screen px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-8">
            <div class="rounded-3xl bg-white/85 p-6 shadow-lg ring-1 ring-black/5 backdrop-blur sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            v-if="logoSrc"
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-black/5"
                        >
                            <img :src="logoSrc" :alt="bar.name" class="h-12 w-12 object-contain" />
                        </div>
                        <div v-else class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                            <span class="text-2xl font-bold" :style="{ color: primaryColor }">{{ bar.name.slice(0, 1) }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ bar.name }}</h1>
                            <p class="text-sm text-gray-500">Carte des boissons</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-4 py-2 text-sm text-gray-600">
                        <span class="font-medium" :style="{ color: primaryColor }">Disponible aujourd’hui</span>
                    </div>
                </div>
            </div>

            <div v-if="!hasBeers && !hasWines" class="rounded-3xl bg-white p-8 text-center shadow-lg">
                <p class="text-base text-gray-600">Aucune boisson disponible pour le moment.</p>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="activeType === 'all' ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            @click="activeType = 'all'"
                        >
                            Tout
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="activeType === 'wine' ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            @click="activeType = 'wine'"
                        >
                            Vins
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="activeType === 'beer' ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            @click="activeType = 'beer'"
                        >
                            Bières
                        </button>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span>Trier par</span>
                        <select
                            v-model="sortBy"
                            class="rounded-full border border-gray-200 bg-white px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                        >
                            <option value="popular">Popularité</option>
                            <option value="price">Prix</option>
                            <option value="abv">Degré</option>
                            <option value="name">Nom</option>
                        </select>
                    </div>
                </div>
            </div>

            <section v-if="hasWines && (activeType === 'all' || activeType === 'wine')" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Vins</h2>
                    <span class="text-xs font-medium text-gray-500">{{ wines.length }} références</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <article
                        v-for="wine in sortedWines"
                        :key="wine.id"
                        class="rounded-3xl border border-amber-100 bg-white p-5 shadow-sm transition hover:shadow-md"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ wine.name }}</h3>
                                <p v-if="wine.grape" class="text-sm text-gray-500">Cépage : {{ wine.grape }}</p>
                                <p v-if="wine.region" class="text-sm text-gray-500">Région : {{ wine.region }}</p>
                            </div>
                            <span
                                v-if="wine.price"
                                class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800"
                            >
                                {{ formatPrice(wine.price) }}
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-600">
                            <span class="rounded-full bg-gray-50 px-3 py-1">ABV {{ getAbv(wine.abv_x10) }}%</span>
                            <span v-if="sortBy === 'popular'" class="rounded-full bg-gray-50 px-3 py-1">
                                Popularité {{ wine.popularity ?? 0 }}
                            </span>
                        </div>
                        <p v-if="wine.description" class="mt-4 text-sm text-gray-700">
                            {{ wine.description }}
                        </p>
                        <div v-if="wine.tags.length" class="mt-4 flex flex-wrap gap-2">
                            <span
                                v-for="tag in wine.tags"
                                :key="tag.id"
                                class="rounded-full border border-amber-100 bg-amber-50/40 px-2.5 py-1 text-xs text-amber-800"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </article>
                </div>
            </section>

            <section v-if="hasBeers && (activeType === 'all' || activeType === 'beer')" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Bières</h2>
                    <span class="text-xs font-medium text-gray-500">{{ beers.length }} références</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <article
                        v-for="beer in sortedBeers"
                        :key="beer.id"
                        class="rounded-3xl border border-amber-100 bg-white p-5 shadow-sm transition hover:shadow-md"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ beer.name }}</h3>
                                <p v-if="beer.brewery" class="text-sm text-gray-500">{{ beer.brewery }}</p>
                                <p v-if="beer.style" class="text-sm text-gray-500">Style : {{ beer.style }}</p>
                            </div>
                            <span
                                v-if="beer.price"
                                class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800"
                            >
                                {{ formatPrice(beer.price) }}
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-600">
                            <span class="rounded-full bg-gray-50 px-3 py-1">ABV {{ getAbv(beer.abv_x10) }}%</span>
                            <span v-if="beer.ibu" class="rounded-full bg-gray-50 px-3 py-1">IBU {{ beer.ibu }}</span>
                            <span class="rounded-full bg-gray-50 px-3 py-1">{{ beer.is_on_tap ? 'Pression' : 'Bouteille' }}</span>
                            <span v-if="sortBy === 'popular'" class="rounded-full bg-gray-50 px-3 py-1">
                                Popularité {{ beer.popularity ?? 0 }}
                            </span>
                        </div>
                        <p v-if="beer.description" class="mt-4 text-sm text-gray-700">
                            {{ beer.description }}
                        </p>
                        <div v-if="beer.tags.length" class="mt-4 flex flex-wrap gap-2">
                            <span
                                v-for="tag in beer.tags"
                                :key="tag.id"
                                class="rounded-full border border-amber-100 bg-amber-50/40 px-2.5 py-1 text-xs text-amber-800"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </div>
</template>

