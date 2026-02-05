<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Download, Copy, CheckCircle2, QrCode as QrCodeIcon, Sparkles, CreditCard, AlertCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';

interface Props {
    bar: {
        id: number;
        name: string;
        slug: string;
        public_url: string;
        qr_enabled: boolean;
        is_demo: boolean;
    };
    publicUrl: string;
    qrCodeSvg: string;
    qrFrames: Array<{ id: string; label: string; path: string }>;
    qrOptions: {
        style: string;
        frame: string | null;
        includeLogo: boolean;
        logoAvailable: boolean;
    };
    subscription: {
        status: 'active' | 'trial' | 'inactive';
        trialEndsAt?: string | null;
        canDownload: boolean;
    };
}

const props = defineProps<Props>();
const copied = ref(false);
const qrEnabled = ref(props.bar.qr_enabled);
const isUpdatingQr = ref(false);

const selectedFrame = ref(props.qrOptions.frame ?? 'none');
const includeLogo = ref(props.qrOptions.includeLogo);

const applyOptions = () => {
    const style = selectedFrame.value === 'none' ? 'simple' : 'framed';
    router.get(
        `/bars/${props.bar.slug}/qr-code`,
        {
            style,
            frame: style === 'framed' ? selectedFrame.value : null,
            logo: includeLogo.value ? 1 : 0,
        },
        {
            preserveScroll: true,
            replace: true,
        }
    );
};

const downloadQrCode = () => {
    if (!props.subscription.canDownload) {
        return;
    }

    const style = selectedFrame.value === 'none' ? 'simple' : 'framed';
    const params = new URLSearchParams();
    params.set('style', style);
    if (style === 'framed') {
        params.set('frame', selectedFrame.value);
    }
    if (includeLogo.value) {
        params.set('logo', '1');
    } else {
        params.set('logo', '0');
    }

    window.location.href = `/bars/${props.bar.slug}/qr-code/download?${params.toString()}`;
};

const copyUrl = async () => {
    await navigator.clipboard.writeText(props.publicUrl);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
};

const startSubscription = () => {
    router.post(`/bars/${props.bar.slug}/subscription/checkout`, {}, { preserveScroll: true });
};

const toggleQrEnabled = () => {
    const nextValue = qrEnabled.value;
    isUpdatingQr.value = true;

    router.put(
        `/bars/${props.bar.slug}/qr-code/status`,
        { qr_enabled: nextValue },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                qrEnabled.value = !nextValue;
            },
            onFinish: () => {
                isUpdatingQr.value = false;
            },
        }
    );
};

watch(
    () => props.bar.qr_enabled,
    (value) => {
        qrEnabled.value = value;
    }
);
</script>

