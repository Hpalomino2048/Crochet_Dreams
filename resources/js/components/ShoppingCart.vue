<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'update-cart']);

// Obtener usuario autenticado de Inertia
const page = usePage();
const user = computed(() => page.props.auth?.user || null);

// =====================================================
// DIRECCIONES DE ENVÍO - PUERTO VALLARTA
// =====================================================
const shippingLocations = ref([
  {
    id: 'galerias',
    name: 'Galerías Vallarta',
    address: 'Av. Francisco Medina Ascencio 2920, Zona Hotelera Norte',
    city: 'Puerto Vallarta',
    state: 'Jalisco',
    zipCode: '48333',
    price: 50,
    estimatedDays: '1-2 días',
    icon: '🏬'
  },
  {
    id: 'isla',
    name: 'La Isla Shopping Village',
    address: 'Blvd. Francisco Medina Ascencio 2479, Zona Hotelera Norte',
    city: 'Puerto Vallarta',
    state: 'Jalisco',
    zipCode: '48333',
    price: 60,
    estimatedDays: '1-2 días',
    icon: '🌴'
  },
  {
    id: 'malecon',
    name: 'Malecón Puerto Vallarta',
    address: 'Paseo Díaz Ordaz, Centro',
    city: 'Puerto Vallarta',
    state: 'Jalisco',
    zipCode: '48300',
    price: 45,
    estimatedDays: '1-2 días',
    icon: '🌊'
  },
  {
    id: 'custom',
    name: 'Otra dirección',
    address: '',
    city: '',
    state: '',
    zipCode: '',
    price: 99,
    estimatedDays: '2-4 días',
    icon: '📍'
  }
]);

// Estado
const cart = ref([]);
const isProcessing = ref(false);
const showCheckout = ref(false);
const selectedShippingId = ref('galerias');

const customerData = ref({
  name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zipCode: '',
  notes: ''
});

// =====================================================
// KEY ÚNICO POR USUARIO
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

// =====================================================
// CARGAR Y GUARDAR CARRITO
// =====================================================
const loadCart = () => {
  try {
    const cartKey = getCartKey();
    const savedCart = localStorage.getItem(cartKey);
    if (savedCart) {
      const parsed = JSON.parse(savedCart);
      // Validar que sea un array
      if (Array.isArray(parsed)) {
        cart.value = parsed;
      } else {
        cart.value = [];
      }
    } else {
      cart.value = [];
    }
    emit('update-cart', itemsCount.value);
  } catch (error) {
    console.error('Error al cargar el carrito:', error);
    cart.value = [];
  }
};

const saveCart = () => {
  try {
    const cartKey = getCartKey();
    localStorage.setItem(cartKey, JSON.stringify(cart.value));
    emit('update-cart', itemsCount.value);
  } catch (error) {
    console.error('Error al guardar el carrito:', error);
  }
};

// =====================================================
// FUNCIÓN GLOBAL PARA AGREGAR AL CARRITO (AGRUPA PRODUCTOS)
// =====================================================
const addToCart = (product) => {
  // Crear key único: productId + colorId
  const itemKey = `${product.productId}-${product.colorId || 'default'}`;
  
  // Buscar si ya existe en el carrito
  const existingIndex = cart.value.findIndex(item => {
    const existingKey = `${item.productId}-${item.colorId || 'default'}`;
    return existingKey === itemKey;
  });
  
  if (existingIndex !== -1) {
    // Ya existe: incrementar cantidad
    cart.value[existingIndex].quantity += (product.quantity || 1);
    // Limitar a 99
    if (cart.value[existingIndex].quantity > 99) {
      cart.value[existingIndex].quantity = 99;
    }
  } else {
    // No existe: agregar nuevo
    cart.value.push({
      productId: product.productId,
      colorId: product.colorId || null,
      name: product.name,
      colorName: product.colorName || null,
      colorCode: product.colorCode || null,
      price: product.price,
      quantity: product.quantity || 1,
      thumbnail: product.thumbnail || null
    });
  }
  
  saveCart();
};

// Exponer función globalmente para que otros componentes puedan usarla
if (typeof window !== 'undefined') {
  window.addToCart = addToCart;
}

