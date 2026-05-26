<template>
  <Head :title="t('indicator.title')" />
  
  <AppLayout :isLoading="isLoading" @resetFilters="resetFilters">
    
    <div class="bg-white rounded-lg shadow-md p-6 min-h-4" style="min-height: 80dvh;">
      
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-title">{{ t('indicator.title') }}</h1>
      </div>
      
      <!-- TOP BAR -->
      <div class="flex w-full justify-between">
        <div>
          {{ t('index.total_registers') }}: {{ indicatorsCopy.total }}
        </div>
        
        <div>
          <button class="btn primary-button" @click="cleanFilters">
            {{ t('filter.clean_filters') }}
          </button>
          
          <Link
            :href="route('indicators.create')"
            class="ml-2 btn primary-button"
          >
            {{ t('indicator.actions.create') }}
          </Link>
        </div>
      </div>
      
      <br>
      <hr class="mt-6 mb-0">
      
      <!-- DATATABLE -->
      <datatable
        :data="indicatorsCopy"
        :filters="filtersCopy"
        :columns="props.columns"
        :actions="props.actions"
        :is-row-clickable="true"
        route-after-click="indicators.show"
        
        @per-page-change="onPerPage"
        @page-change="onPage"
        @sort-change="onSort"
        @filter-change="onFilter"
        @update="onUpdate"
        @delete="onDelete"
        
        fontSize="0.8rem"
      />
      
      <!-- DELETE MODAL -->
      <ConfirmModal
        v-if="showConfirmDeleteModal"
        :show="showConfirmDeleteModal"
        :title="t('indicator.delete_title')"
        :message="t('indicator.delete_confirm')"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />
    
    </div>
  
  </AppLayout>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from "vue-toastification"
import { useI18n } from "vue-i18n"

import AppLayout from "@/Layouts/AppLayout.vue"
import Datatable from "@/Components/Datatable.vue"
import ConfirmModal from "@/Components/ConfirmModal.vue"

const props = defineProps({
  indicators: Object,
  filters: Object,
  columns: Object,
  actions: Object,
})

const { t } = useI18n()
const toast = useToast()

const indicatorsCopy = ref({ ...props.indicators })
const filtersCopy = ref({ ...props.filters })

const isLoading = ref(false)

const showConfirmDeleteModal = ref(false)
const registerToDelete = ref(null)

/**
 * UPDATE
 */
function onUpdate(id) {
  router.visit(route('indicators.edit', id), {
    preserveState: true,
    preserveScroll: true,
  })
}

/**
 * DELETE
 */
function onDelete(id) {
  showConfirmDeleteModal.value = true
  registerToDelete.value = id
}

/**
 * PAGINATION
 */
function onPage(event) {
  filtersCopy.value.page = event.page
  getData()
}

/**
 * SORT
 */
function onSort(event) {
  filtersCopy.value.page = 1
  filtersCopy.value[event.col].order_direction = event.order
  getData()
}

/**
 * FILTER TEXT
 */
function onFilter(event) {
  filtersCopy.value.page = 1
  filtersCopy.value[event.col].value = event.value
  getData()
}

/**
 * GET DATA
 */
const getData = async () => {
  
  isLoading.value = true
  
  await axios.post(route('indicators.getData'), filtersCopy.value)
    .then(response => {
      indicatorsCopy.value = response.data
    })
    .catch(error => {
      console.log(error)
    })
    .finally(() => {
      isLoading.value = false
    })
}

/**
 * CLEAN FILTERS
 */
function cleanFilters() {
  resetFilters()
  getData()
}

/**
 * RESET FILTERS
 */
function resetFilters() {
  
  filtersCopy.value.page = 1
  
  Object.keys(props.filters).forEach(key => {
    filtersCopy.value[key].value = ''
  })
}

/**
 * DELETE CONFIRM
 */
async function confirmDelete() {
  
  isLoading.value = true
  
  try {
    
    const response = await axios.delete(route('indicators.destroy', {
      indicator: registerToDelete.value
    }))
    
    if (response.data.success) {
      toast.success(response.data.message)
      showConfirmDeleteModal.value = false
      getData()
    } else {
      toast.error(response.data.message)
    }
    
  } catch (e) {
    toast.error(t('app.delete_error'))
  } finally {
    isLoading.value = false
  }
}

/**
 * CANCEL DELETE
 */
function cancelDelete() {
  registerToDelete.value = null
  showConfirmDeleteModal.value = false
}

/**
 * CLEANUP
 */
onBeforeUnmount(() => {
  isLoading.value = false
})
</script>

<style scoped src="@/Styles/crud.css"></style>