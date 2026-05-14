<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Datatable from "@/Components/Datatable.vue";
import {ref} from "vue";

const props = defineProps({
  exchanges: Object,
  columns: Array,
  filters: Object,
})

const exchangesCopy = ref(props.exchanges)
const filterCopy = ref(props.filters)
const loading = ref(false)

function onPage(page) {
  filterCopy.value.page = page
  getData()
}

function onSort(event) {
  (filterCopy.value)[event.col].order_direction = event.order
  getData()
}

function onFilter(event) {
  filterCopy.value[event.col].value = event.value
  getData()
}

async function getData(){
  loading.value = true
  try{
    const response = await axios.post(route('exchanges.getData'), filterCopy.value)
    exchangesCopy.value = response.data
  }finally{
    loading.value = false
  }
  
}
</script>

<template>
  <AppLayout :loading="loading">
    <Datatable
      :data="exchangesCopy"
      :columns="columns"
      :filters="filterCopy"
      @page-change="onPage"
      @sort-change="onSort"
      @filter-change="onFilter"
    />
  </AppLayout>
</template>