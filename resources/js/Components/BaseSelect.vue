<template>
  <div>

    <label v-if="label" class="block text-sm font-medium mb-1">
      {{ t(label) }}
    </label>

    <select
      class="base-select"
      :value="modelValue"
      @change="onChange"
    >
      <option value="" disabled>
        {{ t(placeholder) }}
      </option>

      <option
        v-for="option in options"
        :key="optionValue ? option[optionValue] : option"
        :value="optionValue ? option[optionValue] : option"
      >
        {{ optionLabel ? option[optionLabel] : option }}
      </option>
    </select>

  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  modelValue: [String, Number, Object, null],

  options: {
    type: Array,
    default: () => []
  },

  label: {
    type: String,
    default: ''
  },

  placeholder: {
    type: String,
    default: 'select.select_option'
  },

  optionLabel: {
    type: String,
    default: 'label'
  },

  optionValue: {
    type: String,
    default: 'value'
  }
})

const emit = defineEmits(['update:modelValue'])

function onChange(event) {
  emit('update:modelValue', event.target.value)
}
</script>

<style scoped src="@/Styles/BaseSelect.css"></style>
