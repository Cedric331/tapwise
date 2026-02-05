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
}

const props = defineProps<Props>();

const currentStep = ref(1);
const totalSteps = 5;

const form = useForm({
    bitterness: '',
    color: [] as string[],
    aromas: [] as string[],
    max_abv: 5,
    format: '',
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
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
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
                        :style="{ width: `${(currentStep / totalSteps) * 100}%`, backgroundColor: primaryColor }"
                    ></div>
                </div>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Étape {{ currentStep }} sur {{ totalSteps }}
                </p>
            </div>

            <!-- Form steps -->
            <div class="rounded-lg bg-white p-8 shadow-lg">
                <!-- Step 1: Bitterness -->
                <div v-if="currentStep === 1">
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
                <div v-if="currentStep === 2">
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
                <div v-if="currentStep === 3">
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
                <div v-if="currentStep === 4">
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
                <div v-if="currentStep === 5">
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

                <!-- Navigation buttons -->
                <div class="mt-8 flex justify-between">
                    <button
                        v-if="currentStep > 1"
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-6 py-2 text-gray-700 hover:bg-gray-50"
                        @click="prevStep"
                    >
                        Précédent
                    </button>
                    <div v-else></div>

                    <button
                        v-if="currentStep < totalSteps"
                        type="button"
                        class="rounded-md px-6 py-2 text-white disabled:opacity-50"
                        :style="{ backgroundColor: primaryColor }"
                        :disabled="(currentStep === 1 && !form.bitterness) || (currentStep === 2 && form.color.length === 0) || (currentStep === 3 && form.aromas.length === 0)"
                        @click="nextStep"
                    >
                        Suivant
                    </button>
                    <button
                        v-else
                        type="button"
                        class="rounded-md px-6 py-2 text-white disabled:opacity-50"
                        :style="{ backgroundColor: primaryColor }"
                        :disabled="!form.format || form.processing"
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


