<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'

const props = defineProps<{
  modelValue: string
  error?: string
  isEditing?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'manual-edit': [value: boolean]
}>()

const isLoadingSku = ref(false)
const skuStatus = ref<'available' | 'taken' | 'checking' | null>(null)

async function fetchNextSku() {
  if (props.isEditing) return
  
  isLoadingSku.value = true
  
  try {
    const response = await fetch('/admin/products/next-sku')
    const data = await response.json()
    
    if (data.sku && !props.modelValue) {
      emit('update:modelValue', data.sku)
    }
  } catch (error) {
    console.error('Error fetching next SKU:', error)
  } finally {
    isLoadingSku.value = false
  }
}

async function checkSkuAvailability(sku: string) {
  if (!sku || sku.length < 2) {
    skuStatus.value = null
    return
  }

  skuStatus.value = 'checking'

  try {
    const response = await fetch(`/admin/products/check-sku?sku=${encodeURIComponent(sku)}`)
    const data = await response.json()
    
    skuStatus.value = data.available ? 'available' : 'taken'
  } catch (error) {
    console.error('Error checking SKU:', error)
    skuStatus.value = null
  }
}

async function refreshSku() {
  emit('manual-edit', false)
  await fetchNextSku()
}

watch(() => props.modelValue, (newSku, oldSku) => {
  if (oldSku && newSku) {
    emit('manual-edit', true)
  }
  if (newSku) {
    checkSkuAvailability(newSku)
  }
})

onMounted(() => {
  if (!props.isEditing) {
    fetchNextSku()
  }
})
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
      SKU 
      <span class="text-xs text-gray-500">(auto)</span>
    </label>
    <div class="relative">
      <input 
        :value="modelValue"
        @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        type="text"
        :disabled="isLoadingSku"
        class="w-full px-3 py-2 pr-20 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
        :class="{ 'bg-gray-50': isLoadingSku }"
        placeholder="SKU-00001" 
      />
      
      <div class="absolute right-3 top-2.5 flex items-center gap-2">
        <!-- Spinner -->
        <svg 
          v-if="isLoadingSku || skuStatus === 'checking'" 
          class="animate-spin h-4 w-4 text-gray-400" 
          fill="none" 
          viewBox="0 0 24 24"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        
        <!-- Available -->
        <svg 
          v-else-if="skuStatus === 'available'" 
          class="h-4 w-4 text-green-500" 
          fill="currentColor" 
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        
        <!-- Taken -->
        <svg 
          v-else-if="skuStatus === 'taken'" 
          class="h-4 w-4 text-red-500" 
          fill="currentColor" 
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>

        <!-- Refresh button -->
        <button
          v-if="!isEditing"
          type="button"
          @click="refreshSku"
          :disabled="isLoadingSku"
          class="text-gray-400 hover:text-purple-600 transition-colors disabled:opacity-50"
          title="Generar nuevo SKU"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
        </button>
      </div>
    </div>
    
    <div class="mt-1">
      <p v-if="skuStatus === 'available'" class="text-xs text-green-600">✓ SKU disponible</p>
      <p v-else-if="skuStatus === 'taken'" class="text-xs text-red-600">✗ SKU ya existe</p>
      <p v-else-if="error" class="text-xs text-red-600">{{ error }}</p>
      <p v-else-if="!isEditing" class="text-xs text-gray-500">Se genera automáticamente</p>
    </div>
  </div>
</template>