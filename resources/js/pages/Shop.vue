<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppFooter from './Components_Welcome/AppFooter.vue';
import AppHeader from './Components_Welcome/AppHeader.vue';
import ProductModal from './Components_Shop/ProductModal.vue';

// Props desde el controlador
const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  sizes: {
    type: Array,
    default: () => []
  }
});

// Obtener el usuario desde los props compartidos de Inertia
const page = usePage();
const user = computed(() => page.props.auth?.user || null);

// Estado reactivo para filtros
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedSizes = ref([]);
const priceRange = ref({ min: '', max: '' });
const showOnlyInStock = ref(false);
const sortBy = ref('default');
const currentPage = ref(1);
const itemsPerPage = ref(12);

// Estado para favoritos y carrito
const favorites = ref([]);
const cart = ref([]);

// Estado para el modal de producto
const showProductModal = ref(false);
const selectedProduct = ref(null);

// =====================================================
// ESTADO PARA MODAL DE CONFIRMACIÓN
// =====================================================
const showAddToCartModal = ref(false);
const productToAdd = ref(null);
const selectedColorForCart = ref(null);
const quantityToAdd = ref(1);

// =====================================================
// KEY ÚNICO POR USUARIO PARA CARRITO
// =====================================================
const getCartKey = () => {
  if (user.value && user.value.id) {
    return `cart_user_${user.value.id}`;
  }
  // Para invitados, usar un ID de sesión
  let guestId = localStorage.getItem('guest_cart_id');
  if (!guestId) {
    guestId = 'guest_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    localStorage.setItem('guest_cart_id', guestId);
  }
  return `cart_${guestId}`;
};

const getFavoritesKey = () => {
  if (user.value && user.value.id) {
    return `favorites_user_${user.value.id}`;
  }
  return 'favorites_guest';
};

// =====================================================
// CARGAR Y GUARDAR DATOS
// =====================================================
const loadCart = () => {
  try {
    const cartKey = getCartKey();
    const savedCart = localStorage.getItem(cartKey);
    if (savedCart) {
      const parsed = JSON.parse(savedCart);
      cart.value = Array.isArray(parsed) ? parsed : [];
    } else {
      cart.value = [];
    }
  } catch (error) {
    console.error('Error al cargar el carrito:', error);
    cart.value = [];
  }
};

const saveCart = () => {
  try {
    const cartKey = getCartKey();
    localStorage.setItem(cartKey, JSON.stringify(cart.value));
    
    // Disparar evento para sincronizar con otros componentes
    window.dispatchEvent(new CustomEvent('cart-updated', { 
      detail: { count: cartItemsCount.value } 
    }));
  } catch (error) {
    console.error('Error al guardar el carrito:', error);
  }
};

const loadFavorites = () => {
  try {
    const favKey = getFavoritesKey();
    const savedFavorites = localStorage.getItem(favKey);
    if (savedFavorites) {
      favorites.value = JSON.parse(savedFavorites);
    }
  } catch (error) {
    console.error('Error al cargar favoritos:', error);
    favorites.value = [];
  }
};

const saveFavorites = () => {
  try {
    const favKey = getFavoritesKey();
    localStorage.setItem(favKey, JSON.stringify(favorites.value));
  } catch (error) {
    console.error('Error al guardar favoritos:', error);
  }
};

// Sincronizar con otras pestañas
const handleStorageChange = (event) => {
  const cartKey = getCartKey();
  const favKey = getFavoritesKey();
  
  if (event.key === cartKey) {
    loadCart();
  }
  if (event.key === favKey) {
    loadFavorites();
  }
};

// Cargar datos al montar
onMounted(() => {
  loadCart();
  loadFavorites();
  window.addEventListener('storage', handleStorageChange);
});

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange);
});

// Recargar cuando cambia el usuario
watch(() => user.value?.id, () => {
  loadCart();
  loadFavorites();
});

