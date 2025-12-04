<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { computed, ref, onMounted } from 'vue'

interface FlashMessageProps {
  success?: string
  error?: string
}

const page = usePage()

const flash = computed<FlashMessageProps>(() => {
  return (page.props.flash ?? {}) as FlashMessageProps
})

const localFlash = ref<FlashMessageProps>({ success: '', error: '' })
const visible = ref(false)

watch(flash, (newFlash) => {
  if (newFlash.success || newFlash.error) {
    localFlash.value = { ...newFlash }
    visible.value = true

    setTimeout(() => {
      visible.value = false
      localFlash.value = { success: '', error: '' }
    }, 4000)
  }
})

</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <transition name="fade" mode="out-in">
      <div
        v-if="visible && flash.success"
        key="success"
        class="max-w-xs rounded-lg bg-green-600 px-4 py-3 text-white shadow-lg"
      >
        {{ flash.success }}
      </div>
    </transition>

    <transition name="fade" mode="out-in">
      <div
        v-if="visible && flash.error"
        key="error"
        class="max-w-xs rounded-lg bg-red-600 px-4 py-3 text-white shadow-lg"
      >
        {{ flash.error }}
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
