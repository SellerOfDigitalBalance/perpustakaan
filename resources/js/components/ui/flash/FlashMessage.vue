<script setup>
import { flashStore } from '@/Stores/flash';
</script>

<template>
    <transition name="fade">
        <div
            v-if="flashStore.show"
            class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow"
        >
            {{ flashStore.message }}
        </div>
    </transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity .3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
