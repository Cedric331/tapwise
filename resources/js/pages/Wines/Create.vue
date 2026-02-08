<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X, Sparkles } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface WineTag {
    id: number;
    name: string;
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
    };
    tags: WineTag[];
    colorOptions: Record<string, string>;
    foodPairingOptions: Array<{ id: string; label: string }>;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    color: '',
    grape: '',
    region: '',
    food_pairings: [] as string[],
    abv_x10: 125,
    description: '',
    is_available: true,
    price: null as number | null,
    tags: [] as number[],
});

const submit = () => {
    form.post(`/bars/${props.bar.slug}/wines`);
};

const toggleTag = (tagId: number) => {
    const index = form.tags.indexOf(tagId);
    if (index > -1) {
        form.tags.splice(index, 1);
    } else {
        form.tags.push(tagId);
    }
};

const togglePairing = (pairingId: string) => {
    const index = form.food_pairings.indexOf(pairingId);
    if (index > -1) {
        form.food_pairings.splice(index, 1);
    } else {
        form.food_pairings.push(pairingId);
    }
};
</script>

<template>
    <Head title="Ajouter un vin" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <Link
                        :href="`/bars/${bar.slug}/wines`"
                        class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-amber-800 transition-colors hover:text-amber-700"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Retour au catalogue
                    </Link>
                    <p class="text-sm font-medium text-amber-800">Nouveau vin</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Ajouter un vin</h1>
                    <p class="mt-3 text-lg text-gray-600">Remplissez les informations de votre nouveau vin.</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Sparkles class="h-4 w-4" />
                            Recommandations plus précises
                        </span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Informations principales</h2>
                        <p class="mb-6 text-sm text-gray-600">Les champs marqués d’un * sont indispensables.</p>

                        <div class="space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Nom du vin *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    placeholder="Bordeaux Supérieur"
                                />
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Couleur *</label>
                                    <select
                                        v-model="form.color"
                                        required
                                        class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    >
                                        <option value="" disabled>Sélectionner une couleur</option>
                                        <option v-for="(label, value) in colorOptions" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Cépage</label>
                                    <input
                                        v-model="form.grape"
                                        type="text"
                                        class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                        placeholder="Merlot"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Région / Appellation</label>
                                <input
                                    v-model="form.region"
                                    type="text"
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    placeholder="Bordeaux"
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    placeholder="Fruits rouges, notes boisées..."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Caractéristiques</h2>
                        <p class="mb-6 text-sm text-gray-600">Ajustez le degré d’alcool et le prix.</p>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Taux d'alcool *</label>
                                <input
                                    v-model.number="form.abv_x10"
                                    type="number"
                                    min="0"
                                    max="250"
                                    required
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                                <p class="mt-1 text-xs text-gray-500">Ex: 125 pour 12.5%</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Prix (en centimes)</label>
                                <input
                                    v-model.number="form.price"
                                    type="number"
                                    min="0"
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                                <p class="mt-1 text-xs text-gray-500">Ex: 750 pour 7.50€</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Options</h2>

                        <div class="space-y-4">
                            <label class="flex items-center gap-3">
                                <input
                                    v-model="form.is_available"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-amber-200 text-amber-600 focus:ring-amber-200"
                                />
                                <span class="text-sm font-medium text-gray-700">Disponible</span>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Accords mets-vin</h2>
                        <p class="mb-6 text-sm text-gray-600">Indiquez les plats conseillés pour ce vin.</p>

                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="option in foodPairingOptions"
                                :key="option.id"
                                type="button"
                                class="rounded-full border px-4 py-2 text-sm transition"
                                :class="form.food_pairings.includes(option.id)
                                    ? 'border-amber-300 bg-amber-50 text-amber-800'
                                    : 'border-amber-100 text-gray-600 hover:border-amber-200'"
                                @click="togglePairing(option.id)"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Tags</h2>
                        <p class="mb-6 text-sm text-gray-600">Ajoutez des tags pour faciliter les recommandations.</p>

                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="tag in tags"
                                :key="tag.id"
                                type="button"
                                class="rounded-full border px-4 py-2 text-sm transition"
                                :class="form.tags.includes(tag.id)
                                    ? 'border-amber-300 bg-amber-50 text-amber-800'
                                    : 'border-amber-100 text-gray-600 hover:border-amber-200'"
                                @click="toggleTag(tag.id)"
                            >
                                {{ tag.name }}
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link
                            :href="`/bars/${bar.slug}/wines`"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-white px-6 py-3 text-sm font-semibold text-amber-800 transition-colors hover:border-amber-300 hover:bg-amber-50"
                        >
                            <X class="h-4 w-4" />
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                            :disabled="form.processing"
                        >
                            <Save class="h-4 w-4" />
                            <span v-if="form.processing">Enregistrement...</span>
                            <span v-else>Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

