<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { QrCode, Users, TrendingUp, CheckCircle2, ArrowRight, Sparkles, Zap, Shield, Clock, Heart, Star, MessageSquare, Folder } from 'lucide-vue-next';
import { login, register } from '@/routes';
import AppLogo from '@/components/AppLogo.vue';
import MarketingNavbar from '@/components/MarketingNavbar.vue';

const demoUrl = '/b/demo';
const baseUrl = 'https://tapwise.fr';
const ogImage = `${baseUrl}/assets/illustration-beer-glass.png`;
const heroPoster = '/assets/hero-video.png';
const heroVideoSrc = '/assets/hero-video.mp4';
const schemaOrgJson = JSON.stringify({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'Organization',
            name: 'Tapwise',
            url: baseUrl,
            logo: `${baseUrl}/assets/logo.svg`,
        },
        {
            '@type': 'WebSite',
            name: 'Tapwise',
            url: baseUrl,
            inLanguage: 'fr-FR',
        },
        {
            '@type': 'SoftwareApplication',
            name: 'Tapwise',
            applicationCategory: 'BusinessApplication',
            operatingSystem: 'Web',
            url: baseUrl,
            description:
                'Logiciel de recommandations de boissons (bières & vins) pour établissements avec QR code.',
            offers: {
                '@type': 'Offer',
                price: '19.99',
                priceCurrency: 'EUR',
            },
        },
    ],
});
const schemaScriptId = 'schema-org-tapwise';
let schemaScriptEl: HTMLScriptElement | null = null;
const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const notification = ref<{ type: 'success' | 'error'; message: string } | null>(null);
const showCookieBanner = ref(false);
const cookieConsentKey = 'tapwise_cookie_consent';
let notificationTimer: number | null = null;

const showNotification = (type: 'success' | 'error', message: string) => {
    notification.value = { type, message };
    if (notificationTimer) {
        window.clearTimeout(notificationTimer);
    }
    notificationTimer = window.setTimeout(() => {
        notification.value = null;
    }, 4500);
};

const submitContact = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            showNotification('success', 'Message envoyé. Nous vous répondrons rapidement.');
            form.reset();
        },
        onError: (errors) => {
            const message =
                errors.form ||
                errors.message ||
                Object.values(errors)[0] ||
                "Une erreur est survenue. Merci de réessayer.";
            showNotification('error', message);
        },
    });
};

const setCookieConsent = (value: 'accepted' | 'refused') => {
    try {
        localStorage.setItem(cookieConsentKey, value);
    } catch (error) {
        // Ignore storage errors (private mode or disabled storage).
    }
    showCookieBanner.value = false;
};

onMounted(() => {
    if (!document.getElementById(schemaScriptId)) {
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.id = schemaScriptId;
        script.text = schemaOrgJson;
        document.head.appendChild(script);
        schemaScriptEl = script;
    }

    const video = document.querySelector<HTMLVideoElement>('video[data-src]');
    if (!video) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || window.innerWidth < 768) {
        video.remove();
        return;
    }

    const observer = new IntersectionObserver(
        ([entry]) => {
            if (!entry.isIntersecting) {
                return;
            }

            video.src = video.dataset.src ?? '';
            video.playbackRate = 0.5;
            video.play().catch(() => {
                // Ignore autoplay failures (e.g., user settings or browser policies).
            });
            observer.disconnect();
        },
        { threshold: 0.25 }
    );

    observer.observe(video);

    try {
        if (!localStorage.getItem(cookieConsentKey)) {
            showCookieBanner.value = true;
        }
    } catch (error) {
        showCookieBanner.value = true;
    }
});

onBeforeUnmount(() => {
    if (schemaScriptEl && schemaScriptEl.parentNode) {
        schemaScriptEl.parentNode.removeChild(schemaScriptEl);
        schemaScriptEl = null;
    }
});
</script>