// Pre-llenar datos del usuario
const prefillUserData = () => {
  if (user.value) {
    customerData.value.name = user.value.name || '';
    customerData.value.email = user.value.email || '';
    customerData.value.phone = user.value.phone || '';
  }
};

// Sincronizar con otras pestañas
const handleStorageChange = (event) => {
  const cartKey = getCartKey();
  if (event.key === cartKey) {
    loadCart();
  }
};

onMounted(() => {
  loadCart();
  prefillUserData();
  // Escuchar cambios en localStorage de otras pestañas
  window.addEventListener('storage', handleStorageChange);
});

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange);
});

// =====================================================
// COMPUTED
// =====================================================
const selectedShipping = computed(() => {
  return shippingLocations.value.find(loc => loc.id === selectedShippingId.value) || shippingLocations.value[0];
});

const isCustomAddress = computed(() => selectedShippingId.value === 'custom');

const subtotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const shipping = computed(() => {
  if (subtotal.value >= 800) return 0;
  return selectedShipping.value.price;
});

const tax = computed(() => subtotal.value * 0.16);

const total = computed(() => subtotal.value + shipping.value + tax.value);

const itemsCount = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.quantity, 0);
});

// =====================================================
// MÉTODOS
// =====================================================
const increaseQuantity = (index) => {
  if (cart.value[index].quantity < 99) {
    cart.value[index].quantity++;
    saveCart();
  }
};

const decreaseQuantity = (index) => {
  if (cart.value[index].quantity > 1) {
    cart.value[index].quantity--;
    saveCart();
  }
};

const removeItem = (index) => {
  cart.value.splice(index, 1);
  saveCart();
};

const clearCart = () => {
  if (confirm('¿Vaciar el carrito?')) {
    cart.value = [];
    saveCart();
  }
};

const closeCart = () => {
  showCheckout.value = false;
  emit('close');
};

const goToCheckout = () => {
  showCheckout.value = true;
};

const backToCart = () => {
  showCheckout.value = false;
};

const selectShipping = (locationId) => {
  selectedShippingId.value = locationId;
  if (locationId !== 'custom') {
    const location = shippingLocations.value.find(loc => loc.id === locationId);
    if (location) {
      customerData.value.address = location.address;
      customerData.value.city = location.city;
      customerData.value.state = location.state;
      customerData.value.zipCode = location.zipCode;
    }
  } else {
    customerData.value.address = '';
    customerData.value.city = '';
    customerData.value.state = '';
    customerData.value.zipCode = '';
  }
};

const isFormValid = computed(() => {
  const baseValid = customerData.value.name.trim() !== '' &&
         customerData.value.email.trim() !== '' &&
         customerData.value.phone.trim() !== '';
  if (isCustomAddress.value) {
    return baseValid && customerData.value.address.trim() !== '';
  }
  return baseValid;
});

