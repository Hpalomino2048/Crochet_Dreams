<script setup lang="ts">
defineProps<{
  totalStock: number
  variantsCount: number
}>()
</script>

<template>
  <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
    <div class="flex items-center gap-3">
      <div class="flex-shrink-0 w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-900">Stock Total Calculado</p>
        <p class="text-2xl font-bold text-purple-600">{{ totalStock }} unidades</p>
        <p class="text-xs text-gray-600 mt-0.5">
          Suma automática de todas las variantes • {{ variantsCount }} variante{{ variantsCount !== 1 ? 's' : '' }}
        </p>
      </div>
    </div>
  </div>
</template>