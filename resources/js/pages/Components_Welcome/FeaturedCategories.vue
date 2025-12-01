<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

export interface Category {
  name: string
  icon: string
  count: string
  slug: string
}

interface Props {
  categories: Category[]
}

defineProps<Props>()
</script>

<template>
  <section class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
          Explora Nuestras Categorías
        </h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
          Encuentra patrones y proyectos para cada ocasión
        </p>
      </div>
      
      <!-- Grid con las 6 categorías: 2 columnas en móvil, 3 en desktop -->
      <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <Link
          v-for="category in categories"
          :key="category.name"
          :href="`/tienda?category=${category.slug}`"
          class="bg-white rounded-2xl p-6 text-center hover:shadow-xl transition-all
                 cursor-pointer border-2 border-purple-100 hover:border-purple-300
                 group block"
        >
          <div class="text-5xl sm:text-6xl mb-4 group-hover:scale-110 transition-transform">
            {{ category.icon }}
          </div>
          <h3 class="font-semibold text-gray-900 mb-2 text-lg">
            {{ category.name }}
          </h3>
          <p class="text-sm text-gray-500">
            {{ category.count }}
          </p>
        </Link>
      </div>
    </div>
  </section>
</template>