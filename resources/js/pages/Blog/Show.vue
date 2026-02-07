<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import MarketingNavbar from '@/components/MarketingNavbar.vue';

interface BlogPost {
    title: string;
    slug: string;
    content: string;
    excerpt: string;
    cover_image_url: string | null;
    published_at_iso: string | null;
    tags: string[];
}

interface Props {
    post: BlogPost;
}

const props = defineProps<Props>();

const copyMessage = ref<string | null>(null);
let copyTimer: number | null = null;
const baseUrl = 'https://tapwise.fr';
const pageUrl = `${baseUrl}/blog/${props.post.slug}`;
const ogImage = props.post.cover_image_url ?? `${baseUrl}/assets/illustration-beer-glass.png`;

const encodedUrl = computed(() => encodeURIComponent(pageUrl));
const encodedTitle = computed(() => encodeURIComponent(props.post.title));
const facebookShareUrl = computed(() => `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl.value}`);
const twitterShareUrl = computed(() => `https://twitter.com/intent/tweet?url=${encodedUrl.value}&text=${encodedTitle.value}`);
const linkedInShareUrl = computed(() => `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl.value}`);
const whatsappShareUrl = computed(() => `https://wa.me/?text=${encodedTitle.value}%20${encodedUrl.value}`);
const emailShareUrl = computed(() => `mailto:?subject=${encodedTitle.value}&body=${encodedUrl.value}`);
const instagramShareUrl = computed(() => `https://www.instagram.com/?url=${encodedUrl.value}`);

const formatDate = (value: string | null) => {
    if (!value) return null;
    const date = new Date(`${value}T00:00:00`);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

const copyShareLink = async () => {
    try {
        await navigator.clipboard.writeText(pageUrl);
        copyMessage.value = 'Lien copié dans le presse-papiers.';
    } catch (error) {
        copyMessage.value = 'Impossible de copier le lien automatiquement.';
    }

    if (copyTimer) {
        window.clearTimeout(copyTimer);
    }
    copyTimer = window.setTimeout(() => {
        copyMessage.value = null;
    }, 3000);
};
</script>

<template>
    <Head>
        <title>{{ props.post.title }} — Blog Tapwise</title>
        <meta name="description" :content="props.post.excerpt" />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" :content="`${props.post.title} — Blog Tapwise`" />
        <meta property="og:description" :content="props.post.excerpt" />
        <meta property="og:type" content="article" />
        <meta property="og:url" :content="pageUrl" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:alt" :content="props.post.title" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="`${props.post.title} — Blog Tapwise`" />
        <meta name="twitter:description" :content="props.post.excerpt" />
        <meta name="twitter:image" :content="ogImage" />
        <link rel="canonical" :href="pageUrl" />
        <meta v-if="props.post.published_at_iso" property="article:published_time" :content="props.post.published_at_iso" />
    </Head>

    <div class="min-h-screen bg-[#FDFDFC] text-gray-900">
        <MarketingNavbar active="blog" anchor-prefix="/" />

        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <Link href="/blog" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-800 hover:text-amber-900">
                <ArrowLeft class="h-4 w-4" />
                Retour au blog
            </Link>

            <article class="mt-8 overflow-hidden rounded-3xl border border-amber-100 bg-white shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)]">
                <div v-if="props.post.cover_image_url" class="aspect-[16/9] w-full overflow-hidden bg-amber-50">
                    <img
                        :src="props.post.cover_image_url"
                        :alt="props.post.title"
                        class="h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                    />
                </div>
                <div class="p-8 sm:p-10">
                    <p v-if="props.post.published_at_iso" class="text-xs font-semibold text-amber-700">
                        {{ formatDate(props.post.published_at_iso) }}
                    </p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        {{ props.post.title }}
                    </h1>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="tag in props.post.tags"
                            :key="tag"
                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800"
                        >
                            {{ tag }}
                        </span>
                    </div>
                    <div class="mt-6 rounded-2xl border border-amber-100 bg-amber-50/40 p-4">
                        <p class="text-sm font-semibold text-gray-900">Partager l'article</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a
                                :href="facebookShareUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                Facebook
                            </a>
                            <a
                                :href="twitterShareUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                X (Twitter)
                            </a>
                            <a
                                :href="linkedInShareUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                LinkedIn
                            </a>
                            <a
                                :href="whatsappShareUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                WhatsApp
                            </a>
                            <a
                                :href="instagramShareUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                Instagram
                            </a>
                            <a
                                :href="emailShareUrl"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                            >
                                Email
                            </a>
                            <button
                                type="button"
                                class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50"
                                @click="copyShareLink"
                            >
                                Copier le lien
                            </button>
                        </div>
                        <p v-if="copyMessage" class="mt-3 text-xs font-semibold text-amber-800">
                            {{ copyMessage }}
                        </p>
                    </div>
                    <div class="prose prose-amber mt-8 max-w-none text-gray-700" v-html="props.post.content"></div>
                </div>
            </article>
        </section>

        <footer class="border-t border-amber-100 bg-[#FDFDFC]">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="inline-flex items-center gap-1 flex-nowrap">
                                <AppLogo class="h-12 w-12 p-0 -mr-1 shrink-0" />
                            </div>
                        </div>
                        <p class="max-w-sm text-sm text-gray-600 leading-relaxed mb-6">
                            Sublimez l'expérience de vos clients avec des recommandations de bières personnalisées.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-900 mb-4">Produit</h3>
                        <ul class="space-y-3">
                            <li>
                                <Link href="/#fonctionnement" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Fonctionnement
                                </Link>
                            </li>
                            <li>
                                <Link href="/#avantages" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Avantages
                                </Link>
                            </li>
                            <li>
                                <Link href="/#tarifs" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Tarifs
                                </Link>
                            </li>
                            <li>
                                <Link href="/blog" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Blog
                                </Link>
                            </li>
                            <li>
                                <Link href="/b/demo" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Démo
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-900 mb-4">Légal</h3>
                        <ul class="space-y-3">
                            <li>
                                <Link href="/mentions-legales" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Mentions légales
                                </Link>
                            </li>
                            <li>
                                <Link href="/politique-de-confidentialite" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
                                    Confidentialité
                                </Link>
                            </li>
                            <li>
                                <Link href="/cgu" class="text-sm text-gray-600 transition-colors hover:text-amber-800">
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
                        <p class="text-xs text-gray-500">
                            Fait avec passion pour les amateurs de bières
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

