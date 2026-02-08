<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Edit, Trash2, Beer as BeerIcon, Sparkles, Upload, FileText, Mail } from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
const importFile = ref<File | null>(null);
const importFilename = ref<string | null>(null);
const importError = ref<string | null>(null);
const importPreviewError = ref<string | null>(null);
const importInputRef = ref<HTMLInputElement | null>(null);
const importPreviewColumns = ref<string[]>([]);
const importPreviewRows = ref<string[][]>([]);

type MappingField =
    | 'name'
    | 'brewery'
    | 'style'
    | 'color'
    | 'abv'
    | 'abv_x10'
    | 'ibu'
    | 'description'
    | 'is_on_tap'
    | 'is_available'
    | 'price';

const mappingFieldLabels: Record<MappingField, string> = {
    name: 'Nom',
    brewery: 'Brasserie',
    style: 'Style',
    color: 'Couleur',
    abv: 'ABV',
    abv_x10: 'ABV x10',
    ibu: 'IBU',
    description: 'Description',
    is_on_tap: 'À la pression',
    is_available: 'Disponible',
    price: 'Prix',
};

const mappingFields: MappingField[] = [
    'name',
    'brewery',
    'style',
    'color',
    'abv',
    'abv_x10',
    'ibu',
    'price',
    'is_on_tap',
    'is_available',
    'description',
];

const importMapping = ref<Record<MappingField, string | null>>({
    name: null,
    brewery: null,
    style: null,
    color: null,
    abv: null,
    abv_x10: null,
    ibu: null,
    description: null,
    is_on_tap: null,
    is_available: null,
    price: null,
});

const deleteBeer = (beerId: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette bière ?')) {
        router.delete(`/bars/${props.bar.slug}/beers/${beerId}`);
    }
};

const getAbv = (abvX10: number) => {
    return (abvX10 / 10).toFixed(1);
};

const handleImportFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    importForm.file = file;
    importFile.value = file;
    importFilename.value = file ? file.name : null;
    importError.value = null;
    importPreviewError.value = null;

    if (file) {
        parseImportFile(file);
    } else {
        importPreviewColumns.value = [];
        importPreviewRows.value = [];
        resetImportMapping();
    }
};

const openImportPicker = () => {
    importInputRef.value?.click();
};

