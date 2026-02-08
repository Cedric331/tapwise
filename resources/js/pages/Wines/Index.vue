<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Edit, Trash2, Upload, FileText, Mail, Sparkles, Wine as WineIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface WineTag {
    id: number;
    name: string;
}

interface Wine {
    id: number;
    name: string;
    grape: string | null;
    region: string | null;
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
    wines: Wine[];
    tags: WineTag[];
    importReport?: {
        status: 'success' | 'warning' | 'error';
        message?: string;
        imported?: number;
        total?: number;
        failed?: { row: number; errors: string[] }[];
        failedCount?: number;
        missingHeaders?: string[];
    } | null;
    contactEmail?: string | null;
}

const props = defineProps<Props>();

const importForm = useForm({
    file: null as File | null,
});
const importFilename = ref<string | null>(null);
const importError = ref<string | null>(null);
const importInputRef = ref<HTMLInputElement | null>(null);

const deleteWine = (wineId: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce vin ?')) {
        router.delete(`/bars/${props.bar.slug}/wines/${wineId}`);
    }
};

const handleImportFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    importForm.file = file;
    importFilename.value = file ? file.name : null;
    importError.value = null;
};

const openImportPicker = () => {
    importInputRef.value?.click();
};

const submitImport = () => {
    if (!importForm.file || props.bar.is_demo) {
        importError.value = 'Sélectionnez un fichier CSV avant d’importer.';
        return;
    }

    importForm.post(`/bars/${props.bar.slug}/wines/import`, {
        forceFormData: true,
    });
};

const importReportClass = computed(() => {
    switch (props.importReport?.status) {
        case 'success':
            return 'border-green-200 bg-green-50 text-green-900';
        case 'warning':
            return 'border-amber-200 bg-amber-50 text-amber-900';
        case 'error':
            return 'border-red-200 bg-red-50 text-red-900';
        default:
            return 'border-amber-200 bg-amber-50 text-amber-900';
    }
});
</script>

<template>
    <Head title="Catalogue de vins" />

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
                            <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Catalogue de vins</h1>
                            <p class="mt-2 text-sm text-gray-500">
                                Affinez votre sélection pour des recommandations toujours plus pertinentes.
                            </p>
                        </div>
                        <Link
                            v-if="!bar.is_demo"
                            :href="`/bars/${bar.slug}/wines/create`"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                        >
                            <Plus class="h-4 w-4" />
                            Ajouter un vin
                        </Link>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Sparkles class="h-4 w-4" />
                            Sélection premium
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1">
                            <WineIcon class="h-4 w-4 text-amber-700" />
                            Recommandations instantanées
                        </span>
                    </div>
                </div>

                <div v-if="importReport" class="mb-8 rounded-2xl border p-4 text-sm" :class="importReportClass">
                    <p class="font-semibold">{{ importReport.message || 'Import terminé.' }}</p>
                    <p v-if="importReport.total !== undefined" class="mt-1 text-xs">
                        {{ importReport.imported ?? 0 }} / {{ importReport.total }} lignes importées
                        <span v-if="importReport.failedCount">· {{ importReport.failedCount }} lignes en erreur</span>
                    </p>
                    <div v-if="importReport.failed?.length" class="mt-3 space-y-2 text-xs">
                        <p class="font-semibold">Exemples d'erreurs :</p>
                        <ul class="space-y-1">
                            <li v-for="failure in importReport.failed" :key="failure.row">
                                Ligne {{ failure.row }} : {{ failure.errors.join(' · ') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div v-if="!bar.is_demo" class="mb-10 grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <Upload class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Importer un fichier Excel</h2>
                                <p class="text-sm text-gray-500">Exportez votre Excel en CSV puis importez-le ici.</p>
                            </div>
                        </div>
                        <form @submit.prevent="submitImport" class="space-y-4">
                            <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/40 p-4">
                                <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-amber-800">
                                    <FileText class="h-5 w-5" />
                                    <span v-if="importFilename">{{ importFilename }}</span>
                                    <span v-else>Aucun fichier sélectionné</span>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-amber-200 bg-white px-3 py-1 text-xs font-semibold text-amber-800 shadow-sm transition-colors hover:text-amber-700"
                                        @click="openImportPicker"
                                    >
                                        Choisir un fichier CSV
                                    </button>
                                </div>
                                <input
                                    ref="importInputRef"
                                    type="file"
                                    accept=".csv,text/csv"
                                    class="hidden"
                                    @change="handleImportFileChange"
                                />
                                <p class="mt-2 text-xs text-gray-500">
                                    Colonnes attendues : nom, couleur, abv. Les autres sont optionnelles.
                                </p>
                                <p v-if="importError" class="mt-2 text-xs font-medium text-red-600">
                                    {{ importError }}
                                </p>
                                <div class="mt-2 text-xs text-gray-500">
                                    <a
                                        :href="`/bars/${bar.slug}/wines/template`"
                                        class="inline-flex items-center gap-1 text-amber-800 hover:text-amber-700"
                                    >
                                        Télécharger le modèle CSV
                                    </a>
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                                :disabled="importForm.processing"
                            >
                                <Upload class="h-4 w-4" />
                                <span v-if="importForm.processing">Import en cours...</span>
                                <span v-else>Importer les vins</span>
                            </button>
                        </form>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Service d'onboarding</h2>
                                <p class="text-sm text-gray-500">On importe votre carte pour vous, en une fois.</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            <p class="font-medium">Besoin d'aide pour un gros catalogue ?</p>
                            <p class="mt-2 text-xs text-amber-800">
                                Envoyez votre fichier ou votre carte à notre équipe, on s'occupe du reste.
                            </p>
                            <p class="mt-2 text-xs text-amber-800">
                                Veuillez indiquer le nom de votre établissement dans l'objet de l'email.
                            </p>
                            <a
                                v-if="contactEmail"
                                :href="`mailto:${contactEmail}`"
                                class="mt-3 inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-xs font-semibold text-amber-800 shadow-sm transition-colors hover:text-amber-700"
                            >
                                <Mail class="h-4 w-4" />
                                {{ contactEmail }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-10 overflow-hidden rounded-3xl border border-amber-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-amber-100 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Votre sélection</h2>
                            <p class="text-sm text-gray-500">Suivez vos vins disponibles et leurs informations.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <WineIcon class="h-4 w-4 text-amber-600" />
                            {{ wines.length }} vin{{ wines.length > 1 ? 's' : '' }}
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-amber-100">
                            <thead class="bg-amber-50/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Cépage</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Région</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Tags</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100/60 bg-white">
                                <tr v-for="wine in wines" :key="wine.id" class="transition-colors hover:bg-amber-50/40">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ wine.name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ wine.grape || '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ wine.region || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="tag in wine.tags"
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
                                            :class="wine.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        >
                                            {{ wine.is_available ? 'Disponible' : 'Indisponible' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="`/bars/${bar.slug}/wines/${wine.id}/edit`"
                                                class="inline-flex items-center gap-1 text-sm font-semibold text-amber-800 hover:text-amber-900"
                                            >
                                                <Edit class="h-4 w-4" />
                                                Modifier
                                            </Link>
                                            <button
                                                v-if="!bar.is_demo"
                                                type="button"
                                                class="inline-flex items-center gap-1 text-sm font-semibold text-red-600 hover:text-red-700"
                                                @click="deleteWine(wine.id)"
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

