<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        logo_path?: string;
        brand_background_color?: string;
        brand_primary_color?: string;
        welcome_message?: string;
    };
    colorOptions: Record<string, string>;
    aromaOptions: Array<{ value: string; label: string }>;
    styleOptions: string[];
    breweryOptions: string[];
    maxPrice: number | null;
    questionOptions: Array<{ id: string; label: string; description: string }>;
    selectedQuestions: string[];
}

const props = defineProps<Props>();

const currentStep = ref(0);
const hasQuestionData = (questionId: string) => {
    switch (questionId) {
        case 'color':
            return Object.keys(props.colorOptions || {}).length > 0;
        case 'aromas':
            return (props.aromaOptions || []).length > 0;
        case 'style':
            return (props.styleOptions || []).length > 0;
        case 'brewery':
            return (props.breweryOptions || []).length > 0;
        case 'max_price':
            return (props.maxPrice ?? 0) > 0;
        default:
            return true;
    }
};

const questionOrder = computed(() => props.selectedQuestions.filter(hasQuestionData));
const totalSteps = computed(() => questionOrder.value.length);
const currentQuestionId = computed(() => questionOrder.value[currentStep.value]);

const maxPriceEuros = computed(() => {
    const max = props.maxPrice ?? 0;
    const maxEuros = max > 0 ? Math.ceil(max / 100) : 12;
    return Math.max(maxEuros, 5);
});

const form = useForm({
    bitterness: '',
    color: [] as string[],
    aromas: [] as string[],
    max_abv: 5,
    format: '',
    style: 'any',
    brewery: 'any',
    max_price: maxPriceEuros.value,
});

const bitternessOptions = [
    { value: 'faible', label: 'Faible' },
    { value: 'moyenne', label: 'Moyenne' },
    { value: 'forte', label: 'Forte' },
];

const formatOptions = [
    { value: 'pression', label: 'Pression' },
    { value: 'bouteille', label: 'Bouteille' },
];

const toggleAroma = (aroma: string) => {
    const index = form.aromas.indexOf(aroma);
    if (index > -1) {
        form.aromas.splice(index, 1);
    } else {
        form.aromas.push(aroma);
    }
};

const toggleColor = (color: string) => {
    const index = form.color.indexOf(color);
    if (index > -1) {
        form.color.splice(index, 1);
    } else {
        form.color.push(color);
    }
};

const nextStep = () => {
    if (currentStep.value < totalSteps.value - 1) {
        currentStep.value += 1;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value -= 1;
    }
};

const submit = () => {
    form.post(`/b/${props.bar.slug}/recommend`);
};

const backgroundColor = props.bar.brand_background_color || '#ffffff';
const primaryColor = props.bar.brand_primary_color || '#4f46e5';
const logoSrc = computed(() => {
    if (!props.bar.logo_path) return null;
    return props.bar.logo_path.startsWith('assets/')
        ? `/${props.bar.logo_path}`
        : `/storage/${props.bar.logo_path}`;
});

const isStepValid = (questionId?: string) => {
    switch (questionId) {
        case 'bitterness':
            return !!form.bitterness;
        case 'color':
            return form.color.length > 0;
        case 'aromas':
            return form.aromas.length > 0;
        case 'format':
            return !!form.format;
        default:
            return true;
    }
};
</script>

