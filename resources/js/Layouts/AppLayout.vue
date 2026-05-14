<script setup>
import Sidebar from '@/Components/Sidebar.vue'

defineProps({
  loading: {
    type:Boolean,
    default: false
  }
})
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    
    <!-- SIDEBAR -->
    <Sidebar />
    
    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
      
      <!-- TOP BAR -->
      <header class="h-14 bg-white border-b flex items-center px-4">
        <h1 class="text-sm font-semibold text-gray-700">
          Trading Dashboard
        </h1>
      </header>
      
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