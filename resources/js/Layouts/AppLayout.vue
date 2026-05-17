<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Header from "@/Components/Header.vue";
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { watch } from 'vue'
import { useToast } from 'vue-toastification'

const page = usePage()
const toast = useToast()

const flash = computed(() => page.props.flash || {})

defineProps({
  loading: {
    type:Boolean,
    default: false
  }
})

watch(
  () => page.props.flash,
  (flash) => {
    if (!flash) return
    
    if (flash.success) {
      toast.success(flash.success)
    }
    
    if (flash.error) {
      toast.error(flash.error)
    }
    
    if (flash.warning) {
      toast.warning(flash.warning)
    }
    
    if (flash.info) {
      toast.info(flash.info)
    }
  },
  { deep: true, immediate: true }
)
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    
    <!-- SIDEBAR -->
    <Sidebar />
    
    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
      
      <!-- TOP BAR -->
      <Header class="h-14 bg-white border-b flex items-center px-4 justify-end" />
      
      <!-- PAGE CONTENT -->
      <main class="flex-1 overflow-y-auto p-4">
        <div
          v-if="loading"
          class="absolute inset-0 bg-white/70 z-50 flex items-center justify-center"
        >
          Loading...
        </div>
        <slot />
      </main>
    
    </div>
  </div>
</template>