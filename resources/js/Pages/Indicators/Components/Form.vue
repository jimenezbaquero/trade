<template>
  <form
    @submit.prevent="submit"
    class="space-y-6 bg-white p-6 rounded-xl shadow"
  >
    
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold">
        {{ title }}
      </h1>
      
      <p class="text-sm text-gray-500 mt-1">
        {{ t('indicator.create_description') }}
      </p>
    </div>
    
    <!-- Code -->
    <BaseInput
      v-model="form.code"
      label="indicator.fields.code"
      :error="form.errors.code"
    />
    
    <!-- Name -->
    <BaseInput
      v-model="form.name"
      label="indicator.fields.name"
      :error="form.errors.name"
    />
    
    <!-- Description -->
    <BaseTextarea
      v-model="form.description"
      label="indicator.fields.description"
      :error="form.errors.description"
      :rows="4"
    />
    
    <!-- Handler -->
    <BaseSelect
      v-model="form.handler"
      :options="handlers"
      option-label="label"
      option-value="value"
      label="indicator.fields.handler"
      placeholder="select.select_option"
    />
    
    <!-- CONFIG AS JSON -->
    <BaseTextarea
      v-model="form.config"
      label="indicator.fields.config"
      :error="form.errors.config"
      :rows="10"
      placeholder='{"period": 14, "source": "close"}'
    />
    
    <!-- Actions -->
    <div class="flex justify-end gap-3">
      
      <input
        type="button"
        class="inline-flex items-center cancel-button"
        :value="t('app.cancel')"
        @click.stop="emit('cancel')"
      />
      
      <input
        type="button"
        class="inline-flex items-center primary-button"
        :value="t('app.save')"
        @click.stop="emit('save')"
      />
    
    </div>
  
  </form>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

import BaseInput from "@/Components/BaseInput.vue"
import BaseTextarea from "@/Components/BaseTextarea.vue"
import BaseSelect from "@/Components/BaseSelect.vue"

defineProps({
  form: Object,
  title: String,
  description: String,
  
  handlers: {
    type: Array,
    default: () => [
      {label: 'EMA', value: 'ema'},
      {label: 'RSI', value: 'rsi'},
      {label: 'MACD', value: 'macd'},
      {label: 'ATR', value: 'atr'},
      {label: 'Bollinger', value: 'bollinger'},
    ]
  }
})

const emit = defineEmits(['cancel', 'save'])

const {t} = useI18n()
</script>

<style scoped src="@/Styles/crud.css"></style>