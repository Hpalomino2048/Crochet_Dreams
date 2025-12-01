<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ProductCard from './ProductCard.vue'

// Estructura del producto que viene desde tu backend (tienda)
export interface ShopProduct {
  id: number
  name: string
  price: number
  thumbnail?: string | null
  slug: string
}

interface Props {
  // productos reales que te manda Inertia en la página principal
  products: ShopProduct[]
}

const props = defineProps<Props>()

// Tomamos solo algunos como "destacados" (por ejemplo, los primeros 4)
const featuredProducts = computed(() => props.products.slice(0, 4))
</script>

<template>
  <section class="py-12 sm:py-16 bg-white/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
          Productos Destacados
        </h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
          Algunas de las piezas tejidas que puedes encontrar en nuestra tienda
        </p>
      </div>

      <!-- Grid de productos -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in featuredProducts"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Botón para ir a la tienda completa -->
      <div class="text-center mt-10">
        <Link 
          href="/tienda"
          class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-full font-semibold transition-all shadow-lg hover:shadow-xl"
        >
          Ver todos los productos
        </Link>
      </div>
    </div>
  </section>
</template>
