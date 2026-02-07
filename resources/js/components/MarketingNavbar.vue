<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { login, register, dashboard } from '@/routes';
import AppLogo from '@/components/AppLogo.vue';

type Props = {
    anchorPrefix?: string;
    active?: 'home' | 'blog';
};

const props = withDefaults(defineProps<Props>(), {
    anchorPrefix: '',
    active: 'home',
});

const demoUrl = '/b/demo';
const isMobileMenuOpen = ref(false);
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const navLinkClass = (isActive: boolean) =>
    isActive
        ? 'text-sm font-medium text-amber-800'
        : 'text-sm font-medium text-gray-700 transition-colors hover:text-amber-800';

const mobileLinkClass = (isActive: boolean) =>
    isActive
        ? 'rounded-lg px-3 py-2 text-sm font-medium text-amber-800'
        : 'rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-amber-50 hover:text-amber-800';

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};
</script>

<template>
    <nav class="sticky top-0 z-50 border-b border-amber-100/50 bg-white/95 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <Link href="/" class="inline-flex items-center gap-1 flex-nowrap">
                    <AppLogo class="h-12 w-12 p-0 -mr-1 shrink-0" />
                </Link>
                <div class="hidden md:flex md:items-center md:gap-8">
                    <Link :href="`${anchorPrefix}#fonctionnement`" :class="navLinkClass(active === 'home')">
                        Fonctionnement
                    </Link>
                    <Link :href="`${anchorPrefix}#avantages`" :class="navLinkClass(active === 'home')">
                        Avantages
                    </Link>
                    <Link :href="`${anchorPrefix}#tarifs`" :class="navLinkClass(active === 'home')">
                        Tarifs
                    </Link>
                    <Link href="/blog" :class="navLinkClass(active === 'blog')">
                        Blog
                    </Link>
                    <Link :href="demoUrl" :class="navLinkClass(false)">
                        Démo
                    </Link>
                    <template v-if="isAuthenticated">
                        <Link
                            :href="dashboard()"
                            class="rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md hover:from-amber-600 hover:to-orange-700"
                        >
                            Tableau de bord
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" :class="navLinkClass(false)">
                            Connexion
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md hover:from-amber-600 hover:to-orange-700"
                        >
                            Commencer
                        </Link>
                    </template>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-amber-100 bg-white p-2 text-amber-900 shadow-sm transition hover:bg-amber-50 md:hidden"
                    aria-label="Menu"
                    aria-controls="mobile-menu"
                    :aria-expanded="isMobileMenuOpen"
                    @click="toggleMobileMenu"
                >
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            v-show="!isMobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-show="isMobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
            <div
                id="mobile-menu"
                class="md:hidden"
                :class="isMobileMenuOpen ? 'block' : 'hidden'"
            >
                <div class="flex flex-col gap-2 pb-6 pt-2">
                    <Link
                        :href="`${anchorPrefix}#fonctionnement`"
                        :class="mobileLinkClass(active === 'home')"
                        @click="closeMobileMenu"
                    >
                        Fonctionnement
                    </Link>
                    <Link
                        :href="`${anchorPrefix}#avantages`"
                        :class="mobileLinkClass(active === 'home')"
                        @click="closeMobileMenu"
                    >
                        Avantages
                    </Link>
                    <Link
                        :href="`${anchorPrefix}#tarifs`"
                        :class="mobileLinkClass(active === 'home')"
                        @click="closeMobileMenu"
                    >
                        Tarifs
                    </Link>
                    <Link
                        href="/blog"
                        :class="mobileLinkClass(active === 'blog')"
                        @click="closeMobileMenu"
                    >
                        Blog
                    </Link>
                    <Link
                        :href="demoUrl"
                        :class="mobileLinkClass(false)"
                        @click="closeMobileMenu"
                    >
                        Démo
                    </Link>
                    <template v-if="isAuthenticated">
                        <Link
                            :href="dashboard()"
                            class="mt-1 inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md hover:from-amber-600 hover:to-orange-700"
                            @click="closeMobileMenu"
                        >
                            Tableau de bord
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            :class="mobileLinkClass(false)"
                            @click="closeMobileMenu"
                        >
                            Connexion
                        </Link>
                        <Link
                            :href="register()"
                            class="mt-1 inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md hover:from-amber-600 hover:to-orange-700"
                            @click="closeMobileMenu"
                        >
                            Commencer
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>