<template>
    <Head :title="`${bar.name} - Recommandation`" />

    <div :style="{ backgroundColor }" class="relative min-h-screen overflow-hidden py-10 px-4 sm:px-6 lg:px-8">
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-56 w-56 rounded-full blur-3xl"
            :style="{ backgroundColor: primaryColor, opacity: 0.2 }"
        ></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-white/40 blur-3xl"></div>

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-3xl bg-white/85 p-6 shadow-lg ring-1 ring-black/5 backdrop-blur sm:p-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            v-if="logoSrc"
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-black/5"
                        >
                            <img :src="logoSrc" :alt="bar.name" class="h-12 w-12 object-contain" />
                        </div>
                        <div v-else class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                            <span class="text-2xl font-bold" :style="{ color: primaryColor }">{{ bar.name.slice(0, 1) }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ bar.name }}</h1>
                            <p class="text-sm text-gray-500">Questionnaire de recommandation</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-4 py-2 text-sm text-gray-600">
                        <span class="font-medium" :style="{ color: primaryColor }">Étape {{ currentStep + 1 }}</span>
                        <span>/ {{ totalSteps }}</span>
                    </div>
                </div>

                <div v-if="bar.welcome_message" class="mt-6 rounded-2xl bg-white/70 px-5 py-4 text-base text-gray-700 shadow-sm">
                    {{ bar.welcome_message }}
                </div>

                <div class="mt-6">
                    <div class="h-2 rounded-full bg-gray-200/80">
                        <div
                            class="h-2 rounded-full transition-all"
                            :style="{ width: `${((currentStep + 1) / totalSteps) * 100}%`, backgroundColor: primaryColor }"
                        ></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-gray-600">
                        <span>Progression</span>
                        <span class="font-medium" :style="{ color: primaryColor }">
                            {{ Math.round(((currentStep + 1) / totalSteps) * 100) }}%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form steps -->
            <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-black/5 sm:p-8">
                <!-- Step 1: Bitterness -->
                <div v-if="currentQuestionId === 'bitterness'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Quelle amertume préférez-vous ?</h2>
                        <p class="text-sm text-gray-500">Choisissez l'intensité qui vous plaît.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <label
                            v-for="option in bitternessOptions"
                            :key="option.value"
                            class="group flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                            :class="form.bitterness === option.value ? 'border-indigo-600 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full border text-sm font-semibold"
                                    :class="form.bitterness === option.value ? 'border-indigo-600 bg-white text-indigo-600' : 'border-gray-200 text-gray-500 group-hover:text-gray-700'"
                                >
                                    {{ option.label.slice(0, 1) }}
                                </span>
                                <span class="text-lg font-medium">{{ option.label }}</span>
                            </div>
                            <input
                                v-model="form.bitterness"
                                type="radio"
                                :value="option.value"
                                class="h-4 w-4"
                                :style="{ accentColor: primaryColor }"
                            />
                        </label>
                    </div>
                </div>

                <!-- Step 2: Color -->
                <div v-if="currentQuestionId === 'color'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Quelle couleur de bière préférez-vous ?</h2>
                        <p class="text-sm text-gray-500">Sélectionnez une ou plusieurs couleurs.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button
                            v-for="(label, value) in colorOptions"
                            :key="value"
                            type="button"
                            class="flex items-center justify-between rounded-2xl border p-4 text-left transition"
                            :class="form.color.includes(value) ? 'border-indigo-600 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                            :style="form.color.includes(value) ? { borderColor: primaryColor, backgroundColor: `${primaryColor}15` } : {}"
                            @click="toggleColor(value)"
                        >
                            <span class="text-base font-medium">{{ label }}</span>
                            <span
                                class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-full border text-xs"
                                :class="form.color.includes(value) ? 'border-indigo-600 text-indigo-600' : 'border-gray-200 text-gray-400'"
                            >
                                ✓
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Aromas -->
                <div v-if="currentQuestionId === 'aromas'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Quels arômes vous plaisent ?</h2>
                        <p class="text-sm text-gray-500">Sélectionnez un ou plusieurs arômes.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <button
                            v-for="option in aromaOptions"
                            :key="option.value"
                            type="button"
                            class="flex items-center justify-between rounded-2xl border p-4 text-left transition"
                            :class="form.aromas.includes(option.value) ? 'border-indigo-600 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                            :style="form.aromas.includes(option.value) ? { borderColor: primaryColor, backgroundColor: `${primaryColor}15` } : {}"
                            @click="toggleAroma(option.value)"
                        >
                            <span class="text-base font-medium">{{ option.label }}</span>
                            <span
                                class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-full border text-xs"
                                :class="form.aromas.includes(option.value) ? 'border-indigo-600 text-indigo-600' : 'border-gray-200 text-gray-400'"
                            >
                                ✓
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Max ABV -->
                <div v-if="currentQuestionId === 'max_abv'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Degré d'alcool maximum souhaité</h2>
                        <p class="text-sm text-gray-500">Glissez pour définir votre seuil.</p>
                    </div>
                    <div class="space-y-5">
                        <input
                            v-model.number="form.max_abv"
                            type="range"
                            min="0"
                            max="15"
                            step="0.5"
                            class="w-full"
                            :style="{ accentColor: primaryColor }"
                        />
                        <div class="text-center">
                            <span class="inline-flex items-baseline gap-2 rounded-full bg-gray-50 px-5 py-2 text-3xl font-bold" :style="{ color: primaryColor }">
                                {{ form.max_abv }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Format -->
                <div v-if="currentQuestionId === 'format'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Quel format préférez-vous ?</h2>
                        <p class="text-sm text-gray-500">Choisissez le format idéal.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label
                            v-for="option in formatOptions"
                            :key="option.value"
                            class="flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                            :class="form.format === option.value ? 'border-indigo-600 bg-indigo-50 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full border text-sm font-semibold"
                                    :class="form.format === option.value ? 'border-indigo-600 bg-white text-indigo-600' : 'border-gray-200 text-gray-500'"
                                >
                                    {{ option.label.slice(0, 1) }}
                                </span>
                                <span class="text-lg font-medium">{{ option.label }}</span>
                            </div>
                            <input
                                v-model="form.format"
                                type="radio"
                                :value="option.value"
                                class="h-4 w-4"
                                :style="{ accentColor: primaryColor }"
                            />
                        </label>
                    </div>
                </div>

                <div v-if="currentQuestionId === 'style'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Quel style de bière préférez-vous ?</h2>
                        <p class="text-sm text-gray-500">Choisissez un style ou laissez "Peu importe".</p>
                    </div>
                    <select
                        v-model="form.style"
                        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                    >
                        <option value="any">Peu importe</option>
                        <option v-for="style in styleOptions" :key="style" :value="style">
                            {{ style }}
                        </option>
                    </select>
                </div>

                <div v-if="currentQuestionId === 'brewery'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Une brasserie en particulier ?</h2>
                        <p class="text-sm text-gray-500">Choisissez une brasserie ou laissez "Peu importe".</p>
                    </div>
                    <select
                        v-model="form.brewery"
                        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                    >
                        <option value="any">Peu importe</option>
                        <option v-for="brewery in breweryOptions" :key="brewery" :value="brewery">
                            {{ brewery }}
                        </option>
                    </select>
                </div>

                <div v-if="currentQuestionId === 'max_price'">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-semibold tracking-tight">Budget maximum par bière</h2>
                        <p class="text-sm text-gray-500">Glissez pour définir votre budget.</p>
                    </div>
                    <div class="space-y-5">
                        <input
                            v-model.number="form.max_price"
                            type="range"
                            min="0"
                            :max="maxPriceEuros"
                            step="0.5"
                            class="w-full"
                            :style="{ accentColor: primaryColor }"
                        />
                        <div class="text-center">
                            <span class="inline-flex items-baseline gap-2 rounded-full bg-gray-50 px-5 py-2 text-3xl font-bold" :style="{ color: primaryColor }">
                                {{ form.max_price.toFixed(1) }}€
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Navigation buttons -->
                <div class="mt-10 flex justify-between">
                    <button
                        v-if="currentStep > 0"
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-6 py-2 text-gray-700 transition hover:border-gray-400 hover:bg-gray-50"
                        @click="prevStep"
                    >
                        Précédent
                    </button>
                    <div v-else></div>

                    <button
                        v-if="currentStep < totalSteps - 1"
                        type="button"
                        class="rounded-md px-6 py-2 text-white transition disabled:opacity-50"
                        :style="{ backgroundColor: primaryColor }"
                        :disabled="!isStepValid(currentQuestionId)"
                        @click="nextStep"
                    >
                        Suivant
                    </button>
                    <button
                        v-else
                        type="button"
                        class="rounded-md px-6 py-2 text-white transition disabled:opacity-50"
                        :style="{ backgroundColor: primaryColor }"
                        :disabled="!isStepValid(currentQuestionId) || form.processing"
                        @click="submit"
                    >
                        <span v-if="form.processing">Traitement...</span>
                        <span v-else>Voir les recommandations</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>


