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
const questionOrder = computed(() => props.selectedQuestions);
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

    <div :style="{ backgroundColor }" class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <!-- Logo -->
            <div v-if="logoSrc" class="mb-8 text-center">
                <img :src="logoSrc" :alt="bar.name" class="mx-auto h-24" />
            </div>
            <div v-else class="mb-8 text-center">
                <h1 class="text-3xl font-bold" :style="{ color: primaryColor }">{{ bar.name }}</h1>
            </div>

            <!-- Welcome message -->
            <div v-if="bar.welcome_message" class="mb-8 text-center">
                <p class="text-lg">{{ bar.welcome_message }}</p>
            </div>

            <!-- Progress bar -->
            <div class="mb-8">
                <div class="h-2 rounded-full bg-gray-200">
                    <div
                        class="h-2 rounded-full transition-all"
                        :style="{ width: `${((currentStep + 1) / totalSteps) * 100}%`, backgroundColor: primaryColor }"
                    ></div>
                </div>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Étape {{ currentStep + 1 }} sur {{ totalSteps }}
                </p>
            </div>

            <!-- Form steps -->
            <div class="rounded-lg bg-white p-8 shadow-lg">
                <!-- Step 1: Bitterness -->
                <div v-if="currentQuestionId === 'bitterness'">
                    <h2 class="mb-4 text-2xl font-semibold">Quelle amertume préférez-vous ?</h2>
                    <div class="space-y-3">
                        <label
                            v-for="option in bitternessOptions"
                            :key="option.value"
                            class="flex cursor-pointer items-center rounded-lg border-2 p-4 transition"
                            :class="form.bitterness === option.value ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200'"
                        >
                            <input
                                v-model="form.bitterness"
                                type="radio"
                                :value="option.value"
                                class="mr-3 h-4 w-4"
                                :style="{ accentColor: primaryColor }"
                            />
                            <span class="text-lg">{{ option.label }}</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Color -->
                <div v-if="currentQuestionId === 'color'">
                    <h2 class="mb-4 text-2xl font-semibold">Quelle couleur de bière préférez-vous ?</h2>
                    <p class="mb-4 text-gray-600">Sélectionnez une ou plusieurs couleurs</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            v-for="(label, value) in colorOptions"
                            :key="value"
                            type="button"
                            class="rounded-lg border-2 p-4 text-left transition"
                            :class="form.color.includes(value) ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200'"
                            :style="form.color.includes(value) ? { borderColor: primaryColor, backgroundColor: `${primaryColor}15` } : {}"
                            @click="toggleColor(value)"
                        >
                            <span class="text-lg">{{ label }}</span>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Aromas -->
                <div v-if="currentQuestionId === 'aromas'">
                    <h2 class="mb-4 text-2xl font-semibold">Quels arômes vous plaisent ?</h2>
                    <p class="mb-4 text-gray-600">Sélectionnez un ou plusieurs arômes</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            v-for="option in aromaOptions"
                            :key="option.value"
                            type="button"
                            class="rounded-lg border-2 p-4 text-left transition"
                            :class="form.aromas.includes(option.value) ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200'"
                            :style="form.aromas.includes(option.value) ? { borderColor: primaryColor, backgroundColor: `${primaryColor}15` } : {}"
                            @click="toggleAroma(option.value)"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </div>

                <!-- Step 4: Max ABV -->
                <div v-if="currentQuestionId === 'max_abv'">
                    <h2 class="mb-4 text-2xl font-semibold">Degré d'alcool maximum souhaité</h2>
                    <div class="space-y-4">
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
                            <span class="text-3xl font-bold" :style="{ color: primaryColor }">{{ form.max_abv }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Format -->
                <div v-if="currentQuestionId === 'format'">
                    <h2 class="mb-4 text-2xl font-semibold">Quel format préférez-vous ?</h2>
                    <div class="space-y-3">
                        <label
                            v-for="option in formatOptions"
                            :key="option.value"
                            class="flex cursor-pointer items-center rounded-lg border-2 p-4 transition"
                            :class="form.format === option.value ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200'"
                        >
                            <input
                                v-model="form.format"
                                type="radio"
                                :value="option.value"
                                class="mr-3 h-4 w-4"
                                :style="{ accentColor: primaryColor }"
                            />
                            <span class="text-lg">{{ option.label }}</span>
                        </label>
                    </div>
                </div>

                <div v-if="currentQuestionId === 'style'">
                    <h2 class="mb-4 text-2xl font-semibold">Quel style de bière préférez-vous ?</h2>
                    <p class="mb-4 text-gray-600">Choisissez un style ou laissez "Peu importe"</p>
                    <select
                        v-model="form.style"
                        class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                    >
                        <option value="any">Peu importe</option>
                        <option v-for="style in styleOptions" :key="style" :value="style">
                            {{ style }}
                        </option>
                    </select>
                </div>

                <div v-if="currentQuestionId === 'brewery'">
                    <h2 class="mb-4 text-2xl font-semibold">Une brasserie en particulier ?</h2>
                    <p class="mb-4 text-gray-600">Choisissez une brasserie ou laissez "Peu importe"</p>
                    <select
                        v-model="form.brewery"
                        class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                    >
                        <option value="any">Peu importe</option>
                        <option v-for="brewery in breweryOptions" :key="brewery" :value="brewery">
                            {{ brewery }}
                        </option>
                    </select>
                </div>

                <div v-if="currentQuestionId === 'max_price'">
                    <h2 class="mb-4 text-2xl font-semibold">Budget maximum par bière</h2>
                    <div class="space-y-4">
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
                            <span class="text-3xl font-bold" :style="{ color: primaryColor }">
                                {{ form.max_price.toFixed(1) }}€
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Navigation buttons -->
                <div class="mt-8 flex justify-between">
                    <button
                        v-if="currentStep > 0"
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-6 py-2 text-gray-700 hover:bg-gray-50"
                        @click="prevStep"
                    >
                        Précédent
                    </button>
                    <div v-else></div>

                    <button
                        v-if="currentStep < totalSteps - 1"
                        type="button"
                        class="rounded-md px-6 py-2 text-white disabled:opacity-50"
                        :style="{ backgroundColor: primaryColor }"
                        :disabled="!isStepValid(currentQuestionId)"
                        @click="nextStep"
                    >
                        Suivant
                    </button>
                    <button
                        v-else
                        type="button"
                        class="rounded-md px-6 py-2 text-white disabled:opacity-50"
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


