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
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    create,
    destroy,
    edit,
    index,
    resetPassword,
    show,
} from '@/routes/users';
import { BreadcrumbItem, PaginatedResponse, User } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye, Pencil, RotateCcw, Trash2 } from 'lucide-vue-next';
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
            level?: string;
        }) || {}
    );
});
const searchQuery = ref(pageProps.value.search ?? '');
const searchBy = ref('');
const selectedSort = ref(pageProps.value.sortColumn ?? 'created_at');
const sortOrder = ref<'asc' | 'desc'>(pageProps.value.order ?? 'asc');
const levelSearch = ref(pageProps.value.level ?? '');
const updateUsers = () => {
    router.get(
        '/users',
        {
            search: searchQuery.value,
            sortColumn: selectedSort.value,
            order: sortOrder.value,
            column: searchBy.value,
            level: levelSearch.value,
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
    [searchQuery, levelSearch],
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
function resetFilters() {
    searchQuery.value = '';
    searchBy.value = '';
    levelSearch.value = '';

    // Langsung trigger update
    updateUsers();
}
const columns = [
    { key: 'no', label: 'No' },
    { key: 'nik', label: 'Nik', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'level', label: 'Level', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
</script>

<template>
    <Head title="Daftar Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
            <FlashMessage />
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
                                        <Label for="levelSearch" class="mb-2"
                                            >Level Pengguna</Label
                                        >
                                        <select
                                            id="levelSearch"
                                            v-model="levelSearch"
                                            class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none"
                                        >
                                            <option value="">
                                                -- Semua Level --
                                            </option>
                                            <option value="admin">Admin</option>
                                            <option value="petugas">
                                                Petugas
                                            </option>
                                            <option value="anggota">
                                                Anggota
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
                                    Tambah Pengguna
                                </Button>
                            </Link>
                        </div>
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
                                <Link :href="show(user.id)" as="button">
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>

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
