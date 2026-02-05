<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Beer, Plus, ArrowRight } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Bar {
    id: number;
    name: string;
    slug: string;
    beers_count: number;
}

interface Props {
    bars: Bar[];
}

defineProps<Props>();
</script>

<template>
    <Head title="Mes Bars" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900">Mes Bars</h1>
                    <p class="mt-2 text-gray-600">Gérez tous vos établissements</p>
                </div>

                <div v-if="bars.length === 0" class="rounded-xl bg-white p-16 text-center shadow-sm ring-1 ring-gray-200">
                    <Beer class="mx-auto h-16 w-16 text-gray-300" />
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucun bar</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Votre bar a été créé lors de l'inscription. Si vous ne le voyez pas, contactez le support.
                    </p>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="bar in bars"
                        :key="bar.id"
                        :href="`/bars/${bar.slug}`"
                        class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-lg hover:ring-indigo-300"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                                        <Beer class="h-6 w-6" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600">{{ bar.name }}</h2>
                                        <p class="mt-1 text-sm text-gray-500">{{ bar.beers_count }} bière{{ bar.beers_count > 1 ? 's' : '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <ArrowRight class="h-5 w-5 text-gray-400 transition-colors group-hover:text-indigo-600" />
                        </div>
                        <div class="mt-4 flex items-center text-sm font-medium text-indigo-600">
                            <span>Accéder au dashboard</span>
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
