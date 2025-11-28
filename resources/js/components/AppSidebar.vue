<script setup lang="ts">
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
import { index as IndexCategories } from '@/routes/categories';
import { index as IndexBook } from '@/routes/databukus';
import { index as IndexPeminjaman } from '@/routes/peminjamanbukus';
import { index as IndexPengajuanPeminjaman } from '@/routes/pengajuanpeminjamans';
import { index as IndexStatusPeminjamans } from '@/routes/statuspeminjamans/index';
import { index as IndexUser } from '@/routes/users';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folders, LayoutGrid, User } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

// 🟢 Ambil user dari Inertia props
const user = page.props.auth?.user;

interface NavGroup {
    label: string;
    items: {
        title: string;
        href: NonNullable<InertiaLinkProps['href']>;
        icon: any;
    }[];
}

const adminNavItems: NavGroup[] = [
    {
        label: 'Umum',
        items: [{ title: 'Beranda', href: dashboard(), icon: LayoutGrid }],
    },
    {
        label: 'Data Master',
        items: [
            { title: 'Pengguna', href: IndexUser(), icon: User },
            {
                title: 'Kategori Buku',
                href: IndexCategories(),
                icon: Folders,
            },
            { title: 'Data Buku', href: IndexBook(), icon: BookOpen },
        ],
    },
    {
        label: 'Transaksi',
        items: [
            {
                title: 'Pengajuan Peminjaman',
                href: IndexPengajuanPeminjaman(),
                icon: BookOpen,
            },
            {
                title: 'Status Peminjaman Buku',
                href: IndexStatusPeminjamans(),
                icon: BookOpen,
            },
        ],
    },
];

const anggotaNavItems: NavGroup[] = [
    {
        label: 'Umum',
        items: [{ title: 'Beranda', href: dashboard(), icon: LayoutGrid }],
    },
    {
        label: 'Transaksi',
        items: [
            {
                title: 'Peminjaman Buku',
                href: IndexPeminjaman(),
                icon: BookOpen,
            },
            // {
            //     title: 'Daftar Pengajuan Peminjaman',
            //     href: IndexPeminjaman(),
            //     icon: BookOpen,
            // },
            // {
            //     title: 'Riwayat Peminjaman',
            //     href: IndexPeminjaman(),
            //     icon: BookOpen,
            // },
        ],
    },
];

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];

const mainNavItems = computed(() => {
    if (user.level === 'admin') {
        return adminNavItems;
    } else if (user.level === 'anggota') {
        return anggotaNavItems;
        // } else if (user.level === 'guru') {
        //     return guruNavItems;
        // } else if (user.level === 'karyawan') {
        //     return karyawanNavItems;
    } else {
        return [];
    }
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
            <NavMain :groups="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
