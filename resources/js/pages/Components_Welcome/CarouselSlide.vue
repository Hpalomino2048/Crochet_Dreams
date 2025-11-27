<script setup lang="ts">
import type { Slide } from '../Components_Welcome/SeaonCarousel.vue'

interface Props {
  slide: Slide
  isActive: boolean
}

defineProps<Props>()
</script>

<template>
  <div 
    :class="[
      'absolute inset-0 transition-all duration-700 ease-in-out',
      isActive ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-full'
    ]"
  >
    <div :class="['h-full bg-gradient-to-r', slide.color, 'flex items-center']">
      <div class="max-w-7xl mx-auto px-8 sm:px-12 w-full">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
          <!-- Content -->
          <div class="flex-1 text-center lg:text-left">
            <span class="inline-block bg-white/80 backdrop-blur-sm text-purple-600 px-4 py-2 rounded-full text-sm font-semibold mb-4">
              {{ slide.discount }}
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-3">
              {{ slide.title }}
            </h2>
            <p class="text-lg sm:text-xl text-gray-700 mb-6">
              {{ slide.subtitle }}
            </p>
            <button class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-full font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              Ver Ofertas
            </button>
          </div>

          <!-- Image/Emoji -->
          <div class="flex-1 flex justify-center">
            <div class="text-8xl sm:text-9xl lg:text-[12rem] animate-bounce-slow">
              {{ slide.image }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 3s ease-in-out infinite;
}
</style>