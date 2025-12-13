<script Setup lang="ts"></script>
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
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/routes/pengembalians';
import {
    BreadcrumbItem,
    PaginatedResponse,
    PengajuanPeminjaman,
} from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengembalian Buku',
        href: index().url,
    },
];
const props = defineProps<{
    pengembalianResource: PaginatedResponse<PengajuanPeminjaman>;
}>();
console.log(props.pengembalianResource);
const pagination = computed(() => ({
    previous: props.pengembalianResource.prev_page_url,
    next: props.pengembalianResource.next_page_url,
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
const updatePengembalians = () => {
    router.get(
        '/pengembalians',
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
    updatePengembalians();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatePengembalians();
        }
    },
    { debounce: 500 },
);
const isOpen = ref<Record<number, boolean>>({});
const isOpenBatal = ref<Record<number, boolean>>({});
const now = () => new Date().toISOString(); // Returns current date and time in ISO format
const handleUpdateStatus = (id: number, type: 'hilang' | 'dikembalikan') => {
    // Determine the status based on the type
    const status = type === 'hilang' ? 'hilang' : 'dikembalikan';

    // Send the request to update the status
    router.post(
        store().url, // Assuming this provides the endpoint URL
        {
            id,
            status, // Status being updated
            tanggal_pengembalian: now(), // Current timestamp
        },
        {
            onSuccess: () => {
                // Close the modal or perform any other necessary actions based on the type
                if (type === 'hilang') {
                    isOpen.value[id] = false;
                } else {
                    isOpenBatal.value[id] = false;
                }
            },
        },
    );
};

function resetFilters() {
    searchQuery.value = '';
    searchBy.value = '';

    // Langsung trigger update
    updatePengembalians();
}
const columns = [
    { key: 'no', label: 'No' },
    { key: 'kode_transaksi', label: 'KodeTransaksi', sortable: true },
    { key: 'users_id', label: 'Nama Anggota', sortable: true },
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
</script>

<template>
    <Head title="Pengembalian Buku" />
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
                                @change="updatePengembalians"
                                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                            >
                                <option value="">- Semua Kolom -</option>
                                <option value="kode_transaksi">
                                    Kode Transaksi
                                </option>
                                <option value="users_id">Nama Anggota</option>
                                <option value="judul_buku">Judul Buku</option>
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
                                        <div class="mt-2 flex flex-col">
                                            <Button
                                                type="button"
                                                class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700"
                                                @click="resetFilters"
                                            >
                                                Reset Filter
                                            </Button>
                                        </div>
                                    </div>
                                </PopoverContent>
                            </Popover>
                        </div>
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="pengembalianResource.data"
                        :links="pengembalianResource.links"
                        :current_page="props.pengembalianResource.current_page"
                        :per_page="props.pengembalianResource.per_page"
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
                        <template #users_id="{ item }">
                            {{ item.users?.name || 'tidak ada' }}
                        </template>
                        <template #judul_buku="{ item }">
                            {{ item.databukus?.judul_buku || 'tidak ada' }}
                        </template>
                        <template #actions="{ item: user }">
                            <div class="flex items-center gap-2">
                                <Dialog
                                    v-model:open="isOpen[user.id]"
                                    :key="'hilang-' + user.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            class="w-full sm:w-20"
                                        >
                                            Hilang
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Konfirmasi Buku Hilang
                                            </DialogTitle>

                                            <h1 class="mt-2">
                                                Dilaporkan oleh:<br />
                                                <strong>{{
                                                    user.users?.name
                                                }}</strong>
                                            </h1>

                                            <h1 class="mt-2">
                                                Judul buku:<br />
                                                <strong>{{
                                                    user.databukus?.judul_buku
                                                }}</strong>
                                            </h1>

                                            <DialogDescription>
                                                Apakah Anda yakin ingin menandai
                                                buku ini sebagai
                                                <strong>hilang</strong>? Setelah
                                                dikonfirmasi, status tidak dapat
                                                diubah dan mungkin memerlukan
                                                tindakan lanjutan.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleUpdateStatus(
                                                        user.id,
                                                        'hilang',
                                                    )
                                                "
                                            >
                                                Konfirmasi Hilang
                                            </Button>

                                            <DialogClose as-child>
                                                <Button
                                                    type="button"
                                                    variant="secondary"
                                                >
                                                    Batal
                                                </Button>
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                                <Dialog
                                    v-model:open="isOpenBatal[user.id]"
                                    :key="'dikembalikan-' + user.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="secondary"
                                            size="icon"
                                            class="w-full sm:w-30"
                                        >
                                            Dikembalikan
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Konfirmasi Pengembalian Buku
                                            </DialogTitle>

                                            <h1 class="mt-2">
                                                Dikembalikan oleh:<br />
                                                <strong>{{
                                                    user.users?.name
                                                }}</strong>
                                            </h1>

                                            <h1 class="mt-2">
                                                Judul buku:<br />
                                                <strong>{{
                                                    user.databukus?.judul_buku
                                                }}</strong>
                                            </h1>

                                            <DialogDescription>
                                                Apakah Anda yakin ingin menandai
                                                buku ini sebagai
                                                <strong>dikembalikan</strong>?
                                                Setelah dikonfirmasi, data
                                                peminjaman akan diperbarui.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleUpdateStatus(
                                                        user.id,
                                                        'dikembalikan',
                                                    )
                                                "
                                            >
                                                Konfirmasi Pengembalian
                                            </Button>

                                            <DialogClose as-child>
                                                <Button
                                                    type="button"
                                                    variant="secondary"
                                                >
                                                    Batal
                                                </Button>
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
            :links="props.pengembalianResource.links"
        />
    </AppLayout>
</template>
