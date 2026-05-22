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
      :type="type"
      :placeholder="resolvedPlaceholder"
      class="base-input"
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
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
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
  
  type: {
    type: String,
    default: 'text',
  },
})

const emit = defineEmits([
  'update:modelValue',
])

const resolvedPlaceholder = computed(() => {
  return props.placeholder
    ? window.__ ? props.placeholder : props.placeholder
    : ''
})

const onInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<style scoped src="@/Styles/BaseInput.css"></style>