<template>
    <Head title="QR Code" />

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
                    <p class="text-sm font-medium text-amber-800">QR Code</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Votre QR Code Tapwise</h1>
                    <p class="mt-3 text-lg text-gray-600">Téléchargez et partagez une expérience fluide pour vos clients.</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-medium text-amber-800">
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
                            <Sparkles class="h-4 w-4" />
                            Design premium
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1">
                            <QrCodeIcon class="h-4 w-4 text-amber-700" />
                            Accès instantané
                        </span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-if="subscription.status !== 'active'" class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-amber-800">
                                <AlertCircle class="h-5 w-5 text-amber-700" />
                                <span v-if="subscription.status === 'trial'">Essai en cours · Pensez a activer l'abonnement.</span>
                                <span v-else>Abonnement inactif · Le telechargement est bloque.</span>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-amber-700"
                                @click="startSubscription"
                            >
                                <CreditCard class="h-4 w-4" />
                                Activer l'abonnement
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr] lg:items-start">
                        <!-- URL Card -->
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 to-amber-800 p-8 shadow-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <label class="text-lg font-semibold text-white">URL publique</label>
                                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">
                                            Partage instantané
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-amber-100">Partagez cette URL ou le QR code avec vos clients</p>
                                    <div class="mt-4 flex gap-2">
                                        <input
                                            :value="publicUrl"
                                            readonly
                                            class="flex-1 rounded-lg border-0 bg-white/20 px-4 py-2.5 font-mono text-sm text-white placeholder:text-amber-100 backdrop-blur-sm focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50"
                                        />
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-amber-700 shadow-sm transition-colors hover:bg-amber-50"
                                            @click="copyUrl"
                                        >
                                            <Copy v-if="!copied" class="h-4 w-4" />
                                            <CheckCircle2 v-else class="h-4 w-4 text-green-600" />
                                            <span>{{ copied ? 'Copié !' : 'Copier' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="pointer-events-none absolute right-0 top-0 h-48 w-48 -translate-y-12 translate-x-12 rounded-full bg-white/10 blur-3xl"></div>
                            <div class="pointer-events-none absolute -bottom-16 left-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                        </div>

                        <!-- Status Toggle -->
                        <div v-if="!bar.is_demo" class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">Statut du QR code</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ qrEnabled ? 'Le QR code est actif et accessible' : 'Le QR code est désactivé' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                        :class="qrEnabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ qrEnabled ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input
                                            v-model="qrEnabled"
                                            type="checkbox"
                                            class="peer sr-only"
                                            :disabled="isUpdatingQr"
                                            @change="toggleQrEnabled"
                                        />
                                        <div
                                            class="h-6 w-11 rounded-full bg-gray-200 peer peer-checked:bg-amber-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                        ></div>
                                    </label>
                                </div>
                            </div>
                            <p class="mt-4 text-xs text-gray-500">
                                Activez ou désactivez l'accès client sans quitter cette page.
                            </p>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Personnalisation du QR code</h2>
                                <p class="mt-1 text-sm text-gray-600">Choisissez un style adapté à votre établissement.</p>
                            </div>
                        </div>

                        <div class="mt-6  gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Cadre</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-4">
                                    <label
                                        v-for="frame in qrFrames"
                                        :key="frame.id"
                                        class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-white px-4 py-3 text-sm font-medium text-gray-700"
                                    >
                                        <input
                                            v-model="selectedFrame"
                                            type="radio"
                                            :value="frame.id"
                                            class="h-4 w-4 border-amber-200 text-amber-600 focus:ring-amber-200"
                                            @change="applyOptions"
                                        />
                                        {{ frame.label }}
                                    </label>
                                    <label class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-white px-4 py-3 text-sm font-medium text-gray-700">
                                        <input
                                            v-model="selectedFrame"
                                            type="radio"
                                            value="none"
                                            class="h-4 w-4 border-amber-200 text-amber-600 focus:ring-amber-200"
                                            @change="applyOptions"
                                        />
                                        Aucun
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div v-if="qrOptions.logoAvailable" class="mt-6 flex items-center gap-3">
                            <label class="flex items-center gap-3 text-sm font-medium text-gray-700">
                                <input
                                    v-model="includeLogo"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-amber-200 text-amber-600 focus:ring-amber-200"
                                    @change="applyOptions"
                                />
                                Ajouter le logo du bar au centre
                            </label>
                        </div>
                        <p v-else class="mt-6 text-xs text-gray-500">
                            Ajoutez un logo dans les paramètres pour activer l’option “logo au centre”.
                        </p>
                    </div>

                    <!-- QR Code Preview -->
                    <div class="rounded-3xl border border-amber-100 bg-white p-8 shadow-sm">
                        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">
                                    <Sparkles class="h-4 w-4" />
                                    Aperçu premium
                                </div>
                                <h3 class="mt-4 text-2xl font-semibold text-gray-900">Un QR code à votre image</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Le QR code sera généré lors du téléchargement. Placez-le au comptoir, sur les tables ou dans votre menu.
                                </p>
                                <div class="mt-6 flex flex-wrap gap-3 text-xs text-gray-600">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1 font-medium text-amber-800">
                                        <QrCodeIcon class="h-4 w-4" />
                                        Haute qualité
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1 font-medium text-amber-800">
                                        <CheckCircle2 class="h-4 w-4 text-emerald-600" />
                                        Prêt à imprimer
                                    </span>
                                </div>
                                <p class="mt-4 text-xs text-gray-500">
                                    Le logo est ajouté si disponible dans les paramètres du bar.
                                </p>
                                <Link
                                    :href="`/bars/${bar.slug}/settings`"
                                    class="mt-3 inline-flex items-center text-xs font-medium text-amber-800 hover:text-amber-700"
                                >
                                    Mettre à jour le logo du bar
                                </Link>
                            </div>
                            <div class="flex justify-center">
                                <div class="relative inline-flex items-center justify-center rounded-3xl border-2 border-dashed border-amber-200 bg-amber-50/40 p-6">
                                    <div class="qr-preview h-72 w-72" v-html="qrCodeSvg"></div>
                                    <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-100/50 blur-2xl"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <div class="flex justify-center">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="!subscription.canDownload"
                            @click="downloadQrCode"
                        >
                            <Download class="h-5 w-5" />
                            Télécharger le QR code
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.qr-preview :deep(svg) {
    width: 100%;
    height: 100%;
    display: block;
}
</style>
