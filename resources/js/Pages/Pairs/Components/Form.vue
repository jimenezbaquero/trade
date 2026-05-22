<template>
  <form
    @submit.prevent="submit"
    class="space-y-6 bg-white p-6 rounded-xl shadow"
  >
    
    <!-- Header slot (create / edit) -->
    <div>
      <h1 class="text-2xl font-bold">
        {{ title }}
      </h1>
      
      <p class="text-sm text-gray-500 mt-1">
        {{ t('pair.create_description') }}
      </p>
    </div>
    
    <!-- Exchange -->
    <BaseSelect
      v-model="form.exchange_id"
      :options="exchanges"
      option-label="name"
      option-value="id"
      :label="t('pair.fields.exchange')"
      placeholder="select.select_option"
    />
    
    <!-- Symbol -->
    <BaseInput
      v-model="form.symbol"
      label="pair.fields.symbol"
      :error="form.errors.symbol"
    />
    
    <!-- Base / Quote -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <BaseInput
        v-model="form.base_asset"
        label="pair.fields.base_asset"
        :error="form.errors.base_asset"
      />
      
      <BaseInput
        v-model="form.quote_asset"
        label="pair.fields.quote_asset"
        :error="form.errors.quote_asset"
      />
    </div>
    
    <!-- Status -->
    <BaseInput
      v-model="form.status"
      label="pair.fields.status"
      :error="form.errors.status"
    />
    
    <!-- Precision -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <BaseNumberInput
        v-model="form.price_precision"
        label="pair.fields.price_precision"
        :error="form.errors.price_precision"
        step="1"
      />
      
      <BaseNumberInput
        v-model="form.quantity_precision"
        label="pair.fields.quantity_precision"
        :error="form.errors.quantity_precision"
        step="1"
      />
    </div>
    
    <!-- Min / Max -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <BaseNumberInput
        v-model="form.min_qty"
        label="pair.fields.min_qty"
        :error="form.errors.min_qty"
        step="0.00000001"
      />
      
      <BaseNumberInput
        v-model="form.max_qty"
        label="pair.fields.max_qty"
        :error="form.errors.max_qty"
        step="0.00000001"
      />
    </div>
    
    <!-- Metadata -->
    <BaseTextarea
      v-model="form.metadata"
      label="pair.fields.metadata"
      :error="form.errors.metadata"
      :rows="8"
    />
    
    <!-- Active -->
    <BaseCheckbox
      v-model="form.is_active"
      label="pair.fields.is_active"
    />
    
    <!-- Actions slot -->
    <div class="flex justify-end gap-3">
      <slot name="actions" />
    </div>
    
    <div class="flex justify-end gap-3">
      <input type="button"
             class="inline-flex items-center cancel-button"
             :value ="t('app.cancel')"
             @click.stop = "emit('cancel')"
      />
      
      <input type="button"
        class="inline-flex items-center primary-button"
        :value ="t('pair.actions.create')"
             @click.stop = "emit('save')"
      />

    
    </div>
  
  </form>
</template>

<script setup>
import {NumberFormat, useI18n} from 'vue-i18n'

import BaseSelect from "@/Components/BaseSelect.vue"
import BaseInput from "@/Components/BaseInput.vue"
import BaseNumberInput from "@/Components/BaseNumberInput.vue"
import BaseTextarea from "@/Components/BaseTextarea.vue"
import BaseCheckbox from "@/Components/BaseCheckbox.vue"


defineProps({
  form: Object,
  exchanges: Array,
  title: String,
  description: String
})

const emit = defineEmits(['cancel', 'save'])

const { t } = useI18n()
</script>

<style scoped src="@/Styles/crud.css"></style>