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
  pair: Object,
  exchanges: Array,
})

const { t } = useI18n()

const isLoading = ref(false)

const form = useForm({
  exchange_id: props.pair.exchange_id,
  base_asset: props.pair.base_asset,
  quote_asset: props.pair.quote_asset,
  symbol: props.pair.symbol,
  status: props.pair.status,
  price_precision: props.pair.price_precision,
  quantity_precision: props.pair.quantity_precision,
  min_qty: props.pair.min_qty,
  max_qty: props.pair.max_qty,
  tick_size: props.pair.tick_size,
  step_size: props.pair.step_size,
  min_notional: props.pair.min_notional,
  metadata: JSON.stringify(props.pair.metadata ?? '{}', null, 2),
  is_active: props.pair.is_active,
})

function save() {
  form.transform((data) => {
    return {
      ...data,
      metadata: safeJsonParse(data.metadata)
    }
  }).put(route('pairs.update', props.pair.id))
}

function cancel() {
  router.visit(route('pairs.index'))
}
</script>

<style scoped src="@/Styles/crud.css"></style>