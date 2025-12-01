<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

import AppHeader from './Components_Welcome/AppHeader.vue'
import AppFooter from './Components_Welcome/AppFooter.vue'

type User = {
  name: string
  apellido_paterno?: string
  role?: string
}

// Usuario autenticado (igual que en Welcome)
const page = usePage()
const user = page.props.auth?.user as User | null

// Estado para el formulario de notificación
const email = ref<string>('')
const isSubmitting = ref<boolean>(false)
const successMessage = ref<string>('')

const handleNotifyMe = () => {
  isSubmitting.value = true

  // Aquí integrarás con tu backend cuando esté listo
  setTimeout(() => {
    successMessage.value = '¡Gracias! Te notificaremos cuando esté disponible.'
    email.value = ''
    isSubmitting.value = false

    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }, 1000)
}
</script>

<template>
  <Head title="Pedidos personalizados | Crochet Dreams" />

  <div class="min-h-screen bg-white">
    <AppHeader :user="user" />

    <!-- Hero Section -->
    <section class="relative bg-white py-20 px-5 text-center overflow-hidden">
      <!-- Decoraciones sutiles de fondo -->
      <div class="pointer-events-none absolute inset-0">
        <div class="absolute top-0 left-1/4 w-2 h-2 bg-purple-400 rounded-full opacity-40" />
        <div class="absolute top-20 right-1/3 w-3 h-3 bg-pink-400 rounded-full opacity-30" />
        <div class="absolute bottom-20 left-1/2 w-2 h-2 bg-purple-300 rounded-full opacity-40" />
        <div class="absolute top-1/2 right-1/4 w-2 h-2 bg-pink-300 rounded-full opacity-30" />
      </div>

      <div class="relative max-w-5xl mx-auto space-y-12">
        <!-- Título principal -->
        <div class="space-y-4">
          <h1 class="text-5xl md:text-6xl lg:text-7xl font-black">
            <span class="text-purple-600">✨</span>
            <span
              class="bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 bg-clip-text text-transparent px-3"
            >
              Pedidos Personalizados
            </span>
            <span class="text-purple-600">✨</span>
          </h1>

          <p class="text-xl md:text-2xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
            Crea tu pieza única tejida con
            <span class="font-bold text-pink-600">amor</span>
            y
            <span class="font-bold text-purple-600">dedicación</span>
          </p>
        </div>

        <!-- Línea decorativa -->
        <div class="flex items-center justify-center gap-4 pt-8">
          <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent" />
          <div class="text-purple-400">✦</div>
          <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent" />
        </div>
      </div>
    </section>

    <!-- Coming Soon Section -->
    <section class="py-20 px-5 bg-gradient-to-b from-purple-50/30 to-white">
      <div class="max-w-5xl mx-auto">
        <!-- Tarjeta principal -->
        <div class="bg-white border-2 border-purple-100 rounded-3xl shadow-sm overflow-hidden">
          <!-- Header con gradiente -->
          <div class="relative h-64 bg-gradient-to-r from-purple-600 to-pink-600 overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center text-white p-8">
                <div class="text-7xl mb-6 animate-pulse">
                  🎀
                </div>
                <h2 class="text-4xl md:text-5xl font-black mb-3">
                  Próximamente
                </h2>
                <p class="text-xl md:text-2xl opacity-90">
                  Estamos preparando algo especial para ti
                </p>
              </div>
            </div>

            <!-- Decoraciones -->
            <div
              class="absolute top-10 left-10 w-3 h-3 bg-white rounded-full opacity-30 animate-float"
            />
            <div
              class="absolute bottom-20 right-20 w-2 h-2 bg-white rounded-full opacity-40"
              style="animation: float 3s ease-in-out infinite;"
            />
            <div
              class="absolute top-1/2 right-10 w-3 h-3 bg-white rounded-full opacity-30"
              style="animation: float 5s ease-in-out infinite;"
            />
          </div>

          <div class="p-8 md:p-12">
            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 gap-8 mb-12">
              <!-- Feature 1 -->
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-7 h-7 text-purple-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Personalización Total
                  </h3>
                  <p class="text-gray-600 leading-relaxed">
                    Diseña tu pieza exactamente como la imaginas, eligiendo colores, tamaños y detalles
                    especiales
                  </p>
                </div>
              </div>

              <!-- Feature 2 -->
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-7 h-7 text-purple-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                      />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Hecho con Amor
                  </h3>
                  <p class="text-gray-600 leading-relaxed">
                    Cada pedido personalizado se teje a mano con la misma dedicación y cuidado de siempre
                  </p>
                </div>
              </div>

              <!-- Feature 3 -->
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-7 h-7 text-purple-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Tiempos Claros
                  </h3>
                  <p class="text-gray-600 leading-relaxed">
                    Conocerás el tiempo de elaboración desde el inicio para planificar tu regalo especial
                  </p>
                </div>
              </div>

              <!-- Feature 4 -->
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-7 h-7 text-purple-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"
                      />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Comunicación Directa
                  </h3>
                  <p class="text-gray-600 leading-relaxed">
                    Mantente en contacto durante todo el proceso para asegurar que tu pedido sea perfecto
                  </p>
                </div>
              </div>
            </div>

            <!-- Notify Me Form -->
            <div
              class="bg-gradient-to-b from-purple-50/50 to-pink-50/50 border-2 border-purple-100 rounded-2xl p-8"
            >
              <div class="text-center mb-6">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                  ¿Quieres ser el primero en saberlo?
                </h3>
                <p class="text-gray-600">
                  Te notificaremos cuando la sección de pedidos personalizados esté lista
                </p>
              </div>

              <form @submit.prevent="handleNotifyMe" class="max-w-md mx-auto">
                <div class="flex flex-col sm:flex-row gap-3">
                  <input
                    v-model="email"
                    type="email"
                    placeholder="tu@email.com"
                    required
                    class="flex-1 px-5 py-4 border-2 border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                  />
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl
                           hover:from-purple-700 hover:to-pink-700 hover:scale-105 hover:shadow-lg
                           transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                  >
                    {{ isSubmitting ? 'Enviando...' : 'Notificarme' }}
                  </button>
                </div>

                <p
                  v-if="successMessage"
                  class="text-green-600 font-semibold text-center mt-4"
                >
                  {{ successMessage }}
                </p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Custom Section -->
    <section class="py-20 px-5 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-black mb-4">
            <span
              class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"
            >
              ¿Por qué un pedido personalizado?
            </span>
          </h2>
          <div class="flex items-center justify-center gap-4">
            <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent" />
            <div class="text-purple-400 text-2xl">
              ✦
            </div>
            <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent" />
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <!-- Emoji decorativo -->
          <div class="flex justify-center items-center">
            <div class="relative">
              <div
                class="absolute inset-0 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full blur-2xl opacity-60"
              />
              <div class="relative text-8xl md:text-9xl animate-float">
                🧶
              </div>
            </div>
          </div>

          <!-- Contenido -->
          <div class="space-y-6">
            <p class="text-gray-700 text-lg leading-relaxed">
              Los pedidos personalizados te permiten crear algo
              <span class="font-bold text-purple-600">totalmente único</span>,
              perfecto para regalos especiales, celebraciones o simplemente para tener una pieza que
              nadie más tendrá.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed">
              Podrás elegir
              <span class="font-bold text-pink-600">colores, tamaños, detalles</span>
              y hasta agregar
              elementos personalizados como nombres, fechas o mensajes especiales tejidos en tu pieza.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed">
              Cada pedido personalizado es una
              <span class="font-bold text-purple-600">colaboración creativa</span>
              entre
              tú y CC Flowers, donde tus ideas se convierten en realidad hilo por hilo.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-5 bg-gradient-to-b from-purple-50/50 to-white text-center">
      <div class="max-w-3xl mx-auto space-y-8">
        <div class="space-y-4">
          <h2 class="text-4xl md:text-5xl font-black">
            <span
              class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"
            >
              Mientras tanto, explora nuestra tienda
            </span>
          </h2>

          <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto leading-relaxed">
            Descubre nuestra colección actual de productos únicos hechos a mano.
            Cada pieza está lista para enviar y llevar un toque especial a tu vida.
          </p>
        </div>

        <div class="pt-4">
          <Link
            href="/tienda"
            class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-10 rounded-full text-lg font-bold
                   hover:from-purple-700 hover:to-pink-700 hover:scale-105 hover:shadow-xl transition-all duration-300"
          >
            Ver tienda
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 7l5 5m0 0l-5 5m5-5H6"
              />
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <AppFooter />
  </div>
</template>

<style scoped>
/* Animación flotante suave */
@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-15px);
  }
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}
</style>
