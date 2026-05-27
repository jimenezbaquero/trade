<template>
  <Head :title="t('indicator.actions.update')" />

  <AppLayout :isLoading="isLoading">

    <div class="max-w-4xl mx-auto">

      <Form
        :form="form"
        :title="t('indicator.actions.update')"
        :handlers="handlers"
        @cancel="onCancel"
        @save="onSave"
      />

    </div>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from "vue-toastification"
import { useI18n } from "vue-i18n"

import AppLayout from "@/Layouts/AppLayout.vue"
import Form from "@/Pages/Indicators/Components/Form.vue"

const props = defineProps({
  indicator: Object,
})

const { t } = useI18n()
const toast = useToast()

const isLoading = ref(false)

/**
 * FORM
 */
const form = ref({
  code: props.indicator.code ?? '',
  name: props.indicator.name ?? '',
  description: props.indicator.description ?? '',
  handler: props.indicator.handler ?? '',

  // IMPORTANTE: stringify para textarea JSON
  config: props.indicator.config
    ? JSON.stringify(props.indicator.config, null, 2)
    : '{}',

  errors: {},
})

/**
 * HANDLERS
 */
const handlers = [
  { label: 'EMA', value: 'ema' },
  { label: 'RSI', value: 'rsi' },
  { label: 'MACD', value: 'macd' },
  { label: 'ATR', value: 'atr' },
  { label: 'Bollinger', value: 'bollinger' },
]

/**
 * SAVE
 */
async function onSave() {

  isLoading.value = true

  try {

    const response = await axios.put(
      route('indicators.update', props.indicator.id),
      form.value
    )

    toast.success(t('indicator.messages.updated_successfully'))

    router.visit(route('indicators.index'))

  } catch (error) {

    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    }

    toast.error(t('indicator.messages.updated_error'))

  } finally {
    isLoading.value = false
  }
}

/**
 * CANCEL
 */
function onCancel() {
  router.visit(route('indicators.index'))
}
</script>

<style scoped src="@/Styles/crud.css"></style>
