<template>
  <div class="general-container mt-4 w-full max-w-full overflow-hidden">
    
    <div class="table-scroll-wrapper" style="min-height: 50dvh">
      <table class="w-full mt-3 table-fixed" :style="{ fontSize: fontSize }">
        <thead>
        <tr>
          <th v-for="(value, key) in columns" :key="key"
              :class="['column-' + key, value.width === 'auto' ? 'auto-width' : '']"
              :style="'width:'+value.width ">
            <div class="flex items-center justify-between">
              <div v-if="filters[key].type === 'funnel'" class="flex items-center">
                <span :class="['funnel-button-' + key]" @click="showChecks(key)"
                      class="flex items-center cursor-pointer">
                  <i
                    class="pi funnel-icon"
                    :class="funnelActive(key) ? 'pi-filter-fill' : 'pi-filter'"
                  />
                </span>
                <div v-if="filters[key].showFunnel" :class="['filter__checkbox', 'filter__checkbox-' + key]">
                  <div class="filter__header">
                    <h3 class="filter__title">{{ value.name }}</h3>
                    <div class="filter__actions">
                      <h3 class="clean__link" @click="checkAllFunnels(key)">{{ t('filter.select_all') }}</h3>
                      <h3 class="clean__link" @click="onFunnelFilter(key)">{{ t('filter.filter') }}</h3>
                      <h3 class="clean__link" @click="uncheckFunnel(key)">{{ t('filter.clean') }}</h3>
                    </div>
                  </div>
                  <div class="filter__body">
                    <div class="block" v-for="(option, index) in filters[key].options" :key="index">
                      <input type="checkbox" class="custom-checkbox align-middle m-auto" v-model="option.checked">
                      <label class="font-size-13 text-80 leading-tight pl-2">
                        {{ option.label }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="value.filterable" class="filter__general container flex items-center truncate-text min-w-0">
                <input v-if="value.type === 'date'" :id="'filter_' + key" type="text" :placeholder="t(value.header)"
                       :style="{ fontSize: fontSize }"
                       v-model="filters[key].value" class="filter--input text no-border-input flex items-center"
                       onfocus="this.type='date'" v-on:blur="checkFilter(key)" @change="onFilterChange(key)"
                       :title="t(value.header)">
                <input v-else :type="value.type === 'date' ? 'date' : 'text'" :placeholder="t(value.header)"
                       v-model="filters[key].value" class="filter--input text no-border-input w-full"
                       :class="'text-' + value.align" @keyup="onFilterChange(key)" :style="{ fontSize: fontSize }">
              </div>
              <div v-else class="filter__general container flex items-center truncate-text min-w-0">
                <a class="filter--input text no-border-input flex items-center">
                  {{ t(value.header) }}
                </a>
              </div>
              <div v-if="value.sortable" class="ml-2 cursor-pointer flex items-center">
                
                <i
                  v-if="filters[key].order_direction === 'desc'"
                  class="pi pi-sort-down"
                  @click="toggleSort(key,'')"
                />
                
                <i
                  v-else-if="filters[key].order_direction === 'asc'"
                  class="pi pi-sort-up"
                  @click="toggleSort(key,'desc')"
                />
                
                <i
                  v-else
                  class="pi pi-sort"
                  @click="toggleSort(key,'asc')"
                />
              
              </div>
            </div>
          </th>
          <th v-if="actions.length > 0" class="w-1/12 text-right actions-column-header">
            {{ t('datatable.actions') }}
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(row, index) in data.data" :key="index"
            :class="[isRowClickable ? 'clickable-row' : '']">
          <template v-for="(value, key) in columns" :key="key">
            <td v-if="key !== 'id'" class="w-3/12" :class="classSelector(value)" :style="'text-' + row[key]"
                :title="row[key]">
              <a :href="isRowClickable ? route(routeAfterClick, row.id) : null"
                 :class="[isRowClickable ? 'clickable-row' : '']"
                 class="block m-0 py-4 no-underline truncate w-full min-w-0" :style="{ fontSize: fontSize }">
                <template v-if="Array.isArray(value)">
                  <template v-if="value.length > 0">
                    <template v-for="(arrayValue, arrayKey) in row[key]">
                      {{ arrayValue[columns[key].field_name] }}
                      <template v-if="arrayKey < row[key].length - 1">
                        {{ ', ' }}
                      </template>
                    </template>
                  </template>
                  <template v-else>
                    ---
                  </template>
                </template>
                <template v-else-if="value.type && value.type === 'html'">
                  <div v-html="row[value.key]"></div>
                </template>
                <template v-else-if="columns[key] && Object.keys(columns[key]).includes('url')">
                  <Link class="link-cell" :href="route(columns[key].url, row.id)">
                    {{ row[key] }}
                  </Link>
                </template>
                <template v-else>
                  {{ row[key]? row[key] : typeof row[key] === 'number'? row[key] : '---' }}
                </template>
              </a>
            </td>
          </template>
          <td v-if="actions.length > 0" class="text-right w-1/12">
            <div class="flex justify-end items-center" :style="{ fontSize: fontSize }">
              
              <!-- EDIT -->
              <button
                v-if="actions.includes('edit') || actions.includes('update')"
                @click.stop="$emit('update', row.id)"
                class="action-button edit-button"
                title="Edit"
              >
                <i class="pi pi-pencil action-icon"></i>
              </button>
              
              <!-- DELETE -->
              <button
                v-if="actions.includes('delete')"
                @click.stop="$emit('delete', row.id)"
                class="action-button delete-button"
                title="Delete"
              >
                <i class="pi pi-trash action-icon"></i>
              </button>
            
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <template v-if="data.data.length == 0">
        <div class="flex items-center justify-center h-24 bg-gray-100">
          <p class="text-gray-600">{{ t('datatable.no_data') }}</p>
        </div>
      </template>
    </div>
    
    <!-- Pagination footer -->
    <div class="pagination__container">
      <div class="select__wrapper">
        {{ t('datatable.show') }}
        <select v-model="itemsPerPage" class="page-select custom-select" @change="onPerPageChange">
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
        {{ t('datatable.per_page') }}
      </div>
      <div class="pagination__wrapper">
        <pagination-links :data="data" @change-page="onPageChange"></pagination-links>
      </div>
    </div>
  </div>
</template>


<script setup>
import {ref, onMounted, onUnmounted, watch, computed, nextTick} from 'vue';
import PaginationLinks from './PaginationLinks.vue';
import {Link, usePage} from '@inertiajs/vue3';
import {useI18n} from "vue-i18n";


const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: true,
  },
  columns: {
    type: Object,
    required: true,
  },
  actions: {
    type: Array,
    default: () => [],
  },
  funnelOptions: {
    type: Object,
    default: () => {}
  },
  isRowClickable: {
    type: Boolean,
    default: false,
  },
  routeAfterClick: {
    type: String,
    default: '',
  },
  fontSize: {
    type: String,
    default: '1rem',
  },
});

