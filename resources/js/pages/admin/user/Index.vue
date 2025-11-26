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
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, resetPassword } from '@/routes/users';
import { BreadcrumbItem, PaginatedResponse, User } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Pencil, RotateCcw, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengguna',
        href: index().url,
    },
];
const props = defineProps<{
    userResource: PaginatedResponse<User>;
}>();
const pagination = computed(() => ({
    previous: props.userResource.prev_page_url,
    next: props.userResource.next_page_url,
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
const updateUsers = () => {
    router.get(
        '/users',
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
    updateUsers();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updateUsers();
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
    { key: 'nik', label: 'Nik', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'no_hp', label: 'Telepon', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
</script>

<template>
    <Head title="Daftar Pengguna" />
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
                                @change="updateUsers"
                                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                            >
                                <option value="">- Semua Kolom -</option>
                                <option value="nik">Nik</option>
                                <option value="name">Nama</option>
                                <option value="email">Email</option>
                                <option value="no_hp">Telepon</option>
                            </select>
                        </div>
                        <Link
                            :href="create().url"
                            as="button"
                            class="w-full sm:w-auto"
                        >
                            <Button variant="outline" class="w-full sm:w-40">
                                Tambah Pengguna
                            </Button>
                        </Link>
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="userResource.data"
                        :links="userResource.links"
                        :current_page="props.userResource.current_page"
                        :per_page="props.userResource.per_page"
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
                        <template #actions="{ item: user }">
                            <div class="flex items-center gap-2">
                                <!-- Edit -->
                                <Link :href="edit(user.id)" as="button">
                                    <Button variant="outline" size="icon">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                </Link>

                                <!-- Reset Password -->
                                <Link
                                    :href="`${resetPassword(user.id).url}`"
                                    as="button"
                                    method="put"
                                >
                                    <Button variant="outline" size="icon">
                                        <RotateCcw class="h-4 w-4" />
                                    </Button>
                                </Link>

                                <!-- Hapus -->
                                <Dialog
                                    v-model:open="isOpen[user.id]"
                                    :key="user.id"
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
                                                @click="handleDelete(user.id)"
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
            :links="props.userResource.links"
        />
    </AppLayout>
</template>
