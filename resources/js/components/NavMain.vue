<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { AppPageProps } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';

defineProps<{
    groups: {
        label: string;
        items: {
            title: string;
            href: NonNullable<InertiaLinkProps['href']>;
            icon: any;
        }[];
    }[];
}>();

const page = usePage<AppPageProps>();
</script>

<template>
    <div>
        <SidebarGroup
            v-for="group in groups"
            :key="group.label"
            class="px-2 py-0"
        >
            <SidebarGroupLabel>{{ group.label }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in group.items" :key="item.title">
                    <SidebarMenuButton
                        as-child
                        :is-active="item.href === page.url"
                        :tooltip="item.title"
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroup>
    </div>
</template>
