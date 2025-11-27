<script setup lang="ts">
const props = defineProps<{
  imagePreview: string | null
  colorName: string
}>()

const emit = defineEmits<{
  'upload': [event: Event]
  'remove': []
}>()
</script>

<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">
      Imagen de esta variante <span class="text-red-600">*</span>
    </label>
    
    <div class="flex items-start gap-4">
      <!-- Preview de imagen -->
      <div
        v-if="imagePreview"
        class="relative group"
      >
        <div class="w-28 h-28 border-2 border-gray-300 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
          <img
            :src="imagePreview"
            :alt="`Imagen de ${colorName || 'producto'}`"
            class="w-full h-full object-cover"
          />
        </div>
        <button
          type="button"
          @click="$emit('remove')"
          class="absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full hover:bg-red-700 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs font-bold shadow-lg"
        >
          ×
        </button>
      </div>
      
      <!-- Input de archivo -->
      <div class="flex-1">
        <label class="cursor-pointer">
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-purple-400 hover:bg-purple-50 transition-colors">
            <div class="flex items-center gap-3">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <div>
                <p class="text-sm font-medium text-gray-700">
                  {{ imagePreview ? 'Cambiar imagen' : 'Subir imagen' }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                  PNG, JPG, WEBP hasta 4MB
                </p>
              </div>
            </div>
          </div>
          <input
            type="file"
            accept="image/*"
            @change="$emit('upload', $event)"
            class="hidden"
          />
        </label>
      </div>
    </div>
    <p class="mt-2 text-xs text-purple-600">
      💡 La imagen de la variante predeterminada se mostrará como imagen principal del producto
    </p>
  </div>
</template>