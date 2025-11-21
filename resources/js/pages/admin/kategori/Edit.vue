<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/categories';
import { BreadcrumbItem, Category } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const { currentCategory } = defineProps<{
    currentCategory: {
        data: Category;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kategori Buku',
        href: index().url,
    },
    {
        title: 'Tambah Kategori Buku',
        href: edit(currentCategory.data.id).url,
    },
];

const form = useForm({
    name: currentCategory.data?.name ?? '',
    _method: 'put',
});
const submit = () => {
    form.post(update.url(currentCategory.data.id));
};
</script>
<template>
    <Head title="Merubah Kategori" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-x1 flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto mt-8 min-w-md p-6">
                <form @submit.prevent="submit">
                    <Card class="mx-auto w-full max-w-6xl">
                        <CardHeader>
                            <CardTitle>Tambah Kategori Buku</CardTitle>
                            <CardDescription
                                >Silakan isi data Kategori.</CardDescription
                            >
                        </CardHeader>
                        <CardContent>
                            <div
                                class="grid grid-cols-1 gap-y-6 md:grid-cols-2 md:gap-x-4"
                            >
                                <div class="grid gap-2">
                                    <Label for="name">Nama Lengkap</Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        required
                                        :tabindex="2"
                                        autocomplete="name"
                                        v-model="form.name"
                                        placeholder="Masukkan nama lengkap"
                                        ref="nameInput"
                                    />
                                    <InputError :message="form.errors.name" />
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex justify-between px-6 pt-5 pb-6">
                            <Button
                                type="submit"
                                class="mt-4 w-full"
                                :tabindex="9"
                                :disabled="form.processing"
                            >
                                <LoaderCircle
                                    v-if="form.processing"
                                    class="h-4 w-4 animate-spin"
                                />
                                Simpan
                            </Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
