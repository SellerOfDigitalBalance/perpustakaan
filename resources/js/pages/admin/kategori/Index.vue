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
import FlashMessage from '@/components/ui/flash/FlashMessage.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index } from '@/routes/categories';
import { BreadcrumbItem, Category, PaginatedResponse } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kategori Buku',
        href: index().url,
    },
];

const columns = [
    { key: 'no', label: 'No' },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'actions', label: 'Aksi', slot: 'actions' },
];

const props = defineProps<{
    categoryResource: PaginatedResponse<Category>;
}>();
console.log('props categoryResource:', props.categoryResource);
const pagination = computed(() => ({
    previous: props.categoryResource.prev_page_url,
    next: props.categoryResource.next_page_url,
}));
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
const selectedSort = ref(pageProps.value.sortColumn ?? 'created_at');
const sortOrder = ref<'asc' | 'desc'>(pageProps.value.order ?? 'asc');
const updateCategory = () => {
    router.get(
        '/categories',
        {
            search: searchQuery.value,
            sortColumn: selectedSort.value,
            order: sortOrder.value,
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
    updateCategory();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updateCategory();
        }
    },
    { debounce: 500 },
);
</script>

<template>
    <Head title="Kategoru Buku" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
            <FlashMessage />
            <Card class="border-transparent">
                <CardContent>
                    <div
                        class="flex flex-col items-stretch justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <Input
                            id="searchQuery"
                            class="w-full sm:w-64"
                            v-model="searchQuery"
                            placeholder="Cari..."
                        />
                        <Link
                            :href="create().url"
                            as="button"
                            class="w-full sm:w-auto"
                        >
                            <Button variant="outline" class="w-full sm:w-40">
                                Tambah Kategori
                            </Button>
                        </Link>
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="props.categoryResource.data"
                        :links="props.categoryResource.links"
                        :current_page="props.categoryResource.current_page"
                        :per_page="props.categoryResource.per_page"
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
                        <template #actions="{ item: categories }">
                            <div class="flex items-center gap-2">
                                <!-- Edit -->
                                <Link :href="edit(categories.id)" as="button">
                                    <Button variant="outline" size="icon">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                </Link>

                                <!-- Hapus -->
                                <Dialog
                                    v-model:open="isOpen[categories.id]"
                                    :key="categories.id"
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
                                                    handleDelete(categories.id)
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
            :links="props.categoryResource.links"
        />
    </AppLayout>
</template>
