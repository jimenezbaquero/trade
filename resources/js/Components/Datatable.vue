<script setup>
import {computed, reactive, ref} from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Object,
    required: true, // paginated Laravel response
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const activeFilters = reactive({})
const activeFiltersValues = reactive({})
let filterTimeout = null

const emit = defineEmits([
  'sort-change',
  'filter-change'
])

function changePage(page, action) {
  if((page > 1 && action === 'prev') || (page < props.data.total && action === 'next')){
    let newPage = 1
    if (action === 'next'){
      newPage = page + 1
    }else{
      newPage = page -1
    }
    
    emit('page-change',
      newPage
    )
  }
}

function toggleSort(col,order) {
  emit('sort-change', {
    col,
    order
  })
}

function onFilterChange(key) {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    if(activeFiltersValues[key] === ''){
      activeFilters[key] = false
    }
    emit('filter-change', {
      col: key,
      value: activeFiltersValues[key]
    })
  },500)
}

function activateFilter(key){
  activeFilters[key] = true
}

</script>

<template>
  <div class="w-full">
    
    <!-- TABLE -->
    <table class="w-full border border-gray-200">
      <thead class="bg-gray-100">
        <tr>
          <th
            v-for="col in columns"
            :key="col.field"
            class="text-left p-2"
          >
            <div class="flex items-center gap-1 justify-between cursor-pointer" @click="activateFilter(col.key)">
              <span v-if="!activeFilters[col.key]">
                {{ col.header }}
              </span>
              <input v-else
                v-model="activeFiltersValues[col.key]"
                @input="onFilterChange(col.key)"
                class="border p-1 w-full mt-1"
                :placeholder="filters[col.key].value"
              />
              
              <span v-if="col.sortable" class="text-xs opacity-70">
                <i
                  v-if="filters[col.key].order_direction === 'desc'"
                  class="pi pi-sort-down"
                  @click="toggleSort(col.key,'')"
                />
    
                <i
                  v-else-if="filters[col.key].order_direction === 'asc'"
                  class="pi pi-sort-up"
                  @click="toggleSort(col.key,'desc')"
                />
    
                <i
                  v-else
                  class="pi pi-sort"
                  @click="toggleSort(col.key,'asc')"
                />
                
              </span>
            </div>
          </th>
        </tr>
      </thead>
      
      <tbody>
        <tr
          v-for="row in data.data"
          :key="row.id"
          class="border-t"
        >
          <td
            v-for="col in columns"
            :key="col.field"
            class="p-2"
          >
            {{ row[col.field] }}
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- PAGINATION -->
    <div class="flex gap-2 mt-3">
      <button
        class="px-2 py-1 border"
        :disabled="data.current_page === 1"
        @click="changePage(data.current_page, 'prev')"
      >
        Prev
      </button>
      
      <span class="px-2 py-1">
        Page {{ data.current_page }} / {{ data.last_page }}
      </span>
      
      <button
        class="px-2 py-1 border"
        :disabled="data.current_page === data.last_page"
        @click="changePage(data.current_page, 'next')"
      >
        Next
      </button>
    </div>
  
  </div>
</template>