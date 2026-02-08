<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X, Sparkles } from 'lucide-vue-next';
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
    color: string;
    abv_x10: number;
    ibu: number | null;
    description: string | null;
    is_on_tap: boolean;
    is_available: boolean;
    price: number | null;
    tags: Tag[];
}

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
    };
    beer: Beer;
    tags: Tag[];
    colorOptions: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.beer.name,
    brewery: props.beer.brewery ?? '',
    style: props.beer.style,
    color: props.beer.color,
    abv_x10: props.beer.abv_x10,
    ibu: props.beer.ibu,
    description: props.beer.description || '',
    is_on_tap: props.beer.is_on_tap,
    is_available: props.beer.is_available,
    price: props.beer.price,
    tags: props.beer.tags.map((t) => t.id),
});

const submit = () => {
    form.put(`/bars/${props.bar.slug}/beers/${props.beer.id}`);
};

const toggleTag = (tagId: number) => {
    const index = form.tags.indexOf(tagId);
    if (index > -1) {
        form.tags.splice(index, 1);
    } else {
        form.tags.push(tagId);
    }
};
</script>

<template>
    <Head title="Modifier une bière" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <Link
                        :href="`/bars/${bar.slug}/beers`"
                        class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-amber-800 transition-colors hover:text-amber-700"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Retour au catalogue
                    </Link>
                    <p class="text-sm font-medium text-amber-800">Édition</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Modifier une bière</h1>
                    <p class="mt-3 text-lg text-gray-600">{{ beer.name }}</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Sparkles class="h-4 w-4" />
                            Expérience client optimisée
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1">
                            <Save class="h-4 w-4" />
                            Historique conservé
                        </span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Informations principales</h2>
                        <p class="mb-6 text-sm text-gray-600">Mettez à jour les détails visibles par vos clients.</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Nom de la bière *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Brasserie</label>
                                    <input
                                        v-model="form.brewery"
                                        type="text"
                                        class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Style *</label>
                                    <input
                                        v-model="form.style"
                                        type="text"
                                        required
                                        class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Couleur *</label>
                                <select
                                    v-model="form.color"
                                    required
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                >
                                    <option value="" disabled>Sélectionner une couleur</option>
                                    <option
                                        v-for="(label, value) in colorOptions"
                                        :key="value"
                                        :value="value"
                                    >
                                        {{ label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Caractéristiques</h2>
                        <p class="mb-6 text-sm text-gray-600">Ajustez le profil aromatique et la force en alcool.</p>
                        
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Taux d'alcool *</label>
                                <input
                                    v-model.number="form.abv_x10"
                                    type="number"
                                    min="0"
                                    max="200"
                                    required
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                                <p class="mt-1 text-xs text-gray-500">Ex: 50 pour 5.0%</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">IBU (amertume)</label>
                                <input
                                    v-model.number="form.ibu"
                                    type="number"
                                    min="0"
                                    max="120"
                                    class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                />
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Prix (en centimes)</label>
                            <input
                                v-model.number="form.price"
                                type="number"
                                min="0"
                                class="block w-full rounded-lg border border-amber-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                            />
                            <p class="mt-1 text-xs text-gray-500">Ex: 650 pour 6.50€</p>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Options</h2>
                        
                        <div class="space-y-4">
                            <label class="flex items-center gap-3">
                                <input
                                    v-model="form.is_on_tap"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-amber-200 text-amber-600 focus:ring-amber-200"
                                />
                                <span class="text-sm font-medium text-gray-700">En pression</span>
                            </label>

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
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Tags</h2>
                        <p class="mb-4 text-sm text-gray-500">Sélectionnez les tags qui correspondent à cette bière</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="tag in tags"
                                :key="tag.id"
                                type="button"
                                class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-colors"
                                :class="form.tags.includes(tag.id) ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100'"
                                @click="toggleTag(tag.id)"
                            >
                                {{ tag.name }}
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <Link
                            :href="`/bars/${bar.slug}/beers`"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-white px-6 py-2.5 text-sm font-medium text-amber-800 transition-colors hover:bg-amber-50"
                        >
                            <X class="h-4 w-4" />
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <Save class="h-4 w-4" />
                            <span v-if="form.processing">Mise à jour...</span>
                            <span v-else>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
