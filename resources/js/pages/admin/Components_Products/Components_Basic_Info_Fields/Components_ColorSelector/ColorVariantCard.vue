<script setup lang="ts">
import type { ColorVariant } from './ColorVariantTypes'
import { AVAILABLE_COLORS } from './ColorVariantTypes'
import ImageUploader from './ImageUploader.vue'

const props = defineProps<{
  variant: ColorVariant
  index: number
  error?: string
}>()

const emit = defineEmits<{
  'update': [field: keyof ColorVariant, value: any]
  'remove': []
  'upload-image': [event: Event]
  'remove-image': []
}>()

// Obtener URL de preview
const getImagePreview = (): string | null => {
  if (props.variant.imagePreview) return props.variant.imagePreview
  if (typeof props.variant.image === 'string') return `/storage/${props.variant.image}`
  return null
}
</script>

<template>
  <div class="border border-gray-200 rounded-lg p-5 bg-white shadow-sm hover:shadow-md transition-all">
    <!-- Header de variante -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center gap-2">
        <div class="flex items-center justify-center w-7 h-7 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
          {{ index + 1 }}
        </div>
        <span class="text-sm font-medium text-gray-700">
          {{ variant.name || 'Nueva variante' }}
        </span>
        <span
          v-if="variant.is_default"
          class="px-2 py-0.5 text-xs font-medium text-purple-700 bg-purple-100 rounded-full"
        >
          ⭐ Predeterminado
        </span>
      </div>
      
      <button
        type="button"
        @click="$emit('remove')"
        class="text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded text-sm font-medium transition-colors"
        :title="`Eliminar variante ${index + 1}`"
      >
        Eliminar
      </button>
    </div>

    <!-- Campos del formulario -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Nombre del color -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Nombre del Color <span class="text-red-600">*</span>
        </label>
        <select
          :value="variant.name"
          @change="$emit('update', 'name', ($event.target as HTMLSelectElement).value)"
          required
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-shadow"
        >
          <option value="">Seleccionar color</option>
          <option
            v-for="availColor in AVAILABLE_COLORS"
            :key="availColor.name"
            :value="availColor.name"
          >
            {{ availColor.name }}
          </option>
        </select>
      </div>

      <!-- Código Hex -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Código Hexadecimal
        </label>
        <div class="flex gap-2">
          <div
            v-if="variant.code"
            class="w-10 h-10 rounded border-2 border-gray-300 flex-shrink-0 shadow-sm"
            :style="{ backgroundColor: variant.code }"
            :title="variant.code"
          />
          <input
            type="text"
            :value="variant.code"
            @input="$emit('update', 'code', ($event.target as HTMLInputElement).value)"
            placeholder="#FF0000"
            maxlength="7"
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-shadow"
          />
        </div>
      </div>

      <!-- Stock -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Stock de esta variante <span class="text-red-600">*</span>
        </label>
        <input
          type="number"
          :value="variant.stock"
          @input="$emit('update', 'stock', parseInt(($event.target as HTMLInputElement).value) || 0)"
          min="0"
          step="1"
          required
          placeholder="Ej: 50"
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-shadow"
        />
        <p class="mt-1 text-xs text-gray-500">
          Se sumará al stock total del producto
        </p>
      </div>

      <!-- Color predeterminado -->
      <div class="flex items-center pt-6">
        <label class="flex items-center cursor-pointer group">
          <input
            type="checkbox"
            :checked="variant.is_default"
            @change="$emit('update', 'is_default', ($event.target as HTMLInputElement).checked)"
            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-600 cursor-pointer"
          />
          <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900 transition-colors">
            Establecer como predeterminado
          </span>
        </label>
      </div>

      <!-- Imagen del producto -->
      <div class="md:col-span-2">
        <ImageUploader
          :image-preview="getImagePreview()"
          :color-name="variant.name"
          @upload="$emit('upload-image', $event)"
          @remove="$emit('remove-image')"
        />
      </div>
    </div>

    <!-- Mensaje de error -->
    <div
      v-if="error"
      class="mt-3 p-3 text-sm text-red-700 bg-red-50 rounded-lg border border-red-200"
    >
      {{ error }}
    </div>
  </div>
</template>