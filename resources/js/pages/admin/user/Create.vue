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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index } from '@/routes/users';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Label } from 'reka-ui';
import { ref } from 'vue';
import { BreadcrumbItem } from '../../../types/index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengguna',
        href: index().url,
    },
    {
        title: 'Tambah Pengguna',
        href: create().url,
    },
];
const form = useForm({
    nik: '',
    name: '',
    email: '',
    email_verified_at: '',
    password: '',
    password_confirmation: '',
    no_hp: '',
    profile_user: null as File | null,
    level: '',
});
const imagePreview = ref<string | null>(null);
const filePreviewUrl = ref<string | null>(null);
function handleFileUpload(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;
    form.profile_user = file;
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
    form.post(index().url, {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-x1 flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto mt-8 min-w-md p-6">
                <form @submit.prevent="submit">
                    <Card class="mx-auto w-full max-w-6xl">
                        <CardHeader>
                            <CardTitle>Tambah Pengguna</CardTitle>
                            <CardDescription
                                >Silakan isi data pengguna dengan
                                lengkap.</CardDescription
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
                                            ref="nikInput"
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

                                    <div class="grid gap-2">
                                        <Label for="password">Kata Sandi</Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            required
                                            :tabindex="5"
                                            autocomplete="new-password"
                                            v-model="form.password"
                                            placeholder="Masukkan kata sandi"
                                            ref="passwordInput"
                                        />
                                        <InputError
                                            :message="form.errors.password"
                                        />
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="w-full space-y-3 md:max-w-full">
                                    <div class="grid gap-2">
                                        <Label for="password_confirmation"
                                            >Konfirmasi Kata Sandi</Label
                                        >
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            required
                                            :tabindex="6"
                                            autocomplete="new-password"
                                            v-model="form.password_confirmation"
                                            placeholder="Ulangi kata sandi"
                                            ref="passwordconfirmationInput"
                                        />
                                        <InputError
                                            :message="
                                                form.errors
                                                    .password_confirmation
                                            "
                                        />
                                    </div>

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
                                        <InputError
                                            :message="form.errors.level"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="profile_user"
                                            >Foto Profil</Label
                                        >
                                        <Input
                                            id="profile_user"
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
                                            :message="form.errors.profile_user"
                                        />
                                    </div>
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
                    <!-- Modal Gambar -->
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
                                    <button
                                        @click="closeImageModal"
                                        class="text-sm text-blue-600 hover:underline"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
