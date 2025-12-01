<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import CarouselSlide from './CarouselSlide.vue'
import CarouselNavigation from './CarouselNavigation.vue'
import CarouselDots from './CarouselDots.vue'

export interface Slide {
  id: number
  title: string
  subtitle: string
  discount: string
  image: string
  color: string
}

interface Props {
  slides: Slide[]
  autoplayInterval?: number
}

const props = withDefaults(defineProps<Props>(), {
  autoplayInterval: 5000
})

const currentSlide = ref(0)
let autoplayTimer: number | null = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % props.slides.length
}

const prevSlide = () => {
  currentSlide.value = currentSlide.value === 0 
    ? props.slides.length - 1 
    : currentSlide.value - 1
}

const goToSlide = (index: number) => {
  currentSlide.value = index
}

onMounted(() => {
  if (props.autoplayInterval > 0) {
    autoplayTimer = window.setInterval(nextSlide, props.autoplayInterval)
  }
})

onUnmounted(() => {
  if (autoplayTimer) {
    clearInterval(autoplayTimer)
  }
})
</script>

<template>
  <!-- Sin padding ni max-width - Ancho completo -->
  <section class="relative overflow-hidden">
    <div class="relative">
      <!-- Slides Container - Sin bordes redondeados para ancho completo -->
      <div class="relative h-[400px] sm:h-[450px] lg:h-[500px]">
        <CarouselSlide
          v-for="(slide, index) in slides"
          :key="slide.id"
          :slide="slide"
          :is-active="currentSlide === index"
        />
      </div>
      
      <!-- Navigation -->
      <CarouselNavigation 
        @prev="prevSlide"
        @next="nextSlide"
      />
      
      <!-- Dots -->
      <CarouselDots
        :total="slides.length"
        :current="currentSlide"
        @go-to="goToSlide"
      />
    </div>
  </section>
</template>