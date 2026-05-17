<template>
    <div class="pagination bg-20 rounded-b p-4 flex flex-col items-center">
      <nav class="flex items-center justify-center">
        <div class="flex text-sm space-x-2">
          <!-- First Link -->
          <button
            :disabled="!hasPreviousPages || linksDisabled"
            class="btn h-9 min-w-9 px-3 py-1 border rounded pagination-inactive-button"
            :class="[
              hasPreviousPages ? '' : 'cursor-not-allowed',
              linksDisabled ? 'cursor-not-allowed' : ''
            ]"
            rel="first"
            @click.prevent="selectPage(1)"
            dusk="first"
          >
            Primera
          </button>
  
          <!-- Previous Link -->
          <button
            :disabled="!hasPreviousPages || linksDisabled"
            class="btn h-9 min-w-9 px-3 py-1 border rounded pagination-inactive-button"
            :class="[
              hasPreviousPages ? '' : 'cursor-not-allowed',
              linksDisabled ? 'cursor-not-allowed' : ''
            ]"
            rel="prev"
            @click.prevent="selectPreviousPage"
            dusk="previous"
          >
            <span>&lt;</span>
          </button>
  
          <!-- Pages Links -->
          <button
            :disabled="linksDisabled"
            v-for="n in printPages"
            :key="n"
            class="btn h-9 min-w-9 px-3 py-1 border rounded"
            :class="[
              data.current_page !== n ? 'pagination-inactive-button' : 'pagination-active-button',
              linksDisabled ? 'cursor-not-allowed' : ''
            ]"
            @click.prevent="selectPage(n)"
            :dusk="`page:${n}`"
          >
            {{ n }}
          </button>
  
          <!-- Next Link -->
          <button
            :disabled="!hasMorePages || linksDisabled"
            class="btn h-9 min-w-9 px-3 py-1 border rounded pagination-inactive-button"
            :class="[
              hasMorePages ? '' : 'cursor-not-allowed',
              linksDisabled ? 'cursor-not-allowed' : ''
            ]"
            rel="next"
            @click.prevent="selectNextPage"
            dusk="next"
          >
            <span>&gt;</span>
          </button>
  
          <!-- Last Link -->
          <button
            :disabled="!hasMorePages || linksDisabled"
            class="btn h-9 min-w-9 px-3 py-1 border rounded pagination-inactive-button"
            :class="[
              hasMorePages ? '' : 'cursor-not-allowed',
              linksDisabled ? 'cursor-not-allowed' : ''
            ]"
            rel="last"
            @click.prevent="selectPage(data.last_page)"
            dusk="last"
          >
            Última
          </button>
        </div>
  
        <slot />
      </nav>
    </div>
  </template>
  
  <script setup>
  import { computed, ref, watch } from 'vue';
  
  const props = defineProps({
    data: {
      type: Object,
      required: true,
    },
  });
  
  const emit = defineEmits(['changePage']);
  
  const linksDisabled = ref(false);
  
  const hasPreviousPages = computed(() => {
    return props.data && props.data.current_page > 1;
  });
  
  const hasMorePages = computed(() => {
    return props.data && props.data.current_page < props.data.last_page;
  });
  
  const printPages = computed(() => {

    if (!props.data) {
      return [];
    }
    const middlePage = Math.min(Math.max(3, props.data.current_page), props.data.last_page - 2);
    const fromPage = Math.max(middlePage - 2, 1);
    const toPage = Math.min(middlePage + 2, props.data.last_page);
  
    let pages = [];
  
    for (let n = fromPage; n <= toPage; ++n) {
      if (n > 0) pages.push(n);
    }
  
    return pages;
  });
  
  const selectPage = (page) => {
    if (props.data.current_page !== page) {
      linksDisabled.value = true;
      emit('changePage', page);
    }
  };
  
  const selectPreviousPage = () => {
    selectPage(props.data.current_page - 1);
  };
  
  const selectNextPage = () => {
    selectPage(props.data.current_page + 1);
  };
  
  watch(() => props.data, () => {
    linksDisabled.value = false;
  });
  </script>

<style scoped src="@/Styles/PaginationLinks.css"></style>