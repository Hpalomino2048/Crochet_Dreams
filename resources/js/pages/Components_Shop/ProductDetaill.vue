<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppFooter from '@/Components/Welcome/AppFooter.vue';
import AppHeader from '@/Components/Welcome/AppHeader.vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  relatedProducts: {
    type: Array,
    default: () => []
  }
});

const page = usePage();
const user = computed(() => page.props.auth?.user || null);

// Estado local
const selectedColor = ref(null);
const quantity = ref(1);
const currentImageIndex = ref(0);
const activeTab = ref('description');

// Estado para favoritos y carrito
const favorites = ref([]);
const cart = ref([]);

onMounted(() => {
  const savedFavorites = localStorage.getItem('shop_favorites');
  const savedCart = localStorage.getItem('shop_cart');
  
  if (savedFavorites) favorites.value = JSON.parse(savedFavorites);
  if (savedCart) cart.value = JSON.parse(savedCart);

  // Seleccionar color por defecto
  if (props.product.colors && props.product.colors.length > 0) {
    const defaultColor = props.product.colors.find(c => c.is_default) || props.product.colors[0];
    selectedColor.value = defaultColor;
  }
});

watch(favorites, (newVal) => {
  localStorage.setItem('shop_favorites', JSON.stringify(newVal));
}, { deep: true });

watch(cart, (newVal) => {
  localStorage.setItem('shop_cart', JSON.stringify(newVal));
}, { deep: true });

// Computed
const allImages = computed(() => {
  const images = [];
  
  if (props.product.thumbnail) {
    images.push(props.product.thumbnail);
  }
  
  if (selectedColor.value?.image && !images.includes(selectedColor.value.image)) {
    images.unshift(selectedColor.value.image);
  }
  
  if (selectedColor.value?.gallery) {
    selectedColor.value.gallery.forEach(img => {
      if (!images.includes(img)) images.push(img);
    });
  }
  
  if (props.product.images) {
    props.product.images.forEach(img => {
      if (!images.includes(img)) images.push(img);
    });
  }
  
  return images;
});

const currentStock = computed(() => {
  if (selectedColor.value) return selectedColor.value.stock;
  return props.product.total_stock || props.product.stock || 0;
});

const isOutOfStock = computed(() => currentStock.value === 0);
const maxQuantity = computed(() => Math.min(currentStock.value, 10));

const isFavorite = computed(() => favorites.value.includes(props.product.id));
const cartItemsCount = computed(() => cart.value.reduce((total, item) => total + item.quantity, 0));

// Métodos
const selectColor = (color) => {
  selectedColor.value = color;
  currentImageIndex.value = 0;
};

const setImage = (index) => {
  currentImageIndex.value = index;
};

const incrementQuantity = () => {
  if (quantity.value < maxQuantity.value) quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) quantity.value--;
};

const toggleFavorite = () => {
  const index = favorites.value.indexOf(props.product.id);
  if (index > -1) {
    favorites.value.splice(index, 1);
  } else {
    favorites.value.push(props.product.id);
  }
};

const addToCart = () => {
  const colorId = selectedColor.value?.id;
  const existingIndex = cart.value.findIndex(
    item => item.productId === props.product.id && item.colorId === colorId
  );
  
  if (existingIndex > -1) {
    cart.value[existingIndex].quantity += quantity.value;
  } else {
    cart.value.push({
      productId: props.product.id,
      colorId: colorId,
      name: props.product.name,
      price: props.product.price,
      thumbnail: selectedColor.value?.image || props.product.thumbnail,
      colorName: selectedColor.value?.name,
      colorCode: selectedColor.value?.code,
      quantity: quantity.value
    });
  }
  
  // Feedback visual
  showAddedFeedback.value = true;
  setTimeout(() => showAddedFeedback.value = false, 2000);
};

const showAddedFeedback = ref(false);

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(price);
};

const isLightColor = (hexColor) => {
  if (!hexColor) return true;
  const hex = hexColor.replace('#', '');
  const r = parseInt(hex.substr(0, 2), 16);
  const g = parseInt(hex.substr(2, 2), 16);
  const b = parseInt(hex.substr(4, 2), 16);
  const brightness = (r * 299 + g * 587 + b * 114) / 1000;
  return brightness > 128;
};

