<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

export interface Product {
  id: number
  name: string
  price: number
  thumbnail?: string | null
  slug: string
}

const props = defineProps<{
  product: Product
}>()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(price)
}
</script>

<template>
  <article
    class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-2xl hover:border-purple-200 transition-all duration-300 hover:-translate-y-1 flex flex-col"
  >
    <!-- Imagen -->
    <Link :href="`/tienda/${product.slug}`" class="block">
      <!-- 👇 relative para poder posicionar la etiqueta -->
      <div class="relative aspect-square bg-gradient-to-br from-purple-50 via-pink-50 to-purple-100 overflow-hidden">
        <!-- Etiqueta NUEVO -->
        <div class="absolute left-0 top-0 z-10">
          <div
            class="bg-gradient-to-r from-purple-600 to-pink-500 text-[10px] sm:text-xs font-bold text-white
                   px-3 py-1 rounded-br-2xl shadow-md uppercase tracking-wide"
          >
            Nuevo
          </div>
        </div>

        <img
          v-if="product.thumbnail"
          :src="product.thumbnail"
          :alt="product.name"
          class="w-full h-full object-cover object-center hover:scale-110 transition-transform duration-500"
          loading="lazy"
        />
        <div v-else class="w-full h-full flex items-center justify-center text-purple-200">
          <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
      </div>
    </Link>

    <!-- Info -->
    <div class="p-4 flex-1 flex flex-col">
      <Link :href="`/tienda/${product.slug}`">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-purple-600 transition-colors">
          {{ product.name }}
        </h3>
      </Link>

      <div class="mt-auto flex items-center justify-between">
        <span class="text-lg font-bold text-purple-700">
          {{ formatPrice(product.price) }}
        </span>

        <Link
          :href="`/tienda/${product.slug}`"
          class="text-sm font-semibold text-purple-600 hover:text-purple-800"
        >
          Ver detalle →
        </Link>
      </div>
    </div>
  </article>
</template>
