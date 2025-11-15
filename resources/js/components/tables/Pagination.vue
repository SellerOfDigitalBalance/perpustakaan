<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    previousPage?: string | null;
    nextPage?: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}>();
</script>

<template>
    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
        <!-- Mobile: Hanya "Previous" dan "Next" -->
        <div class="flex w-full justify-between md:hidden">
            <Link
                :href="previousPage || '#'"
                class="rounded-md border bg-gray-200 px-4 py-2 hover:bg-gray-300 disabled:opacity-50 dark:bg-gray-900"
                :class="{ 'pointer-events-none opacity-50': !previousPage }"
            >
                Previous
            </Link>

            <Link
                :href="nextPage || '#'"
                class="rounded-md border bg-gray-200 px-4 py-2 hover:bg-gray-300 disabled:opacity-50 dark:bg-gray-900"
                :class="{ 'pointer-events-none opacity-50': !nextPage }"
            >
                Next
            </Link>
        </div>

        <!-- Desktop: Semua halaman -->
        <div class="hidden flex-wrap gap-2 md:flex">
            <Link
                v-for="(link, index) in links"
                :key="index"
                :href="link.url ? link.url : '#'"
                class="rounded-md border px-3 py-1"
                :class="{
                    'bg-gray-200 dark:bg-gray-900': link.active,
                    'cursor-not-allowed opacity-50': !link.url,
                }"
                :disabled="!link.url"
            >
                <span v-html="link.label"></span>
            </Link>
        </div>
    </div>
</template>
