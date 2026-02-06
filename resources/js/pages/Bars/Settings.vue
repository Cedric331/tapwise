<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Upload, Image as ImageIcon, Palette, MessageSquare, ToggleLeft, Save, ListChecks } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        logo_path?: string;
        brand_background_color?: string;
        brand_primary_color?: string;
        welcome_message?: string;
        qr_enabled: boolean;
        is_demo: boolean;
        recommendation_questions?: string[] | null;
    };
    recommendationQuestions: Array<{ id: string; label: string; description: string }>;
    selectedRecommendationQuestions: string[];
}

const props = defineProps<Props>();

const logoPreview = ref<string | null>(null);
const logoSrc = computed(() => {
    if (!props.bar.logo_path) return null;
    return props.bar.logo_path.startsWith('assets/')
        ? `/${props.bar.logo_path}`
        : `/storage/${props.bar.logo_path}`;
});

const form = useForm({
    logo: null as File | null,
    brand_background_color: props.bar.brand_background_color || '#ffffff',
    brand_primary_color: props.bar.brand_primary_color || '#4f46e5',
    welcome_message: props.bar.welcome_message || '',
    qr_enabled: props.bar.qr_enabled,
    recommendation_questions: [...props.selectedRecommendationQuestions],
});

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        form.logo = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    } else {
        form.logo = null;
        logoPreview.value = null;
    }
};

const submit = () => {
    // Use POST with _method for file uploads
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(`/bars/${props.bar.slug}/settings`, {
        forceFormData: true,
    });
};

const selectedQuestionCount = computed(() => form.recommendation_questions.length);
const canSelectMoreQuestions = computed(() => selectedQuestionCount.value < 10);
const hasMinimumQuestions = computed(() => selectedQuestionCount.value >= 3);
const isQuestionDisabled = (questionId: string) =>
    !form.recommendation_questions.includes(questionId) && !canSelectMoreQuestions.value;
</script>