console.log(props.filters)

const emit = defineEmits(['sort-change', 'filter-change', 'page-change', 'per-page-change', 'funnel-filter', 'update', 'delete']);

const perPageOptions = [10, 20, 30, 50, 100];
const itemsPerPage = ref(perPageOptions[0]);
const {t} = useI18n()

let filterTimeout = null

const rowClick = (id) => {
  emit('rowClick', id);
};

function toggleSort(col, order) {
  emit('sort-change', {
    col,
    order
  })
}

const onFunnelFilter = (key) => {
  props.filters[key].showFunnel = false;
  funnelActive(key)
  emit('funnel-filter', {
    col: key
  })
}

function onFilterChange(key) {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    emit('filter-change', {
      col: key,
      value: props.filters[key].value
    })
  }, 500)
}

function onPerPageChange() {
  emit('per-page-change', {registers: itemsPerPage.value})
}

function onPageChange(page) {
  emit('page-change', {page: page})
}

const getElementPosition = (element) => {
  const table = document.getElementsByTagName('table')[0]
  const rect = table.getBoundingClientRect();
  return {
    left: rect.left,
    right: rect.right
  };
};

const funnelActive = (key) => {
  return Object.values(props.filters[key].options).filter((o) => o.checked).length > 0
}

const showChecks = (key) => {
  props.filters[key].showFunnel = !props.filters[key].showFunnel;
  
  nextTick(() => {
    const funnelDropdown = document.querySelector(`.filter__checkbox-${key}`);
    const maxRight = funnelDropdown.getBoundingClientRect().right
    
    if(funnelDropdown){
      const posRight = getElementPosition(funnelDropdown).right;
      
      if(posRight < maxRight){
        funnelDropdown.style.right = '0px';
        funnelDropdown.style.left = 'auto';
      }else{
        funnelDropdown.style.left = '0px';
        funnelDropdown.style.right = 'auto';
      }
    }
  });
};

const uncheckFunnel = (key) => {
  Object.values(props.filters[key].options).forEach((o) => o.checked = false)
}
const checkAllFunnels = (key) => {
  Object.values(props.filters[key].options).forEach((o) => o.checked = true)
}

const classSelector = (value) => {
  const alignClass = value.align? 'text-' + value.align + ' ' + value.width : '';
  return 'item w-full truncate-text ml-4 ' + alignClass;
};

const handleClickOutside = (event) => {
  Object.keys(props.columns).forEach(key => {
    const funnel = document.querySelector(`.filter__checkbox-${key}`);
    const funnelButton = document.querySelector(`.funnel-button-${key}`);
    if(funnel && !funnel.contains(event.target) && !funnelButton.contains(event.target)){
      props.filters[key].showFunnel = false;
    }
  });
};


const checkFilter = (key) => {
  let input = document.getElementById('filter_' + key)
  if(input.value == ''){
    input.type = 'text'
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>

<!-- <style src="./styles/simple-index-table.scss"></style> -->
<style scoped src="@/Styles/Datatable.css"></style>


