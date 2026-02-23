<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, UserRoundPen, UserRoundCog, UsersRound, School, GraduationCap } from 'lucide-vue-next';
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
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import { dashboard } from '@/routes';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: '/user',
        icon: UsersRound,
    },
    {
        title: 'Roles',
        href: '/rol',
        icon: UserRoundCog,
    },
    {
        title: 'Permisos',
        href: '/permission',
        icon: UserRoundPen,
    },
    {
        title: 'Alumnos',
        href: '/alumno',
        icon: School,
    },
    {
        title: 'Profesores',
        href: '/profesor',
        icon: GraduationCap,
    },
];

const page = usePage();

const roleNames = computed<string[]>(() => {
    const roles = page.props.auth?.role;

    if (!Array.isArray(roles)) {
        return [];
    }

    return roles.map((role) => String(role).toLowerCase());
});

const filteredNavItems = computed<NavItem[]>(() => {
    if (roleNames.value.some((role) => role === 'admin' || role === 'administrador')) {
        return mainNavItems;
    }

    if (roleNames.value.includes('alumno')) {
        return mainNavItems.filter((item) => item.title === 'Dashboard' || item.title === 'Alumnos');
    }

    if (roleNames.value.includes('profesor')) {
        return mainNavItems.filter((item) => item.title === 'Dashboard' || item.title === 'Profesores');
    }

    return [mainNavItems[0]];
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