// Guardar cuando cambian los favoritos
watch(favorites, () => {
  saveFavorites();
}, { deep: true });

// Computed: Productos filtrados
const filteredProducts = computed(() => {
  let filtered = [...props.products];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) ||
      (p.description && p.description.toLowerCase().includes(query)) ||
      (p.category && p.category.toLowerCase().includes(query))
    );
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(p => p.category === selectedCategory.value);
  }

  if (selectedSizes.value.length > 0) {
    filtered = filtered.filter(p => selectedSizes.value.includes(p.size));
  }

  if (priceRange.value.min !== '') {
    filtered = filtered.filter(p => p.price >= parseFloat(priceRange.value.min));
  }
  if (priceRange.value.max !== '') {
    filtered = filtered.filter(p => p.price <= parseFloat(priceRange.value.max));
  }

  if (showOnlyInStock.value) {
    filtered = filtered.filter(p => p.total_stock > 0);
  }

  switch (sortBy.value) {
    case 'price-asc':
      filtered.sort((a, b) => a.price - b.price);
      break;
    case 'price-desc':
      filtered.sort((a, b) => b.price - a.price);
      break;
    case 'name':
      filtered.sort((a, b) => a.name.localeCompare(b.name));
      break;
    case 'newest':
      break;
  }

  return filtered;
});

// Paginación
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage.value));

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredProducts.value.slice(start, start + itemsPerPage.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 5) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    } else if (current >= total - 2) {
      pages.push(1);
      pages.push('...');
      for (let i = total - 3; i <= total; i++) pages.push(i);
    } else {
      pages.push(1);
      pages.push('...');
      pages.push(current - 1);
      pages.push(current);
      pages.push(current + 1);
      pages.push('...');
      pages.push(total);
    }
  }
  return pages;
});

watch([searchQuery, selectedCategory, selectedSizes, priceRange, showOnlyInStock, sortBy], () => {
  currentPage.value = 1;
});

// Métodos
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

const isFavorite = (productId) => favorites.value.includes(productId);

const toggleFavorite = (productId) => {
  const index = favorites.value.indexOf(productId);
  if (index > -1) {
    favorites.value.splice(index, 1);
  } else {
    favorites.value.push(productId);
  }
};

// =====================================================
// FUNCIONES DEL CARRITO MEJORADAS
// =====================================================

const isInCart = (productId) => cart.value.some(item => item.productId === productId);

const getCartQuantity = (productId) => {
  return cart.value
    .filter(item => item.productId === productId)
    .reduce((sum, item) => sum + item.quantity, 0);
};

// Abrir modal de confirmación
const openAddToCartModal = (product, color = null) => {
  productToAdd.value = product;
  
  if (color) {
    selectedColorForCart.value = color;
  } else if (product.colors && product.colors.length > 0) {
    selectedColorForCart.value = product.colors.find(c => c.is_default) || product.colors[0];
  } else {
    selectedColorForCart.value = null;
  }
  
  quantityToAdd.value = 1;
  showAddToCartModal.value = true;
};

// Cerrar modal
const closeAddToCartModal = () => {
  showAddToCartModal.value = false;
  productToAdd.value = null;
  selectedColorForCart.value = null;
  quantityToAdd.value = 1;
};

// Confirmar y agregar (AGRUPA PRODUCTOS)
const confirmAddToCart = () => {
  if (!productToAdd.value) return;
  
  const product = productToAdd.value;
  const color = selectedColorForCart.value;
  const quantity = quantityToAdd.value;
  
  const colorId = color?.id || null;
  const itemKey = `${product.id}-${colorId || 'default'}`;
  
  const existingIndex = cart.value.findIndex(item => {
    const existingKey = `${item.productId}-${item.colorId || 'default'}`;
    return existingKey === itemKey;
  });
  
  if (existingIndex !== -1) {
    cart.value[existingIndex].quantity += quantity;
    if (cart.value[existingIndex].quantity > 99) {
      cart.value[existingIndex].quantity = 99;
    }
  } else {
    cart.value.push({
      productId: product.id,
      colorId: colorId,
      name: product.name,
      price: Number(product.price),
      thumbnail: color?.image || product.thumbnail,
      colorName: color?.name || null,
      colorCode: color?.code || null,
      quantity: quantity
    });
  }
  
  saveCart();
  closeAddToCartModal();
  showSuccessNotification(`${product.name} agregado al carrito`);
};

