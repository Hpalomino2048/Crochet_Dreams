<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  isFavorite: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'toggle-favorite', 'add-to-cart', 'view-details']);

// Estado local
const selectedColor = ref(null);
const quantity = ref(1);
const currentImageIndex = ref(0);
const isClosing = ref(false);

// Inicializar color seleccionado
onMounted(() => {
  if (props.product.colors && props.product.colors.length > 0) {
    const defaultColor = props.product.colors.find(c => c.is_default) || props.product.colors[0];
    selectedColor.value = defaultColor;
  }
  // Bloquear scroll del body
  document.body.style.overflow = 'hidden';
});

onUnmounted(() => {
  document.body.style.overflow = '';
});

// Computed
const currentImage = computed(() => {
  if (selectedColor.value?.image) {
    return selectedColor.value.image;
  }
  return props.product.thumbnail;
});

const allImages = computed(() => {
  const images = [];
  
  if (props.product.thumbnail) {
    images.push(props.product.thumbnail);
  }
  
  if (selectedColor.value?.gallery && selectedColor.value.gallery.length > 0) {
    images.push(...selectedColor.value.gallery);
  }
  
  if (props.product.images && props.product.images.length > 0) {
    images.push(...props.product.images);
  }
  
  return [...new Set(images)]; // Eliminar duplicados
});

const currentStock = computed(() => {
  if (selectedColor.value) {
    return selectedColor.value.stock;
  }
  return props.product.total_stock || props.product.stock || 0;
});

const isOutOfStock = computed(() => currentStock.value === 0);

const maxQuantity = computed(() => Math.min(currentStock.value, 10));

// Métodos
const selectColor = (color) => {
  selectedColor.value = color;
  currentImageIndex.value = 0;
};

const incrementQuantity = () => {
  if (quantity.value < maxQuantity.value) {
    quantity.value++;
  }
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const nextImage = () => {
  if (allImages.value.length > 1) {
    currentImageIndex.value = (currentImageIndex.value + 1) % allImages.value.length;
  }
};

const prevImage = () => {
  if (allImages.value.length > 1) {
    currentImageIndex.value = currentImageIndex.value === 0 
      ? allImages.value.length - 1 
      : currentImageIndex.value - 1;
  }
};

const handleAddToCart = () => {
  emit('add-to-cart', props.product, selectedColor.value?.id, quantity.value);
  closeModal();
};

const closeModal = () => {
  isClosing.value = true;
  setTimeout(() => {
    emit('close');
  }, 200);
};

const handleBackdropClick = (e) => {
  if (e.target === e.currentTarget) {
    closeModal();
  }
};

// Cerrar con ESC
const handleKeydown = (e) => {
  if (e.key === 'Escape') {
    closeModal();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});

// Formatear precio
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(price);
};
</script>

