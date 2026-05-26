<template>
  <Head :title="t('pair.title')" />
  
  <AppLayout :isLoading="isLoading">
    
    <div class="max-w-4xl mx-auto">
      
      <Form
        :form="form"
        :exchanges="props.exchanges"
        :title="t('pair.actions.create')"
        :description="t('pair.create_description')"
        @cancel = "cancel"
        @save = "save"
      />
    
    </div>
  
  </AppLayout>
</template>

<script setup>
import {Head, useForm, router} from '@inertiajs/vue3'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import AppLayout from "@/Layouts/AppLayout.vue"
import Form from "./Components/Form.vue"
import { safeJsonParse } from "@/Utils/json.js"

const props = defineProps({
  'exchanges': Array
})

const { t } = useI18n()

const isLoading = ref(false)

const form = useForm({
  exchange_id: '',
  base_asset: '',
  quote_asset: '',
  symbol: '',
  status: '',
  price_precision: '',
  quantity_precision: '',
  min_qty: '',
  max_qty: '',
  tick_size: '',
  step_size: '',
  min_notional: '',
  metadata: '{ }',
  is_active: true,
})

function save() {
  form.transform((data) => {
    return {
      ...data,
      metadata: safeJsonParse(data.metadata)
    }
  }).post(route('indicators.store'))
}

function cancel() {
  router.visit(route('indicators.index'))
}
</script>
