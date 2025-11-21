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
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { create, destroy, edit, index, show } from '@/routes/databukus';
import { Book, BreadcrumbItem, PaginatedResponse } from '@/types/index';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Data Buku',
        href: index().url,
    },
];

const props = defineProps<{
    dataBukuResource: PaginatedResponse<Book>;
}>();

const pagination = computed(() => ({
    previous: props.dataBukuResource.prev_page_url,
    next: props.dataBukuResource.next_page_url,
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
const updateDataBukus = () => {
    router.get(
        '/admin/databukus',
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
    updateDataBukus();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updateDataBukus();
        }
    },
    { debounce: 500 },
);
const isOpen = ref<Record<number, boolean>>({});

const handleDelete = (id: number) => {
    console.log('Button hapus ditekan, isOpen:', isOpen.value);

    router.delete(destroy.url(id), {
        onSuccess: () => {
            isOpen.value[id] = false;
            // Tutup dialog setelah sukses hapus
        },
    });
};

const columns = [
    { key: 'no', label: 'No' },
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'penulis_buku', label: 'Penulis Buku', sortable: true },
    { key: 'penerbit_buku', label: 'Penerbit Buku', sortable: true },
    { key: 'tahun_terbit', label: 'Tahun Terbit', sortable: true },
    { key: 'categories_id', label: 'Kategori', sortable: true },
    // { key: 'ISBN', label: 'ISBN', sortable: true },
    // { key: 'jumlah_halaman', label: 'Jumlah Halaman', sortable: true },
    // { key: 'deskripsi_singkat', label: 'Deskripsi Singkat', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
</script>
<template>
    <Head title="Data Buku" />
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
                                @change="updateDataBukus"
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
                            </select>
                        </div>
                        <Link
                            :href="create().url"
                            as="button"
                            class="w-full sm:w-auto"
                        >
                            <Button variant="outline" class="w-full sm:w-40">
                                Tambah Buku
                            </Button>
                        </Link>
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="dataBukuResource.data"
                        :links="dataBukuResource.links"
                        :current_page="props.dataBukuResource.current_page"
                        :per_page="props.dataBukuResource.per_page"
                        :filters="{
                            search: searchQuery,
                            sortColumn: selectedSort,
                            sortOrder: sortOrder,
                        }"
                        @toggleSort="toggleSort"
                    >
                        <template #categories_id="{ item }">
                            {{ item.category?.name || 'Tidak Ada' }}
                        </template>
                        <template #no="{ i, current_page, per_page }">
                            {{ (current_page - 1) * per_page + i + 1 }}
                        </template>
                        <template #actions="{ item: databukus }">
                            <div class="flex items-center gap-2">
                                <Link :href="show(databukus.id)" as="button">
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>

                                <Link :href="edit(databukus.id)" as="button">
                                    <Button variant="outline" size="icon">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                </Link>

                                <Dialog
                                    v-model:open="isOpen[databukus.id]"
                                    :key="databukus.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-md">
                                        <DialogHeader>
                                            <DialogTitle
                                                >Konfirmasi Hapus</DialogTitle
                                            >
                                            <DialogDescription>
                                                Apakah Anda yakin ingin
                                                menghapus pengguna ini?
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleDelete(databukus.id)
                                                "
                                            >
                                                Hapus
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
            :links="props.dataBukuResource.links"
        />
    </AppLayout>
</template>
