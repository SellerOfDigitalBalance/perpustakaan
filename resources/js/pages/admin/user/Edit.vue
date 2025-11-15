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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/users';
import { BreadcrumbItem, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const { currentUser } = defineProps<{
    currentUser: {
        data: User;
    };
}>();
console.log(currentUser.data);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengguna',
        href: index().url,
    },
    {
        title: 'Merubah Pengguna',
        href: edit(currentUser.data.id).url,
    },
];
const form = useForm({
    nik: currentUser.data?.nik ?? '',
    name: currentUser.data?.name ?? '',
    email: currentUser.data?.email ?? '',
    no_hp: currentUser.data?.no_hp ?? '',
    level: currentUser.data?.level ?? '',
    image: currentUser.data?.profile_user ?? '',
    _method: 'put',
});
console.log(form.image);
const imagePreview = ref<string | null>(
    currentUser.data.profile_user
        ? `/preview/${currentUser.data.profile_user}`
        : null,
);
const filePreviewUrl = ref<string | null>(null);
function handleFileUpload(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;
    form.image = file;
    const isImage = file.type.startsWith('image/');
    if (isImage) {
        imagePreview.value = URL.createObjectURL(file);
        filePreviewUrl.value = null;
    } else {
        imagePreview.value = null;
        filePreviewUrl.value = URL.createObjectURL(file);
    }
}
const imageModalUrl = ref<string | null>(null);
const showImageModal = ref(false);
const openImageModal = (url: string) => {
    imageModalUrl.value = url;
    showImageModal.value = true;
};
const closeImageModal = () => {
    showImageModal.value = false;
    imageModalUrl.value = null;
};
const submit = () => {
    form.post(update.url(currentUser.data.id));
};
</script>

<template>
    <Head title="Merubah Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-x1 flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto mt-8 min-w-md p-6">
                <form @submit.prevent="submit">
                    <Card class="mx-auto w-full max-w-6xl">
                        <CardHeader>
                            <CardTitle>Ubah Data Pengguna</CardTitle>
                            <CardDescription
                                >Gunakan formulir ini untuk mengubah data
                                pengguna. Pastikan data sudah benar sebelum
                                menyimpan perubahan.</CardDescription
                            >
                        </CardHeader>
                        <CardContent>
                            <div
                                class="grid grid-cols-1 gap-y-6 md:grid-cols-2 md:gap-x-4"
                            >
                                <!-- Kolom Kiri -->
                                <div class="w-full space-y-3 md:max-w-md">
                                    <!-- <div class="grid gap-6"> -->
                                    <div class="grid gap-2">
                                        <Label for="nik">NIK</Label>
                                        <Input
                                            id="nik"
                                            type="text"
                                            required
                                            autofocus
                                            :tabindex="1"
                                            autocomplete="nik"
                                            v-model="form.nik"
                                            placeholder="Masukkan Nama Pengguna"
                                            ref="nik"
                                        />
                                        <InputError
                                            :message="form.errors.nik"
                                        />
                                    </div>

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
                                        <InputError
                                            :message="form.errors.name"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email">Alamat Email</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            required
                                            :tabindex="3"
                                            autocomplete="email"
                                            v-model="form.email"
                                            placeholder="contoh@email.com"
                                            ref="emailInput"
                                        />
                                        <InputError
                                            :message="form.errors.email"
                                        />
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="w-full space-y-3 md:max-w-full">
                                    <div class="grid gap-2">
                                        <Label for="no_hp">No. Telepon</Label>
                                        <Input
                                            id="no_hp"
                                            type="text"
                                            :tabindex="7"
                                            autocomplete="tel"
                                            v-model="form.no_hp"
                                            placeholder="Masukkan nomor telepon"
                                        />
                                        <InputError
                                            :message="form.errors.no_hp"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="level">Level</Label>
                                        <InputError
                                            :message="form.errors.level"
                                        />
                                        <Select
                                            v-model="form.level"
                                            id="level"
                                            ref="levelInput"
                                        >
                                            <SelectTrigger class="w-full">
                                                <SelectValue
                                                    placeholder="Pilih Level"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="admin"
                                                        >Admin</SelectItem
                                                    >
                                                    <SelectItem value="anggota"
                                                        >Anggota</SelectItem
                                                    >
                                                    <SelectItem value="petugas"
                                                        >Petugas</SelectItem
                                                    >
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="image">Foto Profil</Label>
                                        <Input
                                            id="image"
                                            type="file"
                                            :tabindex="8"
                                            @change="handleFileUpload"
                                        />
                                        <!-- Jika file adalah gambar -->
                                        <div v-if="imagePreview">
                                            <Button
                                                type="button"
                                                class="rounded bg-blue-600 text-sm text-white hover:bg-blue-700"
                                                @click="
                                                    openImageModal(imagePreview)
                                                "
                                            >
                                                Lihat Gambar
                                            </Button>
                                        </div>
                                        <InputError
                                            :message="form.errors.image"
                                        />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex justify-between px-6 pb-6">
                            <Button
                                type="submit"
                                class="mt-4 w-full"
                                :tabindex="8"
                                :disabled="form.processing"
                            >
                                <LoaderCircle
                                    v-if="form.processing"
                                    class="h-4 w-4 animate-spin"
                                />
                                Simpan Perubahan
                            </Button>
                        </CardFooter>
                    </Card>
                    <template v-if="showImageModal">
                        <div
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                            @click.self="closeImageModal"
                        >
                            <div
                                class="max-h-[90vh] w-full max-w-4xl overflow-auto rounded-xl bg-white p-4 shadow-lg"
                            >
                                <img
                                    :src="imageModalUrl || '-'"
                                    alt="Full Image"
                                    class="h-auto w-full rounded-lg"
                                />
                                <div class="mt-4 text-right">
                                    <Button
                                        @click="closeImageModal"
                                        class="text-sm text-blue-600 hover:underline"
                                    >
                                        Tutup
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
