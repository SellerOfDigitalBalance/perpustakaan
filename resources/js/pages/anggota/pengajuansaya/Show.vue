<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import ShowData from '@/components/ShowData.vue';
import { Card, CardContent } from '@/components/ui/card';
import { index, show } from '@/routes/pengajuananggotas';
import { BreadcrumbItem, PengajuanPeminjaman } from '@/types';

const { currentPengajuan } = defineProps<{
    currentPengajuan: {
        data: PengajuanPeminjaman;
    };
}>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengajuan Saya',
        href: index().url,
    },
    {
        title: 'Detail Pengajuan Saya',
        href: show(currentPengajuan.data.id).url,
    },
];

const columns = [
    { key: 'kode_transaksi', label: 'Kode Transaksi', sortable: true },
    { key: 'data_bukus_id', label: 'judul Buku', sortable: true },
    { key: 'tanggal_peminjaman', label: 'Tanggal Peminjaman', sortable: true },
    { key: 'status', label: 'status', sortable: true },
    { key: 'catatan', label: 'catatan', sortable: true },
];
</script>
<template>
    <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
        <div class="mb-10 ml-6">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
        <Card class="border-transparent">
            <CardContent>
                <ShowData :columns="columns" :data="currentPengajuan.data"
                    ><template #users_id="{ data }">
                        {{ data.users?.name || 'Tidak Ada' }}
                    </template>
                    <template #data_bukus_id="{ data }">
                        {{ data.databukus?.judul_buku || 'Tidak Ada' }}
                    </template>
                </ShowData>
            </CardContent>
        </Card>
    </div>
</template>
