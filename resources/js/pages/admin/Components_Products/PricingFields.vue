<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const props = defineProps<{
  form: InertiaForm<any>
}>()

// Establecer valores por defecto
onMounted(() => {
  if (!props.form.price) {
    props.form.price = 0.00
  }
  if (!props.form.stock) {
    props.form.stock = 0
  }
  // Asegurar que la moneda siempre sea MXN
  props.form.currency = 'MXN'
})
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Precio (MXN) <span class="text-red-600">*</span>
      </label>
      <div class="relative">
        <span class="absolute left-3 top-2 text-gray-500 text-sm">$</span>
        <input v-model="form.price" type="number" step="0.01" min="0" required
          class="w-full pl-7 pr-14 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
          placeholder="299.00" />
        <span class="absolute right-3 top-2 text-gray-500 text-sm font-medium">MXN</span>
      </div>
      <p v-if="form.errors.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</p>
      <p class="mt-1 text-xs text-gray-500">Ejemplo: 299.00, 1499.99</p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Stock <span class="text-red-600">*</span>
      </label>
      <input v-model.number="form.stock" type="number" min="0" step="1" required
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
        placeholder="0" />
      <p v-if="form.errors.stock" class="mt-1 text-xs text-red-600">{{ form.errors.stock }}</p>
      <p class="mt-1 text-xs text-gray-500">Unidades disponibles</p>
    </div>
  </div>
</template>