<template>
  <Teleport to="body">
    <div 
      :class="[
        'fixed inset-0 z-50 flex items-center justify-center p-4 transition-all duration-300',
        isClosing ? 'opacity-0' : 'opacity-100'
      ]"
      @click="handleBackdropClick"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
      
      <!-- Modal Content -->
      <div 
        :class="[
          'relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden transition-all duration-300',
          isClosing ? 'scale-95 opacity-0' : 'scale-100 opacity-100'
        ]"
      >
        <!-- Botón Cerrar -->
        <button 
          @click="closeModal"
          class="absolute top-4 right-4 z-20 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:scale-110 transition-all"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="flex flex-col lg:flex-row max-h-[90vh]">
          <!-- Galería de imágenes -->
          <div class="w-full lg:w-1/2 lg:mx-auto flex items-center justify-center relative bg-gradient-to-br from-purple-100 to-fuchsia-100">
            <!-- Imagen principal -->
            <div class="aspect-square relative overflow-hidden">
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

              <!-- Navegación de imágenes -->
              <template v-if="allImages.length > 1">
                <button 
                  @click="prevImage"
                  class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:scale-110 transition-all"
                >
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button 
                  @click="nextImage"
                  class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:scale-110 transition-all"
                >
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </template>

              <!-- Indicadores de imagen -->
              <div v-if="allImages.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                <button
                  v-for="(img, index) in allImages"
                  :key="index"
                  @click="currentImageIndex = index"
                  :class="[
                    'w-2 h-2 rounded-full transition-all',
                    currentImageIndex === index 
                      ? 'bg-white w-6' 
                      : 'bg-white/50 hover:bg-white/80'
                  ]"
                ></button>
              </div>

              <!-- Badge de agotado -->
              <span
                v-if="isOutOfStock"
                class="absolute top-4 left-4 bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg"
              >
                Agotado
              </span>
            </div>

            <!-- Miniaturas -->
            <div v-if="allImages.length > 1" class="p-4 flex gap-2 overflow-x-auto">
              <button
                v-for="(img, index) in allImages.slice(0, 6)"
                :key="index"
                @click="currentImageIndex = index"
                :class="[
                  'w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border-2 transition-all',
                  currentImageIndex === index 
                    ? 'border-purple-500 ring-2 ring-purple-200' 
                    : 'border-transparent hover:border-purple-200'
                ]"
              >
                <img :src="img" :alt="`Imagen ${index + 1}`" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Información del producto -->
          <div class="lg:w-1/2 p-6 lg:p-8 overflow-y-auto max-h-[60vh] lg:max-h-[90vh]">
            <!-- Categoría -->
            <span 
              v-if="product.category"
              class="inline-block px-3 py-1 text-xs font-bold uppercase bg-gradient-to-r from-purple-100 to-fuchsia-100 text-purple-700 rounded-full mb-3"
            >
              {{ product.category }}
            </span>

            <!-- Nombre -->
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-3">
              {{ product.name }}
            </h2>

            <!-- Precio -->
            <div class="flex items-center gap-4 mb-6">
              <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-600 to-purple-600">
                {{ formatPrice(product.price) }}
              </span>
              <span v-if="!isOutOfStock" class="text-sm text-green-600 font-medium bg-green-50 px-3 py-1 rounded-full">
                {{ currentStock }} disponibles
              </span>
            </div>

            <!-- Descripción -->
            <p class="text-gray-600 mb-6 leading-relaxed">
              {{ product.description }}
            </p>

            <!-- Tamaño -->
            <div v-if="product.size" class="mb-6">
              <h4 class="text-sm font-semibold text-gray-700 mb-2">Tamaño</h4>
              <span class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium">
                {{ product.size }}
              </span>
            </div>

            <!-- Selector de color -->
            <div v-if="product.colors && product.colors.length > 0" class="mb-6">
              <h4 class="text-sm font-semibold text-gray-700 mb-3">
                Color: <span class="text-purple-600">{{ selectedColor?.name || 'Selecciona un color' }}</span>
              </h4>
              <div class="flex flex-wrap gap-3">
                <button
                  v-for="color in product.colors"
                  :key="color.id"
                  @click="selectColor(color)"
                  :class="[
                    'w-10 h-10 rounded-full border-2 transition-all duration-200 relative',
                    selectedColor?.id === color.id 
                      ? 'border-purple-500 ring-4 ring-purple-200 scale-110' 
                      : 'border-gray-200 hover:border-purple-300 hover:scale-105'
                  ]"
                  :style="{ backgroundColor: color.code }"
                  :title="color.name"
                >
                  <!-- Check mark para color seleccionado -->
                  <span v-if="selectedColor?.id === color.id" class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-5 h-5" :class="isLightColor(color.code) ? 'text-gray-800' : 'text-white'" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </span>
                  
                  <!-- Indicador de sin stock -->
                  <span 
                    v-if="color.stock === 0" 
                    class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center"
                  >
                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </span>
                </button>
              </div>
            </div>

            <!-- Selector de cantidad -->
            <div class="mb-8" v-if="!isOutOfStock">
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Cantidad</h4>
              <div class="flex items-center gap-3">
                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                  <button 
                    @click="decrementQuantity"
                    :disabled="quantity <= 1"
                    class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="w-16 text-center font-bold text-lg text-gray-800">
                    {{ quantity }}
                  </span>
                  <button 
                    @click="incrementQuantity"
                    :disabled="quantity >= maxQuantity"
                    class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
                <span class="text-sm text-gray-500">
                  Máximo {{ maxQuantity }} unidades
                </span>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col gap-3">
              <!-- Agregar al carrito -->
              <button
                @click="handleAddToCart"
                :disabled="isOutOfStock"
                :class="[
                  'w-full py-4 rounded-2xl font-bold text-lg transition-all duration-300 flex items-center justify-center gap-3',
                  isOutOfStock
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-gradient-to-r from-fuchsia-500 to-purple-600 text-white hover:shadow-xl hover:shadow-purple-300/50 hover:scale-[1.02] active:scale-[0.98]'
                ]"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ isOutOfStock ? 'Producto agotado' : `Agregar al carrito - ${formatPrice(product.price * quantity)}` }}
              </button>

              <div class="flex gap-3">
                <!-- Favorito -->
                <button
                  @click="$emit('toggle-favorite')"
                  :class="[
                    'flex-1 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 border-2',
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
              </div>
            </div>

           
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
export default {
  methods: {
    // Determinar si un color es claro u oscuro
    isLightColor(hexColor) {
      if (!hexColor) return true;
      const hex = hexColor.replace('#', '');
      const r = parseInt(hex.substr(0, 2), 16);
      const g = parseInt(hex.substr(2, 2), 16);
      const b = parseInt(hex.substr(4, 2), 16);
      const brightness = (r * 299 + g * 587 + b * 114) / 1000;
      return brightness > 128;
    }
  }
}
</script>

<style scoped>
/* Ocultar scrollbar pero mantener funcionalidad */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #d1d5db;
}
</style>