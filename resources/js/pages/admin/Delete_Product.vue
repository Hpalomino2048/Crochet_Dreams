<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'

interface ProductColor {
  id: number
  name: string
  hex_code: string
}

interface Product {
  id: number
  name: string
  sku: string
  descriptions: string
  price: number
  currency: string
  stock: number
  thumbnail: string | null
  colors?: ProductColor[]
  has_variants: boolean
  created_at: string
}

const props = defineProps<{
  products: {
    data: Product[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Productos', href: '/admin/products' },
  { title: 'Eliminar productos', href: '#' },
]

const searchQuery = ref('')
const sortBy = ref('created_at')
const sortOrder = ref<'asc' | 'desc'>('desc')
const showDeleteModal = ref(false)
const selectedProduct = ref<Product | null>(null)
const isDeleting = ref(false)

// Filtrar y ordenar productos
const filteredProducts = computed(() => {
  let filtered = props.products.data

  // Buscar
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.sku.toLowerCase().includes(query) ||
      p.descriptions?.toLowerCase().includes(query)
    )
  }

  // Ordenar
  filtered = [...filtered].sort((a, b) => {
    let aVal, bVal

    switch (sortBy.value) {
      case 'name':
        aVal = a.name.toLowerCase()
        bVal = b.name.toLowerCase()
        break
      case 'sku':
        aVal = a.sku
        bVal = b.sku
        break
      case 'stock':
        aVal = a.stock
        bVal = b.stock
        break
      case 'created_at':
        aVal = new Date(a.created_at).getTime()
        bVal = new Date(b.created_at).getTime()
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

const openDeleteModal = (product: Product) => {
  selectedProduct.value = product
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  if (!isDeleting.value) {
    showDeleteModal.value = false
    selectedProduct.value = null
  }
}

const confirmDelete = () => {
  if (!selectedProduct.value || isDeleting.value) return

  isDeleting.value = true

  router.delete(`/admin/products/${selectedProduct.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      selectedProduct.value = null
      isDeleting.value = false
    },
    onError: () => {
      isDeleting.value = false
    }
  })
}

const truncateText = (text: string, maxLength: number) => {
  if (!text) return 'Sin descripción'
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}
</script>

<template>
  <Head title="Eliminar productos"/>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-8 py-6 space-y-6">
      <!-- Header -->
      <div class="pb-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-red-900">
          Eliminar productos
        </h1>
        <p class="mt-1 text-sm text-gray-600">
          Selecciona el producto que deseas eliminar. Esta acción requiere confirmación.
        </p>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center gap-4">
          <!-- Búsqueda -->
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
                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors"
              />
            </div>
          </div>

          <!-- Ordenar -->
          <div class="w-56">
            <select
              v-model="sortBy"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors"
            >
              <option value="created_at">Fecha de creación</option>
              <option value="name">Nombre</option>
              <option value="sku">SKU</option>
              <option value="stock">Stock</option>
            </select>
          </div>
        </div>

        <!-- Stats -->
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
          <!-- Table Header -->
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

          <!-- Table Body -->
          <div class="divide-y divide-gray-200">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id"
              class="hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="grid grid-cols-12 gap-6 px-6 py-4 items-center">
                <!-- Imagen -->
                <div class="col-span-1">
                  <div class="w-12 h-12 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-200">
                    <img
                      v-if="product.thumbnail"
                      :src="product.thumbnail"
                      :alt="product.name"
                      class="w-full h-full object-cover"
                      @error="(e) => {
                        console.error('Error cargando imagen:', product.thumbnail);
                        (e.target as HTMLImageElement).style.display = 'none';
                      }"
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

                <!-- Producto -->
                <div class="col-span-3">
                  <h3 class="text-sm font-semibold text-gray-900 leading-tight">
                    {{ product.name }}
                  </h3>
                  <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                    {{ truncateText(product.descriptions, 60) }}
                  </p>
                </div>

                <!-- SKU -->
                <div class="col-span-2">
                  <p class="text-sm font-mono text-gray-700 font-medium">
                    {{ product.sku }}
                  </p>
                </div>

                <!-- Precio -->
                <div class="col-span-2">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ product.currency }} ${{ product.price }}
                  </p>
                </div>

                <!-- Stock -->
                <div class="col-span-1">
                  <span 
                    class="inline-flex items-center justify-center min-w-[3rem] px-2.5 py-1 rounded-md text-xs font-semibold"
                    :class="product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ product.stock }}
                  </span>
                </div>

                <!-- Variantes -->
                <div class="col-span-2">
                  <div v-if="product.colors && product.colors.length > 0" class="flex items-center gap-2.5">
                    <div class="flex items-center -space-x-1.5">
                      <div
                        v-for="color in product.colors.slice(0, 3)"
                        :key="color.id"
                        :style="{ backgroundColor: color.hex_code }"
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

                <!-- Acción -->
                <div class="col-span-1 text-center">
                  <button
                    @click="openDeleteModal(product)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-red-600 hover:bg-red-50 transition-colors duration-150"
                    title="Eliminar producto"
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
              </div>
            </div>
          </div>
        </div>

        <!-- Empty state -->
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
          <Link
            v-if="!searchQuery"
            href="/admin/Create_Product"
            class="mt-6 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Crear primer producto
          </Link>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 z-50 overflow-y-auto"
          @click.self="closeDeleteModal"
        >
          <!-- Overlay -->
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

          <!-- Modal content -->
          <div class="flex min-h-full items-center justify-center p-4">
            <Transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div
                v-if="showDeleteModal"
                class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-auto"
                @click.stop
              >
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200">
                  <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                      <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                          />
                        </svg>
                      </div>
                      <div>
                        <h3 class="text-xl font-bold text-gray-900">
                          Confirmar eliminación
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                          Esta acción no se puede deshacer
                        </p>
                      </div>
                    </div>
                    <button
                      @click="closeDeleteModal"
                      :disabled="isDeleting"
                      class="text-gray-400 hover:text-gray-600 transition-colors disabled:opacity-50"
                    >
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Body -->
                <div v-if="selectedProduct" class="px-6 py-5">
                  <div class="flex gap-4 mb-4">
                    <!-- Thumbnail del producto -->
                    <div class="flex-shrink-0">
                      <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                        <img
                          v-if="selectedProduct.thumbnail"
                          :src="selectedProduct.thumbnail"
                          :alt="selectedProduct.name"
                          class="w-full h-full object-cover"
                          @error="(e) => {
                            console.error('Error cargando imagen del modal:', selectedProduct?.thumbnail);
                            (e.target as HTMLImageElement).style.display = 'none';
                          }"
                        />
                        <svg
                          v-else
                          class="w-12 h-12 text-gray-300"
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

                    <!-- Info del producto -->
                    <div class="flex-1">
                      <h4 class="text-lg font-semibold text-gray-900">
                        {{ selectedProduct.name }}
                      </h4>
                      <p class="text-sm text-gray-600 mt-1">
                        SKU: <span class="font-mono">{{ selectedProduct.sku }}</span>
                      </p>
                      <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                          <span class="text-xs text-gray-500">Precio:</span>
                          <p class="text-sm font-medium text-gray-900">
                            {{ selectedProduct.currency }} ${{ selectedProduct.price }}
                          </p>
                        </div>
                        <div>
                          <span class="text-xs text-gray-500">Stock:</span>
                          <p class="text-sm font-medium" :class="selectedProduct.stock > 0 ? 'text-green-600' : 'text-red-600'">
                            {{ selectedProduct.stock }} unidades
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Advertencia -->
                  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex gap-3">
                      <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <div>
                        <h5 class="font-semibold text-red-900 text-sm">
                          ¿Estás seguro de eliminar este producto?
                        </h5>
                        <ul class="mt-2 text-sm text-red-800 space-y-1">
                          <li>• Se eliminará la información del producto</li>
                          <li>• Se eliminarán todas las imágenes asociadas</li>
                          <li v-if="selectedProduct.colors && selectedProduct.colors.length > 0">
                            • Se eliminarán {{ selectedProduct.colors.length }} variante{{ selectedProduct.colors.length !== 1 ? 's' : '' }} de color
                          </li>
                          <li class="font-semibold">• Esta acción no se puede deshacer</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex items-center justify-end gap-3">
                  <button
                    @click="closeDeleteModal"
                    :disabled="isDeleting"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Cancelar
                  </button>
                  <button
                    @click="confirmDelete"
                    :disabled="isDeleting"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2"
                  >
                    <svg 
                      v-if="isDeleting"
                      class="animate-spin h-4 w-4" 
                      xmlns="http://www.w3.org/2000/svg" 
                      fill="none" 
                      viewBox="0 0 24 24"
                    >
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                    {{ isDeleting ? 'Eliminando...' : 'Sí, eliminar producto' }}
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>