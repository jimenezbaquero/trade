<template>
  <Head :title="t('exchange.title')"/>
  <AppLayout :isLoading="isLoading" @resetFilters="resetFilters">
    <div class="bg-white rounded-lg shadow-md p-6 min-h-4" style="min-height: 80dvh;">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-title">{{ t('exchange.title') }}</h1>
      </div>
      <div class="flex w-full justify-between">
        <div>
          {{ t('index.total_registers') }}: {{ exchangesCopy.total }}
        </div>
        <div>
          <button class="btn primary-button" @click="cleanFilters">
            {{ t('filter.clean_filters') }}
          </button>
          <Link
            :href="route('exchanges.create')"
            class="ml-2 btn primary-button"
          >
            {{ t('exchange.actions.create') }}
          </Link>
        </div>
      </div>
      <br>
      
      <hr class="mt-6 mb-0">
      
      <datatable :data="exchangesCopy"
                 :filters="filtersCopy"
                 :columns="props.columns"
                 :actions="props.actions"
                 :funnelOptions="funnelOptions"
                 @funnel-filter='onFunnelFilter'
                 @per-page-change="onPerPage"
                 @page-change="onPage"
                 @sort-change="onSort"
                 @filter-change="onFilter"
                 @update="onUpdate"
                 @delete="onDelete"
                 fontSize="0.8rem">
      </datatable>
      <ConfirmModal v-if="showConfirmDeleteModal" :show="showConfirmDeleteModal"
                    :title="t('exchange.delete_title')"
                    :message="t('exchange.delete_confirm')"
                    @confirm="confirmDelete"
                    @cancel="cancelDelete"/>
    </div>
  </AppLayout>
</template>

<script setup>
import {reactive, ref, defineProps, onBeforeUnmount, computed} from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import {useToast} from "vue-toastification";
import {useI18n} from "vue-i18n";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import axios from 'axios';
import AppLayout from "@/Layouts/AppLayout.vue";
import Datatable from "@/Components/Datatable.vue";

const props = defineProps({
  title: String,
  table: Object,
  filters: Object,
  configuration: Object,
  user: Object,
  errors: Object,
  success: String,
  data: Object,
  actions: Object,
  columns: Object,
  funnelOptions: Object,
  showCheckBox: Boolean,
  generalFilters: Object,
  exchanges: Object,
});

const toast = useToast();
const checkBoxes = ref([]);
const copyData = ref({...props.exchanges});
const isLoading = ref(false);
const showConfirmDeleteModal = ref(false);
const {t} = useI18n()


const exchangesCopy = ref({...props.exchanges})
const filtersCopy = ref({...props.filters})
const loading = ref(false)
const registerToDelete = ref(null)


function onUpdate(id) {
  router.visit(route('exchanges.edit', id), {
    preserveState: true,
    preserveScroll: true,
  })
}

function onDelete(id) {
  showConfirmDeleteModal.value = true
  registerToDelete.value = id
}

function onPage(event) {
  filtersCopy.value.page = event.page
  getData()
}

function onFunnelFilter(event) {
  filtersCopy.value[event.col].showFunnel = false
  filtersCopy.value.page = 1
  getData()
}

function onPerPage(event) {
  filtersCopy.value.page = 1
  filtersCopy.value.perPage = event.registers
  getData()
}

function onSort(event) {
  filtersCopy.value.page = 1
  filtersCopy.value[event.col].order_direction = event.order
  getData()
}

function onFilter(event) {
  filtersCopy.value.page = 1
  filtersCopy.value[event.col].value = event.value
  getData()
}

const getData = async() => {
  isLoading.value = true;
  console.log(filtersCopy)
  await axios.post(route('exchanges.getData'), filtersCopy.value, {
    headers: {"Content-Type": "multipart/form-data"},
  }).then(response => {
    exchangesCopy.value = response.data
  }).catch(response => {
    console.log(response)
  }).finally(() => {
    isLoading.value = false
  });
};


const cleanFilters = () => {
  resetFilters();
  getData()
};

const resetFilters = () => {
  filtersCopy.value.page = 1
  Object.keys(props.filters).forEach(key => {
    filtersCopy.value[key].value = '';
    if(filtersCopy.value[key].type === 'funnel'){
      Object.keys(filtersCopy.value[key].options).forEach((option) => {
        console.log(option)
        filtersCopy.value[key].options[option].checked = false
      })
    }
  });
}

const showDeleteModal = () => {
  showConfirmDeleteModal.value = true;
};

const confirmDelete = async() => {
  isLoading.value = true
  await deleteRegister(registerToDelete.value)
  isLoading.value = false
};

const deleteRegister = async(id) => {
  try{
    await axios.delete(route('exchanges.destroy', {exchange: id}))
      .then(response => {
        if(response.data.success){
          showConfirmDeleteModal.value = false
          getData()
          toast.success(response.data.message)
        }else{
          toast.error(response.data.message)
        }
      })
      .finally(() => {
        isLoading.value = false
      })
  }catch(error){
    toast.error(t('app.delete_error'))
    isLoading.value = false
  }
}


const cancelDelete = () => {
  registerToDelete.value = null
  showConfirmDeleteModal.value = false;
};


onBeforeUnmount(() => {
  isLoading.value = false;
  checkBoxes.value = [];
});


</script>

<style scoped src="@/Styles/crud.css"></style>
