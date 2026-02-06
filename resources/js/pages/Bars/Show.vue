<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle2, AlertCircle, Beer, Settings, QrCode, Copy, ExternalLink, Plus, Sparkles, CreditCard } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

interface Beer {
    id: number;
    name: string;
    brewery: string | null;
    style: string;
    is_available: boolean;
    tags: Array<{ id: number; name: string }>;
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        is_demo: boolean;
        qr_enabled: boolean;
    };
    stats: {
        total_beers: number;
        available_beers: number;
        on_tap_beers: number;
        total_tags: number;
    };
    recentBeers: Beer[];
    publicUrl: string;
    subscription: {
        status: 'active' | 'trial' | 'inactive';
        trialEndsAt?: string | null;
        trialDaysLeft?: number | null;
        canAccessPublic: boolean;
    };
}

const props = defineProps<Props>();
const copied = ref(false);

const copyUrl = async () => {
    await navigator.clipboard.writeText(props.publicUrl);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
};

const startSubscription = () => {
    router.post(`/bars/${props.bar.slug}/subscription/checkout`, {}, { preserveScroll: true });
};
</script>

<template>
    <Head :title="bar.name" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-10">
                    <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                        <div>
                            <p class="text-sm font-medium text-amber-800">Tableau de bord</p>
                            <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ bar.name }}</h1>
                            <p class="mt-3 max-w-2xl text-lg text-gray-600">
                                Une vue claire sur votre activité, vos bières et les actions prioritaires.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link
                                    :href="`/bars/${bar.slug}/beers/create`"
                                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                                >
                                    <Plus class="h-4 w-4" />
                                    Ajouter une bière
                                </Link>
                                <Link
                                    :href="`/bars/${bar.slug}/qr-code`"
                                    class="inline-flex items-center gap-2 rounded-lg border-2 border-amber-200 bg-white px-5 py-3 text-sm font-semibold text-amber-800 transition-all hover:border-amber-300 hover:bg-amber-50"
                                >
                                    <QrCode class="h-4 w-4" />
                                    QR Code
                                </Link>
                            </div>
                        </div>
                        <div class="relative hidden lg:block z-0">
                            <div class="relative">
                                <div class="absolute -left-6 -top-6 h-40 w-40 rounded-full bg-amber-100/70 blur-2xl"></div>
                                <div class="relative z-10">
                                    <img
                                        src="/assets/illustration-qr-frame.png"
                                        alt="Illustration QR code"
                                        class="h-auto w-full max-w-sm drop-shadow-xl"
                                    />
                                </div>
                                <div class="absolute -bottom-25 -right-15 z-0 opacity-80 pointer-events-none">
                                    <img
                                        src="/assets/illustration-bartender.png"
                                        alt="Illustration barman"
                                        class="h-auto w-48 max-w-none"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <div v-if="bar.is_demo" class="mt-4 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                        <AlertCircle class="h-5 w-5 text-amber-800" />
                        <p class="text-sm font-medium text-amber-800">Bar de démonstration (lecture seule)</p>
                    </div>
                    <div v-if="!bar.qr_enabled" class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <AlertCircle class="h-5 w-5 text-red-600" />
                        <p class="text-sm font-medium text-red-800">Le QR code est actuellement désactivé</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="mb-6">
                    <p class="text-sm font-medium text-amber-800">Indicateurs clés</p>
                    <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-2xl font-semibold text-gray-900">Votre activité en un coup d’œil</h2>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Mesurez l’impact de vos recommandations et de votre catalogue.</p>
                </div>
                <div class="mb-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_beers }}</p>
                                <p class="mt-1 text-xs text-gray-500">bières</p>
                            </div>
                            <div class="rounded-full bg-amber-50 p-3 text-amber-700">
                                <Beer class="h-6 w-6" />
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-medium text-amber-800">Catalogue actif</p>
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-100/50 blur-2xl"></div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Disponibles</p>
                                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ stats.available_beers }}</p>
                                <p class="mt-1 text-xs text-gray-500">en stock</p>
                            </div>
                            <div class="rounded-full bg-emerald-50 p-3 text-emerald-600">
                                <CheckCircle2 class="h-6 w-6" />
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-medium text-emerald-700">Prêtes à servir</p>
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-emerald-100/50 blur-2xl"></div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">En pression</p>
                                <p class="mt-2 text-3xl font-bold text-amber-700">{{ stats.on_tap_beers }}</p>
                                <p class="mt-1 text-xs text-gray-500">au comptoir</p>
                            </div>
                            <div class="rounded-full bg-amber-50 p-3 text-amber-700">
                                <Beer class="h-6 w-6" />
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-medium text-amber-800">Mises en avant</p>
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-100/50 blur-2xl"></div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Tags</p>
                                <p class="mt-2 text-3xl font-bold text-amber-700">{{ stats.total_tags }}</p>
                                <p class="mt-1 text-xs text-gray-500">catégories</p>
                            </div>
                            <div class="rounded-full bg-amber-50 p-3 text-amber-700">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-medium text-amber-800">Accords & styles</p>
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-100/50 blur-2xl"></div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-10 rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Actions rapides</h2>
                            <p class="mt-1 text-sm text-gray-600">Passez à l’essentiel en un clic.</p>
                        </div>
                        <span class="hidden items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800 sm:inline-flex">
                            <Sparkles class="h-4 w-4" />
                            Suggestions Tapwise
                        </span>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <Link
                            :href="`/bars/${bar.slug}/beers`"
                            class="group relative flex items-center gap-4 rounded-2xl border border-amber-100 bg-amber-50/30 p-6 transition-all hover:bg-amber-50 hover:shadow-md"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-amber-700 shadow-sm transition-colors group-hover:bg-amber-700 group-hover:text-white">
                                <Beer class="h-6 w-6" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 group-hover:text-amber-800">Gérer les bières</h3>
                                <p class="mt-1 text-sm text-gray-500">Catalogue complet</p>
                            </div>
                            <ExternalLink class="h-5 w-5 text-gray-400 transition-colors group-hover:text-amber-700" />
                        </Link>

                        <Link
                            :href="`/bars/${bar.slug}/settings`"
                            class="group relative flex items-center gap-4 rounded-2xl border border-amber-100 bg-amber-50/30 p-6 transition-all hover:bg-amber-50 hover:shadow-md"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-amber-700 shadow-sm transition-colors group-hover:bg-amber-700 group-hover:text-white">
                                <Settings class="h-6 w-6" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 group-hover:text-amber-800">Paramètres</h3>
                                <p class="mt-1 text-sm text-gray-500">Personnalisation</p>
                            </div>
                            <ExternalLink class="h-5 w-5 text-gray-400 transition-colors group-hover:text-amber-700" />
                        </Link>

                        <Link
                            :href="`/bars/${bar.slug}/qr-code`"
                            class="group relative flex items-center gap-4 rounded-2xl border border-amber-100 bg-amber-50/30 p-6 transition-all hover:bg-amber-50 hover:shadow-md"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-amber-700 shadow-sm transition-colors group-hover:bg-amber-700 group-hover:text-white">
                                <QrCode class="h-6 w-6" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 group-hover:text-amber-800">QR Code</h3>
                                <p class="mt-1 text-sm text-gray-500">Télécharger</p>
                            </div>
                            <ExternalLink class="h-5 w-5 text-gray-400 transition-colors group-hover:text-amber-700" />
                        </Link>
                    </div>
                </div>

                <!-- Public URL Card -->
                <div class="relative mb-10 overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 to-amber-800 p-8 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-lg font-semibold text-white">URL publique</h3>
                                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">Partage instantané</span>
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    :class="subscription.canAccessPublic ? 'bg-emerald-500/25 text-white' : 'bg-white/20 text-white'"
                                >
                                    {{ subscription.canAccessPublic ? 'Lien actif' : 'Lien désactivé' }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-amber-100">Partagez cette URL avec vos clients</p>
                            <div class="mt-4 flex gap-2">
                                <input
                                    :value="publicUrl"
                                    readonly
                                    class="flex-1 rounded-lg border-0 bg-white/20 px-4 py-2.5 font-mono text-sm text-white placeholder:text-amber-100 backdrop-blur-sm focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50"
                                />
                                <button
                                    type="button"
                                    class="flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-amber-700 shadow-sm transition-colors hover:bg-amber-50"
                                    @click="copyUrl"
                                >
                                    <Copy class="h-4 w-4" />
                                    <span>{{ copied ? 'Copié !' : 'Copier' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="pointer-events-none absolute right-0 top-0 h-48 w-48 -translate-y-12 translate-x-12 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-16 left-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                </div>

                <!-- Recent Beers -->
                <div v-if="recentBeers.length > 0">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Bières récentes</h2>
                        <Link
                            :href="`/bars/${bar.slug}/beers`"
                            class="flex items-center gap-1 text-sm font-medium text-amber-800 transition-colors hover:text-amber-700"
                        >
                            Voir tout
                            <ExternalLink class="h-4 w-4" />
                        </Link>
                    </div>
                    <div class="overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-amber-100">
                                <thead class="bg-amber-50/60">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Brasserie</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Style</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Tags</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-amber-100/60 bg-white">
                                    <tr v-for="beer in recentBeers" :key="beer.id" class="transition-colors hover:bg-amber-50/40">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ beer.name }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ beer.brewery }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ beer.style }}</td>
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
                                                {{ beer.is_available ? 'Disponible' : 'Indisponible' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-3xl border border-amber-100 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-50">
                        <Beer class="h-8 w-8 text-amber-400" />
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">Aucune bière</h3>
                    <p class="mt-2 text-sm text-gray-600">Commencez par ajouter votre première bière à votre catalogue.</p>
                    <p class="mt-3 text-xs font-medium text-amber-800">Astuce : 5 à 7 références suffisent pour des recommandations efficaces.</p>
                    <Link
                        v-if="!bar.is_demo"
                        :href="`/bars/${bar.slug}/beers/create`"
                        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                    >
                        <Plus class="h-4 w-4" />
                        Ajouter une bière
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
