<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import CarouselSlide from '../Components_Welcome/CarouselSlide.vue'
import CarouselNavigation from '../Components_Welcome/CarouselNavigation.vue'
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
  <section class="relative overflow-hidden py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="relative rounded-3xl overflow-hidden shadow-2xl">
        <!-- Slides Container -->
        <div class="relative h-64 sm:h-80 lg:h-96">
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
    </div>
  </section>
</template>