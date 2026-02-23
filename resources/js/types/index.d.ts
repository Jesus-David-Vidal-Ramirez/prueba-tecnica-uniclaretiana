import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import type { PageProps } from '@inertiajs/core';
// import { IAlertCrud } from './alert';

export interface Auth {
    user: User;
    rol;
    permissions;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface SharedData extends PageProps {
    name: string;
    auth: Auth;
    // quote: { message: string; author: string };
    // ziggy: Config & { location: string };
    sidebarOpen: boolean;
    alert?: AlertCrud,
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isOpen?: boolean;
    items?: NavItem[]
}

export interface RawNavItem {
    title: string
    href: string
    icon: string
    permission: string
    items?: RawNavItem[]
}

export interface FooterNavItem {
    title: string;
    href: string;
    icon: LucideIcon;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Users extends SharedData{
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}


export interface IUser {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    is_active: boolean;
    is_active_text?: string;
    roles: IRoles[]
}

interface IRoles {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
    isGiven?: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
