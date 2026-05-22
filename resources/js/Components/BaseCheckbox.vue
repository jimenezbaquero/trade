<template>
  <div class="flex items-center gap-2">
    
    <input
      :checked="modelValue"
      @change="onChange"
      type="checkbox"
      class="base-checkbox"
    />
    
    <label
      v-if="label"
      class="base-checkbox-label text-sm font-medium"
    >
      {{ $t(label) }}
    </label>
  
  </div>
  
  <p
    v-if="error"
    class="text-error mt-1"
  >
    {{ error }}
  </p>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  
  label: {
    type: String,
    default: '',
  },
  
  error: {
    type: String,
    default: '',
  },
})

const emit = defineEmits([
  'update:modelValue',
])

const onChange = (event) => {
  emit('update:modelValue', event.target.checked)
}
</script>

<style scoped src="@/Styles/BaseCheckbox.css"></style>