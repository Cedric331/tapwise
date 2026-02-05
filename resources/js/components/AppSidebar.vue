<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, Folder, Beer, LayoutGrid, Store } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
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
const currentBar = computed(() => (page.props as any).currentBar as { id: number; name: string; slug: string } | undefined);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Tableau de bord',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (currentBar.value) {
        items.push(
            {
                title: 'Catalogue de bières',
                href: `/bars/${currentBar.value.slug}/beers`,
                icon: Beer,
            },
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [

];
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
