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
import { destroy, index, show } from '@/routes/pengajuananggotas';
import {
    BreadcrumbItem,
    PaginatedResponse,
    PengajuanPeminjaman,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Eye, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengajuan Saya',
        href: index().url,
    },
];
const props = defineProps<{
    PengajuanAnngotaResource: PaginatedResponse<PengajuanPeminjaman>;
}>();
console.log(props.PengajuanAnngotaResource);
const pagination = computed(() => ({
    previous: props.PengajuanAnngotaResource.prev_page_url,
    next: props.PengajuanAnngotaResource.next_page_url,
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
const updatepengajuananggotas = () => {
    router.get(
        '/pengajuananggotas',
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
    updatepengajuananggotas();
}
watchDebounced(
    [searchQuery, statusSearch],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatepengajuananggotas();
        }
    },
    { debounce: 500 },
);
const resetFilters = () => {
    searchQuery.value = '';
    statusSearch.value = '';
    searchBy.value = '';
    updatepengajuananggotas();
};
const columns = [
    { key: 'no', label: 'No' },
    { key: 'kode_transaksi', label: 'Kode Transaksi', sortable: true },
    { key: 'data_bukus_id', label: 'Judul Buku', sortable: true },
    // { key: 'tanggal_peminjaman', label: 'Tanggal Peminjaman', sortable: true },
    { key: 'status', label: 'status', sortable: true },
    // { key: 'catatan', label: 'catatan', sortable: true },
    { key: 'actions', label: 'Aksi' },
];
const isOpen = ref<Record<number, boolean>>({});
const handleBatalkanPengajuan = (id: number) => {
    console.log('Button hapus ditekan, isOpen:', isOpen.value);

    router.delete(destroy.url(id), {
        onSuccess: () => {
            isOpen.value[id] = false;
            // Tutup dialog setelah sukses hapus
        },
    });
};
const isOpenHapus = ref<Record<number, boolean>>({});

const handleDelete = (id: number) => {
    console.log('Button hapus ditekan, isOpen:', isOpenHapus.value);

    router.delete(destroy.url(id), {
        onSuccess: () => {
            isOpen.value[id] = false;
            // Tutup dialog setelah sukses hapus
        },
    });
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
                                @change="updatepengajuananggotas"
                                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:ring-2 focus:ring-primary focus:outline-none sm:w-40"
                            >
                                <option value="">- Semua Kolom -</option>
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
                        :data="PengajuanAnngotaResource.data"
                        :links="PengajuanAnngotaResource.links"
                        :current_page="
                            props.PengajuanAnngotaResource.current_page
                        "
                        :per_page="props.PengajuanAnngotaResource.per_page"
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
                        <template #actions="{ item: pengajuananggotas }">
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
                                    :key="'batal-' + pengajuananggotas.id"
                                    v-if="
                                        pengajuananggotas.status === 'pending'
                                    "
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="secondary"
                                            size="icon"
                                            class="w-full sm:w-20"
                                        >
                                            Batalkan
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Konfirmasi Pembatalan Pengajuan
                                                Peminjaman Buku
                                            </DialogTitle>

                                            <h1 class="mt-2">
                                                Pengajuan oleh:<br />
                                                <strong>
                                                    {{
                                                        pengajuananggotas.users
                                                            ?.name
                                                    }}
                                                </strong>
                                            </h1>

                                            <h1 class="mt-2">
                                                Judul buku:<br />
                                                <strong>
                                                    {{
                                                        pengajuananggotas
                                                            .databukus
                                                            ?.judul_buku
                                                    }}
                                                </strong>
                                            </h1>

                                            <DialogDescription>
                                                Apakah Anda yakin ingin
                                                membatalkan pengajuan ini?
                                                Setelah dibatalkan, pengajuan
                                                tidak dapat dikembalikan lagi.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleBatalkanPengajuan(
                                                        pengajuananggotas.id,
                                                    )
                                                "
                                            >
                                                Batalkan Pengajuan
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
                                    v-else
                                    v-model:open="
                                        isOpenHapus[pengajuananggotas.id]
                                    "
                                    :key="pengajuananggotas.id"
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
                                                    handleDelete(
                                                        pengajuananggotas.id,
                                                    )
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
            :links="props.PengajuanAnngotaResource.links"
        />
    </AppLayout>
</template>
