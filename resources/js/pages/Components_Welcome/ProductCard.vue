<script setup lang="ts">
import type { Product } from './FeaturedProducts.vue'

interface Props {
  product: Product
}

defineProps<Props>()
</script>

<template>
  <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all group border border-purple-100">
    <div class="bg-gradient-to-br from-purple-100 to-pink-100 p-8 flex items-center justify-center h-48 group-hover:scale-105 transition-transform">
      <span class="text-7xl">{{ product.image }}</span>
    </div>
    
    <div class="p-5">
      <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">
        {{ product.name }}
      </h3>
      
      <div class="flex items-center space-x-1 mb-3">
        <span class="text-yellow-400">⭐</span>
        <span class="text-sm font-medium text-gray-700">{{ product.rating }}</span>
        <span class="text-sm text-gray-500">({{ product.reviews }})</span>
      </div>
      
      <div class="flex items-center justify-between">
        <div>
          <span class="text-2xl font-bold text-purple-600">${{ product.price }}</span>
          <span class="text-sm text-gray-400 line-through ml-2">${{ product.originalPrice }}</span>
        </div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>