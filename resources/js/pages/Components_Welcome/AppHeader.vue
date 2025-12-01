<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import ShoppingCart from '@/components/ShoppingCart.vue'

type User = {
  name: string
  apellido_paterno?: string
  role?: string
}

interface Props {
  user: User | null
  cartCount?: number
}

const props = withDefaults(defineProps<Props>(), {
  cartCount: 0
})

// Estado del carrito
const isCartOpen = ref(false)
const localCartCount = ref(props.cartCount)

// Funciones del carrito
const openCart = () => {
  isCartOpen.value = true
}

const closeCart = () => {
  isCartOpen.value = false
}

const updateCartCount = (count: number) => {
  localCartCount.value = count
}

// Event listener para actualización del carrito en tiempo real
const handleCartUpdate = (event: CustomEvent) => {
  const { count } = event.detail
  localCartCount.value = count
  
  // Animación visual opcional
  triggerCartAnimation()
}

const handleCartItemAdded = () => {
  // Animación cuando se añade un item
  triggerCartAnimation()
}

// Animación del badge
const isAnimating = ref(false)
const triggerCartAnimation = () => {
  isAnimating.value = true
  setTimeout(() => {
    isAnimating.value = false
  }, 600)
}

// Utilidad para mostrar nombre completo
const getUserDisplayName = (user: User | null) => {
  if (!user) return ''
  return user.apellido_paterno 
    ? `${user.name} ${user.apellido_paterno}`
    : user.name
}

// Lifecycle hooks
onMounted(() => {
  // Escuchar eventos personalizados de actualización del carrito
  window.addEventListener('cart:updated', handleCartUpdate as EventListener)
  window.addEventListener('cart:item-added', handleCartItemAdded as EventListener)
})

onUnmounted(() => {
  // Limpiar listeners
  window.removeEventListener('cart:updated', handleCartUpdate as EventListener)
  window.removeEventListener('cart:item-added', handleCartItemAdded as EventListener)
})
</script>

<template>
  <header class="w-full bg-white/90 backdrop-blur-md shadow-sm border-b border-purple-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <nav class="flex items-center justify-between py-4">
        <!-- Brand -->
        <div class="flex items-center space-x-3 flex-shrink-0">
          <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center shadow-lg">
            <span class="text-white font-bold text-sm sm:text-lg">🧸</span>
          </div>
          <span class="text-lg sm:text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
            Crochet Dreams
          </span>
        </div>

        <!-- Center Navigation Menu -->
        <div class="hidden lg:flex items-center space-x-8">
          <Link href="/" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">INICIO</Link>
          <Link href="/tienda" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">TIENDA</Link>
          <Link href="/nosotros" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">NOSOTROS</Link>
          <Link href="/pedidos" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">PEDIDOS PERSONALES</Link>
        </div>

        <!-- Right side actions -->
        <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
          <!-- User Actions -->
          <template v-if="user">
            <div class="flex items-center space-x-3 bg-purple-50 hover:bg-purple-100 rounded-xl px-3 py-2 transition-colors group">
              <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                {{ user.name?.charAt(0)?.toUpperCase() }}
              </div>
              
              <div class="flex items-center space-x-2">
                <div class="hidden sm:block text-sm">
                  <p class="font-medium text-gray-900 group-hover:text-purple-600 transition-colors">
                    {{ getUserDisplayName(user) }}
                  </p>
                  <p class="text-xs text-gray-500 capitalize">
                    {{ user.role || 'usuario' }}
                  </p>
                </div>
                
                <Link 
                  :href="user.role === 'admin' ? '/admin' : '/dashboard'" 
                  class="flex items-center space-x-1.5 text-gray-600 hover:text-purple-600 transition-colors bg-white hover:bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-200 hover:border-purple-300 shadow-sm"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="user.role === 'admin'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path v-if="user.role === 'admin'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span class="text-sm font-medium">
                    {{ user.role === 'admin' ? 'Admin' : 'Mi Panel' }}
                  </span>
                </Link>
              </div>
            </div>
          </template>

          <template v-else>
            <Link 
              href="/login" 
              class="text-gray-600 hover:text-purple-600 transition-colors font-medium px-4 py-2 border border-purple-300 rounded-full hover:border-purple-500 text-sm bg-white hover:bg-purple-50"
            >
              <span class="hidden sm:inline">Iniciar Sesión</span>
              <span class="sm:hidden">Login</span>
            </Link>
          </template>

          <!-- Cart Button -->
          <div class="relative">
            <button 
              @click="openCart"
              class="p-2.5 text-gray-600 hover:text-purple-600 transition-all bg-purple-50 hover:bg-purple-100 rounded-xl border border-purple-200 hover:border-purple-300 transform hover:scale-110 duration-200"
              :class="{ 'animate-bounce': isAnimating }"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m2.6 8L6 7H3m4 6v6a2 2 0 002 2h8a2 2 0 002-2v-6M9 19h6" />
              </svg>
            </button>
            
            <!-- Cart Badge -->
            <span 
              v-if="localCartCount > 0"
              class="absolute -top-1 -right-1 bg-gradient-to-r from-pink-500 to-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium shadow-lg transition-transform duration-300"
              :class="{ 'scale-125 animate-pulse': isAnimating }"
            >
              {{ localCartCount > 99 ? '99+' : localCartCount }}
            </span>
          </div>
        </div>
      </nav>
    </div>

    <!-- Shopping Cart Component -->
    <ShoppingCart
      :is-open="isCartOpen"
      @close="closeCart"
      @update-cart="updateCartCount"
    />
  </header>
</template>

<style scoped>
@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce {
  animation: bounce 0.6s ease-in-out;
}
</style>