<template>
  <div>
    
    <label
      v-if="label"
      class="block text-sm font-medium mb-1"
    >
      {{ $t(label) }}
    </label>
    
    <textarea
      :value="modelValue"
      @input="onInput"
      :rows="rows"
      :placeholder="placeholder ? $t(placeholder) : ''"
      class="base-textarea"
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
    type: String,
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
  
  rows: {
    type: Number,
    default: 5,
  },
})

const emit = defineEmits([
  'update:modelValue',
])

const onInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<style scoped src="@/Styles/BaseTextarea.css"></style>