const processPayment = async () => {
  if (!isFormValid.value) {
    alert('Por favor completa todos los campos obligatorios');
    return;
  }

  isProcessing.value = true;

  try {
    const finalAddress = isCustomAddress.value ? customerData.value.address : selectedShipping.value.address;
    const finalCity = isCustomAddress.value ? customerData.value.city : selectedShipping.value.city;
    const finalState = isCustomAddress.value ? customerData.value.state : selectedShipping.value.state;
    const finalZipCode = isCustomAddress.value ? customerData.value.zipCode : selectedShipping.value.zipCode;

    const orderData = {
      items: cart.value.map(item => ({
        product_id: item.productId,
        product_color_id: item.colorId || null,
        product_name: item.name,
        color_name: item.colorName || null,
        product_price: item.price,
        quantity: item.quantity,
        subtotal: item.price * item.quantity
      })),
      customer: {
        name: customerData.value.name,
        email: customerData.value.email,
        phone: customerData.value.phone,
        address: finalAddress,
        city: finalCity,
        state: finalState,
        zip_code: finalZipCode
      },
      shipping: {
        location_id: selectedShippingId.value,
        location_name: selectedShipping.value.name,
        cost: shipping.value
      },
      user_id: user.value?.id || null,
      subtotal: subtotal.value,
      shipping_cost: shipping.value,
      tax: tax.value,
      total: total.value,
      notes: customerData.value.notes || ''
    };

    const response = await fetch('/api/create-order', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify(orderData)
    });

    const result = await response.json();

    if (result.success) {
      cart.value = [];
      saveCart();
      if (result.payment_url) {
        window.location.href = result.payment_url;
      } else {
        closeCart();
        if (result.order_number) {
          window.location.href = `/order-confirmation/${result.order_number}`;
        }
      }
    } else {
      alert('Error: ' + (result.message || 'Error desconocido'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error al procesar la orden');
  } finally {
    isProcessing.value = false;
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(price);
};

// Watches
watch(() => user.value?.id, () => {
  loadCart();
  prefillUserData();
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loadCart();
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

// Exponer métodos para uso externo
defineExpose({ addToCart, loadCart });
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        role="dialog"
        aria-modal="true"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeCart"></div>

        <!-- Modal -->
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl max-h-[90vh] flex flex-col overflow-hidden" @click.stop>
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4 flex-shrink-0">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-xl font-bold">{{ showCheckout ? 'Finalizar Compra' : 'Mi Carrito' }}</h2>
                    <p class="text-white/80 text-sm">{{ itemsCount }} artículo{{ itemsCount !== 1 ? 's' : '' }}</p>
                  </div>
                </div>
                <button @click="closeCart" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Contenido -->
            <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
              
              <!-- Carrito Vacío -->
              <div v-if="cart.length === 0" class="flex-1 flex flex-col items-center justify-center p-8">
                <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                  <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tu carrito está vacío</h3>
                <p class="text-gray-500 mb-6">¡Agrega productos increíbles!</p>
                <button @click="closeCart" class="px-6 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                  Explorar productos
                </button>
              </div>

              <!-- Lista de Productos -->
              <div v-else-if="!showCheckout" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="space-y-3">
                  <div
                    v-for="(item, index) in cart"
                    :key="`${item.productId}-${item.colorId || 'default'}`"
                    class="bg-white rounded-xl p-4 shadow-sm flex gap-4 items-center"
                  >
                    <!-- Imagen -->
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                      <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-gray-900 truncate">{{ item.name }}</h4>
                      <div v-if="item.colorName" class="flex items-center gap-1.5 mt-1">
                        <span v-if="item.colorCode" :style="{ backgroundColor: item.colorCode }" class="w-3 h-3 rounded-full border border-gray-200"></span>
                        <span class="text-xs text-gray-500">{{ item.colorName }}</span>
                      </div>
                      <p class="text-purple-600 font-medium mt-1">{{ formatPrice(item.price) }}</p>
                    </div>

                    <!-- Cantidad -->
                    <div class="flex items-center gap-2">
                      <button @click="decreaseQuantity(index)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-gray-600" :disabled="item.quantity <= 1">−</button>
                      <span class="w-8 text-center font-semibold">{{ item.quantity }}</span>
                      <button @click="increaseQuantity(index)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-gray-600">+</button>
                    </div>

                    <!-- Subtotal -->
                    <div class="text-right min-w-[80px]">
                      <p class="font-bold text-purple-600">{{ formatPrice(item.price * item.quantity) }}</p>
                    </div>

                    <!-- Eliminar -->
                    <button @click="removeItem(index)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>

                <button @click="clearCart" class="mt-4 w-full py-2 text-red-500 hover:bg-red-50 rounded-lg text-sm font-medium border border-red-200">
                  🗑️ Vaciar carrito
                </button>
              </div>

              <!-- Checkout Form -->
              <div v-else class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="space-y-4">
                  
                  <!-- Datos personales -->
                  <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-3">👤 Datos personales</h3>
                    <div class="space-y-3">
                      <input v-model="customerData.name" type="text" placeholder="Nombre completo *" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                      <div class="grid grid-cols-2 gap-3">
                        <input v-model="customerData.email" type="email" placeholder="Email *" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        <input v-model="customerData.phone" type="tel" placeholder="Teléfono *" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                      </div>
                    </div>
                  </div>

                  <!-- Punto de entrega -->
                  <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-3">📍 Punto de entrega</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                      <button
                        v-for="location in shippingLocations"
                        :key="location.id"
                        @click="selectShipping(location.id)"
                        :class="[
                          'p-3 rounded-xl border-2 text-left transition-all',
                          selectedShippingId === location.id 
                            ? 'border-purple-500 bg-purple-50' 
                            : 'border-gray-200 hover:border-purple-300'
                        ]"
                      >
                        <div class="flex items-center gap-2">
                          <span class="text-xl">{{ location.icon }}</span>
                          <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ location.name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ location.address || 'Ingresa tu dirección' }}</p>
                          </div>
                          <div class="text-right">
                            <p class="font-bold text-purple-600 text-sm">{{ formatPrice(location.price) }}</p>
                            <p class="text-xs text-gray-400">{{ location.estimatedDays }}</p>
                          </div>
                        </div>
                      </button>
                    </div>

                    <!-- Dirección personalizada -->
                    <div v-if="isCustomAddress" class="mt-3 space-y-2">
                      <input v-model="customerData.address" type="text" placeholder="Dirección completa *" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" />
                      <div class="grid grid-cols-3 gap-2">
                        <input v-model="customerData.city" type="text" placeholder="Ciudad" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" />
                        <input v-model="customerData.state" type="text" placeholder="Estado" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" />
                        <input v-model="customerData.zipCode" type="text" placeholder="C.P." class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" />
                      </div>
                    </div>
                  </div>

                  <!-- Notas -->
                  <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-2">📝 Notas (opcional)</h3>
                    <textarea v-model="customerData.notes" rows="2" placeholder="Instrucciones especiales..." class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 resize-none"></textarea>
                  </div>
                </div>
              </div>

              <!-- Panel Resumen -->
              <div v-if="cart.length > 0" class="w-full lg:w-72 bg-white border-t lg:border-t-0 lg:border-l p-4 flex flex-col flex-shrink-0">
                <h3 class="font-bold text-gray-900 mb-3">Resumen</h3>

                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">{{ formatPrice(subtotal) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Envío ({{ selectedShipping.name }})</span>
                    <span :class="shipping === 0 ? 'text-green-600 font-medium' : 'font-medium'">
                      {{ shipping === 0 ? '¡Gratis!' : formatPrice(shipping) }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">IVA (16%)</span>
                    <span class="font-medium">{{ formatPrice(tax) }}</span>
                  </div>

                  <!-- Envío gratis -->
                  <div v-if="subtotal < 800" class="bg-amber-50 rounded-lg p-2 mt-2">
                    <p class="text-xs text-amber-700">
                      Te faltan <strong>{{ formatPrice(800 - subtotal) }}</strong> para envío gratis
                    </p>
                    <div class="w-full bg-amber-200 rounded-full h-1 mt-1">
                      <div class="bg-amber-500 h-1 rounded-full" :style="{ width: `${(subtotal / 800) * 100}%` }"></div>
                    </div>
                  </div>
                  <div v-else class="bg-green-50 rounded-lg p-2 mt-2">
                    <p class="text-xs text-green-700 font-medium">✓ ¡Envío gratis!</p>
                  </div>

                  <div class="pt-3 mt-2 border-t">
                    <div class="flex justify-between items-center">
                      <span class="font-bold text-gray-900">Total</span>
                      <span class="text-xl font-black text-purple-600">{{ formatPrice(total) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Botones -->
                <div class="mt-4 space-y-2">
                  <button
                    v-if="!showCheckout"
                    @click="goToCheckout"
                    class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 transition-all"
                  >
                    Proceder al pago
                  </button>

                  <template v-else>
                    <button
                      @click="processPayment"
                      :disabled="!isFormValid || isProcessing"
                      class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                      <svg v-if="isProcessing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>
                      {{ isProcessing ? 'Procesando...' : 'Confirmar pedido' }}
                    </button>
                    <button @click="backToCart" class="w-full py-2 text-gray-600 hover:text-gray-900 text-sm">
                      ← Volver al carrito
                    </button>
                  </template>

                  <button v-if="!showCheckout" @click="closeCart" class="w-full py-2 text-gray-500 text-sm hover:text-gray-700">
                    Seguir comprando
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>