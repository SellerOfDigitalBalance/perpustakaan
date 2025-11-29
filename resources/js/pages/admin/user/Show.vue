<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import ShowData from '@/components/ShowData.vue';
import { Card, CardContent } from '@/components/ui/card';
import { index, show } from '@/routes/users';
import { BreadcrumbItem, User } from '@/types';

const { currentUser } = defineProps<{
    currentUser: User;
}>();
console.log(currentUser);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengguna',
        href: index().url,
    },
    {
        title: 'Detail Pengguna',
        href: show(currentUser.id).url,
    },
];
const columns = [
    { key: 'nik', label: 'Nik', sortable: true },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'no_hp', label: 'Telepon', sortable: false },
    { key: 'level', label: 'Level', sortable: true },
];
</script>
<template>
    <div class="mx-auto mt-5 max-w-6xl overflow-x-auto">
        <div class="mb-10 ml-6">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
        <Card class="border-transparent">
            <CardContent>
                <ShowData :columns="columns" :data="currentUser">
                    <template #categories_id="{ data }">
                        {{ data.category?.name || 'Tidak Ada' }}
                    </template>
                </ShowData>
            </CardContent>
        </Card>
    </div>
</template>
