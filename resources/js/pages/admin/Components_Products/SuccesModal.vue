<script setup lang="ts">
defineProps<{
  show: boolean
}>()
</script>

<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="bg-white rounded-xl p-8 shadow-2xl max-w-sm mx-4 text-center animate-scale">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full mx-auto flex items-center justify-center">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h3 class="mt-4 text-xl font-semibold text-gray-900">Producto Creado</h3>
        <p class="mt-1 text-sm text-gray-600">Registrado exitosamente</p>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.animate-scale {
  animation: scale 0.3s ease-out;
}

@keyframes scale {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>