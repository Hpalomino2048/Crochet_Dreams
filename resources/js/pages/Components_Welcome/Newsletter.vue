<script setup lang="ts">
import { ref } from 'vue'

const email = ref('')

const handleSubscribe = () => {
  if (email.value) {
    console.log('Subscribing:', email.value)
    // Aquí iría la lógica de suscripción
    email.value = ''
  }
}
</script>

<template>
  <section class="py-16 sm:py-20 bg-gradient-to-r from-purple-600 to-pink-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
      <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
        Únete a Nuestra Comunidad
      </h2>
      <p class="text-purple-100 text-lg mb-8 max-w-2xl mx-auto">
        Recibe los mejores patrones, tutoriales exclusivos y ofertas especiales directamente en tu correo
      </p>
      <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
        <input 
          v-model="email"
          type="email" 
          placeholder="Tu correo electrónico"
          class="flex-1 px-6 py-3 rounded-full border-2 border-white/30 bg-white/10 backdrop-blur-sm text-white placeholder-purple-200 focus:outline-none focus:border-white"
          @keyup.enter="handleSubscribe"
        />
        <button 
          @click="handleSubscribe"
          class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-purple-50 transition-colors shadow-lg"
        >
          Suscribirse
        </button>
      </div>
    </div>
  </section>
</template>