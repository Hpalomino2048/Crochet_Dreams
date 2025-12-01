<script setup lang="ts">
const emit = defineEmits<{
  prev: []
  next: []
}>()
</script>

<template>
  <div>
    <!-- Previous Button -->
    <button 
      @click="emit('prev')"
      class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-purple-600 p-3 rounded-full shadow-lg transition-all hover:scale-110 z-10"
      aria-label="Anterior"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <!-- Next Button -->
    <button 
      @click="emit('next')"
      class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-purple-600 p-3 rounded-full shadow-lg transition-all hover:scale-110 z-10"
      aria-label="Siguiente"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
</template>