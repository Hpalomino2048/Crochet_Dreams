<script setup lang="ts">
defineEmits<{
  'add-variant': []
}>()
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h3 class="text-lg font-semibold text-gray-900">
        Variantes de Color
      </h3>
      <p class="text-sm text-gray-600 mt-0.5">
        Define diferentes colores del producto con stock e imagen individual
      </p>
    </div>
    <button
      type="button"
      @click="$emit('add-variant')"
      class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Agregar Variante
    </button>
  </div>
</template>