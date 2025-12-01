<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
  modelValue: string
  error?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:slug': [slug: string]
  'manual-edit': [value: boolean]
}>()

function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

watch(() => props.modelValue, (newName) => {
  if (newName) {
    const slug = slugify(newName)
    emit('update:slug', slug)
    emit('manual-edit', false)
  }
})
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
      Nombre <span class="text-red-600">*</span>
    </label>
    <input 
      :value="modelValue"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      type="text" 
      required
      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
      placeholder="Nombre del producto" 
    />
    <p v-if="error" class="mt-1 text-xs text-red-600">
      {{ error }}
    </p>
  </div>
</template>