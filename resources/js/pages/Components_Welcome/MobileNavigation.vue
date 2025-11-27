<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

type User = {
  name: string
  apellido_paterno?: string
  role?: string
}

interface Props {
  user: User | null
}

defineProps<Props>()
</script>

<template>
  <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-purple-200 px-6 py-3 z-40 shadow-lg">
    <div class="flex justify-around">
      <Link href="/" class="flex flex-col items-center text-purple-600 font-semibold transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="text-xs mt-1">Inicio</span>
      </Link>

      <Link href="/Shop" class="flex flex-col items-center text-gray-600 hover:text-purple-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z"></path>
        </svg>
        <span class="text-xs mt-1">Tienda</span>
      </Link>

      <Link href="/about" class="flex flex-col items-center text-gray-600 hover:text-purple-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-xs mt-1">Nosotros</span>
      </Link>

      <Link 
        :href="user ? (user.role === 'admin' ? '/admin' : '/dashboard') : '/login'" 
        class="flex flex-col items-center text-gray-600 hover:text-purple-600 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-xs mt-1">{{ user ? 'Perfil' : 'Login' }}</span>
      </Link>
    </div>
  </div>
</template>