const submitImport = () => {
    if (!importFile.value || props.bar.is_demo) {
        importError.value = 'Sélectionnez un fichier CSV avant d’importer.';
        return;
    }

    if (!canImport.value) {
        importError.value = 'Complétez le mappage avant d’importer.';
        return;
    }

    importForm.transform((data) => ({
        ...data,
        mapping: buildMappingPayload(),
    })).post(`/bars/${props.bar.slug}/beers/import`, {
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

const missingHeaderLabels: Record<string, string> = {
    name: 'Nom',
    style: 'Style',
    color: 'Couleur',
    abv: 'ABV',
};

const canImport = computed(() => {
    if (!importFile.value) return false;

    const required = ['name', 'style', 'color'] as const;
    const hasRequired = required.every((field) => Boolean(importMapping.value[field]));
    const hasAbv = Boolean(importMapping.value.abv) || Boolean(importMapping.value.abv_x10);

    return hasRequired && hasAbv;
});

const resetImportMapping = () => {
    importMapping.value = {
        name: null,
        brewery: null,
        style: null,
        color: null,
        abv: null,
        abv_x10: null,
        ibu: null,
        description: null,
        is_on_tap: null,
        is_available: null,
        price: null,
    };
};

const parseImportFile = (file: File) => {
    const reader = new FileReader();

    reader.onload = () => {
        const content = typeof reader.result === 'string' ? reader.result : '';
        if (!content.trim()) {
            importPreviewError.value = 'Le fichier semble vide.';
            importPreviewColumns.value = [];
            importPreviewRows.value = [];
            resetImportMapping();
            return;
        }

        const [firstLine] = content.split(/\r?\n/);
        const delimiter = detectDelimiter(firstLine || '');
        const rows = parseCsv(content, delimiter);

        if (rows.length === 0) {
            importPreviewError.value = 'Impossible de lire le fichier.';
            importPreviewColumns.value = [];
            importPreviewRows.value = [];
            resetImportMapping();
            return;
        }

        importPreviewColumns.value = rows[0].map((value) => value.trim());
        importPreviewRows.value = rows.slice(1, 6);
        importMapping.value = suggestMapping(importPreviewColumns.value);
    };

    reader.onerror = () => {
        importPreviewError.value = 'Erreur lors de la lecture du fichier.';
    };

    reader.readAsText(file);
};

const detectDelimiter = (line: string) => {
    const commaCount = (line.match(/,/g) || []).length;
    const semicolonCount = (line.match(/;/g) || []).length;

    return semicolonCount >= commaCount ? ';' : ',';
};

const parseCsv = (text: string, delimiter: string) => {
    const rows: string[][] = [];
    let row: string[] = [];
    let field = '';
    let inQuotes = false;

    for (let i = 0; i < text.length; i += 1) {
        const char = text[i];
        const next = text[i + 1];

        if (inQuotes) {
            if (char === '"') {
                if (next === '"') {
                    field += '"';
                    i += 1;
                } else {
                    inQuotes = false;
                }
            } else {
                field += char;
            }
            continue;
        }

        if (char === '"') {
            inQuotes = true;
            continue;
        }

        if (char === delimiter) {
            row.push(field);
            field = '';
            continue;
        }

        if (char === '\n') {
            row.push(field);
            rows.push(row);
            row = [];
            field = '';
            continue;
        }

        if (char === '\r') {
            continue;
        }

        field += char;
    }

    if (field.length > 0 || row.length > 0) {
        row.push(field);
        rows.push(row);
    }

    return rows;
};

const normalizeHeader = (value: string) => {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();
};

const headerAliases: Record<MappingField, string[]> = {
    name: ['name', 'nom', 'biere', 'nom biere', 'nom biere'],
    brewery: ['brewery', 'brasserie', 'producteur', 'brasseur'],
    style: ['style', 'type'],
    color: ['color', 'couleur', 'robe'],
    abv: ['abv', 'alcool', 'degre', 'taux', 'vol'],
    abv_x10: ['abv_x10', 'abv x10', 'abv10', 'degre x10'],
    ibu: ['ibu', 'amertume'],
    description: ['description', 'notes'],
    is_on_tap: ['is_on_tap', 'pression', 'draft', 'on tap', 'tirage'],
    is_available: ['is_available', 'disponible', 'dispo', 'available', 'actif'],
    price: ['price', 'prix', 'tarif', 'prix ttc'],
};

const suggestMapping = (headers: string[]) => {
    const mapping: Record<MappingField, string | null> = {
        name: null,
        brewery: null,
        style: null,
        color: null,
        abv: null,
        abv_x10: null,
        ibu: null,
        description: null,
        is_on_tap: null,
        is_available: null,
        price: null,
    };

    headers.forEach((header) => {
        const normalized = normalizeHeader(header);
        (Object.keys(headerAliases) as MappingField[]).forEach((field) => {
            if (mapping[field]) return;
            const match = headerAliases[field].some((alias) => normalizeHeader(alias) === normalized);
            if (match) {
                mapping[field] = header;
            }
        });
    });

    return mapping;
};

const buildMappingPayload = () => {
    const payload: Record<string, string> = {};

    Object.entries(importMapping.value).forEach(([field, value]) => {
        if (value) {
            payload[field] = value;
        }
    });

    return payload;
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

                <div v-if="importReport" class="mb-8 rounded-2xl border p-4 text-sm" :class="importReportClass">
                    <p class="font-semibold">{{ importReport.message || 'Import terminé.' }}</p>
                    <p v-if="importReport.total !== undefined" class="mt-1 text-xs">
                        {{ importReport.imported ?? 0 }} / {{ importReport.total }} lignes importées
                        <span v-if="importReport.failedCount">· {{ importReport.failedCount }} lignes en erreur</span>
                    </p>
                    <div v-if="importReport.missingHeaders?.length" class="mt-2 text-xs">
                        Colonnes manquantes :
                        <span class="font-medium">
                            {{ importReport.missingHeaders.map((header) => missingHeaderLabels[header] || header).join(', ') }}
                        </span>
                    </div>
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
                                    Colonnes attendues : nom, style, couleur, abv. Les autres sont optionnelles.
                                </p>
                                <p v-if="importPreviewError" class="mt-2 text-xs font-medium text-red-600">
                                    {{ importPreviewError }}
                                </p>
                                <p v-if="importError" class="mt-2 text-xs font-medium text-red-600">
                                    {{ importError }}
                                </p>
                                <div class="mt-2 text-xs text-gray-500">
                                    <a
                                        :href="`/bars/${bar.slug}/beers/template`"
                                        class="inline-flex items-center gap-1 text-amber-800 hover:text-amber-700"
                                    >
                                        Télécharger le modèle CSV
                                    </a>
                                </div>
                            </div>
                            <div v-if="importPreviewColumns.length" class="space-y-4 rounded-2xl border border-amber-100 bg-white p-4 text-sm">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">Mappage des colonnes</h3>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Associez vos colonnes aux champs Tapwise avant l’import.
                                    </p>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div v-for="field in mappingFields" :key="field" class="space-y-1">
                                        <label class="text-xs font-medium text-gray-700">
                                            {{ mappingFieldLabels[field] }}
                                            <span v-if="['name', 'style', 'color', 'abv'].includes(field)" class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="importMapping[field]"
                                            class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs text-gray-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                        >
                                            <option value="">Ne pas importer</option>
                                            <option v-for="column in importPreviewColumns" :key="column" :value="column">
                                                {{ column }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <p v-if="!canImport" class="text-xs text-amber-700">
                                    Les champs requis sont : Nom, Style, Couleur et ABV (ou ABV x10).
                                </p>
                            </div>
                            <div v-if="importPreviewRows.length" class="rounded-2xl border border-amber-100 bg-white p-4 text-xs text-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900">Aperçu des lignes</h3>
                                <div class="mt-3 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-amber-100">
                                        <thead class="bg-amber-50/60">
                                            <tr>
                                                <th
                                                    v-for="column in importPreviewColumns"
                                                    :key="column"
                                                    class="px-3 py-2 text-left text-[11px] font-semibold uppercase text-gray-600"
                                                >
                                                    {{ column }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-amber-100/60 bg-white">
                                            <tr v-for="(row, rowIndex) in importPreviewRows" :key="rowIndex">
                                                <td v-for="(cell, cellIndex) in importPreviewColumns" :key="cellIndex" class="px-3 py-2">
                                                    {{ row[cellIndex] || '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                                :disabled="importForm.processing"
                            >
                                <Upload class="h-4 w-4" />
                                <span v-if="importForm.processing">Import en cours...</span>
                                <span v-else>Importer les bières</span>
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
                                Contacter l'équipe
                            </a>
                            <p v-else class="mt-3 text-xs text-amber-700">Email de contact à configurer.</p>
                        </div>
                    </div>
                </div>

                <div v-else class="mb-10 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Le mode démo ne permet pas l'import de bières ni l'onboarding.
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