<template>
    <Head title="Paramètres" />

    <AppLayout>
        <div class="min-h-screen bg-[#FDFDFC]">
            <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <Link
                        :href="`/bars/${bar.slug}`"
                        class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-amber-800 transition-colors hover:text-amber-700"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Retour au dashboard
                    </Link>
                    <p class="text-sm font-medium text-amber-800">Paramètres</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Personnalisez votre bar</h1>
                    <p class="mt-3 text-lg text-gray-600">Affinez votre identité visuelle pour une expérience cohérente.</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Palette class="h-4 w-4" />
                            Identité visuelle
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1">
                            <MessageSquare class="h-4 w-4" />
                            Ton personnalisé
                        </span>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Demo Warning -->
                    <div v-if="bar.is_demo" class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-medium text-amber-800">Ce bar de démonstration ne peut pas être modifié.</p>
                    </div>

                    <!-- Logo Section -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <ImageIcon class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Logo</h2>
                                <p class="text-sm text-gray-500">Téléchargez le logo de votre établissement</p>
                            </div>
                        </div>
                        
                        <div v-if="!bar.is_demo" class="mt-4">
                            <label class="block">
                                <div class="flex h-32 w-full items-center justify-center rounded-2xl border-2 border-dashed border-amber-200 bg-amber-50/20 cursor-pointer transition-colors hover:border-amber-300 hover:bg-amber-50/40">
                                    <div class="flex flex-col items-center">
                                        <Upload class="h-8 w-8 text-amber-500" />
                                        <span class="mt-2 text-sm font-medium text-gray-700">Cliquez pour télécharger</span>
                                        <span class="mt-1 text-xs text-gray-500">PNG, JPG jusqu'à 2MB</span>
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleLogoChange"
                                    />
                                </div>
                            </label>
                        </div>

                        <div class="mt-6">
                            <!-- Preview of new logo if selected -->
                            <div v-if="logoPreview" class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Aperçu du nouveau logo :</p>
                                <div class="inline-block rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                    <img :src="logoPreview" alt="Preview" class="h-24 w-auto object-contain" />
                                </div>
                            </div>
                            <!-- Current logo if no new logo selected -->
                            <div v-else-if="logoSrc" class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Logo actuel :</p>
                                <div class="inline-block rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                                    <img :src="logoSrc" alt="Logo actuel" class="h-24 w-auto object-contain" />
                                </div>
                            </div>
                            <div v-else class="rounded-2xl border-2 border-dashed border-amber-200 bg-amber-50/30 p-8 text-center">
                                <ImageIcon class="mx-auto h-12 w-12 text-amber-300" />
                                <p class="mt-2 text-sm text-gray-500">Aucun logo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Branding Colors -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <Palette class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Couleurs de la marque</h2>
                                <p class="text-sm text-gray-500">Personnalisez les couleurs de votre interface publique</p>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Couleur de fond</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        v-model="form.brand_background_color"
                                        type="color"
                                        class="h-12 w-20 rounded-lg border-2 border-amber-200 cursor-pointer"
                                        :disabled="bar.is_demo"
                                    />
                                    <input
                                        v-model="form.brand_background_color"
                                        type="text"
                                        class="flex-1 rounded-lg border border-amber-200 px-3 py-2 font-mono text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                        :disabled="bar.is_demo"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Couleur principale</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        v-model="form.brand_primary_color"
                                        type="color"
                                        class="h-12 w-20 rounded-lg border-2 border-amber-200 cursor-pointer"
                                        :disabled="bar.is_demo"
                                    />
                                    <input
                                        v-model="form.brand_primary_color"
                                        type="text"
                                        class="flex-1 rounded-lg border border-amber-200 px-3 py-2 font-mono text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                        :disabled="bar.is_demo"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Welcome Message -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <MessageSquare class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Message d'accueil</h2>
                                <p class="text-sm text-gray-500">Un message personnalisé pour vos clients</p>
                            </div>
                        </div>
                        <textarea
                            v-model="form.welcome_message"
                            rows="4"
                            class="mt-4 block w-full rounded-lg border border-amber-200 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                            placeholder="Bienvenue ! Répondez à quelques questions pour découvrir la bière parfaite pour vous."
                            :disabled="bar.is_demo"
                        />
                    </div>

                    <!-- Recommendation Questions -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <ListChecks class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Questions de recommandation</h2>
                                <p class="text-sm text-gray-500">Sélectionnez entre 3 et 8 questions à poser</p>
                            </div>
                        </div>

                        <div class="mb-4 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                            <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                                {{ selectedQuestionCount }} / 8 sélectionnées
                            </span>
                            <span
                                v-if="!hasMinimumQuestions"
                                class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-amber-800"
                            >
                                Minimum 3 questions
                            </span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label
                                v-for="question in recommendationQuestions"
                                :key="question.id"
                                class="flex cursor-pointer items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50/20 p-4 transition hover:border-amber-200"
                                :class="{
                                    'opacity-60 cursor-not-allowed': bar.is_demo || isQuestionDisabled(question.id),
                                    'border-amber-300 bg-amber-50': form.recommendation_questions.includes(question.id),
                                }"
                            >
                                <input
                                    v-model="form.recommendation_questions"
                                    type="checkbox"
                                    :value="question.id"
                                    class="mt-1 h-4 w-4"
                                    :disabled="bar.is_demo || isQuestionDisabled(question.id)"
                                />
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ question.label }}</p>
                                    <p class="text-xs text-gray-500">{{ question.description }}</p>
                                </div>
                            </label>
                        </div>

                        <p class="mt-4 text-xs text-gray-500">
                            Par défaut, les questions actuelles sont conservées tant que vous ne modifiez pas cette sélection.
                        </p>
                    </div>

                    <!-- QR Code Toggle -->
                    <div v-if="!bar.is_demo" class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                    <ToggleLeft class="h-5 w-5" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Activer le QR code</h2>
                                    <p class="text-sm text-gray-500">Permettre aux clients d'accéder aux recommandations</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    v-model="form.qr_enabled"
                                    type="checkbox"
                                    class="sr-only peer"
                                />
                                <div class="h-6 w-11 rounded-full bg-gray-200 peer peer-checked:bg-amber-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div v-if="!bar.is_demo" class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                            :disabled="form.processing || !hasMinimumQuestions"
                        >
                            <Save class="h-4 w-4" />
                            <span v-if="form.processing">Enregistrement...</span>
                            <span v-else>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
