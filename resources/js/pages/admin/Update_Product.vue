<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

interface ProductColor {
  id: number | null
  name: string
  code: string
  stock: number
  is_default: boolean
  image: string | null
  gallery: string[]
  newImage?: File | null
  newGalleryImages?: File[]
  removedGalleryImages?: string[]
  _isNew?: boolean
}

interface Products {
  id: number
  name: string
  sku: string
  descriptions: string
  price: number
  currency: string
  stock: number
  thumbnail: string | null
  colors: ProductColor[]
  has_variants: boolean
  created_at: string
}

interface EditFormData {
  descriptions: string
  price: number
  stock: number
  colors: ProductColor[]
  removedColors: number[]
}

const props = withDefaults(defineProps<{
  products?: Products[]
}>(), {
  products: () => [] as Products[],
})

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Productos', href: '/admin/products' },
  { title: 'Actualizar productos', href: '#' },
]

const searchQuery = ref('')
const sortBy = ref('created_at')
const sortOrder = ref<'asc' | 'desc'>('desc')
const showEditModal = ref(false)
const selectedProduct = ref<Products | null>(null)
const isUpdating = ref(false)
const showColorDeleteConfirm = ref(false)
const colorToDelete = ref<number | null>(null)

const formData = ref<EditFormData>({
  descriptions: '',
  price: 0,
  stock: 0,
  colors: [],
  removedColors: []
})

const errors = ref<Record<string, string>>({})
const imagePreviewUrls = ref<Record<string, string>>({})

const filteredProducts = computed<Products[]>(() => {
  if (!props.products || !Array.isArray(props.products)) {
    return []
  }

  let filtered = [...props.products]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p =>
      p.name?.toLowerCase().includes(query) ||
      p.sku?.toLowerCase().includes(query) ||
      p.descriptions?.toLowerCase().includes(query)
    )
  }

  filtered = filtered.sort((a, b) => {
    let aVal: any
    let bVal: any

    switch (sortBy.value) {
      case 'name':
        aVal = a.name?.toLowerCase() || ''
        bVal = b.name?.toLowerCase() || ''
        break
      case 'sku':
        aVal = a.sku || ''
        bVal = b.sku || ''
        break
      case 'stock':
        aVal = a.stock || 0
        bVal = b.stock || 0
        break
      case 'created_at':
        aVal = a.created_at ? new Date(a.created_at).getTime() : 0
        bVal = b.created_at ? new Date(b.created_at).getTime() : 0
        break
      default:
        return 0
    }

    if (aVal < bVal) return sortOrder.value === 'asc' ? -1 : 1
    if (aVal > bVal) return sortOrder.value === 'asc' ? 1 : -1
    return 0
  })

  return filtered
})

/**
 * ✅ CORREGIDO: Inicializa correctamente todos los campos
 */
const openEditModal = (product: Products) => {
  selectedProduct.value = product
  
  // Limpiar previews anteriores
  Object.values(imagePreviewUrls.value).forEach(url => URL.revokeObjectURL(url))
  imagePreviewUrls.value = {}
  
  formData.value = {
    descriptions: product.descriptions || '',
    price: product.price || 0,
    stock: product.stock || 0,
    colors: (product.colors || []).map(color => ({
      id: color.id,
      name: color.name,
      code: color.code,
      stock: color.stock,
      is_default: color.is_default,
      image: color.image,
      gallery: [...(color.gallery || [])], // Copia del array original
      newImage: null,
      newGalleryImages: [],
      removedGalleryImages: [], // ✅ Siempre iniciar vacío
      _isNew: false
    })),
    removedColors: []
  }
  
  errors.value = {}
  showEditModal.value = true
}

const closeEditModal = () => {
  if (!isUpdating.value) {
    showEditModal.value = false
    selectedProduct.value = null
    formData.value = {
      descriptions: '',
      price: 0,
      stock: 0,
      colors: [],
      removedColors: []
    }
    errors.value = {}
    
    Object.values(imagePreviewUrls.value).forEach(url => URL.revokeObjectURL(url))
    imagePreviewUrls.value = {}
  }
}

