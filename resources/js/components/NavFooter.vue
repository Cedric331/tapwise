<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

type Props = {
    items: NavItem[];
    class?: string;
};

const { isCurrentUrl } = useCurrentUrl();

defineProps<Props>();
</script>

<template>
    <SidebarGroup
        :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`"
    >
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        class="rounded-xl px-3 py-3 text-[15px] font-semibold text-amber-900 transition-all hover:bg-amber-50 data-[active=true]:bg-amber-100 data-[active=true]:text-amber-900 [&>svg]:text-amber-700"
                        as-child
                        size="lg"
                        :is-active="isCurrentUrl(item.href)"
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
