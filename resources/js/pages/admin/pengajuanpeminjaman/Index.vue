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
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show, store } from '@/routes/pengajuanpeminjamans';
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
        title: 'Pengajuan Peminjaman',
        href: index().url,
    },
];
const props = defineProps<{
    PengajuanPeminjamanResource: PaginatedResponse<PengajuanPeminjaman>;
}>();
console.log(props.PengajuanPeminjamanResource);
const pagination = computed(() => ({
    previous: props.PengajuanPeminjamanResource.prev_page_url,
    next: props.PengajuanPeminjamanResource.next_page_url,
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
const updatepengajuanpeminjamans = () => {
    router.get(
        '/pengajuanpeminjamans',
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
    updatepengajuanpeminjamans();
}
watchDebounced(
    [searchQuery],
    (newQuery, oldQuery) => {
        if (newQuery !== oldQuery) {
            updatepengajuanpeminjamans();
        }
    },
    { debounce: 500 },
);
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
const isOpen = ref<Record<number, boolean>>({});
const handleTerimaPengajuan = (id: number) => {
    router.post(
        store().url,
        {
            id: id,
            status: 'dipinjam',
        },
        {
            onSuccess: () => {
                isOpen.value[id] = false;
                console.log('Status berhasil diperbarui menjadi dipinjam.');
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
                    <DataTable
                        :columns="columns"
                        :data="PengajuanPeminjamanResource.data"
                        :links="PengajuanPeminjamanResource.links"
                        :current_page="
                            props.PengajuanPeminjamanResource.current_page
                        "
                        :per_page="props.PengajuanPeminjamanResource.per_page"
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
                        <template #actions="{ item: pengajuanpeminjamans }">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="show(pengajuanpeminjamans.id)"
                                    as="button"
                                >
                                    <Button variant="outline" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Dialog
                                    v-model:open="
                                        isOpen[pengajuanpeminjamans.id]
                                    "
                                    :key="pengajuanpeminjamans.id"
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            class="w-full sm:w-20"
                                        >
                                            Terima
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent class="sm:max-w-xl">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Konfirmasi Penerimaan Pengajuan
                                                Peminjaman Buku
                                            </DialogTitle>

                                            <h1 class="mt-2">
                                                Pengajuan oleh:<br />
                                                <strong>{{
                                                    pengajuanpeminjamans.users
                                                        ?.name
                                                }}</strong>
                                            </h1>

                                            <h1 class="mt-2">
                                                Judul buku:<br />
                                                <strong>
                                                    {{
                                                        pengajuanpeminjamans
                                                            .databukus
                                                            ?.judul_buku
                                                    }}
                                                </strong>
                                            </h1>

                                            <DialogDescription>
                                                Apakah Anda yakin ingin menerima
                                                pengajuan peminjaman buku ini?
                                                Setelah dikonfirmasi, proses
                                                akan dilanjutkan ke tahap
                                                berikutnya.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <DialogFooter class="gap-2">
                                            <Button
                                                variant="destructive"
                                                @click="
                                                    handleTerimaPengajuan(
                                                        pengajuanpeminjamans.id,
                                                    )
                                                "
                                            >
                                                Terima Pengajuan
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
            :links="props.PengajuanPeminjamanResource.links"
        />
    </AppLayout>
</template>
