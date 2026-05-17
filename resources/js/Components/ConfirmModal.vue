<template>
    <transition name="modal">
      <div v-if="show" class="confirmModal-modal-overlay">
        <div class="confirmModal-modal-container">
          <h3 class="confirmModal-modal-title">{{ title }}</h3>
          <p class="confirmModal-modal-message">{{ message }}</p>
          <div class="confirmModal-modal-actions">
            <button type="button" @click="confirm" class="confirmModal-modal-button confirmModal-confirm-button">Eliminar</button>
            <button type="button" @click="cancel" class="confirmModal-modal-button confirmModal-cancel-button">Cancelar</button>
          </div>
        </div>
      </div>
    </transition>
  </template>


<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  show: Boolean,
  title: String,
  message: String,
});

const emit = defineEmits(['confirm', 'cancel']);

const confirm = () => {
  emit('confirm');
};

const cancel = () => {
  emit('cancel');
};
</script>

<style src="@/Styles/ConfirmModal.css" scoped></style>

