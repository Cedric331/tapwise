<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Beer, Wine, CheckCircle2, QrCode, Sparkles } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        offers_beer: boolean;
        offers_wine: boolean;
    };
    stats: {
        total_beers: number;
        available_beers: number;
        on_tap_beers: number;
        total_tags: number;
        total_wines: number;
        available_wines: number;
        total_wine_tags: number;
        total_scans: number;
        scans_last_30_days: number;
        recommendations_last_30_days: number;
    };
    scanSeries: Array<{ label: string; value: number }>;
    recommendationSeries: Array<{ label: string; value: number }>;
    topBeers: Array<{ id: number; name: string; count: number }>;
    topWines: Array<{ id: number; name: string; count: number }>;
}

const props = defineProps<Props>();

const maxScanValue = Math.max(...props.scanSeries.map((item) => item.value), 1);
const maxRecValue = Math.max(...props.recommendationSeries.map((item) => item.value), 1);
</script>

<template>
    <Head title="Statistiques" />

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
                    <p class="text-sm font-medium text-amber-800">Statistiques</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Statistiques</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Suivez les statistiques de votre établissement pour optimiser les recommandations.
                    </p>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm lg:col-span-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Indicateurs clés</h2>
                                <p class="text-sm text-gray-500">Vue rapide de l’activité</p>
                            </div>
                        </div>
                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-amber-800">Total scans</span>
                                    <QrCode class="h-4 w-4 text-amber-700" />
                                </div>
                                <p class="mt-3 text-2xl font-bold text-gray-900">{{ stats.total_scans }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Depuis le lancement</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-emerald-50/40 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-emerald-700">Scans 30 jours</span>
                                    <CheckCircle2 class="h-4 w-4 text-emerald-600" />
                                </div>
                                <p class="mt-3 text-2xl font-bold text-emerald-700">{{ stats.scans_last_30_days }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Activité récente</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-amber-800">Reco 30 jours</span>
                                    <Sparkles class="h-4 w-4 text-amber-700" />
                                </div>
                                <p class="mt-3 text-2xl font-bold text-amber-700">{{ stats.recommendations_last_30_days }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Questionnaires validés</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Catalogue</h2>
                                <p class="text-sm text-gray-500">Synthèse des stocks</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div v-if="bar.offers_beer" class="rounded-2xl border border-amber-100 bg-white px-4 py-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700">Bières</span>
                                    <span class="font-semibold text-gray-900">{{ stats.total_beers }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>Disponibles</span>
                                    <span class="font-medium text-emerald-700">{{ stats.available_beers }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>En pression</span>
                                    <span class="font-medium text-amber-700">{{ stats.on_tap_beers }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>Tags</span>
                                    <span class="font-medium text-amber-700">{{ stats.total_tags }}</span>
                                </div>
                            </div>
                            <div v-if="bar.offers_wine" class="rounded-2xl border border-amber-100 bg-white px-4 py-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700">Vins</span>
                                    <span class="font-semibold text-gray-900">{{ stats.total_wines }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>Disponibles</span>
                                    <span class="font-medium text-emerald-700">{{ stats.available_wines }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <span>Tags</span>
                                    <span class="font-medium text-amber-700">{{ stats.total_wine_tags }}</span>
                                </div>
                            </div>
                            <p v-if="!bar.offers_beer && !bar.offers_wine" class="text-sm text-gray-500">
                                Aucun catalogue activé.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Scans par jour</h2>
                                <p class="text-sm text-gray-500">14 derniers jours</p>
                            </div>
                        </div>
                        <div class="mt-6 flex items-end gap-2">
                            <div
                                v-for="item in scanSeries"
                                :key="item.label"
                                class="flex flex-1 flex-col items-center gap-2"
                            >
                                <div
                                    class="w-full rounded-lg bg-amber-200/70 transition-all"
                                    :style="{ height: `${Math.max((item.value / maxScanValue) * 120, 6)}px` }"
                                ></div>
                                <span class="text-[10px] text-gray-500">{{ item.label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Recommandations par jour</h2>
                                <p class="text-sm text-gray-500">14 derniers jours</p>
                            </div>
                        </div>
                        <div class="mt-6 flex items-end gap-2">
                            <div
                                v-for="item in recommendationSeries"
                                :key="item.label"
                                class="flex flex-1 flex-col items-center gap-2"
                            >
                                <div
                                    class="w-full rounded-lg bg-amber-500/60 transition-all"
                                    :style="{ height: `${Math.max((item.value / maxRecValue) * 120, 6)}px` }"
                                ></div>
                                <span class="text-[10px] text-gray-500">{{ item.label }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <div v-if="bar.offers_beer" class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Top bières recommandées</h2>
                                <p class="text-sm text-gray-500">30 derniers jours</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <div
                                v-for="(item, index) in topBeers"
                                :key="item.id"
                                class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/40 px-4 py-3"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-600 text-sm font-semibold text-white">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                </div>
                                <span class="text-sm font-semibold text-amber-800">{{ item.count }}</span>
                            </div>
                            <p v-if="topBeers.length === 0" class="text-sm text-gray-500">
                                Pas encore de recommandations enregistrées.
                            </p>
                        </div>
                    </div>

                    <div v-if="bar.offers_wine" class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Top vins recommandés</h2>
                                <p class="text-sm text-gray-500">30 derniers jours</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <div
                                v-for="(item, index) in topWines"
                                :key="item.id"
                                class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/40 px-4 py-3"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-600 text-sm font-semibold text-white">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                </div>
                                <span class="text-sm font-semibold text-amber-800">{{ item.count }}</span>
                            </div>
                            <p v-if="topWines.length === 0" class="text-sm text-gray-500">
                                Pas encore de recommandations enregistrées.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

