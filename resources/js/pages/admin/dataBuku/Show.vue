<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import ShowData from '@/components/ShowData.vue';
import { Card, CardContent } from '@/components/ui/card';
import { index } from '@/routes/databukus';
import { Book, BreadcrumbItem, PaginatedResponse } from '@/types';

const props = defineProps<{
    currentBook: PaginatedResponse<Book>;
}>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Data Buku',
        href: index().url,
    },
    {
        title: 'Detail Buku',
        href: '',
    },
];
const columns = [
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'penulis_buku', label: 'Penulis Buku', sortable: true },
    { key: 'penerbit_buku', label: 'Penerbit Buku', sortable: true },
    { key: 'tahun_terbit', label: 'Tahun Terbit', sortable: true },
    { key: 'categories_id', label: 'Kategori', sortable: true },
    { key: 'ISBN', label: 'ISBN', sortable: true },
    { key: 'jumlah_halaman', label: 'Jumlah Halaman', sortable: true },
    { key: 'deskripsi_singkat', label: 'Deskripsi Singkat', sortable: true },
];
</script>
<template>
    <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
        <div class="mb-10 ml-6">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
        <Card class="border-transparent">
            <CardContent>
                <ShowData :columns="columns" :data="props.currentBook.data">
                </ShowData>
            </CardContent>
        </Card>
    </div>
</template>
