<script setup lang="ts">
interface Props {
  total: number
  current: number
}

defineProps<Props>()

const emit = defineEmits<{
  goTo: [index: number]
}>()
</script>

<template>
  <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
    <button 
      v-for="index in total" 
      :key="index"
      @click="emit('goTo', index - 1)"
      :class="[
        'w-2.5 h-2.5 rounded-full transition-all',
        current === index - 1 ? 'bg-purple-600 w-8' : 'bg-white/60 hover:bg-white'
      ]"
      :aria-label="`Ir a slide ${index}`"
      :aria-current="current === index - 1"
    />
  </div>
</template>