const validateForm = (): boolean => {
  errors.value = {}
  
  if (!formData.value.descriptions.trim()) {
    errors.value.descriptions = 'La descripción es requerida'
  }
  
  if (formData.value.price <= 0) {
    errors.value.price = 'El precio debe ser mayor a 0'
  }
  
  if (formData.value.stock < 0) {
    errors.value.stock = 'El stock no puede ser negativo'
  }
  
  formData.value.colors.forEach((color, index) => {
    if (!color.name.trim()) {
      errors.value[`color_${index}_name`] = 'El nombre del color es requerido'
    }
    
    if (!color.code.match(/^#[0-9A-Fa-f]{6}$/)) {
      errors.value[`color_${index}_code`] = 'Código hex inválido (ej: #FF5733)'
    }
    
    if (color.stock < 0) {
      errors.value[`color_${index}_stock`] = 'El stock no puede ser negativo'
    }
  })
  
  return Object.keys(errors.value).length === 0
}

const addNewColor = () => {
  formData.value.colors.push({
    id: null,
    name: '',
    code: '#000000',
    stock: 0,
    is_default: false,
    image: null,
    gallery: [],
    newImage: null,
    newGalleryImages: [],
    removedGalleryImages: [],
    _isNew: true
  })
}

const confirmDeleteColor = (index: number) => {
  colorToDelete.value = index
  showColorDeleteConfirm.value = true
}

const deleteColor = () => {
  if (colorToDelete.value === null) return
  
  const color = formData.value.colors[colorToDelete.value]
  
  if (color.id) {
    formData.value.removedColors.push(color.id)
  }
  
  formData.value.colors.splice(colorToDelete.value, 1)
  
  showColorDeleteConfirm.value = false
  colorToDelete.value = null
}

const handleColorImageUpload = (event: Event, colorIndex: number) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  
  if (file) {
    if (!file.type.startsWith('image/')) {
      errors.value[`color_${colorIndex}_image`] = 'Solo se permiten imágenes'
      return
    }
    
    if (file.size > 5 * 1024 * 1024) {
      errors.value[`color_${colorIndex}_image`] = 'La imagen no debe superar 5MB'
      return
    }
    
    formData.value.colors[colorIndex].newImage = file
    
    const previewUrl = URL.createObjectURL(file)
    imagePreviewUrls.value[`color_${colorIndex}_main`] = previewUrl
    
    delete errors.value[`color_${colorIndex}_image`]
  }
}

const handleGalleryImagesUpload = (event: Event, colorIndex: number) => {
  const input = event.target as HTMLInputElement
  const files = Array.from(input.files || [])
  
  if (files.length === 0) return
  
  const invalidFiles = files.filter(file => !file.type.startsWith('image/'))
  if (invalidFiles.length > 0) {
    errors.value[`color_${colorIndex}_gallery`] = 'Solo se permiten imágenes'
    return
  }
  
  const oversizedFiles = files.filter(file => file.size > 5 * 1024 * 1024)
  if (oversizedFiles.length > 0) {
    errors.value[`color_${colorIndex}_gallery`] = 'Las imágenes no deben superar 5MB'
    return
  }
  
  if (!formData.value.colors[colorIndex].newGalleryImages) {
    formData.value.colors[colorIndex].newGalleryImages = []
  }
  formData.value.colors[colorIndex].newGalleryImages!.push(...files)
  
  // Crear previews para las nuevas imágenes
  files.forEach((file, fileIndex) => {
    const previewUrl = URL.createObjectURL(file)
    const currentNewCount = formData.value.colors[colorIndex].newGalleryImages!.length - files.length + fileIndex
    imagePreviewUrls.value[`color_${colorIndex}_new_gallery_${currentNewCount}`] = previewUrl
  })
  
  delete errors.value[`color_${colorIndex}_gallery`]
  
  // Limpiar el input para permitir seleccionar los mismos archivos de nuevo
  input.value = ''
}

/**
 * ✅ CORREGIDO: Solo marca para eliminar, no modifica el array gallery
 */
const removeExistingGalleryImage = (colorIndex: number, imagePath: string) => {
  const color = formData.value.colors[colorIndex]
  
  if (!color.removedGalleryImages) {
    color.removedGalleryImages = []
  }
  
  // Solo agregar a la lista de eliminadas si no está ya
  if (!color.removedGalleryImages.includes(imagePath)) {
    color.removedGalleryImages.push(imagePath)
  }
}

