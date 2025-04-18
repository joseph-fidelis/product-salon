<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, FileText, Briefcase,  Calendar, Send } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Services',
        href: '/admin/services',
        icon: Briefcase,
    },
    {
        title: 'Appointments',
        href: '/admin/appointments',
        icon: Calendar,
    },
    {
        title: 'Staff',
        href: '/admin/staff',
        icon: Users,
    },
    {
        title: 'Invoice',
        href: '/admin/invoice',
        icon: FileText,
    },

];

const footerNavItems: NavItem[] = [
    {
        title: 'Email Developer Support',
        href: 'mailto:sofrosyninc@gmail.com?subject=Service%20Inquiry%20Salon%20Solution&body=Hello%2C%0AI%20would%20like%20to%20learn%20more%20about%20your%20services.',
        icon: Send,
    }
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
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
