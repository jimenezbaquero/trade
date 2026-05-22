<template>
  <div>
    
    <label
      v-if="label"
      class="block text-sm font-medium mb-1"
    >
      {{ $t(label) }}
    </label>
    
    <input
      :value="modelValue"
      @input="onInput"
      type="number"
      :step="step"
      :min="min"
      :max="max"
      :placeholder="placeholder"
      class="base-input-number"
    />
    
    <p
      v-if="error"
      class="text-error"
    >
      {{ error }}
    </p>
  
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: '',
  },
  
  label: {
    type: String,
    default: '',
  },
  
  placeholder: {
    type: String,
    default: '',
  },
  
  error: {
    type: String,
    default: '',
  },
  
  step: {
    type: String,
    default: 'any',
  },
  
  min: {
    type: [String, Number],
    default: null,
  },
  
  max: {
    type: [String, Number],
    default: null,
  },
})

const emit = defineEmits([
  'update:modelValue',
])

const onInput = (event) => {
  
  const value = event.target.value
  
  emit(
    'update:modelValue',
    value === '' ? '' : Number(value)
  )
}
</script>

<style scoped src="@/Styles/BaseNumberInput.css"></style>