const removeNewGalleryImage = (colorIndex: number, fileIndex: number) => {
  const color = formData.value.colors[colorIndex]
  if (color.newGalleryImages && color.newGalleryImages.length > fileIndex) {
    // Revocar URL del preview
    const previewKey = `color_${colorIndex}_new_gallery_${fileIndex}`
    if (imagePreviewUrls.value[previewKey]) {
      URL.revokeObjectURL(imagePreviewUrls.value[previewKey])
      delete imagePreviewUrls.value[previewKey]
    }
    
    color.newGalleryImages.splice(fileIndex, 1)
  }
}

/**
 * ✅ CORREGIDO: Helper para obtener galería visible (filtrada)
 */
const getVisibleGallery = (color: ProductColor): string[] => {
  if (!color.gallery) return []
  const removed = color.removedGalleryImages || []
  return color.gallery.filter(img => !removed.includes(img))
}

/**
 * ✅ CORREGIDO: Envío correcto del FormData
 */
const updateProduct = async () => {
  if (!selectedProduct.value || isUpdating.value) return
  
  if (!validateForm()) {
    return
  }
  
  isUpdating.value = true
  
  const formDataToSend = new FormData()
  
  // Datos básicos
  formDataToSend.append('descriptions', formData.value.descriptions)
  formDataToSend.append('price', formData.value.price.toString())
  formDataToSend.append('stock', formData.value.stock.toString())
  formDataToSend.append('_method', 'PUT')
  
  // Procesar colores
  formData.value.colors.forEach((color, index) => {
    // ID del color (si existe)
    if (color.id) {
      formDataToSend.append(`colors[${index}][id]`, color.id.toString())
    }
    
    // Datos básicos del color
    formDataToSend.append(`colors[${index}][name]`, color.name)
    formDataToSend.append(`colors[${index}][code]`, color.code)
    formDataToSend.append(`colors[${index}][stock]`, color.stock.toString())
    formDataToSend.append(`colors[${index}][is_default]`, color.is_default ? '1' : '0')
    
    // Nueva imagen principal
    if (color.newImage) {
      formDataToSend.append(`colors[${index}][image]`, color.newImage)
    }
    
    // Nuevas imágenes de galería
    if (color.newGalleryImages && color.newGalleryImages.length > 0) {
      color.newGalleryImages.forEach((file, fileIndex) => {
        formDataToSend.append(`colors[${index}][gallery][${fileIndex}]`, file)
      })
    }
    
    // ✅ Imágenes de galería eliminadas (URLs completas)
    if (color.removedGalleryImages && color.removedGalleryImages.length > 0) {
      color.removedGalleryImages.forEach((path, pathIndex) => {
        formDataToSend.append(`colors[${index}][removed_gallery][${pathIndex}]`, path)
      })
    }
  })
  
  // Colores eliminados
  if (formData.value.removedColors.length > 0) {
    formData.value.removedColors.forEach((colorId, index) => {
      formDataToSend.append(`removed_colors[${index}]`, colorId.toString())
    })
  }
  
  router.post(`/admin/products/${selectedProduct.value.id}`, formDataToSend, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      showEditModal.value = false
      selectedProduct.value = null
      isUpdating.value = false
      
      Object.values(imagePreviewUrls.value).forEach(url => URL.revokeObjectURL(url))
      imagePreviewUrls.value = {}
    },
    onError: (err) => {
      console.error('Error al actualizar:', err)
      errors.value = err as Record<string, string>
      isUpdating.value = false
    }
  })
}

const truncateText = (text: string, maxLength: number) => {
  if (!text) return 'Sin descripción'
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}
</script>

