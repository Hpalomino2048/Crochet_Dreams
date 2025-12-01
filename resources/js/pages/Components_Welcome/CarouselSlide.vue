<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Slide {
  id: number
  title: string
  subtitle: string
  discount: string
  image: string
  color: string
  badge?: string
  ctaText?: string
  urgent?: boolean
  timer?: boolean
  link?: string
}

interface Props {
  slide: Slide
  isActive: boolean
}

const props = defineProps<Props>()

// Countdown timer (opcional)
const timeLeft = ref({
  days: 0,
  hours: 0,
  minutes: 0,
  seconds: 0
})

let timerInterval: ReturnType<typeof setInterval> | null = null

// Calcular tiempo restante hasta fin de semana (ejemplo)
const calculateTimeLeft = () => {
  // Puedes ajustar esta fecha a tu fecha de fin de apertura
  const endDate = new Date()
  endDate.setDate(endDate.getDate() + 7) // 7 días de apertura
  endDate.setHours(23, 59, 59, 999)
  
  const now = new Date().getTime()
  const distance = endDate.getTime() - now
  
  if (distance > 0) {
    timeLeft.value = {
      days: Math.floor(distance / (1000 * 60 * 60 * 24)),
      hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
      minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
      seconds: Math.floor((distance % (1000 * 60)) / 1000)
    }
  }
}

onMounted(() => {
  if (props.slide.timer) {
    calculateTimeLeft()
    timerInterval = setInterval(calculateTimeLeft, 1000)
  }
})

onUnmounted(() => {
  if (timerInterval) {
    clearInterval(timerInterval)
  }
})

// Formato de números con padding
const padNumber = (num: number) => num.toString().padStart(2, '0')
</script>