// Función para ProductModal
const addToCart = (product, colorId = null, quantity = 1) => {
  const color = colorId 
    ? product.colors?.find(c => c.id === colorId) 
    : product.colors?.find(c => c.is_default) || product.colors?.[0] || null;
  
  const itemKey = `${product.id}-${colorId || 'default'}`;
  
  const existingIndex = cart.value.findIndex(item => {
    const existingKey = `${item.productId}-${item.colorId || 'default'}`;
    return existingKey === itemKey;
  });
  
  if (existingIndex !== -1) {
    cart.value[existingIndex].quantity += quantity;
    if (cart.value[existingIndex].quantity > 99) {
      cart.value[existingIndex].quantity = 99;
    }
  } else {
    cart.value.push({
      productId: product.id,
      colorId: colorId || color?.id || null,
      name: product.name,
      price: Number(product.price),
      thumbnail: color?.image || product.thumbnail,
      colorName: color?.name || null,
      colorCode: color?.code || null,
      quantity: quantity
    });
  }
  
  saveCart();
};

// Notificación
const successMessage = ref('');
const showSuccess = ref(false);

const showSuccessNotification = (message) => {
  successMessage.value = message;
  showSuccess.value = true;
  setTimeout(() => {
    showSuccess.value = false;
  }, 3000);
};

const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const cartItemsCount = computed(() => {
  return cart.value.reduce((total, item) => total + item.quantity, 0);
});

const openProductModal = (product) => {
  selectedProduct.value = product;
  showProductModal.value = true;
};

const closeProductModal = () => {
  showProductModal.value = false;
  selectedProduct.value = null;
};

const goToProduct = (slug) => {
  router.visit(`/tienda/${slug}`);
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(price);
};

