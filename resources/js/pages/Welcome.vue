<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppHeader from '../pages/Components_Welcome/AppHeader.vue'
import SeasonCarousel from '../pages/Components_Welcome/SeaonCarousel.vue'
import FeaturedCategories from '../pages/Components_Welcome/FeaturedCategories.vue'
import FeaturedProducts from '../pages/Components_Welcome/FeaturedProducts.vue'
import Newsletter from '../pages/Components_Welcome/Newsletter.vue'
import AppFooter from '../pages/Components_Welcome/AppFooter.vue'
import MobileNavigation from '../pages/Components_Welcome/MobileNavigation.vue'
import type { Slide } from '../pages/Components_Welcome/SeaonCarousel.vue'

type User = {
  name: string
  apellido_paterno?: string
  role?: string
}

const page = usePage()
const user = page.props.auth?.user as User | null

// Datos del carrusel
const slides: Slide[] = [
  {
    id: 1,
    title: 'Colección Primavera',
    subtitle: 'Nuevos patrones florales',
    discount: '30% OFF',
    image: '🌸',
    color: 'from-purple-100 to-pink-100'
  },
  {
    id: 2,
    title: 'Kit Principiantes',
    subtitle: 'Todo lo que necesitas para empezar',
    discount: '25% OFF',
    image: '🧶',
    color: 'from-indigo-100 to-purple-100'
  },
  {
    id: 3,
    title: 'Amigurumis Exclusivos',
    subtitle: 'Patrones premium',
    discount: '40% OFF',
    image: '🐰',
    color: 'from-pink-100 to-rose-100'
  }
]

// Categorías destacadas
const categories = [
  { name: 'Amigurumis', icon: '🧸', count: '120+ patrones' },
  { name: 'Ropa', icon: '👕', count: '85+ diseños' },
  { name: 'Accesorios', icon: '👜', count: '95+ ideas' },
  { name: 'Decoración', icon: '🏠', count: '60+ proyectos' }
]

// Productos destacados
const featuredProducts = [
  {
    id: 1,
    name: 'Patrón Osito Clásico',
    price: 150,
    originalPrice: 200,
    image: '🧸',
    rating: 4.8,
    reviews: 124
  },
  {
    id: 2,
    name: 'Kit Completo Bufanda',
    price: 450,
    originalPrice: 600,
    image: '🧣',
    rating: 4.9,
    reviews: 89
  },
  {
    id: 3,
    name: 'Patrón Flores Decorativas',
    price: 120,
    originalPrice: 180,
    image: '🌺',
    rating: 4.7,
    reviews: 156
  },
  {
    id: 4,
    name: 'Amigurumi Conejo',
    price: 180,
    originalPrice: 250,
    image: '🐰',
    rating: 5.0,
    reviews: 203
  }
]
</script>

<template>
  <Head title="Welcome" />

  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50">
    <!-- Header -->
    <AppHeader :user="user" />

    <!-- Carrusel de Ofertas -->
    <SeasonCarousel :slides="slides" :autoplay-interval="5000" />

    <!-- Categorías Destacadas -->
    <FeaturedCategories :categories="categories" />

    <!-- Productos Destacados -->
    <FeaturedProducts :products="featuredProducts" />

    <!-- Newsletter -->
    <Newsletter />

    <!-- Footer -->
    <AppFooter />

    <!-- Mobile Navigation -->
    <MobileNavigation :user="user" />
  </div>
</template>