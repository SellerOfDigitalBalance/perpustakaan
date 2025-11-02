<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase
        title="Buat akun"
        description="Masukkan detail Anda di bawah ini untuk membuat akun Anda."
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="nik">NIK</Label>
                    <Input
                        id="nik"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="nik"
                        name="nik"
                        placeholder="Nomor Induk Kependudukan"
                    />
                    <InputError :message="errors.nik" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Nama</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Nama lengkap"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Alamat Email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@gmail.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Kata Sandi"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation"
                        >Konfirmasi kata sandi</Label
                    >
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Konfirmasi kata sandi"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Buat akun
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Sudah punya akun?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Masuk</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
