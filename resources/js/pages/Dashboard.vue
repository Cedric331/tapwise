<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Calendar,
    CheckCircle2,
    Clock,
    QrCode,
    Sparkles,
    Star,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 rounded-3xl bg-[#FDFDFC] p-6 text-[#1b1b18] md:p-8">
            <section
                class="relative overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-white via-amber-50/40 to-amber-100/30 p-8 shadow-sm"
            >
                <div class="relative z-10 grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                    <div>
                        <p class="text-sm font-medium text-amber-700">Tableau de bord Tapwise</p>
                        <h1 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl">
                            Pilotez l'expérience client en un clin d'œil
                        </h1>
                        <p class="mt-4 text-lg text-gray-600">
                            Suivez les interactions, optimisez la carte et gardez une longueur d'avance sur les préférences de vos clients.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Link
                                href="/bars"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-md transition-all hover:shadow-lg"
                            >
                                Gérer mes établissements
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <Link
                                href="/bars"
                                class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-white px-5 py-3 text-sm font-semibold text-amber-800 transition-all hover:border-amber-300 hover:bg-amber-50"
                            >
                                Voir la dernière activité
                            </Link>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-4 text-xs text-gray-500">
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-4 w-4 text-green-600" />
                                <span>Configuration complète</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Clock class="h-4 w-4 text-amber-700" />
                                <span>Dernière mise à jour il y a 2 h</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Scans QR aujourd'hui</span>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                                    <QrCode class="h-5 w-5" />
                                </div>
                            </div>
                            <p class="mt-4 text-2xl font-bold text-gray-900">128</p>
                            <p class="mt-2 text-xs text-emerald-600">+18% vs hier</p>
                        </div>
                        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Recommandations</span>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                                    <Sparkles class="h-5 w-5" />
                                </div>
                            </div>
                            <p class="mt-4 text-2xl font-bold text-gray-900">342</p>
                            <p class="mt-2 text-xs text-emerald-600">+9% sur 7 jours</p>
                        </div>
                        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Clients engagés</span>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                                    <Users class="h-5 w-5" />
                                </div>
                            </div>
                            <p class="mt-4 text-2xl font-bold text-gray-900">1 024</p>
                            <p class="mt-2 text-xs text-emerald-600">+6% ce mois</p>
                        </div>
                        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Note moyenne</span>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                                    <Star class="h-5 w-5" />
                                </div>
                            </div>
                            <p class="mt-4 text-2xl font-bold text-gray-900">4,8 / 5</p>
                            <p class="mt-2 text-xs text-emerald-600">Stable cette semaine</p>
                        </div>
                    </div>
                </div>
                <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-amber-200/40 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 left-10 h-56 w-56 rounded-full bg-amber-100/60 blur-3xl"></div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <div class="grid gap-6">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Performance de la semaine</h2>
                                <p class="mt-1 text-sm text-gray-600">Suivi des interactions et des conversions.</p>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                <TrendingUp class="h-4 w-4" />
                                +12% de conversion
                            </span>
                        </div>
                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <p class="text-xs font-medium text-gray-500">Temps moyen par client</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900">42 s</p>
                                <p class="mt-2 text-xs text-emerald-600">-8% vs semaine passée</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <p class="text-xs font-medium text-gray-500">Taux de recommandation</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900">68%</p>
                                <p class="mt-2 text-xs text-emerald-600">+5 points</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <p class="text-xs font-medium text-gray-500">Bières populaires</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900">6</p>
                                <p class="mt-2 text-xs text-gray-500">En tête cette semaine</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Activité récente</h2>
                                <p class="mt-1 text-sm text-gray-600">Derniers moments clés dans vos établissements.</p>
                            </div>
                            <Link href="/bars" class="text-sm font-medium text-amber-800 hover:text-amber-700">
                                Voir tout
                            </Link>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between rounded-2xl border border-amber-100/80 bg-amber-50/30 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm">
                                        <QrCode class="h-5 w-5 text-amber-700" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">18 scans en 30 min</p>
                                        <p class="text-xs text-gray-500">Le Houblon Doré · Heure de pointe</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">Il y a 15 min</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-amber-100/80 bg-amber-50/30 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm">
                                        <Sparkles class="h-5 w-5 text-amber-700" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">3 nouvelles bières recommandées</p>
                                        <p class="text-xs text-gray-500">Le Comptoir des Bières · Craft IPA</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">Il y a 1 h</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-amber-100/80 bg-amber-50/30 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm">
                                        <Users class="h-5 w-5 text-amber-700" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Nouveaux clients fidèles</p>
                                        <p class="text-xs text-gray-500">La Brasserie Artisanale · +24 profils</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">Hier</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">Actions rapides</h2>
                        <p class="mt-1 text-sm text-gray-600">Accès direct aux tâches clés.</p>
                        <div class="mt-6 space-y-3">
                            <Link
                                href="/bars"
                                class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/50 px-4 py-3 text-sm font-medium text-amber-900 transition-all hover:border-amber-200 hover:bg-amber-50"
                            >
                                Ajouter un établissement
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <Link
                                href="/bars"
                                class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/50 px-4 py-3 text-sm font-medium text-amber-900 transition-all hover:border-amber-200 hover:bg-amber-50"
                            >
                                Mettre à jour la carte
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <Link
                                href="/bars"
                                class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/50 px-4 py-3 text-sm font-medium text-amber-900 transition-all hover:border-amber-200 hover:bg-amber-50"
                            >
                                Télécharger le QR code
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">À surveiller</h2>
                            <Calendar class="h-5 w-5 text-amber-700" />
                        </div>
                        <div class="mt-5 space-y-4 text-sm text-gray-600">
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <p class="font-medium text-gray-900">Conseil du jour</p>
                                <p class="mt-2">Mettez en avant une bière locale pour augmenter l'engagement.</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                <p class="font-medium text-gray-900">Prochaine analyse</p>
                                <p class="mt-2">Rapport hebdo généré chaque lundi à 9h.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-700">
                                <CheckCircle2 class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Checklist de mise en place</h2>
                                <p class="text-sm text-gray-600">Gardez votre configuration impeccable.</p>
                            </div>
                        </div>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600">
                            <li class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/40 px-4 py-3">
                                Ajouter les nouvelles références
                                <span class="rounded-full bg-white px-2 py-1 text-xs text-emerald-700">Terminé</span>
                            </li>
                            <li class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/40 px-4 py-3">
                                Vérifier les préférences saisonnières
                                <span class="rounded-full bg-white px-2 py-1 text-xs text-amber-700">En cours</span>
                            </li>
                            <li class="flex items-center justify-between rounded-2xl border border-amber-100 bg-amber-50/40 px-4 py-3">
                                Analyser les recommandations
                                <span class="rounded-full bg-white px-2 py-1 text-xs text-gray-500">À faire</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