<template>
    <Head>
        <title>Tapwise — Recommandations de bières & vins pour établissements</title>
        <meta
            name="description"
            content="Recommandations de bières et vins pour bars, restaurants et caves : QR code, catalogue, suggestions personnalisées. Boostez l'expérience et les ventes."
        />
        <meta name="keywords" content="bar, restaurant, cave, vin, bière, carte des boissons, QR code, recommandation client, logiciel établissement, SaaS" />
        <meta name="author" content="Tapwise" />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Tapwise — Recommandations de bières & vins" />
        <meta property="og:description" content="QR code et catalogue intelligent pour guider les clients et augmenter les ventes." />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="baseUrl" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:alt" content="Tapwise - recommandations de boissons pour établissements" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Tapwise — Recommandations de bières & vins" />
        <meta name="twitter:description" content="QR code et recommandations pour votre carte des boissons." />
        <meta name="twitter:image" :content="ogImage" />
        <link rel="canonical" :href="baseUrl" />
    </Head>

    <div class="min-h-screen overflow-x-hidden bg-[#FDFDFC] text-[#1b1b18]">
        <!-- Navigation -->
        <MarketingNavbar active="home" />

        <!-- Hero Section -->
        <section class="relative overflow-hidden min-h-[90vh] flex items-center">
            <!-- Vidéo en background -->
            <div class="absolute inset-0 z-0">
                <video
                    muted
                    playsinline
                    preload="none"
                    :poster="heroPoster"
                    class="h-full w-full object-cover"
                    :data-src="heroVideoSrc"
                ></video>
                <!-- Overlay blanc semi-transparent -->
                <div class="absolute inset-0 bg-white/85"></div>
            </div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-32 w-full">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Contenu texte -->
                    <div class="text-center lg:text-left">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                            Recommandations de bières & vins pour établissements
                            <span class="block text-amber-800">QR code & carte des boissons optimisée</span>
                        </h1>
                        <p class="mt-6 text-lg text-gray-600 leading-relaxed">
                            Tapwise aide les bars, restaurants et caves à guider leurs clients : QR code, questions rapides et recommandations personnalisées selon votre carte des boissons (bières & vins).
                        </p>
                        <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row lg:justify-start">
                            <Link
                                :href="demoUrl"
                                class="group inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-8 py-4 text-lg font-semibold text-white shadow-lg transition-all hover:shadow-xl hover:scale-[1.02]"
                            >
                                Découvrir l'expérience
                                <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-1" />
                            </Link>
                            <Link
                                :href="register()"
                                class="inline-flex items-center rounded-lg border-2 border-amber-300 bg-white px-8 py-4 text-lg font-semibold text-amber-800 transition-all hover:border-amber-400 hover:bg-amber-50"
                            >
                                Rejoindre l'excellence
                            </Link>
                        </div>
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-600 lg:justify-start">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span>Essai gratuit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span>Sans engagement</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span>5 min d'installation</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Illustration verre -->
                    <!-- <div class="relative flex justify-center lg:justify-end">
                        <div class="relative">
                            <img 
                                src="/assets/illustration-beer-glass.png" 
                                alt="Verre"
                                class="h-auto w-full max-w-md"
                            />
                        </div>
                    </div> -->
                </div>
            </div>
        </section>

        <!-- Comment ça fonctionne -->
        <section id="fonctionnement" class="bg-white py-24 relative shadow-[0_-120px_160px_40px_rgba(255,255,255,1)]"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-sm font-medium text-amber-800 mb-2">Une expérience en 3 temps</p>
                    <h2 class="text-4xl font-bold text-gray-900">Comment ça fonctionne</h2>
                    <div class="mt-4 mx-auto w-24 h-1 bg-gradient-to-r from-amber-500 to-amber-800 rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Illustration QR code décoré -->
                    <div class="relative flex justify-center order-2 lg:order-1">
                        <div class="relative">
                            <img 
                                src="/assets/illustration-qr-frame.png" 
                                alt="QR code décoré" 
                                class="h-auto w-full max-w-md drop-shadow-lg"
                                loading="lazy"
                                decoding="async"
                                width="768"
                                height="768"
                            />
                        </div>
                    </div>
                    
                    <!-- Étapes -->
                    <div class="space-y-10 order-1 lg:order-2">

                        <div class="flex gap-6">
                            <div class="flex-shrink-0">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-800 text-white text-xl font-bold shadow-lg">
                                    01
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <Folder class="h-6 w-6 text-amber-800" />
                                    <h3 class="text-xl font-bold text-gray-900">Importer votre carte</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Importez votre carte des boissons pour que Tapwise puisse les proposer à vos clients.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-800 text-white text-xl font-bold shadow-lg">
                                    02
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <QrCode class="h-6 w-6 text-amber-800" />
                                    <h3 class="text-xl font-bold text-gray-900">Le client scanne</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Un QR code élégant disposé sur votre comptoir ou vos tables invite le client à découvrir votre sélection.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex gap-6">
                            <div class="flex-shrink-0">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-800 text-white text-xl font-bold shadow-lg">
                                    03
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <MessageSquare class="h-6 w-6 text-amber-800" />
                                    <h3 class="text-xl font-bold text-gray-900">Quelques questions</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Un questionnaire raffiné de 5 questions pour comprendre les préférences et l'humeur du moment.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex gap-6">
                            <div class="flex-shrink-0">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-800 text-white text-xl font-bold shadow-lg">
                                    04
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <Sparkles class="h-6 w-6 text-amber-800" />
                                    <h3 class="text-xl font-bold text-gray-900">Recommandations personnalisées</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Notre algorithme suggère 2 à 3 boissons de votre carte, parfaitement adaptées aux goûts du client.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Avantages -->
        <section id="avantages" class="bg-[#FDFDFC] py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-sm font-medium text-amber-800 mb-2">Pourquoi nous choisir</p>
                    <h2 class="text-4xl font-bold text-gray-900">Les avantages Tapwise</h2>
                    <div class="mt-4 mx-auto w-24 h-1 bg-gradient-to-r from-amber-500 to-amber-800 rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="rounded-2xl bg-white p-8 shadow-sm border border-amber-100">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-50 text-amber-800 mb-6">
                            <Clock class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Temps précieux libéré</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Vos équipes peuvent se concentrer sur l'essentiel : créer une atmosphère chaleureuse et des moments mémorables.
                        </p>
                        <p class="text-lg font-semibold text-amber-800">3 min économisées par commande</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-8 shadow-sm border border-amber-100">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-50 text-amber-800 mb-6">
                            <TrendingUp class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Ventes optimisées</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Des recommandations pertinentes incitent à la découverte de nouvelles références et augmentent le panier moyen.
                        </p>
                        <p class="text-lg font-semibold text-amber-800">Découverte de nouvelles références</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-8 shadow-sm border border-amber-100">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-50 text-amber-800 mb-6">
                            <Heart class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Clients conquis</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Une expérience personnalisée qui transforme chaque visite en un moment unique et renforce la fidélité.
                        </p>
                        <p class="text-lg font-semibold text-amber-800">Expérience personnalisée et renforcée</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-8 shadow-sm border border-amber-100">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-50 text-amber-800 mb-6">
                            <Star class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Image premium</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Positionnez votre établissement comme un lieu d'exception où l'innovation rencontre la tradition brassicole.
                        </p>
                        <p class="text-lg font-semibold text-amber-800">Une touche de modernité</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Témoignages -->
        <section class="bg-white py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-sm font-medium text-amber-800 mb-2">Ce qu'ils en pensent</p>
                    <h2 class="text-4xl font-bold text-gray-900">Témoignages de nos partenaires</h2>
                    <div class="mt-4 mx-auto w-24 h-1 bg-gradient-to-r from-amber-500 to-amber-800 rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="rounded-2xl bg-[#FDFDFC] p-8 border border-amber-100">
                        <div class="text-6xl font-bold text-amber-200 mb-4 leading-none">"</div>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            Les clients adorent découvrir de nouvelles boissons qu'ils n'auraient jamais osé commander autrement. Des petites améliorations à faire mais c'est top.
                        </p>
                        <div class="border-t border-amber-100 pt-4">
                            <p class="font-semibold text-gray-900">Marie-Claire Dubois</p>
                            <p class="text-sm text-gray-600">Gérante, Le Houblon Doré</p>
                            <!-- <p class="text-xs text-gray-500">Lyon</p> -->
                        </div>
                    </div>
                    
                    <div class="rounded-2xl bg-[#FDFDFC] p-8 border border-amber-100">
                        <div class="text-6xl font-bold text-amber-200 mb-4 leading-none">"</div>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            J'adore l'idée et cela est ludique pour les clients
                        </p>
                        <div class="border-t border-amber-100 pt-4">
                            <p class="font-semibold text-gray-900">Thomas</p>
                            <p class="text-sm text-gray-600">La Brasserie Artisanale</p>
                            <p class="text-xs text-gray-500">Bordeaux</p>
                        </div>
                    </div>
                    
                    <div class="rounded-2xl bg-[#FDFDFC] p-8 border border-amber-100">
                        <div class="text-6xl font-bold text-amber-200 mb-4 leading-none">"</div>
                        <p class="text-gray-700 leading-relaxed mb-6">
                           Cela permet de mettre en valeur notre sélection de boissons. Merci
                        </p>
                        <div class="border-t border-amber-100 pt-4">
                            <p class="font-semibold text-gray-900">Benoit Conte</p>
                            <p class="text-sm text-gray-600">Comptoir des Boissons</p>
                            <p class="text-xs text-gray-500">Paris</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Découvrez l'expérience -->
        <section class="bg-[#FDFDFC] py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Contenu texte à gauche -->
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2">Une expérience unique</p>
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Découvrez Tapwise en action</h2>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            Scannez le QR code avec votre smartphone pour vivre l'expérience client. En quelques secondes, découvrez comment Tapwise transforme la découverte de vins et bières en un moment privilégié.
                        </p>
                        
                        <!-- Étapes numérotées -->
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-sm flex-shrink-0">
                                    1
                                </div>
                                <span class="text-gray-700">Scannez le code ou cliquez sur le bouton</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-sm flex-shrink-0">
                                    2
                                </div>
                                <span class="text-gray-700">Répondez aux 5 questions de préférences</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-sm flex-shrink-0">
                                    3
                                </div>
                                <span class="text-gray-700">Recevez vos recommandations personnalisées</span>
                            </div>
                        </div>
                        
                        <!-- Bouton -->
                        <Link
                            :href="demoUrl"
                            class="group inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-8 py-4 text-lg font-semibold text-white shadow-lg transition-all hover:shadow-xl hover:scale-[1.02]"
                        >
                            Essayer la démo
                            <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>
                    
                    <!-- Illustrations à droite -->
                    <div class="relative flex justify-center lg:justify-end">
                        <div class="relative">
                            <!-- QR code décoré (grande illustration) -->
                            <div class="relative z-10">
                            <img 
                                    src="/assets/illustration-qr-frame.png" 
                                    alt="QR code décoré" 
                                class="h-auto w-full max-w-md drop-shadow-lg"
                                loading="lazy"
                                decoding="async"
                                width="768"
                                height="768"
                                />
                            </div>
                            <!-- Illustration barman (partiellement visible derrière) -->
                            <div class="absolute -right-18 -bottom-44 z-0 opacity-80">
                            <img 
                                    src="/assets/illustration-bartender.png" 
                                    alt="Barman" 
                                class="h-auto w-64 max-w-none"
                                loading="lazy"
                                decoding="async"
                                width="512"
                                height="768"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="tarifs" class="bg-white py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-sm font-medium text-amber-800 mb-2">Simple et transparent</p>
                    <h2 class="text-4xl font-bold text-gray-900">Un tarif unique, tout inclus</h2>
                    <div class="mt-4 mx-auto w-24 h-1 bg-gradient-to-r from-amber-500 to-amber-800 rounded-full"></div>
                </div>
                
                <div class="flex justify-center">
                    <div class="w-full max-w-md rounded-3xl border-2 border-amber-200 bg-white p-10 shadow-xl">
                        <div class="text-center">
                            <div class="inline-flex items-baseline">
                                <span class="text-6xl font-bold text-gray-900">19,99€</span>
                                <span class="ml-2 text-2xl text-gray-600">/mois</span>
                            </div>
                            <p class="mt-4 text-gray-600">
                                Tout ce dont vous avez besoin pour sublimer l'expérience de vos clients
                            </p>
                        </div>
                        <ul class="mt-10 space-y-5 text-left">
                            <li class="flex items-start gap-4">
                                <CheckCircle2 class="mt-0.5 h-6 w-6 flex-shrink-0 text-green-600" />
                                <span class="text-gray-700 leading-relaxed">QR code personnalisé à votre image</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <CheckCircle2 class="mt-0.5 h-6 w-6 flex-shrink-0 text-green-600" />
                                <span class="text-gray-700 leading-relaxed">Gestion illimitée de votre carte des boissons</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <CheckCircle2 class="mt-0.5 h-6 w-6 flex-shrink-0 text-green-600" />
                                <span class="text-gray-700 leading-relaxed">Personnalisation de l'expérience</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <CheckCircle2 class="mt-0.5 h-6 w-6 flex-shrink-0 text-green-600" />
                                <span class="text-gray-700 leading-relaxed">Support client</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <CheckCircle2 class="mt-0.5 h-6 w-6 flex-shrink-0 text-green-600" />
                                <span class="text-gray-700 leading-relaxed">Sans engagement, résiliable à tout moment</span>
                            </li>
                        </ul>
                        <div class="mt-10">
                            <Link
                                :href="register()"
                                class="block w-full rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-4 py-4 text-center font-semibold text-white shadow-lg transition-all hover:shadow-xl"
                            >
                                Démarrer l'aventure
                            </Link>
                        </div>
                        <p class="mt-6 text-center text-sm text-gray-500">
                            Essai gratuit de 14 jours • Aucune carte requise
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="bg-[#FDFDFC] py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2">Contact</p>
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Parlons de votre établissement</h2>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Une question sur Tapwise ? Une démo pour votre carte des boissons ? Écrivez-nous et nous répondons rapidement.
                        </p>
                    </div>
                    <div class="rounded-2xl bg-white p-8 shadow-sm border border-amber-100">
                        <form @submit.prevent="submitContact" class="space-y-5">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="marketing-name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                                    <input
                                        id="marketing-name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        autocomplete="name"
                                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                        placeholder="Nom"
                                    />
                                </div>
                                <div>
                                    <label for="marketing-email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input
                                        id="marketing-email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        autocomplete="email"
                                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                        placeholder="email@email.com"
                                    />
                                </div>
                            </div>
                            <div>
                                <label for="marketing-subject" class="block text-sm font-medium text-gray-700 mb-2">Sujet *</label>
                                <input
                                    id="marketing-subject"
                                    v-model="form.subject"
                                    type="text"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                    placeholder="Demande de démo / Informations"
                                />
                            </div>
                            <div>
                                <label for="marketing-message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                                <textarea
                                    id="marketing-message"
                                    v-model="form.message"
                                    rows="6"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                    placeholder="Parlez-nous de votre établissement…"
                                />
                            </div>
                            <label for="marketing-consent" class="flex items-center gap-2 text-xs text-gray-500">
                                <input id="marketing-consent" type="checkbox" required class="h-4 w-4 rounded border-gray-300" />
                                <span>
                                    J'accepte que mes données soient utilisées pour répondre à ma demande.
                                    <Link href="/politique-de-confidentialite" class="text-amber-700 underline underline-offset-2 hover:text-amber-800">
                                        En savoir plus
                                    </Link>
                                </span>
                            </label>
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Envoi en cours...</span>
                                <span v-else>Envoyer le message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-amber-100 bg-[#FDFDFC]">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="inline-flex items-center gap-1 flex-nowrap">
                                <AppLogo class="h-12 w-12 p-0 -mr-1 shrink-0" />
                            </div>
                        </div>
                        <p class="max-w-sm text-sm text-gray-600 leading-relaxed mb-6">
                            Sublimez l'expérience de vos clients avec des recommandations de boissons personnalisées.
                        </p>
                    </div>

                    <!-- Product -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-900 mb-4">Produit</h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="#fonctionnement" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Fonctionnement
                                </a>
                            </li>
                            <li>
                                <a href="#avantages" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Avantages
                                </a>
                            </li>
                            <li>
                                <a href="#tarifs" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Tarifs
                                </a>
                            </li>
                            <li>
                                <Link href="/blog" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Blog
                                </Link>
                            </li>
                            <li>
                                <Link :href="demoUrl" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Démo
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-900 mb-4">Légal</h3>
                        <ul class="space-y-3">
                            <li>
                                <Link href="/mentions-legales" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Mentions légales
                                </Link>
                            </li>
                            <li>
                                <Link href="/politique-de-confidentialite" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    Confidentialité
                                </Link>
                            </li>
                            <li>
                                <Link href="/cgu" class="text-sm text-gray-600 underline underline-offset-2 transition-colors hover:text-amber-800">
                                    CGU
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-amber-100 pt-8">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <p class="text-xs text-gray-500">
                            © {{ new Date().getFullYear() }} Tapwise. Tous droits réservés.
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <div
            v-if="showCookieBanner"
            class="fixed inset-x-4 bottom-6 z-50 mx-auto max-w-5xl rounded-2xl border border-amber-100 bg-white p-6 shadow-xl"
        >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-600">
                    <p class="font-semibold text-gray-900">Cookies & confidentialité</p>
                    <p>
                        Nous utilisons des cookies essentiels pour le bon fonctionnement du site.
                        <Link href="/politique-de-confidentialite" class="text-amber-700 underline underline-offset-2 hover:text-amber-800">
                            En savoir plus
                        </Link>
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-800 transition hover:bg-amber-50"
                        @click="setCookieConsent('refused')"
                    >
                        Refuser
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-amber-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-900"
                        @click="setCookieConsent('accepted')"
                    >
                        Tout accepter
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="notification"
            class="fixed bottom-6 right-6 z-50 max-w-sm rounded-lg px-4 py-3 text-sm shadow-lg"
            :class="notification.type === 'success'
                ? 'bg-emerald-600 text-white'
                : 'bg-red-600 text-white'"
        >
            {{ notification.message }}
        </div>
    </div>
</template>
