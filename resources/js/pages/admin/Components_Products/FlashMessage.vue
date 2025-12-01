<script setup lang="ts">
defineProps<{
  flash?: { success?: string; error?: string }
}>()
</script>

<template>
  <div>
    <Transition name="slide">
      <div v-if="flash?.success" class="mb-4 p-3 bg-green-50 border-l-4 border-green-600 rounded text-sm">
        <div class="flex items-center">
          <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-green-800 font-medium">{{ flash.success }}</span>
        </div>
      </div>
    </Transition>
    
    <Transition name="slide">
      <div v-if="flash?.error" class="mb-4 p-3 bg-red-50 border-l-4 border-red-600 rounded text-sm">
        <div class="flex items-center">
          <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <span class="text-red-800 font-medium">{{ flash.error }}</span>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.slide-enter-active { transition: all 0.3s ease-out; }
.slide-leave-active { transition: all 0.2s ease-in; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-10px); }
</style>