const goToProduct = (slug) => {
  router.visit(`/tienda/${slug}`);
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-rose-50 via-fuchsia-50 to-violet-50">
    <AppHeader :user="user" :cartCount="cartItemsCount" />
    
    <!-- Breadcrumb -->
    <div class="bg-white/50 backdrop-blur-sm border-b border-purple-100">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm">
          <Link href="/" class="text-gray-500 hover:text-purple-600 transition-colors">
            Inicio
          </Link>
          <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          <Link href="/tienda" class="text-gray-500 hover:text-purple-600 transition-colors">
            Tienda
          </Link>
          <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          <span class="text-purple-600 font-medium">{{ product.name }}</span>
        </nav>
      </div>
    </div>

    <div class="container mx-auto px-4 py-10">
      <!-- Producto Principal -->
      <div class="bg-white rounded-3xl shadow-xl shadow-purple-100/30 overflow-hidden mb-16">
        <div class="flex flex-col lg:flex-row">
          
          <!-- Galería de imágenes -->
          <div class="lg:w-1/2 p-6 lg:p-8">
            <!-- Imagen principal -->
            <div class="relative aspect-square bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-2xl overflow-hidden mb-4">
              <img
                v-if="allImages.length > 0"
                :src="allImages[currentImageIndex]"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-32 h-32 text-purple-200" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>

              <!-- Badges -->
              <div class="absolute top-4 left-4 flex flex-col gap-2">
                <span v-if="isOutOfStock" class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                  Agotado
                </span>
                <span v-else-if="currentStock < 5" class="bg-amber-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg animate-pulse">
                  ¡Últimas {{ currentStock }}!
                </span>
              </div>

              <!-- Favorito -->
              <button
                @click="toggleFavorite"
                :class="[
                  'absolute top-4 right-4 w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-all duration-300',
                  isFavorite
                    ? 'bg-red-500 text-white scale-110'
                    : 'bg-white/90 backdrop-blur-sm text-gray-600 hover:text-red-500 hover:scale-110'
                ]"
              >
                <svg class="w-6 h-6" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>

            <!-- Miniaturas -->
            <div v-if="allImages.length > 1" class="flex gap-3 overflow-x-auto pb-2">
              <button
                v-for="(img, index) in allImages"
                :key="index"
                @click="setImage(index)"
                :class="[
                  'w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 border-2 transition-all',
                  currentImageIndex === index 
                    ? 'border-purple-500 ring-4 ring-purple-200' 
                    : 'border-transparent hover:border-purple-200'
                ]"
              >
                <img :src="img" :alt="`Imagen ${index + 1}`" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Información del producto -->
          <div class="lg:w-1/2 p-6 lg:p-8 lg:border-l border-gray-100">
            <!-- Categoría y SKU -->
            <div class="flex items-center justify-between mb-4">
              <span v-if="product.category" class="px-4 py-1.5 text-xs font-bold uppercase bg-gradient-to-r from-purple-100 to-fuchsia-100 text-purple-700 rounded-full">
                {{ product.category }}
              </span>
              <span class="text-xs text-gray-400">SKU: {{ product.sku }}</span>
            </div>

            <!-- Nombre -->
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
              {{ product.name }}
            </h1>

            <!-- Precio -->
            <div class="flex items-center gap-4 mb-6">
              <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-600 to-purple-600">
                {{ formatPrice(product.price) }}
              </span>
              <span v-if="!isOutOfStock" class="text-sm text-green-600 font-medium bg-green-50 px-4 py-2 rounded-full">
                {{ currentStock }} disponibles
              </span>
            </div>

            <!-- Descripción corta -->
            <p class="text-gray-600 mb-8 leading-relaxed text-lg">
              {{ product.description }}
            </p>

            <!-- Tamaño -->
            <div v-if="product.size" class="mb-6">
              <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Tamaño</h4>
              <span class="inline-block px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold">
                {{ product.size }}
              </span>
            </div>

            <!-- Selector de color -->
            <div v-if="product.colors && product.colors.length > 0" class="mb-8">
              <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">
                Color: <span class="text-purple-600 normal-case font-semibold">{{ selectedColor?.name || 'Selecciona' }}</span>
              </h4>
              <div class="flex flex-wrap gap-3">
                <button
                  v-for="color in product.colors"
                  :key="color.id"
                  @click="selectColor(color)"
                  :class="[
                    'w-12 h-12 rounded-full border-2 transition-all duration-200 relative group',
                    selectedColor?.id === color.id 
                      ? 'border-purple-500 ring-4 ring-purple-200 scale-110' 
                      : 'border-gray-200 hover:border-purple-300 hover:scale-105'
                  ]"
                  :style="{ backgroundColor: color.code }"
                  :title="color.name"
                >
                  <span v-if="selectedColor?.id === color.id" class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-6 h-6" :class="isLightColor(color.code) ? 'text-gray-800' : 'text-white'" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </span>
                  
                  <span v-if="color.stock === 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </span>

                  <!-- Tooltip -->
                  <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    {{ color.name }} ({{ color.stock }})
                  </span>
                </button>
              </div>
            </div>

            <!-- Selector de cantidad -->
            <div class="mb-8" v-if="!isOutOfStock">
              <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Cantidad</h4>
              <div class="flex items-center gap-4">
                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                  <button 
                    @click="decrementQuantity"
                    :disabled="quantity <= 1"
                    class="w-14 h-14 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-xl font-bold"
                  >
                    −
                  </button>
                  <span class="w-20 text-center font-bold text-xl text-gray-800">
                    {{ quantity }}
                  </span>
                  <button 
                    @click="incrementQuantity"
                    :disabled="quantity >= maxQuantity"
                    class="w-14 h-14 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-xl font-bold"
                  >
                    +
                  </button>
                </div>
                <span class="text-sm text-gray-500">
                  Máximo {{ maxQuantity }} unidades por pedido
                </span>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="space-y-4">
              <button
                @click="addToCart"
                :disabled="isOutOfStock"
                :class="[
                  'relative w-full py-5 rounded-2xl font-bold text-xl transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden',
                  isOutOfStock
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-gradient-to-r from-fuchsia-500 to-purple-600 text-white hover:shadow-2xl hover:shadow-purple-300/50 hover:scale-[1.02] active:scale-[0.98]'
                ]"
              >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ isOutOfStock ? 'Producto agotado' : `Agregar al carrito - ${formatPrice(product.price * quantity)}` }}

                <!-- Feedback de agregado -->
                <transition name="slide">
                  <span v-if="showAddedFeedback" class="absolute inset-0 bg-green-500 flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    ¡Agregado al carrito!
                  </span>
                </transition>
              </button>

              <div class="flex gap-4">
                <button
                  @click="toggleFavorite"
                  :class="[
                    'flex-1 py-4 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 border-2',
                    isFavorite
                      ? 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100'
                      : 'bg-white border-gray-200 text-gray-600 hover:border-purple-300 hover:text-purple-600'
                  ]"
                >
                  <svg class="w-5 h-5" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  {{ isFavorite ? 'En favoritos' : 'Agregar a favoritos' }}
                </button>

                <button class="flex-1 py-4 rounded-xl font-semibold border-2 border-gray-200 text-gray-600 hover:border-purple-300 hover:text-purple-600 transition-all duration-300 flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                  </svg>
                  Compartir
                </button>
              </div>
            </div>

            <!-- Info adicional -->
            <div class="mt-8 pt-8 border-t border-gray-100">
              <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl">
                  <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Envío</p>
                    <p class="text-sm font-semibold text-gray-700">A todo México</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-xl">
                  <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Compra segura</p>
                    <p class="text-sm font-semibold text-gray-700">Garantizado</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Productos relacionados -->
      <section v-if="relatedProducts.length > 0" class="mb-16">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-fuchsia-500 to-purple-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </div>
          También te puede interesar
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <article
            v-for="relatedProduct in relatedProducts"
            :key="relatedProduct.id"
            @click="goToProduct(relatedProduct.slug)"
            class="group bg-white rounded-2xl shadow-lg shadow-purple-100/30 overflow-hidden hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
          >
            <div class="aspect-square bg-gradient-to-br from-purple-50 to-fuchsia-50 overflow-hidden">
              <img
                v-if="relatedProduct.thumbnail"
                :src="relatedProduct.thumbnail"
                :alt="relatedProduct.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            <div class="p-4">
              <h3 class="font-bold text-gray-800 mb-1 line-clamp-1 group-hover:text-purple-600 transition-colors">
                {{ relatedProduct.name }}
              </h3>
              <p class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-600 to-purple-600">
                {{ formatPrice(relatedProduct.price) }}
              </p>
            </div>
          </article>
        </div>
      </section>
    </div>
    
    <AppFooter />
  </div>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}
</style>