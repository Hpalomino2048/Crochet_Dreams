<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppFooter from './Components_Welcome/AppFooter.vue';
import AppHeader from './Components_Welcome/AppHeader.vue';

// Obtener el usuario desde los props compartidos de Inertia
const page = usePage();
const user = computed(() => page.props.auth?.user || null);

// Estado reactivo
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedSizes = ref([]);
const priceRange = ref({ min: '', max: '' });
const showOnlyInStock = ref(false);
const sortBy = ref('default');
const currentPage = ref(1);
const itemsPerPage = ref(9);

// Datos de productos
const products = ref([
  {
    id: 1,
    name: 'Conejo Amigurumi',
    description: 'Adorable conejo tejido a mano con algodón premium',
    size: 'M',
    variants: ['#FFB6C1', '#87CEEB', '#FFFFFF'],
    price: 450.00,
    category: 'Amigurumis',
    stock: 15,
    image: 'https://images.unsplash.com/photo-1587402092301-725e37c70fd8?w=400&h=400&fit=crop'
  },
  
]);

const categories = ref(['Amigurumis', 'Ropa', 'Accesorios', 'Decoración']);
const sizes = ref(['S', 'M', 'L', 'XL', 'Única', 'Ajustable']);

// Computed properties
const filteredProducts = computed(() => {
  let filtered = [...products.value];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.description.toLowerCase().includes(query)
    );
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(p => p.category === selectedCategory.value);
  }

  if (selectedSizes.value.length > 0) {
    filtered = filtered.filter(p => selectedSizes.value.includes(p.size));
  }

  if (priceRange.value.min !== '') {
    filtered = filtered.filter(p => p.price >= priceRange.value.min);
  }
  if (priceRange.value.max !== '') {
    filtered = filtered.filter(p => p.price <= priceRange.value.max);
  }

  if (showOnlyInStock.value) {
    filtered = filtered.filter(p => p.stock > 0);
  }

  if (sortBy.value === 'price-asc') {
    filtered.sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'price-desc') {
    filtered.sort((a, b) => b.price - a.price);
  } else if (sortBy.value === 'name') {
    filtered.sort((a, b) => a.name.localeCompare(b.name));
  }

  return filtered;
});

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage.value);
});

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredProducts.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i);
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    } else if (current >= total - 3) {
      pages.push(1);
      pages.push('...');
      for (let i = total - 4; i <= total; i++) pages.push(i);
    } else {
      pages.push(1);
      pages.push('...');
      for (let i = current - 1; i <= current + 1; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    }
  }
  
  return pages;
});

// Methods
const toggleSize = (size) => {
  const index = selectedSizes.value.indexOf(size);
  if (index > -1) {
    selectedSizes.value.splice(index, 1);
  } else {
    selectedSizes.value.push(size);
  }
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedSizes.value = [];
  priceRange.value = { min: '', max: '' };
  showOnlyInStock.value = false;
  sortBy.value = 'default';
  currentPage.value = 1;
};

