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
import { index, show, store } from '@/routes/daftarpeminjamans';
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
        title: 'Daftar Peminjaman',
        href: index().url,
    },
];
const props = defineProps<{
    daftarpeminjamanResource: PaginatedResponse<PengajuanPeminjaman>;
}>();
console.log(props.daftarpeminjamanResource);
const pagination = computed(() => ({
    previous: props.daftarpeminjamanResource.prev_page_url,
    next: props.daftarpeminjamanResource.next_page_url,
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
const updatedaftarpeminjaman = () => {
    router.get(
        '/daftarpeminjamans',
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
    updatedaftarpeminjaman();
}
watchDebounced(
    [searchQuery, statusSearch],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatedaftarpeminjaman();
        }
    },
    { debounce: 500 },
);
const resetFilters = () => {
    searchQuery.value = '';
    statusSearch.value = '';
    searchBy.value = '';
    updatedaftarpeminjaman();
};
const columns = [
    { key: 'no', label: 'No' },
    { key: 'judul_buku', label: 'Judul Buku', sortable: true },
    { key: 'penulis_buku', label: 'Penulis Buku', sortable: true },
    { key: 'penerbit_buku', label: 'Penerbit Buku', sortable: true },
    { key: 'jatuh_tempo', label: 'Jatuh Tempo', sortable: true },
    { key: 'action', label: 'Aksi', sortable: true },
];

const isOpen = ref<Record<number, boolean>>({});
const handleAjukanPerpanjangan = (
    data_bukus_id: number,
    pengajuananggotas: number,
) => {
    console.log(data_bukus_id);

    router.post(
        store().url,
        {
            data_bukus_id: data_bukus_id,
        },
        {
            onSuccess: () => {
                isOpen.value[pengajuananggotas] = false;
                console.log('Permintaan pinjam berhasil dikirim ke admin.');
            },
        },
    );
};
</script>
<template>
    <Head title="Pengajuan Saya" />
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
                                @change="updatedaftarpeminjaman"
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
                                            -- Semua Status --
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
                        :data="daftarpeminjamanResource.data"
                        :links="daftarpeminjamanResource.links"
                        :current_page="
                            props.daftarpeminjamanResource.current_page
                        "
                        :per_page="props.daftarpeminjamanResource.per_page"
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
                        <template #judul_buku="{ item }">
                            {{ item.databukus?.judul_buku || 'Tidak Ada' }}
                        </template>
                        <template #penulis_buku="{ item }">
                            {{ item.databukus?.penulis_buku || 'Tidak Ada' }}
                        </template>
                        <template #penerbit_buku="{ item }">
                            {{ item.databukus?.penerbit_buku || 'Tidak Ada' }}
                        </template>
                        <template #jatuh_tempo="{ item }">
                            {{
                                Math.ceil(
                                    (Number(
                                        new Date(item.tanggal_jatuh_tempo),
                                    ) -
                                        Number(
                                            new Date(item.tanggal_peminjaman),
                                        )) /
                                        86400000,
                                ) + ' hari'
                            }}
                        </template>
                        <template #categories_id="{ item }">
                            {{ item.databukus?.category?.name || 'Tidak Ada' }}
                        </template>
                        <template #action="{ item: pengajuananggotas }">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="show(pengajuananggotas.id)"
                                    as="button"
                                >
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Dialog
                                    v-model:open="isOpen[pengajuananggotas.id]"
                                    :key="'perpanjang-' + pengajuananggotas.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="secondary"
                                            size="icon"
                                            class="w-full sm:w-28"
                                        >
                                            Perpanjang
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Konfirmasi Pengajuan
                                                Perpanjangan
                                            </DialogTitle>

                                            <h1 class="mt-2">
                                                Judul Buku:<br />
                                                <strong>{{
                                                    pengajuananggotas.databukus
                                                        ?.judul_buku
                                                }}</strong>
                                            </h1>

                                            <h1 class="mt-2">
                                                Jatuh Tempo Saat Ini:<br />
                                                <strong>{{
                                                    pengajuananggotas.tanggal_jatuh_tempo
                                                }}</strong>
                                            </h1>

                                            <DialogDescription class="mt-4">
                                                Anda akan mengajukan
                                                perpanjangan jatuh tempo untuk
                                                buku ini. Admin akan menentukan
                                                tanggal jatuh tempo baru dan
                                                meninjau permintaan Anda.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="default"
                                                @click="
                                                    handleAjukanPerpanjangan(
                                                        pengajuananggotas.data_bukus_id,
                                                        pengajuananggotas.id,
                                                    )
                                                "
                                            >
                                                Ajukan Perpanjangan
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
            :links="props.daftarpeminjamanResource.links"
        />
    </AppLayout>
</template>