const handleImageError = (event) => {
  event.target.style.display = 'none';
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50/50 via-pink-50/30 to-purple-50/50">
    <AppHeader :user="user" :cartCount="cartItemsCount" />
    
    <!-- Notificación de éxito -->
    <Transition name="slide-down">
      <div 
        v-if="showSuccess" 
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ successMessage }}
      </div>
    </Transition>
    
    <!-- MODAL DE CONFIRMACIÓN -->
    <Teleport to="body">
      <Transition name="modal">
        <div 
          v-if="showAddToCartModal && productToAdd" 
          class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        >
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeAddToCartModal"></div>
          
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.stop>
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4">
              <h3 class="text-lg font-bold">Agregar al carrito</h3>
            </div>
            
            <div class="p-6">
              <div class="flex gap-4 mb-6">
                <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                  <img 
                    v-if="selectedColorForCart?.image || productToAdd.thumbnail" 
                    :src="selectedColorForCart?.image || productToAdd.thumbnail" 
                    :alt="productToAdd.name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div class="flex-1">
                  <h4 class="font-bold text-gray-900">{{ productToAdd.name }}</h4>
                  <p class="text-purple-600 font-bold text-lg">{{ formatPrice(productToAdd.price) }}</p>
                  <p class="text-sm text-gray-500">{{ productToAdd.total_stock }} disponibles</p>
                </div>
              </div>
              
              <!-- Colores -->
              <div v-if="productToAdd.colors && productToAdd.colors.length > 0" class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Color:</label>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="color in productToAdd.colors"
                    :key="color.id"
                    @click="selectedColorForCart = color"
                    :class="[
                      'w-10 h-10 rounded-full border-2 transition-all',
                      selectedColorForCart?.id === color.id 
                        ? 'ring-4 ring-purple-400 ring-offset-2 scale-110' 
                        : 'border-gray-200 hover:scale-105'
                    ]"
                    :style="{ backgroundColor: color.code }"
                    :title="color.name"
                  ></button>
                </div>
                <p v-if="selectedColorForCart" class="text-sm text-gray-600 mt-2">
                  Seleccionado: <span class="font-medium">{{ selectedColorForCart.name }}</span>
                </p>
              </div>
              
              <!-- Cantidad -->
              <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cantidad:</label>
                <div class="flex items-center gap-3">
                  <button 
                    @click="quantityToAdd = Math.max(1, quantityToAdd - 1)"
                    class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold"
                  >−</button>
                  <input 
                    v-model.number="quantityToAdd"
                    type="number"
                    min="1"
                    :max="productToAdd.total_stock"
                    class="w-20 text-center font-bold text-lg border-2 border-gray-200 rounded-xl py-2 focus:border-purple-400"
                  />
                  <button 
                    @click="quantityToAdd = Math.min(productToAdd.total_stock, quantityToAdd + 1)"
                    class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold"
                  >+</button>
                </div>
              </div>
              
              <!-- Total -->
              <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600 font-medium">Total:</span>
                  <span class="text-2xl font-black text-purple-600">
                    {{ formatPrice(productToAdd.price * quantityToAdd) }}
                  </span>
                </div>
              </div>
              
              <!-- Botones -->
              <div class="flex gap-3">
                <button 
                  @click="closeAddToCartModal"
                  class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200"
                >
                  Cancelar
                </button>
                <button 
                  @click="confirmAddToCart"
                  class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Confirmar
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
    
    <!-- Hero Section -->
    <section class="relative bg-white border-b border-gray-200">
      <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-5xl md:text-6xl font-black mb-4 bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 bg-clip-text text-transparent">
            ✨ Productos Artesanales ✨
          </h1>
          <p class="text-xl text-gray-700 leading-relaxed mb-8">
            Descubre piezas únicas hechas a mano con 
            <span class="text-pink-600 font-bold">amor</span> y 
            <span class="text-purple-600 font-bold">dedicación</span>
          </p>
          <div class="flex justify-center gap-12 flex-wrap">
            <div class="text-center">
              <div class="text-3xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ products.length }}+</div>
              <div class="text-sm text-gray-600 font-semibold mt-1">Productos</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">100%</div>
              <div class="text-sm text-gray-600 font-semibold mt-1">Artesanal</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">♥</div>
              <div class="text-sm text-gray-600 font-semibold mt-1">Con Amor</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <div class="container mx-auto px-4 py-12">
      <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Panel de Filtros -->
        <aside class="lg:w-80 flex-shrink-0">
          <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg border border-purple-100/50 p-6 sticky top-24">
            <div class="mb-6">
              <h2 class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">Filtros</h2>
              <div class="flex items-center gap-2">
                <div class="h-1 w-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full"></div>
                <p class="text-sm text-gray-600 font-medium">{{ filteredProducts.length }} productos</p>
              </div>
            </div>
            
            <!-- Buscador -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
              <div class="relative">
                <input v-model="searchQuery" type="text" placeholder="¿Qué buscas?" class="w-full pl-11 pr-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-transparent rounded-xl focus:border-purple-400 focus:ring-4 focus:ring-purple-100 text-sm" />
                <svg class="w-5 h-5 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Categoría -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
              <select v-model="selectedCategory" class="w-full px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-transparent rounded-xl focus:border-purple-400 text-sm font-medium cursor-pointer">
                <option value="">Todas las categorías</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>

            <!-- Precio -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Rango de precio</label>
              <div class="flex gap-3">
                <div class="relative flex-1">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-600 text-sm font-bold">$</span>
                  <input v-model.number="priceRange.min" type="number" placeholder="Mín" class="w-full pl-8 pr-3 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-transparent rounded-xl focus:border-purple-400 text-sm" />
                </div>
                <span class="flex items-center text-purple-300 font-bold">—</span>
                <div class="relative flex-1">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-600 text-sm font-bold">$</span>
                  <input v-model.number="priceRange.max" type="number" placeholder="Máx" class="w-full pl-8 pr-3 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-transparent rounded-xl focus:border-purple-400 text-sm" />
                </div>
              </div>
            </div>

            <!-- Tamaño -->
            <div class="mb-6" v-if="sizes.length > 0">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Tamaño</label>
              <div class="flex flex-wrap gap-2">
                <button v-for="size in sizes" :key="size" @click="toggleSize(size)" :class="['px-4 py-2.5 rounded-xl text-sm font-bold transition-all', selectedSizes.includes(size) ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 border-2 border-gray-200 hover:border-purple-300']">
                  {{ size }}
                </button>
              </div>
            </div>

            <!-- Stock -->
            <div class="mb-6">
              <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl hover:bg-purple-50">
                <div class="relative">
                  <input v-model="showOnlyInStock" type="checkbox" class="sr-only peer" />
                  <div class="w-12 h-6 bg-gray-200 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-pink-600"></div>
                  <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-6 transition-all"></div>
                </div>
                <span class="text-sm font-semibold text-gray-700">Solo disponibles</span>
              </label>
            </div>

            <div class="my-6 border-t border-purple-100"></div>

            <button @click="clearFilters" class="w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold text-sm flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Limpiar filtros
            </button>
          </div>
        </aside>

        <!-- Productos -->
        <main class="flex-1">
          <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white/90 p-6 rounded-2xl shadow-lg">
            <div>
              <p class="text-gray-900 font-bold text-lg">
                <span class="text-purple-600">{{ paginatedProducts.length }}</span> de 
                <span class="text-purple-600">{{ filteredProducts.length }}</span> productos
              </p>
            </div>
            <select v-model="sortBy" class="px-5 py-3 bg-purple-50 border-2 border-transparent rounded-xl focus:border-purple-400 font-bold text-sm min-w-[220px] cursor-pointer">
              <option value="default">Ordenar por</option>
              <option value="newest">Más recientes</option>
              <option value="price-asc">Precio: Menor a Mayor</option>
              <option value="price-desc">Precio: Mayor a Menor</option>
              <option value="name">Nombre: A-Z</option>
            </select>
          </div>

          <!-- Vacío -->
          <div v-if="paginatedProducts.length === 0" class="text-center py-20 bg-white rounded-2xl shadow-lg">
            <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No encontramos productos</h3>
            <button @click="clearFilters" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold">Limpiar filtros</button>
          </div>

          <!-- Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <article v-for="product in paginatedProducts" :key="product.id" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
              <div class="relative aspect-square bg-purple-50 overflow-hidden">
                <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700" loading="lazy" @error="handleImageError" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-20 h-20 text-purple-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>

                <div class="absolute top-3 left-3 flex flex-col gap-2">
                  <span v-if="product.total_stock === 0" class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Agotado</span>
                  <span v-else-if="product.total_stock < 5" class="bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold animate-pulse">¡Solo {{ product.total_stock }}!</span>
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent opacity-0 group-hover:opacity-100 transition-all">
                  <div class="absolute bottom-4 left-4 right-4 flex gap-2">
                    <button @click="openProductModal(product)" class="flex-1 py-3 bg-white text-gray-900 rounded-xl font-bold text-sm shadow-2xl flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      Ver detalles
                    </button>
                    <button @click.stop="toggleFavorite(product.id)" :class="['p-3 rounded-xl shadow-2xl', isFavorite(product.id) ? 'bg-pink-500 text-white' : 'bg-white text-gray-600 hover:text-pink-500']">
                      <svg class="w-5 h-5" :fill="isFavorite(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div v-if="isFavorite(product.id)" class="absolute top-3 right-3 w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center shadow-xl group-hover:opacity-0">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </div>
              </div>

              <div class="p-6">
                <span v-if="product.category" class="inline-block px-3 py-1.5 text-xs font-bold uppercase bg-purple-100 text-purple-700 rounded-lg mb-3">{{ product.category }}</span>
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1 cursor-pointer hover:text-purple-600" @click="openProductModal(product)">{{ product.name }}</h3>
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ product.description }}</p>

                <div v-if="product.size" class="flex items-center gap-2 mb-4">
                  <span class="text-xs text-gray-500">Tamaño:</span>
                  <span class="text-sm font-bold text-gray-700 bg-purple-50 px-3 py-1 rounded-lg">{{ product.size }}</span>
                </div>

                <div v-if="product.has_colors && product.colors.length > 0" class="mb-4">
                  <span class="text-xs text-gray-500 block mb-2">Colores:</span>
                  <div class="flex gap-2">
                    <span v-for="color in product.colors.slice(0, 5)" :key="color.id" :style="{ backgroundColor: color.code }" :title="color.name" class="w-8 h-8 rounded-full border-2 border-white shadow-lg ring-1 ring-gray-200 hover:scale-125 cursor-pointer transition-all"></span>
                    <span v-if="product.colors.length > 5" class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-xs font-bold text-purple-700">+{{ product.colors.length - 5 }}</span>
                  </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                  <div>
                    <span class="text-2xl font-black text-purple-600">{{ formatPrice(product.price) }}</span>
                    <p class="text-xs text-gray-500 mt-1">{{ product.total_stock }} disponibles</p>
                  </div>
                  
                  <button @click="openAddToCartModal(product)" :disabled="product.total_stock === 0" :class="['relative px-5 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg transition-all hover:scale-105', product.total_stock === 0 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:shadow-2xl']">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="hidden sm:inline">{{ product.total_stock === 0 ? 'Agotado' : 'Agregar' }}</span>
                    <span v-if="isInCart(product.id) && product.total_stock > 0" class="absolute -top-2 -right-2 w-7 h-7 bg-pink-500 text-white rounded-full text-xs font-bold flex items-center justify-center shadow-lg animate-bounce">{{ getCartQuantity(product.id) }}</span>
                  </button>
                </div>
              </div>
            </article>
          </div>

          <!-- Paginación -->
          <nav v-if="totalPages > 1" class="flex justify-center items-center gap-3 flex-wrap">
            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-6 py-3 bg-white border-2 border-gray-200 rounded-xl disabled:opacity-40 hover:border-purple-300 font-bold text-purple-700 flex items-center gap-2 text-sm shadow-md">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
              Anterior
            </button>
            <div class="flex gap-2">
              <button v-for="pageNum in visiblePages" :key="pageNum" @click="typeof pageNum === 'number' && (currentPage = pageNum)" :disabled="typeof pageNum !== 'number'" :class="['w-12 h-12 rounded-xl font-bold text-sm shadow-md', currentPage === pageNum ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white scale-110' : typeof pageNum === 'number' ? 'bg-white border-2 border-gray-200 hover:border-purple-300 text-purple-700' : 'text-gray-400 cursor-default bg-transparent shadow-none']">
                {{ pageNum }}
              </button>
            </div>
            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-6 py-3 bg-white border-2 border-gray-200 rounded-xl disabled:opacity-40 hover:border-purple-300 font-bold text-purple-700 flex items-center gap-2 text-sm shadow-md">
              Siguiente
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
          </nav>
        </main>
      </div>
    </div>
    
    <ProductModal v-if="showProductModal && selectedProduct" :product="selectedProduct" :is-favorite="isFavorite(selectedProduct.id)" @close="closeProductModal" @toggle-favorite="toggleFavorite(selectedProduct.id)" @add-to-cart="addToCart" @view-details="goToProduct(selectedProduct.slug)" />
    
    <AppFooter />
  </div>
</template>

<style scoped>
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translate(-50%, -20px); }
</style>