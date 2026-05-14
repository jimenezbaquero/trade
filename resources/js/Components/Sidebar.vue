<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <aside class="w-64 bg-gray-900 text-white flex flex-col">
    
    <!-- BRAND -->
    <div class="h-14 flex items-center px-4 border-b border-gray-800">
      <span class="font-bold text-lg">TradeBot</span>
    </div>
    
    <!-- MENU -->
    <nav class="flex-1 p-2 space-y-1">
      
      <Link
        href="/exchanges"
        class="block px-3 py-2 rounded hover:bg-gray-800"
        :class="{ 'bg-gray-800': $page.url.startsWith('/exchanges') }"
      >
        Exchanges
      </Link>
    
    </nav>
  
  </aside>
</template>