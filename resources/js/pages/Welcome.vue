<script setup lang="ts">
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

// ============================================
// ELIGE UNA DE ESTAS OPCIONES SEGÚN TU ESTRUCTURA:
// ============================================

// OPCIÓN 1: Si los componentes están en resources/js/Components_Welcome/
import AppHeader from './Components_Welcome/AppHeader.vue'
import SeasonCarousel from './Components_Welcome/SeaonCarousel.vue'
import FeaturedCategories from './Components_Welcome/FeaturedCategories.vue'
import FeaturedProducts from './Components_Welcome/FeaturedProducts.vue'
import AppFooter from './Components_Welcome/AppFooter.vue'
import MobileNavigation from './Components_Welcome/MobileNavigation.vue'
import type { Slide } from './Components_Welcome/SeaonCarousel.vue'

type User = {
  name: string
  apellido_paterno?: string
  role?: string
}

type Product = {
  id: number
  name: string
  price: number
  thumbnail?: string | null
  slug: string
}

type Category = {
  name: string
  icon: string
  count: string
  slug: string
}

// ==== Props ====
// Hacemos products OPCIONAL porque el backend puede no enviarlo
const props = withDefaults(defineProps<{
  products?: Product[]
  name?: string
  quote?: { message: string; author: string }
  errors?: Record<string, any>
}>(), {
  products: () => []
})

const featuredProducts = computed(() => {
  // Si no hay productos del backend, usamos un array vacío
  if (!props.products || props.products.length === 0) {
    return []
  }
  return props.products.slice(0, 4)
})

// Usuario autenticado
const page = usePage()
const user = page.props.auth?.user as User | null

const slides: (Slide & { badge?: string })[] = [
  {
    id: 1,
    title: '🎊 ¡GRAN APERTURA! 🎊',
    subtitle: 'Tu nuevo lugar de detalles tejidos',
    discount: 'Piezas 100% hechas a mano',
    image: '🎉',
    color: 'from-purple-100 to-pink-100',
    badge: 'NUEVO'
  },
  {
    id: 2,
    title: 'Entrega con detalle',
    subtitle: 'Tu pedido llega listo para regalar',
    discount: 'Empaque bonito + tarjetita de agradecimiento',
    image: '🎁',
    color: 'from-pink-100 to-rose-100',
    badge: 'CUIDADO ESPECIAL'
  },
  {
    id: 3,
    title: 'Acompañamiento después de tu compra',
    subtitle: 'Queremos que tu tejido dure mucho tiempo',
    discount: 'Guía de cuidado y mensaje de seguimiento',
    image: '🐰',
    color: 'from-indigo-100 to-purple-100',
    badge: 'POST-VENTA'
  }
]

const categories: Category[] = [
  {
    name: 'Muñecos y personajes',
    icon: '🧸',
    count: 'Personajes, anime y deportivos',
    slug: 'muñecos-personajes'
  },
  {
    name: 'Ramos tejidos',
    icon: '💐',
    count: 'Ramos de flores y con muñeco',
    slug: 'ramos-tejidos'
  },
  {
    name: 'Llaveros y minis',
    icon: '🔑',
    count: 'Llaveros y mini amigurumis',
    slug: 'llaveros-minis'
  },
  {
    name: 'Flores y plantas',
    icon: '🌷',
    count: 'Tulipanes, rosas y más',
    slug: 'flores-plantas'
  },
  {
    name: 'Decoración y detalles',
    icon: '🏠',
    count: 'Detalles para el hogar y espacios',
    slug: 'decoracion-detalles'
  },
  {
    name: 'Temporada y especiales',
    icon: '🎉',
    count: 'San Valentín, Navidad y más',
    slug: 'temporada-especiales'
  }
]

</script>

<template>
  <Head title="Crochet Dreams" />
  
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50">
    <!-- Header -->
    <AppHeader :user="user" />
    
    <!-- Carrusel de Ofertas / Temporada -->
    <SeasonCarousel :slides="slides" :autoplay-interval="5000" />
    
    <!-- Categorías Destacadas -->
    <FeaturedCategories :categories="categories" />
    
    <!-- Productos Destacados -->
    <FeaturedProducts :products="featuredProducts" />
    
    <!-- Footer -->
    <AppFooter />
    
    <!-- Navegación móvil -->
    <MobileNavigation :user="user" />
  </div>
</template>