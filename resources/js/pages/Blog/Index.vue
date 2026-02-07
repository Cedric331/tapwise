<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import MarketingNavbar from '@/components/MarketingNavbar.vue';

interface BlogPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image_url: string | null;
    published_at_iso: string | null;
    tags: string[];
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    posts: {
        data: BlogPost[];
        links: PaginationLink[];
    };
}

const props = defineProps<Props>();

const baseUrl = 'https://tapwise.fr';
const pageUrl = `${baseUrl}/blog`;
const ogImage = `${baseUrl}/assets/illustration-beer-glass.png`;

const formatDate = (value: string | null) => {
    if (!value) return null;
    const date = new Date(`${value}T00:00:00`);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
};
</script>

<template>
    <Head>
        <title>Blog Tapwise — Conseils et tendances pour bars</title>
        <meta
            name="description"
            content="Articles Tapwise sur l'expérience client, la carte des bières et les tendances pour bars et caves."
        />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Blog Tapwise — Conseils et tendances pour bars" />
        <meta property="og:description" content="Guides et retours d'expérience pour booster les ventes de votre bar." />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="pageUrl" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:alt" content="Blog Tapwise" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Blog Tapwise — Conseils et tendances pour bars" />
        <meta name="twitter:description" content="Guides et retours d'expérience pour booster les ventes de votre bar." />
        <meta name="twitter:image" :content="ogImage" />
        <link rel="canonical" :href="pageUrl" />
    </Head>

    <div class="min-h-screen bg-[#FDFDFC] text-gray-900">
        <MarketingNavbar active="blog" anchor-prefix="/" />

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <p class="text-sm font-medium text-amber-800">Le blog Tapwise</p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Inspirations pour bars et caves
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Conseils pratiques, tendances de la bière et retours d'expérience pour sublimer l'accueil client.
                </p>
            </div>

            <div v-if="props.posts.data.length === 0" class="rounded-2xl border border-amber-100 bg-white p-10 text-center">
                <p class="text-gray-600">Aucun article publié pour le moment.</p>
            </div>

            <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="post in props.posts.data"
                    :key="post.id"
                    class="group overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-[0_18px_40px_-28px_rgba(148,163,184,0.7)] transition-all hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-28px_rgba(148,163,184,0.8)]"
                >
                    <div class="aspect-[16/10] w-full overflow-hidden bg-amber-50">
                        <img
                            v-if="post.cover_image_url"
                            :src="post.cover_image_url"
                            :alt="post.title"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                            decoding="async"
                        />
                        <div v-else class="flex h-full items-center justify-center text-amber-700">
                            <span class="text-sm font-semibold">Tapwise</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-3 flex flex-wrap gap-2">
                            <span
                                v-for="tag in post.tags"
                                :key="tag"
                                class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800"
                            >
                                {{ tag }}
                            </span>
                        </div>
                        <p v-if="post.published_at_iso" class="text-xs font-semibold text-amber-700">
                            {{ formatDate(post.published_at_iso) }}
                        </p>
                        <h2 class="mt-2 text-xl font-bold text-gray-900">{{ post.title }}</h2>
                        <p class="mt-3 text-sm text-gray-600">{{ post.excerpt }}</p>
                        <Link
                            :href="`/blog/${post.slug}`"
                            class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-amber-800 transition-colors hover:text-amber-900"
                        >
                            Lire l'article
                            <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>
                </article>
            </div>

            <div v-if="props.posts.links.length > 1" class="mt-12 flex flex-wrap justify-center gap-2">
                <Link
                    v-for="(link, index) in props.posts.links"
                    :key="index"
                    :href="link.url ?? ''"
                    class="rounded-lg border px-3 py-2 text-sm font-medium transition"
                    :class="[
                        link.active
                            ? 'border-amber-600 bg-amber-600 text-white'
                            : link.url
                                ? 'border-amber-200 bg-white text-amber-800 hover:border-amber-300 hover:bg-amber-50'
                                : 'cursor-not-allowed border-gray-200 bg-gray-50 text-gray-400',
                    ]"
                    :aria-disabled="!link.url"
                    v-html="link.label"
                />
            </div>
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