<template>
  <Head title="Actualizar productos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-8 py-6 space-y-6">
      <!-- Header -->
      <div class="pb-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-indigo-600 ">
          Actualizar productos
        </h1>
        <p class="mt-1 text-sm text-gray-600">
          Selecciona el producto que deseas editar. Podrás modificar la información, variantes e imágenes.
        </p>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <div class="relative">
              <svg 
                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por nombre, SKU o descripción..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
          </div>

          <div class="w-56">
            <select
              v-model="sortBy"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            >
              <option value="created_at">Fecha de creación</option>
              <option value="name">Nombre</option>
              <option value="sku">SKU</option>
              <option value="stock">Stock</option>
            </select>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-4 text-sm">
          <span class="font-medium text-gray-700">
            {{ filteredProducts.length }} producto{{ filteredProducts.length !== 1 ? 's' : '' }} encontrado{{ filteredProducts.length !== 1 ? 's' : '' }}
          </span>
          <span v-if="searchQuery" class="text-gray-500">
            · Filtrando resultados
          </span>
        </div>
      </div>

      <!-- Tabla de productos -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div v-if="filteredProducts.length > 0">
          <div class="bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-12 gap-6 px-6 py-3.5">
              <div class="col-span-1 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Imagen
              </div>
              <div class="col-span-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Producto
              </div>
              <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                SKU
              </div>
              <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Precio
              </div>
              <div class="col-span-1 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Stock
              </div>
              <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Variantes
              </div>
              <div class="col-span-1 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                Acción
              </div>
            </div>
          </div>

          <div class="divide-y divide-gray-200">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id"
              class="hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="grid grid-cols-12 gap-6 px-6 py-4 items-center">
                <div class="col-span-1">
                  <div class="w-12 h-12 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-200">
                    <img
                      v-if="product.thumbnail"
                      :src="product.thumbnail"
                      :alt="product.name"
                      class="w-full h-full object-cover"
                    />
                    <svg
                      v-else
                      class="w-6 h-6 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  </div>
                </div>

                <div class="col-span-3">
                  <h3 class="text-sm font-semibold text-gray-900 leading-tight">
                    {{ product.name }}
                  </h3>
                  <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                    {{ truncateText(product.descriptions, 60) }}
                  </p>
                </div>

                <div class="col-span-2">
                  <p class="text-sm font-mono text-gray-700 font-medium">
                    {{ product.sku }}
                  </p>
                </div>

                <div class="col-span-2">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ product.currency }} ${{ product.price }}
                  </p>
                </div>

                <div class="col-span-1">
                  <span 
                    class="inline-flex items-center justify-center min-w-[3rem] px-2.5 py-1 rounded-md text-xs font-semibold"
                    :class="product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ product.stock }}
                  </span>
                </div>

                <div class="col-span-2">
                  <div v-if="product.colors && product.colors.length > 0" class="flex items-center gap-2.5">
                    <div class="flex items-center -space-x-1.5">
                      <div
                        v-for="(color, cIndex) in product.colors.slice(0, 3)"
                        :key="color.id ?? cIndex"
                        :style="{ backgroundColor: color.code }"
                        class="w-5 h-5 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-200"
                        :title="color.name"
                      />
                    </div>
                    <span class="text-xs text-gray-600 font-medium">
                      {{ product.colors.length }} color{{ product.colors.length !== 1 ? 'es' : '' }}
                    </span>
                  </div>
                  <span v-else class="text-xs text-gray-400 font-medium">Sin variantes</span>
                </div>

                <div class="col-span-1 text-center">
                  <button
                    @click="openEditModal(product)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-blue-600 hover:bg-blue-50 transition-colors duration-150"
                    title="Editar producto"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div 
          v-else 
          class="p-16 text-center"
        >
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
              />
            </svg>
          </div>
          <h3 class="text-base font-semibold text-gray-900">
            No se encontraron productos
          </h3>
          <p class="mt-2 text-sm text-gray-600 max-w-sm mx-auto">
            {{ searchQuery ? 'Intenta con otros términos de búsqueda' : 'No hay productos registrados en el sistema' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Modal de edición -->
    <Teleport to="body">
      <div
        v-if="showEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="closeEditModal"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div class="flex min-h-full items-start justify-center p-4 py-8">
          <div
            v-if="showEditModal && selectedProduct"
            class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto"
            @click.stop
          >
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl z-10">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-gray-900">
                      Actualizar producto
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ selectedProduct.name }}
                    </p>
                  </div>
                </div>
                <button
                  @click="closeEditModal"
                  :disabled="isUpdating"
                  class="text-gray-400 hover:text-gray-600 transition-colors disabled:opacity-50"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
              <div class="space-y-6">
                <!-- Información básica -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <h4 class="text-sm font-semibold text-gray-700 mb-3">Información básica</h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Nombre</label>
                      <p class="text-sm font-semibold text-gray-900">{{ selectedProduct.name }}</p>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">SKU</label>
                      <p class="text-sm font-mono font-medium text-gray-900">{{ selectedProduct.sku }}</p>
                    </div>
                  </div>
                </div>

                <!-- Descripción -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción *
                  </label>
                  <textarea
                    v-model="formData.descriptions"
                    rows="3"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    :class="errors.descriptions ? 'border-red-300' : 'border-gray-300'"
                    placeholder="Descripción del producto..."
                  />
                  <p v-if="errors.descriptions" class="mt-1 text-xs text-red-600">
                    {{ errors.descriptions }}
                  </p>
                </div>

                <!-- Precio y Stock -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Precio ({{ selectedProduct.currency }}) *
                    </label>
                    <input
                      v-model.number="formData.price"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      :class="errors.price ? 'border-red-300' : 'border-gray-300'"
                    />
                    <p v-if="errors.price" class="mt-1 text-xs text-red-600">
                      {{ errors.price }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Stock total *
                    </label>
                    <input
                      v-model.number="formData.stock"
                      type="number"
                      min="0"
                      class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      :class="errors.stock ? 'border-red-300' : 'border-gray-300'"
                    />
                    <p v-if="errors.stock" class="mt-1 text-xs text-red-600">
                      {{ errors.stock }}
                    </p>
                  </div>
                </div>

                <!-- Variantes de color -->
                <div>
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-900">Variantes de color</h4>
                    <button
                      @click="addNewColor"
                      type="button"
                      class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Agregar color
                    </button>
                  </div>

                  <div class="space-y-4">
                    <div
                      v-for="(color, index) in formData.colors"
                      :key="index"
                      class="border border-gray-200 rounded-lg p-4 bg-white"
                    >
                      <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                          <div
                            :style="{ backgroundColor: color.code }"
                            class="w-10 h-10 rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200"
                          />
                          <div>
                            <p class="text-sm font-semibold text-gray-900">
                              {{ color.name || 'Nuevo color' }}
                            </p>
                            <p class="text-xs text-gray-500 font-mono">
                              {{ color.code }}
                            </p>
                          </div>
                        </div>
                        <button
                          @click="confirmDeleteColor(index)"
                          type="button"
                          class="text-red-600 hover:bg-red-50 p-1.5 rounded-md transition-colors"
                          title="Eliminar color"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                          </svg>
                        </button>
                      </div>

                      <div class="grid grid-cols-3 gap-3 mb-4">
                        <div>
                          <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nombre *
                          </label>
                          <input
                            v-model="color.name"
                            type="text"
                            class="w-full px-2.5 py-1.5 text-sm border rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="errors[`color_${index}_name`] ? 'border-red-300' : 'border-gray-300'"
                            placeholder="ej: Rojo"
                          />
                          <p v-if="errors[`color_${index}_name`]" class="mt-1 text-xs text-red-600">
                            {{ errors[`color_${index}_name`] }}
                          </p>
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-gray-600 mb-1">
                            Código hex *
                          </label>
                          <input
                            v-model="color.code"
                            type="text"
                            class="w-full px-2.5 py-1.5 text-sm border rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono"
                            :class="errors[`color_${index}_code`] ? 'border-red-300' : 'border-gray-300'"
                            placeholder="#FF5733"
                          />
                          <p v-if="errors[`color_${index}_code`]" class="mt-1 text-xs text-red-600">
                            {{ errors[`color_${index}_code`] }}
                          </p>
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-gray-600 mb-1">
                            Stock *
                          </label>
                          <input
                            v-model.number="color.stock"
                            type="number"
                            min="0"
                            class="w-full px-2.5 py-1.5 text-sm border rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :class="errors[`color_${index}_stock`] ? 'border-red-300' : 'border-gray-300'"
                          />
                          <p v-if="errors[`color_${index}_stock`]" class="mt-1 text-xs text-red-600">
                            {{ errors[`color_${index}_stock`] }}
                          </p>
                        </div>
                      </div>

                      <!-- Imagen principal -->
                      <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-2">
                          Imagen principal
                        </label>
                        <div class="flex items-center gap-3">
                          <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-200">
                            <img
                              v-if="imagePreviewUrls[`color_${index}_main`]"
                              :src="imagePreviewUrls[`color_${index}_main`]"
                              class="w-full h-full object-cover"
                            />
                            <img
                              v-else-if="color.image"
                              :src="color.image"
                              class="w-full h-full object-cover"
                            />
                            <svg
                              v-else
                              class="w-8 h-8 text-gray-300"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                              />
                            </svg>
                          </div>
                          <div class="flex-1">
                            <label class="cursor-pointer">
                              <span class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ color.newImage || color.image ? 'Cambiar' : 'Subir' }} imagen
                              </span>
                              <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="(e) => handleColorImageUpload(e, index)"
                              />
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                              Max 5MB - JPG, PNG, WEBP
                            </p>
                            <p v-if="errors[`color_${index}_image`]" class="text-xs text-red-600 mt-1">
                              {{ errors[`color_${index}_image`] }}
                            </p>
                          </div>
                        </div>
                      </div>

                      <!-- Galería -->
                      <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">
                          Galería de imágenes
                        </label>
                        
                        <!-- ✅ CORREGIDO: Usar getVisibleGallery para filtrar -->
                        <div v-if="getVisibleGallery(color).length > 0" class="mb-3">
                          <p class="text-xs text-gray-500 mb-2">Imágenes actuales:</p>
                          <div class="grid grid-cols-5 gap-2">
                            <div
                              v-for="(imagePath, imgIndex) in getVisibleGallery(color)"
                              :key="imgIndex"
                              class="relative group"
                            >
                              <img
                                :src="imagePath"
                                class="w-full aspect-square object-cover rounded-lg border border-gray-200"
                              />
                              <button
                                @click="removeExistingGalleryImage(index, imagePath)"
                                type="button"
                                class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-md opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Eliminar imagen"
                              >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                              </button>
                            </div>
                          </div>
                        </div>

                        <!-- Nuevas imágenes de galería -->
                        <div v-if="color.newGalleryImages && color.newGalleryImages.length > 0" class="mb-3">
                          <p class="text-xs text-gray-500 mb-2">Nuevas imágenes:</p>
                          <div class="grid grid-cols-5 gap-2">
                            <div
                              v-for="(file, fileIndex) in color.newGalleryImages"
                              :key="'new-' + fileIndex"
                              class="relative group"
                            >
                              <img
                                :src="imagePreviewUrls[`color_${index}_new_gallery_${fileIndex}`]"
                                class="w-full aspect-square object-cover rounded-lg border-2 border-blue-300"
                              />
                              <button
                                @click="removeNewGalleryImage(index, fileIndex)"
                                type="button"
                                class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-md opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Quitar imagen"
                              >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                              </button>
                            </div>
                          </div>
                        </div>

                        <label class="cursor-pointer">
                          <span class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-dashed border-gray-300 rounded-lg hover:bg-gray-50 transition-colors w-full justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar imágenes
                          </span>
                          <input
                            type="file"
                            accept="image/*"
                            multiple
                            class="hidden"
                            @change="(e) => handleGalleryImagesUpload(e, index)"
                          />
                        </label>
                        <p v-if="errors[`color_${index}_gallery`]" class="text-xs text-red-600 mt-1">
                          {{ errors[`color_${index}_gallery`] }}
                        </p>
                      </div>
                    </div>

                    <div
                      v-if="formData.colors.length === 0"
                      class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg"
                    >
                      <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                      </svg>
                      <p class="text-sm text-gray-600">No hay variantes de color</p>
                      <button
                        @click="addNewColor"
                        type="button"
                        class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar primera variante
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-end gap-3 border-t border-gray-200">
              <button
                @click="closeEditModal"
                :disabled="isUpdating"
                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
              >
                Cancelar
              </button>
              <button
                @click="updateProduct"
                :disabled="isUpdating"
                class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 inline-flex items-center gap-2"
              >
                <svg 
                  v-if="isUpdating"
                  class="animate-spin h-4 w-4" 
                  xmlns="http://www.w3.org/2000/svg" 
                  fill="none" 
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ isUpdating ? 'Guardando...' : 'Guardar cambios' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal confirmación eliminar color -->
    <Teleport to="body">
      <div
        v-if="showColorDeleteConfirm"
        class="fixed inset-0 z-[60] overflow-y-auto"
        @click.self="showColorDeleteConfirm = false"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Eliminar variante</h3>
                <p class="text-sm text-gray-600">Esta acción no se puede deshacer</p>
              </div>
            </div>
            <p class="text-sm text-gray-700 mb-6">
              ¿Estás seguro de que deseas eliminar esta variante de color? Se eliminarán todas las imágenes asociadas.
            </p>
            <div class="flex items-center justify-end gap-3">
              <button
                @click="showColorDeleteConfirm = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="deleteColor"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
              >
                Sí, eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </AppLayout>
</template>