<template>
  <div 
    :class="[
      'absolute inset-0 transition-all duration-700 ease-in-out',
      isActive ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-full'
    ]"
  >
    <div :class="['h-full bg-gradient-to-r', slide.color, 'flex items-center', 'relative overflow-hidden']">
      
      <!-- Elementos decorativos de fondo -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
      </div>

      <!-- Contenedor principal -->
      <div class="w-full px-6 sm:px-8 lg:px-16 relative z-10">
        <div class="max-w-[1400px] mx-auto">
          <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-16">
            
            <!-- Content -->
            <div class="flex-1 text-center lg:text-left space-y-4 lg:space-y-6">
              
              <!-- Badge superior -->
              <div class="flex items-center justify-center lg:justify-start gap-3 flex-wrap">
                <span 
                  :class="[
                    'inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold shadow-lg backdrop-blur-sm',
                    slide.urgent 
                      ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white animate-pulse' 
                      : 'bg-white/90 text-purple-600'
                  ]"
                >
                  <span v-if="slide.urgent" class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                  </span>
                  {{ slide.discount }}
                </span>
                
                <span 
                  v-if="slide.badge"
                  class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider shadow-lg"
                >
                  {{ slide.badge }}
                </span>
              </div>
              
              <!-- Título -->
              <h2 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black text-gray-900 leading-tight">
                {{ slide.title }}
              </h2>
              
              <!-- Subtítulo -->
              <p class="text-xl sm:text-2xl lg:text-3xl text-gray-700 font-semibold">
                {{ slide.subtitle }}
              </p>
              
              <!-- Countdown Timer (si está habilitado) -->
              <div 
                v-if="slide.timer" 
                class="bg-white/80 backdrop-blur-md rounded-2xl p-6 inline-block shadow-xl border-2 border-purple-200"
              >
                <p class="text-sm font-bold text-purple-600 mb-3 uppercase tracking-wider">
                  ⏰ La oferta termina en:
                </p>
                <div class="flex gap-4 justify-center lg:justify-start">
                  <!-- Días -->
                  <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-xl px-4 py-3 min-w-[70px] shadow-lg">
                      <span class="text-3xl font-black">{{ padNumber(timeLeft.days) }}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 mt-1 block">Días</span>
                  </div>
                  
                  <!-- Separador -->
                  <div class="text-3xl font-black text-purple-600 flex items-center">:</div>
                  
                  <!-- Horas -->
                  <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-xl px-4 py-3 min-w-[70px] shadow-lg">
                      <span class="text-3xl font-black">{{ padNumber(timeLeft.hours) }}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 mt-1 block">Horas</span>
                  </div>
                  
                  <!-- Separador -->
                  <div class="text-3xl font-black text-purple-600 flex items-center">:</div>
                  
                  <!-- Minutos -->
                  <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-xl px-4 py-3 min-w-[70px] shadow-lg">
                      <span class="text-3xl font-black">{{ padNumber(timeLeft.minutes) }}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 mt-1 block">Min</span>
                  </div>
                  
                  <!-- Separador -->
                  <div class="text-3xl font-black text-purple-600 flex items-center">:</div>
                  
                  <!-- Segundos -->
                  <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-xl px-4 py-3 min-w-[70px] shadow-lg">
                      <span class="text-3xl font-black">{{ padNumber(timeLeft.seconds) }}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 mt-1 block">Seg</span>
                  </div>
                </div>
              </div>
              
              <!-- CTA Button -->
              <div class="pt-2 lg:pt-4">
                <button 
                  :class="[
                    'px-10 py-4 rounded-full font-black text-lg transition-all shadow-xl transform hover:scale-105',
                    slide.urgent
                      ? 'bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white animate-pulse'
                      : 'bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white',
                    'hover:shadow-2xl hover:-translate-y-1'
                  ]"
                >
                  <span class="flex items-center gap-2">
                    {{ slide.ctaText || 'Ver Ofertas' }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </span>
                </button>
              </div>
            </div>
            
            <!-- Image/Emoji -->
            <div class="flex-1 flex justify-center lg:justify-end">
              <div class="relative">
                <!-- Efectos de fondo mejorados -->
                <div class="absolute inset-0 bg-white/30 rounded-full blur-3xl scale-110 animate-pulse-slow"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-purple-300/20 to-pink-300/20 rounded-full blur-2xl scale-125"></div>
                
                <!-- Partículas brillantes (opcional) -->
                <div v-if="slide.urgent" class="absolute inset-0">
                  <div class="absolute top-0 right-0 w-4 h-4 bg-yellow-400 rounded-full animate-ping"></div>
                  <div class="absolute bottom-0 left-0 w-3 h-3 bg-pink-400 rounded-full animate-ping animation-delay-200"></div>
                  <div class="absolute top-1/2 left-0 w-2 h-2 bg-purple-400 rounded-full animate-ping animation-delay-400"></div>
                </div>
                
                <!-- Emoji principal -->
                <div 
                  :class="[
                    'relative text-[10rem] sm:text-[14rem] lg:text-[18rem] xl:text-[20rem] drop-shadow-2xl',
                    slide.urgent ? 'animate-float-fast' : 'animate-float'
                  ]"
                >
                  {{ slide.image }}
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animación flotante suave */
@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) rotate(-2deg);
  }
  50% {
    transform: translateY(-20px) rotate(0deg);
  }
  75% {
    transform: translateY(-15px) rotate(2deg);
  }
}

/* Animación flotante rápida para urgente */
@keyframes float-fast {
  0%, 100% {
    transform: translateY(0) rotate(0deg) scale(1);
  }
  25% {
    transform: translateY(-20px) rotate(-3deg) scale(1.05);
  }
  50% {
    transform: translateY(-25px) rotate(0deg) scale(1.1);
  }
  75% {
    transform: translateY(-20px) rotate(3deg) scale(1.05);
  }
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

.animate-float-fast {
  animation: float-fast 2s ease-in-out infinite;
}

/* Pulse lento para fondo */
@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.1);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 3s ease-in-out infinite;
}

/* Delays para animaciones */
.animation-delay-200 {
  animation-delay: 200ms;
}

.animation-delay-400 {
  animation-delay: 400ms;
}
</style>