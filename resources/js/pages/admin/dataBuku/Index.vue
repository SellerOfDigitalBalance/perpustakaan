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
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
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
    all_category_names: string[];
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
            category?: string;
        }) || {}
    );
});
const categoryOptions = computed(() => {
    return props.all_category_names;
});
const searchQuery = ref(pageProps.value.search ?? '');
const searchBy = ref('');
const selectedSort = ref(pageProps.value.sortColumn ?? 'created_at');
const sortOrder = ref<'asc' | 'desc'>(pageProps.value.order ?? 'asc');
const categorySearch = ref(pageProps.value.category ?? '');
const updateDataBukus = () => {
    router.get(
        '/databukus',
        {
            search: searchQuery.value,
            sortColumn: selectedSort.value,
            order: sortOrder.value,
            column: searchBy.value,
            category: categorySearch.value,
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
    [searchQuery, categorySearch],
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
function resetFilters() {
    searchQuery.value = '';
    searchBy.value = '';
    categorySearch.value = '';
    // Langsung trigger update
    updateDataBukus();
}
const columns = [
    { key: 'no', label: 'No' },
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'penulis_buku', label: 'Penulis Buku', sortable: true },
    { key: 'penerbit_buku', label: 'Penerbit Buku', sortable: true },
    { key: 'tahun_terbit', label: 'Tahun Terbit', sortable: true },
    { key: 'categories_id', label: 'Kategori', sortable: true },
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
                        <div class="flex items-end gap-2">
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
                                <option value="tahun_terbit">
                                    Tahun Terbit
                                </option>
                                <option value="categories_id">Kategori</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <Popover>
                                <PopoverTrigger
                                    ><Button
                                        variant="outline"
                                        class="flex items-center gap-2 bg-blue-600 text-white hover:bg-blue-700"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-funnel"
                                        >
                                            <path
                                                d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z"
                                            />
                                        </svg> </Button
                                ></PopoverTrigger>
                                <PopoverContent
                                    ><div class="flex flex-col">
                                        <Label for="categorySearch" class="mb-2"
                                            >Kategori</Label
                                        >
                                        <select
                                            id="categorySearch"
                                            v-model="categorySearch"
                                            class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none"
                                        >
                                            <option value="">
                                                -- Semua Kategori --
                                            </option>
                                            <option
                                                v-for="option in categoryOptions"
                                                :key="option"
                                                :value="option"
                                            >
                                                {{ option }}
                                            </option>
                                        </select>
                                        <div class="mt-2 flex flex-col">
                                            <Button
                                                type="button"
                                                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700"
                                                @click="resetFilters"
                                            >
                                                Reset Filter
                                            </Button>
                                        </div>
                                    </div></PopoverContent
                                >
                            </Popover>
                            <Link
                                :href="create().url"
                                as="button"
                                class="w-full sm:w-auto"
                            >
                                <Button
                                    variant="outline"
                                    class="w-full sm:w-40"
                                >
                                    Tambah Buku
                                </Button>
                            </Link>
                        </div>
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
