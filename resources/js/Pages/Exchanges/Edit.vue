<template>
  <Head :title="t('exchange.edit')" />
  
  <AppLayout :isLoading="form.processing">
    
    <div class="max-w-4xl mx-auto">
      
      <form
        @submit.prevent="submit"
        class="space-y-6 bg-white p-6 rounded-xl shadow"
      >
        
        <!-- Header -->
        <div>
          <h1 class="text-2xl font-bold">
            {{ t('exchange.edit') }}
          </h1>
          
          <p class="text-sm text-gray-500 mt-1">
            {{ t('exchange.create_description') }}
          </p>
        </div>
        
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium mb-1">
            {{ t('exchange.fields.name') }}
          </label>
          
          <input
            v-model="form.name"
            type="text"
            class="primary-input"
          />
          
          <p v-if="form.errors.name" class="text-error">
            {{ form.errors.name }}
          </p>
        </div>
        
        <!-- Slug -->
        <div>
          <label class="block text-sm font-medium mb-1">
            {{ t('exchange.fields.slug') }}
          </label>
          
          <input
            v-model="form.slug"
            type="text"
            class="primary-input"
          />
          
          <p v-if="form.errors.slug" class="text-error">
            {{ form.errors.slug }}
          </p>
        </div>
        
        <!-- API URL -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ t('exchange.fields.api_url') }}
            </label>
            
            <input
              v-model="form.api_url"
              type="text"
              class="primary-input"
            />
            
            <p v-if="form.errors.api_url" class="text-error">
              {{ form.errors.api_url }}
            </p>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ t('exchange.fields.testnet_api_url') }}
            </label>
            
            <input
              v-model="form.testnet_api_url"
              type="text"
              class="primary-input"
            />
            
            <p v-if="form.errors.testnet_api_url" class="text-error">
              {{ form.errors.testnet_api_url }}
            </p>
          </div>
        
        </div>
        
        <!-- Websocket -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ t('exchange.fields.websocket_url') }}
            </label>
            
            <input
              v-model="form.websocket_url"
              type="text"
              class="primary-input"
            />
            
            <p v-if="form.errors.websocket_url" class="text-error">
              {{ form.errors.websocket_url }}
            </p>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ t('exchange.fields.testnet_websocket_url') }}
            </label>
            
            <input
              v-model="form.testnet_websocket_url"
              type="text"
              class="primary-input"
            />
            
            <p v-if="form.errors.testnet_websocket_url" class="text-error">
              {{ form.errors.testnet_websocket_url }}
            </p>
          </div>
        
        </div>
        
        <!-- Rate limit -->
        <div>
          <label class="block text-sm font-medium mb-1">
            {{ t('exchange.fields.rate_limit') }}
          </label>
          
          <input
            v-model="form.rate_limit"
            type="number"
            class="primary-input"
          />
          
          <p v-if="form.errors.rate_limit" class="text-error">
            {{ form.errors.rate_limit }}
          </p>
        </div>
        
        <!-- Metadata -->
        <div>
          <label class="block text-sm font-medium mb-1">
            {{ t('exchange.fields.metadata') }}
          </label>
          
          <textarea
            v-model="form.metadata"
            rows="6"
            class="primary-textarea"
          />
          
          <p v-if="form.errors.metadata" class="text-error">
            {{ form.errors.metadata }}
          </p>
        </div>
        
        <!-- Active -->
        <div class="flex items-center gap-2">
          <input
            v-model="form.is_active"
            type="checkbox"
            class="primary-check"
          />
          
          <label class="text-sm font-medium">
            {{ t('exchange.fields.is_active') }}
          </label>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end gap-3">
          
          <Link
            :href="route('exchanges.index')"
            class="cancel-button"
          >
            {{ t('app.cancel') }}
          </Link>
          
          <button
            type="submit"
            :disabled="form.processing"
            class="primary-button"
          >
            {{ t('exchange.actions.update') }}
          </button>
        
        </div>
      
      </form>
    
    </div>
  
  </AppLayout>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from "@/Layouts/AppLayout.vue"

const { t } = useI18n()

const props = defineProps({
  exchange: Object
})

const form = useForm({
  name: props.exchange.name,
  slug: props.exchange.slug,
  api_url: props.exchange.api_url,
  testnet_api_url: props.exchange.testnet_api_url,
  websocket_url: props.exchange.websocket_url,
  testnet_websocket_url: props.exchange.testnet_websocket_url,
  rate_limit: props.exchange.rate_limit,
  metadata: JSON.stringify(props.exchange.metadata ?? {}, null, 2),
  is_active: props.exchange.is_active,
})

const submit = () => {
  form.put(route('exchanges.update', props.exchange.id))
}
</script>

<style scoped src="@/Styles/crud.css"></style>