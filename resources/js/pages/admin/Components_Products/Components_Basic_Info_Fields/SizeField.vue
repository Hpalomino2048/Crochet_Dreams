<script setup lang="ts">
const props = defineProps<{
  modelValue: string | null
  error?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | null]
}>()

const tamaños = [
  { value: 'Chico', label: 'Chico' },
  { value: 'Mediano', label: 'Mediano' },
  { value: 'Grande', label: 'Grande' },
]
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
      Tamaño <span class="text-red-600">*</span>
    </label>
    <select 
      :value="modelValue"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value || null)"
      required
      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
    >
      <option :value="null">Seleccionar tamaño</option>
      <option 
        v-for="tamaño in tamaños" 
        :key="tamaño.value" 
        :value="tamaño.value"
      >
        {{ tamaño.label }}
      </option>
    </select>
    <p v-if="error" class="mt-1 text-xs text-red-600">
      {{ error }}
    </p>
  </div>
</template>