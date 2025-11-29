<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import Pagination from '@/components/tables/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/statuspeminjamans';
import {
    BreadcrumbItem,
    PaginatedResponse,
    PengajuanPeminjaman,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Status Peminjaman',
        href: index().url,
    },
];
const props = defineProps<{
    StatusPeminjamanResource: PaginatedResponse<PengajuanPeminjaman>;
}>();
console.log(props.StatusPeminjamanResource);
const pagination = computed(() => ({
    previous: props.StatusPeminjamanResource.prev_page_url,
    next: props.StatusPeminjamanResource.next_page_url,
}));
const pageProps = computed(() => {
    return (
        (usePage().props.filters as {
            search?: string;
            sortColumn?: string;
            order?: 'asc' | 'desc';
            status?: string;
        }) || {}
    );
});
const searchQuery = ref(pageProps.value.search ?? '');
const searchBy = ref('');
const selectedSort = ref(pageProps.value.sortColumn ?? 'created_at');
const sortOrder = ref<'asc' | 'desc'>(pageProps.value.order ?? 'asc');
const statusSearch = ref(pageProps.value.status ?? '');
const updatestatuspeminjamans = () => {
    router.get(
        '/statuspeminjamans',
        {
            search: searchQuery.value,
            sortColumn: selectedSort.value,
            order: sortOrder.value,
            column: searchBy.value,
            status: statusSearch.value,
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
    updatestatuspeminjamans();
}
watchDebounced(
    [searchQuery, statusSearch],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatestatuspeminjamans();
        }
    },
    { debounce: 500 },
);
const resetFilters = () => {
    searchQuery.value = '';
    searchBy.value = '';
    statusSearch.value = '';

    updatestatuspeminjamans();
};
const columns = [
    { key: 'no', label: 'No' },
    { key: 'users_id', label: 'Nama Anggota', sortable: true },
    { key: 'kode_transaksi', label: 'Kode Transaksi', sortable: true },
    { key: 'data_bukus_id', label: 'judul Buku', sortable: true },
    // { key: 'tanggal_peminjaman', label: 'Tanggal Peminjaman', sortable: true },
    { key: 'status', label: 'status', sortable: true },
    // { key: 'catatan', label: 'catatan', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
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
                                @change="updatestatuspeminjamans"
                                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                            >
                                <option value="">- Semua Kolom -</option>
                                <option value="users_id">Nama Anggota</option>
                                <option value="kode_transaksi">
                                    Kode Transaksi
                                </option>
                                <option value="data_bukus_id">
                                    Judul Buku
                                </option>
                            </select>
                        </div>
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
                                    <Label for="statusSearch" class="mb-2"
                                        >Status</Label
                                    >
                                    <select
                                        id="statusSearch"
                                        v-model="statusSearch"
                                        class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none"
                                    >
                                        <option value="">
                                            -- Semua category --
                                        </option>
                                        <option value="dipinjam">
                                            Dipinjam
                                        </option>
                                        <option value="terlambat">
                                            Terlambat
                                        </option>
                                        <option value="hilang">Hilang</option>
                                        <option value="dikembalikan">
                                            Dikembalikan
                                        </option>
                                        <option value="pending">Pending</option>
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
                    </div>
                    <DataTable
                        :columns="columns"
                        :data="StatusPeminjamanResource.data"
                        :links="StatusPeminjamanResource.links"
                        :current_page="
                            props.StatusPeminjamanResource.current_page
                        "
                        :per_page="props.StatusPeminjamanResource.per_page"
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
                            {{ item.users?.name || 'Tidak Ada' }}
                        </template>
                        <template #data_bukus_id="{ item }">
                            {{ item.databukus?.judul_buku || 'Tidak Ada' }}
                        </template>
                        <template #actions="{ item: statuspeminjamans }">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="show(statuspeminjamans.id)"
                                    as="button"
                                >
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                            </div>
                        </template>
                    </DataTable>
                </CardContent>
            </Card>
        </div>
        <Pagination
            :previousPage="pagination.previous"
            :nextPage="pagination.next"
            :links="props.StatusPeminjamanResource.links"
        />
    </AppLayout>
</template>
