<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Beer, Plus, ArrowRight, Trash2, Store } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Bar {
    id: number;
    name: string;
    slug: string;
    beers_count: number;
    wines_count: number;
    is_demo: boolean;
    can_delete: boolean;
    subscription_status: 'active' | 'trial' | 'inactive';
}

interface Props {
    bars: Bar[];
    canCreate: boolean;
}

const props = defineProps<Props>();

const createForm = useForm({
    name: '',
});

const createBar = () => {
    if (!props.canCreate) return;

    createForm.post('/bars', {
        preserveScroll: true,
        onSuccess: () => createForm.reset('name'),
    });
};

const deleteBar = (bar: Bar) => {
    if (!bar.can_delete) return;

    if (confirm(`Supprimer le bar "${bar.name}" ? Cette action est définitive.`)) {
        router.delete(`/bars/${bar.slug}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Mes Bars" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <p class="text-sm font-medium text-amber-800">Mes bars</p>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900">Gérez vos établissements</h1>
                    </div>
                    <p class="mt-2 max-w-2xl text-gray-600">
                        Centralisez vos établissements, suivez vos catalogues et accédez rapidement aux actions clés.
                    </p>
                </div>

                <div
                    v-if="canCreate"
                    class="mb-10 rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)]"
                >
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Créer un nouveau bar</h2>
                            <p class="mt-1 text-sm text-gray-600">Ajoutez un établissement et invitez votre équipe.</p>
                        </div>
                        <form
                            class="flex w-full max-w-xl flex-col gap-3 sm:flex-row"
                            @submit.prevent="createBar"
                        >
                            <div class="flex-1">
                                <input
                                    v-model="createForm.name"
                                    type="text"
                                    class="w-full rounded-lg border border-amber-200 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-100"
                                    placeholder="Nom du bar"
                                />
                                <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">
                                    {{ createForm.errors.name }}
                                </p>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-2 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                                :disabled="createForm.processing"
                            >
                                <Plus class="h-4 w-4" />
                                Créer
                            </button>
                        </form>
                    </div>
                </div>

                <div v-if="bars.length === 0" class="rounded-2xl border border-amber-100 bg-white p-12 text-center shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)]">
                    <Store class="mx-auto h-14 w-14 text-amber-300" />
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucun bar</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Votre premier bar a été créé lors de l'inscription. Si vous ne le voyez pas, contactez le support.
                    </p>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="bar in bars"
                        :key="bar.id"
                        class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                    <Beer class="h-6 w-6" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ bar.name }}</h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ bar.beers_count }} bière{{ bar.beers_count > 1 ? 's' : '' }} ·
                                        {{ bar.wines_count }} vin{{ bar.wines_count > 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>
                            <span
                                v-if="bar.is_demo"
                                class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800"
                            >
                                Démo
                            </span>
                            <span
                                v-else
                                class="rounded-full border px-3 py-1 text-xs font-semibold"
                                :class="{
                                    'border-emerald-200 bg-emerald-50 text-emerald-700': bar.subscription_status === 'active',
                                    'border-amber-200 bg-amber-50 text-amber-800': bar.subscription_status === 'trial',
                                    'border-red-200 bg-red-50 text-red-700': bar.subscription_status === 'inactive',
                                }"
                            >
                                {{
                                    bar.subscription_status === 'active'
                                        ? 'Abonnement actif'
                                        : bar.subscription_status === 'trial'
                                            ? 'Essai en cours'
                                            : 'Abonnement inactif'
                                }}
                            </span>
                        </div>
                        <div class="mt-6 flex items-center justify-between gap-3">
                            <Link
                                :href="`/bars/${bar.slug}`"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-amber-800 transition-colors hover:text-amber-900"
                            >
                                Accéder au dashboard
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <button
                                v-if="bar.can_delete"
                                type="button"
                                class="inline-flex items-center gap-1 text-sm font-semibold text-red-600 transition-colors hover:text-red-700"
                                @click="deleteBar(bar)"
                            >
                                <Trash2 class="h-4 w-4" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
