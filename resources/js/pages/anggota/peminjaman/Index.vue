<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import Pagination from '@/components/tables/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { index, show, store } from '@/routes/peminjamanbukus';
import { Book, BreadcrumbItem, PaginatedResponse } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Peminjaman Buku',
        href: index().url,
    },
];
const props = defineProps<{
    peminjamanbukuResource: PaginatedResponse<Book>;
}>();
// console.log(props.peminjamanbukuResource);
const pagination = computed(() => ({
    previous: props.peminjamanbukuResource.prev_page_url,
    next: props.peminjamanbukuResource.next_page_url,
}));
const pageProps = computed(() => {
    return (
        (usePage().props.filters as {
            search?: string;
            sortColumn?: string;
            order?: 'asc' | 'desc';
        }) || {}
    );
});
const searchQuery = ref(pageProps.value.search ?? '');
const searchBy = ref('');
const selectedSort = ref(pageProps.value.sortColumn ?? 'created_at');
const sortOrder = ref<'asc' | 'desc'>(pageProps.value.order ?? 'asc');
const updatePeminjamanBukus = () => {
    router.get(
        '/peminjamanbukus',
        {
            search: searchQuery.value,
            sortColumn: selectedSort.value,
            order: sortOrder.value,
            column: searchBy.value,
        },
        { preserveState: true },
    );
};
function toggleSort(key: string) {
    if (selectedSort.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        selectedSort.value = key;
        sortOrder.value = 'asc';
    }
    updatePeminjamanBukus();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatePeminjamanBukus();
        }
    },
    { debounce: 500 },
);
const columns = [
    { key: 'no', label: 'No' },
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'penulis_buku', label: 'Penulis Buku', sortable: true },
    { key: 'penerbit_buku', label: 'Penerbit Buku', sortable: true },
    { key: 'tahun_terbit', label: 'Tahun Terbit', sortable: true },
    { key: 'categories_id', label: 'Kategori', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
const isOpen = ref<Record<number, boolean>>({});
const catatan = ref('');
const handleRequestPinjam = (data_bukus_id: number) => {
    router.post(
        store().url,
        {
            data_bukus_id: data_bukus_id,
            catatan: catatan.value ?? null, // jika ada form catatan
        },
        {
            onSuccess: () => {
                isOpen.value[data_bukus_id] = false;
                console.log('Permintaan pinjam berhasil dikirim ke admin.');
            },
        },
    );
};
</script>
<template>
    <Head title="Peminjaman Buku" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
            <Card class="border-transparent">
                <CardContent>
                    <div
                        class="flex flex-col items-stretch justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <div
                            class="flex flex-col items-stretch justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <Input
                                id="searchQuery"
                                class="w-full sm:w-64"
                                v-model="searchQuery"
                                placeholder="Cari..."
                            />
                            <select
                                id="perPage"
                                v-model="searchBy"
                                @change="updatePeminjamanBukus"
                                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                            >
                                <option value="">- Semua Kolom -</option>
                                <option value="judul_buku">Judul Buku</option>
                                <option value="penulis_buku">
                                    Penulis Buku
                                </option>
                                <option value="penerbit_buku">
                                    Penerbit Buku
                                </option>
                                <option value="tahun_terbit">
                                    Tahun Terbit
                                </option>
                            </select>
                        </div>
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="peminjamanbukuResource.data"
                        :links="peminjamanbukuResource.links"
                        :current_page="
                            props.peminjamanbukuResource.current_page
                        "
                        :per_page="props.peminjamanbukuResource.per_page"
                        :filters="{
                            search: searchQuery,
                            sortColumn: selectedSort,
                            sortOrder: sortOrder,
                        }"
                        @toggleSort="toggleSort"
                    >
                        <template #no="{ i, current_page, per_page }">
                            {{ (current_page - 1) * per_page + i + 1 }}
                        </template>
                        <template #categories_id="{ item }">
                            {{ item.category?.name || 'Tidak Ada' }}
                        </template>
                        <template #actions="{ item: peminjamanbukus }">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="show(peminjamanbukus.id)"
                                    as="button"
                                >
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Dialog
                                    v-model:open="isOpen[peminjamanbukus.id]"
                                    :key="peminjamanbukus.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            class="w-full sm:w-20"
                                        >
                                            Ajukan
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle
                                                >Konfirmasi Pengajuan Peminjaman
                                                Buku</DialogTitle
                                            >
                                            <h1 class="mt-2">
                                                Dengan Judul:
                                                <strong>{{
                                                    peminjamanbukus.judul_buku
                                                }}</strong>
                                            </h1>
                                            <DialogDescription>
                                                Silakan konfirmasi bahwa Anda
                                                ingin mengirim permintaan
                                                peminjaman buku ini. Pengajuan
                                                akan diteruskan ke admin untuk
                                                diproses.
                                            </DialogDescription>

                                            <Textarea
                                                id="catatan"
                                                class="mt-5 w-full"
                                                v-model="catatan"
                                                placeholder="Catatan..."
                                            />
                                        </DialogHeader>
                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleRequestPinjam(
                                                        peminjamanbukus.id,
                                                    )
                                                "
                                            >
                                                Konfirmasi
                                            </Button>
                                            <DialogClose as-child>
                                                <Button
                                                    type="button"
                                                    variant="secondary"
                                                    >Batal</Button
                                                >
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </template>
                    </DataTable>
                </CardContent>
            </Card>
        </div>
        <Pagination
            :previousPage="pagination.previous"
            :nextPage="pagination.next"
            :links="props.peminjamanbukuResource.links"
        />
    </AppLayout>
</template>
