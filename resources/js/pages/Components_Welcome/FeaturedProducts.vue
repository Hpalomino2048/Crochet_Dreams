<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import ProductCard from './ProductCard.vue'

export interface Product {
  id: number
  name: string
  price: number
  originalPrice: number
  image: string
  rating: number
  reviews: number
}

interface Props {
  products: Product[]
}

defineProps<Props>()
</script>

<template>
  <section class="py-12 sm:py-16 bg-white/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
          Productos Destacados
        </h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
          Los patrones y kits más populares de nuestra comunidad
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard 
          v-for="product in products" 
          :key="product.id"
          :product="product"
        />
      </div>

      <div class="text-center mt-10">
        <Link 
          href="/shop"
          class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-full font-semibold transition-all shadow-lg hover:shadow-xl"
        >
          Ver Todos los Productos
        </Link>
      </div>
    </div>
  </section>
</template>