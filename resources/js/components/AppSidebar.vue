<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, Folder, Beer, Wine, LayoutGrid, Store, ChevronDown, CreditCard, AlertCircle, TrendingUp, Settings, QrCode } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import AppLogoIcon from './AppLogoIcon.vue';

const page = usePage();
const currentBar = computed(
    () =>
        (page.props as any).currentBar as
            | { id: number; name: string; slug: string; offers_beer?: boolean; offers_wine?: boolean }
            | undefined
);
const currentBarSubscription = computed(
    () =>
        (page.props as any).currentBarSubscription as
            | { status: 'active' | 'trial' | 'inactive'; trialDaysLeft: number | null }
            | undefined
);
const bars = computed(() => ((page.props as any).bars ?? []) as Array<{ id: number; name: string; slug: string }>);

const barLabel = computed(() => currentBar.value?.name ?? 'Sélectionner un bar');

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Tableau de bord',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (currentBar.value) {
        if (currentBar.value.offers_beer) {
            items.push({
                title: 'Catalogue de bières',
                href: `/bars/${currentBar.value.slug}/beers`,
                icon: Beer,
            });
        }
        if (currentBar.value.offers_wine) {
            items.push({
                title: 'Catalogue de vins',
                href: `/bars/${currentBar.value.slug}/wines`,
                icon: Wine,
            });
        }

        //qr code
        items.push({
            title: 'QR Code',
            href: `/bars/${currentBar.value.slug}/qr-code`,
            icon: QrCode,
        });

        items.push({
            title: 'Statistiques',
            href: `/bars/${currentBar.value.slug}/stats`,
            icon: TrendingUp,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Gérer mon bar',
        href: currentBar.value ? `/bars/${currentBar.value.slug}/settings` : '/bars',
        icon: Settings,
    },
    {
        title: 'Gerer mon abonnement',
        href: currentBar.value ? `/bars/${currentBar.value.slug}/subscription/portal` : '/bars',
        icon: CreditCard,
    },
];

const startSubscription = () => {
    if (!currentBar.value) {
        return;
    }

    router.post(`/bars/${currentBar.value.slug}/subscription/checkout`, {}, { preserveScroll: true });
};
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        :style="{
            '--sidebar-background': 'hsl(0 0% 100%)',
            '--sidebar-foreground': 'hsl(30 15% 25%)',
            '--sidebar-accent': 'hsl(38 90% 96%)',
            '--sidebar-accent-foreground': 'hsl(30 35% 25%)',
            '--sidebar-border': 'hsl(38 40% 90%)',
        }"
    >
        <SidebarHeader class="border-b border-amber-100/70 bg-white/95">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="h-auto py-2">
                        <Link :href="dashboard()">
                            <div class="flex items-center gap-3 px-2 py-1">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-700 shadow-sm">
                                    <AppLogoIcon class="h-12 w-12" />
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <span class="text-lg font-bold text-gray-900">Tapwise</span>
                                    <span class="text-xs text-gray-500">L'art du conseil</span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <div class="px-3 py-4">
                <div class="px-2 text-xs font-semibold uppercase tracking-widest text-amber-800/80">
                    Bars
                </div>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            type="button"
                            class="mt-3 flex w-full items-center justify-between rounded-xl border border-amber-100 bg-white px-3 py-3 text-left text-[15px] font-semibold text-amber-900 shadow-sm transition-all hover:bg-amber-50"
                        >
                            <span class="flex items-center gap-2">
                                <Store class="h-4 w-4 text-amber-700" />
                                <span class="truncate">{{ barLabel }}</span>
                            </span>
                            <ChevronDown class="h-4 w-4 text-amber-700" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-64" align="start">
                        <DropdownMenuLabel>Choisir un bar</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            v-for="bar in bars"
                            :key="bar.id"
                            as-child
                        >
                            <Link :href="`/bars/${bar.slug}`" class="flex w-full items-center gap-2">
                                <Store class="h-4 w-4" />
                                <span class="truncate">{{ bar.name }}</span>
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link href="/bars" class="flex w-full items-center gap-2">
                                <Store class="h-4 w-4" />
                                Gérer mes bars
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
  
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <div
                v-if="currentBarSubscription && currentBarSubscription.status !== 'active'"
                class="mx-3 mb-3 rounded-xl border border-amber-100 bg-amber-50/70 px-3 py-3 text-xs font-medium text-amber-900"
            >
                <div class="flex-row items-start justify-between gap-2">
                    <div class="flex items-start gap-2">
                        <div>
                            <p v-if="currentBarSubscription.status === 'trial'" class="flex flex-wrap items-center gap-1.5">
                                <span>Essai en cours</span>
                                <span
                                    v-if="currentBarSubscription.trialDaysLeft !== null"
                                    class="inline-flex h-5 items-center justify-center rounded-full px-0 text-[12px] font-semibold text-amber-900"
                                >
                                    {{ Math.ceil(currentBarSubscription.trialDaysLeft) }}
                                </span>
                                <span v-if="currentBarSubscription.trialDaysLeft !== null">j restants</span>
                            </p>
                            <p v-else>Abonnement inactif</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex w-full mt-1 justify-center items-center cursor-pointer rounded-lg bg-white px-2.5 py-1 text-[11px] font-semibold text-amber-800 shadow-sm transition-colors hover:bg-amber-100"
                        @click="startSubscription"
                    >
                        Activer
                    </button>
                </div>
            </div>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