// Watchers
watch(filteredProducts, () => {
  currentPage.value = 1;
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50">
    <AppHeader :user="user" />
    
    <div class="container mx-auto px-4 py-8">
      <!-- Título de la página -->
      <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
          Nuestra Tienda
        </h1>
        <p class="text-gray-600">Descubre productos únicos hechos a mano con amor</p>
      </div>
      
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Panel de Filtros -->
        <aside class="lg:w-72 flex-shrink-0">
          <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24 border border-purple-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filtros
            </h2>
            
            <!-- Buscador -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                🔍 Buscar producto
              </label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Conejo, osito, bolso..."
                class="w-full px-4 py-2.5 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all"
              />
            </div>

            <!-- Filtro por Categoría -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                📦 Categoría
              </label>
              <select
                v-model="selectedCategory"
                class="w-full px-4 py-2.5 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all"
              >
                <option value="">Todas las categorías</option>
                <option v-for="cat in categories" :key="cat" :value="cat">
                  {{ cat }}
                </option>
              </select>
            </div>

            <!-- Filtro por Precio -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                💰 Rango de precio
              </label>
              <div class="flex gap-2">
                <input
                  v-model.number="priceRange.min"
                  type="number"
                  placeholder="Min"
                  class="w-1/2 px-3 py-2.5 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-400"
                />
                <input
                  v-model.number="priceRange.max"
                  type="number"
                  placeholder="Max"
                  class="w-1/2 px-3 py-2.5 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-400"
                />
              </div>
            </div>

            <!-- Filtro por Tamaño -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                📏 Tamaño
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="size in sizes"
                  :key="size"
                  @click="toggleSize(size)"
                  :class="[
                    'px-4 py-2 rounded-xl text-sm font-semibold transition-all transform hover:scale-105',
                    selectedSizes.includes(size)
                      ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-md'
                      : 'bg-purple-100 text-purple-700 hover:bg-purple-200'
                  ]"
                >
                  {{ size }}
                </button>
              </div>
            </div>

            <!-- Filtro de Stock -->
            <div class="mb-6">
              <label class="flex items-center cursor-pointer bg-purple-50 p-3 rounded-xl hover:bg-purple-100 transition-colors">
                <input
                  v-model="showOnlyInStock"
                  type="checkbox"
                  class="w-5 h-5 text-purple-600 border-purple-300 rounded focus:ring-purple-500"
                />
                <span class="ml-3 text-sm font-medium text-gray-700">
                  ✨ Solo productos disponibles
                </span>
              </label>
            </div>

            <!-- Botón Limpiar Filtros -->
            <button
              @click="clearFilters"
              class="w-full px-4 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all font-semibold shadow-sm"
            >
              🗑️ Limpiar filtros
            </button>
          </div>
        </aside>

        <!-- Sección de Productos -->
        <main class="flex-1">
          <!-- Información de resultados -->
          <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-purple-100">
            <p class="text-gray-700 font-medium">
              Mostrando <span class="text-purple-600 font-bold">{{ paginatedProducts.length }}</span> de 
              <span class="text-purple-600 font-bold">{{ filteredProducts.length }}</span> productos
            </p>
            <select
              v-model="sortBy"
              class="px-4 py-2 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-400 font-medium"
            >
              <option value="default">Ordenar por</option>
              <option value="price-asc">💵 Precio: Menor a Mayor</option>
              <option value="price-desc">💰 Precio: Mayor a Menor</option>
              <option value="name">🔤 Nombre: A-Z</option>
            </select>
          </div>

          <!-- Grid de Productos -->
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <div
              v-for="product in paginatedProducts"
              :key="product.id"
              class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-purple-100"
            >
              <!-- Imagen del producto -->
              <div class="relative h-64 bg-gradient-to-br from-purple-100 to-pink-100 overflow-hidden group">
                <img
                  :src="product.image"
                  :alt="product.name"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <span
                  v-if="product.stock === 0"
                  class="absolute top-3 right-3 bg-red-500 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-lg"
                >
                  😢 Agotado
                </span>
                <span
                  v-else-if="product.stock < 5"
                  class="absolute top-3 right-3 bg-orange-500 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-lg animate-pulse"
                >
                  ⚡ Últimas {{ product.stock }}
                </span>
              </div>

              <!-- Información del producto -->
              <div class="p-5">
                <span class="inline-block px-3 py-1 text-xs font-bold uppercase bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 rounded-full mb-2">
                  {{ product.category }}
                </span>
                <h3 class="text-lg font-bold text-gray-800 mb-2 hover:text-purple-600 transition-colors">
                  {{ product.name }}
                </h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                  {{ product.description }}
                </p>

                <div class="flex items-center gap-2 mb-3">
                  <span class="text-xs font-semibold text-gray-500">📏 Tamaño:</span>
                  <span class="text-sm font-medium text-gray-700 bg-purple-50 px-2 py-1 rounded-lg">{{ product.size }}</span>
                </div>

                <div v-if="product.variants.length > 0" class="mb-4">
                  <span class="text-xs font-semibold text-gray-500 block mb-2">
                    🎨 Colores disponibles:
                  </span>
                  <div class="flex gap-2">
                    <span
                      v-for="color in product.variants"
                      :key="color"
                      :style="{ backgroundColor: color }"
                      class="w-7 h-7 rounded-full border-3 border-white shadow-md ring-2 ring-purple-200 hover:scale-110 transition-transform cursor-pointer"
                      :title="color"
                    ></span>
                  </div>
                </div>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-purple-100">
                  <div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                      ${{ product.price.toFixed(2) }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">
                      📦 Stock: {{ product.stock }} unidades
                    </p>
                  </div>
                  <button
                    :disabled="product.stock === 0"
                    :class="[
                      'px-5 py-2.5 rounded-xl font-bold transition-all transform hover:scale-105 shadow-md',
                      product.stock === 0
                        ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                        : 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700'
                    ]"
                  >
                    {{ product.stock === 0 ? '❌ Agotado' : '🛒 Agregar' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Paginación -->
          <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 flex-wrap">
            <button
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-5 py-2.5 border-2 border-purple-300 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed hover:bg-purple-50 transition-all font-semibold text-purple-700"
            >
              ← Anterior
            </button>

            <button
              v-for="page in visiblePages"
              :key="page"
              @click="typeof page === 'number' && (currentPage = page)"
              :disabled="typeof page !== 'number'"
              :class="[
                'px-4 py-2.5 rounded-xl font-bold transition-all',
                currentPage === page
                  ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg transform scale-110'
                  : typeof page === 'number'
                  ? 'border-2 border-purple-200 hover:bg-purple-50 text-purple-700'
                  : 'text-purple-400 cursor-default'
              ]"
            >
              {{ page }}
            </button>

            <button
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-5 py-2.5 border-2 border-purple-300 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed hover:bg-purple-50 transition-all font-semibold text-purple-700"
            >
              Siguiente →
            </button>
          </div>
        </main>
      </div>
    </div>
    <AppFooter />
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>