<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Edit, Trash2, Beer as BeerIcon, Sparkles } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Tag {
    id: number;
    name: string;
}

interface Beer {
    id: number;
    name: string;
    brewery: string | null;
    style: string;
    abv_x10: number;
    is_available: boolean;
    tags: Tag[];
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        is_demo: boolean;
    };
    beers: Beer[];
    tags: Tag[];
}

const props = defineProps<Props>();

const deleteBeer = (beerId: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette bière ?')) {
        router.delete(`/bars/${props.bar.slug}/beers/${beerId}`);
    }
};

const getAbv = (abvX10: number) => {
    return (abvX10 / 10).toFixed(1);
};
</script>

<template>
    <Head title="Bières" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <Link
                        :href="`/bars/${bar.slug}`"
                        class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-amber-800 transition-colors hover:text-amber-700"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Retour au dashboard
                    </Link>
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-800">Catalogue</p>
                            <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Catalogue de bières</h1>
                            <p class="mt-3 text-lg text-gray-600">{{ bar.name }}</p>
                            <p class="mt-2 text-sm text-gray-500">
                                Affinez votre sélection pour des recommandations toujours plus pertinentes.
                            </p>
                        </div>
                        <Link
                            v-if="!bar.is_demo"
                            :href="`/bars/${bar.slug}/beers/create`"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                        >
                            <Plus class="h-4 w-4" />
                            Ajouter une bière
                        </Link>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Sparkles class="h-4 w-4" />
                            Sélection premium
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1">
                            <BeerIcon class="h-4 w-4 text-amber-700" />
                            Recommandations instantanées
                        </span>
                    </div>
                </div>

                <div v-if="beers.length === 0" class="rounded-3xl border border-amber-100 bg-white p-16 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-50">
                        <BeerIcon class="h-8 w-8 text-amber-400" />
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">Aucune bière</h3>
                    <p class="mt-2 text-sm text-gray-600">Commencez par ajouter votre première bière à votre catalogue.</p>
                    <p class="mt-3 text-xs font-medium text-amber-800">Astuce : 3 à 5 références suffisent pour démarrer.</p>
                    <Link
                        v-if="!bar.is_demo"
                        :href="`/bars/${bar.slug}/beers/create`"
                        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                    >
                        <Plus class="h-4 w-4" />
                        Ajouter votre première bière
                    </Link>
                </div>

                <div v-else class="overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-amber-100">
                            <thead class="bg-amber-50/60">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Nom</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Brasserie</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Style</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">ABV</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Tags</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Disponible</th>
                                    <th v-if="!bar.is_demo" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100/60 bg-white">
                                <tr v-for="beer in beers" :key="beer.id" class="transition-colors hover:bg-amber-50/40">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ beer.name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ beer.brewery }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ beer.style }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ getAbv(beer.abv_x10) }}%</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="tag in beer.tags"
                                                :key="tag.id"
                                                class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800"
                                            >
                                                {{ tag.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="beer.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        >
                                            {{ beer.is_available ? 'Oui' : 'Non' }}
                                        </span>
                                    </td>
                                    <td v-if="!bar.is_demo" class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link
                                                :href="`/bars/${bar.slug}/beers/${beer.id}/edit`"
                                                class="inline-flex items-center gap-1 text-amber-800 transition-colors hover:text-amber-700"
                                            >
                                                <Edit class="h-4 w-4" />
                                                Modifier
                                            </Link>
                                            <button
                                                class="inline-flex items-center gap-1 text-red-600 transition-colors hover:text-red-700"
                                                @click="deleteBeer(beer.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
