<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps<{
  form: InertiaForm<any>
}>()

const thumbnailPreview = ref<string | null>(null)
const imagesPreview = ref<string[]>([])

// Cargar previews si estamos editando
if (props.form.thumbnail && typeof props.form.thumbnail === 'string') {
  thumbnailPreview.value = props.form.thumbnail
}

if (props.form.image && Array.isArray(props.form.image)) {
  imagesPreview.value = props.form.image.filter((img: any) => typeof img === 'string')
}

const remainingImages = computed(() => {
  const currentCount = Array.isArray(props.form.image) ? props.form.image.length : 0
  return 10 - currentCount
})

const hasImages = computed(() => imagesPreview.value.length > 0)

function createPreview(file: File, callback: (url: string) => void) {
  const reader = new FileReader()
  reader.onload = (e) => callback(e.target?.result as string)
  reader.readAsDataURL(file)
}

function handleThumbnail(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    if (file.size > 4 * 1024 * 1024) {
      alert('La imagen no debe superar 4MB')
      return
    }
    props.form.thumbnail = file
    createPreview(file, (url) => thumbnailPreview.value = url)
  }
}

function removeThumbnail() {
  props.form.thumbnail = null
  thumbnailPreview.value = null
}

function handleImages(event: Event) {
  const target = event.target as HTMLInputElement
  const files = Array.from(target.files || [])
  
  // Inicializar array si no existe
  if (!Array.isArray(props.form.image)) {
    props.form.image = []
  }
  
  const currentCount = props.form.image.length
  
  if (files.length + currentCount > 10) {
    alert('Máximo 10 imágenes permitidas')
    return
  }

  const validFiles = files.filter(file => {
    if (file.size > 4 * 1024 * 1024) {
      alert(`${file.name} supera 4MB`)
      return false
    }
    return true
  })
  
  // Agregar archivos al form
  validFiles.forEach(file => {
    props.form.image.push(file)
    createPreview(file, (url) => imagesPreview.value.push(url))
  })
  
  target.value = ''
}

function removeImage(index: number) {
  if (Array.isArray(props.form.image)) {
    props.form.image.splice(index, 1)
  }
  imagesPreview.value.splice(index, 1)
}
</script>

<template>
  <div>
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div class="flex-1">
          <h4 class="text-sm font-medium text-blue-900 mb-1">Producto sin variantes</h4>
          <p class="text-sm text-blue-700">
            Esta sección solo aparece cuando el producto no tiene variantes de color. Sube aquí la imagen principal del producto.
          </p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Thumbnail -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Imagen Principal <span class="text-red-600">*</span>
        </label>
        <Transition name="fade" mode="out-in">
          <div v-if="thumbnailPreview" class="relative group">
            <img :src="thumbnailPreview" alt="Preview" class="w-full h-48 object-cover rounded-lg border-2 border-gray-300 shadow-sm" />
            <button 
              type="button" 
              @click="removeThumbnail"
              class="absolute top-2 right-2 bg-red-600 text-white rounded-lg px-3 py-1.5 text-sm font-medium hover:bg-red-700 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
            >
              Eliminar
            </button>
          </div>
          <label v-else class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm font-medium text-gray-700">Clic para seleccionar</p>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP (máx. 4MB)</p>
            <input 
              type="file" 
              class="hidden" 
              @change="handleThumbnail" 
              accept="image/jpeg,image/jpg,image/png,image/webp"
              required
            />
          </label>
        </Transition>
        <p v-if="form.errors.thumbnail" class="mt-2 text-xs text-red-600">
          {{ form.errors.thumbnail }}
        </p>
      </div>

      <!-- Galería -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Galería Adicional 
          <span class="text-xs text-gray-500">({{ form.image?.length || 0 }}/10)</span>
        </label>
        <div class="space-y-3">
          <div v-if="hasImages" class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto p-1 bg-gray-50 rounded-lg border border-gray-200">
            <TransitionGroup name="list">
              <div 
                v-for="(preview, i) in imagesPreview" 
                :key="`img-${i}-${preview}`" 
                class="relative group"
              >
                <img 
                  :src="preview" 
                  alt="Preview" 
                  class="w-full h-20 object-cover rounded border-2 border-gray-300 shadow-sm" 
                />
                <button 
                  type="button" 
                  @click="removeImage(i)"
                  class="absolute -top-1.5 -right-1.5 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                >
                  ×
                </button>
              </div>
            </TransitionGroup>
          </div>

          <label 
            :class="[
              'flex items-center justify-center w-full px-4 py-3 rounded-lg cursor-pointer text-sm font-medium border-2 border-dashed transition-all',
              (form.image?.length || 0) >= 10 
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-300' 
                : 'bg-purple-50 text-purple-700 hover:bg-purple-100 border-purple-300 hover:border-purple-400'
            ]"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ (form.image?.length || 0) >= 10 ? 'Límite alcanzado (10)' : `Agregar imágenes (${remainingImages} disponibles)` }}
            <input 
              type="file" 
              @change="handleImages" 
              accept="image/jpeg,image/jpg,image/png,image/webp"
              multiple 
              :disabled="(form.image?.length || 0) >= 10" 
              class="hidden" 
            />
          </label>
          <p class="text-xs text-gray-500 text-center">
            Imágenes adicionales para mostrar más ángulos del producto
          </p>
        </div>
        <p v-if="form.errors.image" class="mt-2 text-xs text-red-600">
          {{ form.errors.image }}
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.list-move, .list-enter-active, .list-leave-active { transition: all 0.3s ease; }
.list-enter-from { opacity: 0; transform: scale(0.8); }
.list-leave-to { opacity: 0; transform: scale(0.8); }
.list-leave-active { position: absolute; }

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>