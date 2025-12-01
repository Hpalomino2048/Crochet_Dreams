<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
  modelValue: string
  error?: string
  productName?: string
  manuallyEdited?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

watch(() => props.productName, (newName) => {
  if (newName && !props.manuallyEdited) {
    const slug = slugify(newName)
    emit('update:modelValue', slug)
  }
})
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
      Slug <span class="text-xs text-gray-500">(auto)</span>
    </label>
    <input 
      :value="modelValue"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      type="text"
      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
      placeholder="producto-nombre" 
    />
    <p v-if="error" class="mt-1 text-xs text-red-600">
      {{ error }}
    </p>
    <p v-else class="mt-1 text-xs text-gray-500">Se genera del nombre</p>
  </div>
</template>