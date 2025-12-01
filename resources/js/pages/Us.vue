<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppHeader from './Components_Welcome/AppHeader.vue';
import AppFooter from './Components_Welcome/AppFooter.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || null);

const goToShop = () => {
    window.location.href = '/tienda';
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <AppHeader :user="user" />
        
        <!-- Hero Section - Estilo minimalista -->
        <section class="relative bg-white py-20 px-5 text-center overflow-hidden">
            <!-- Decoraciones sutiles de fondo -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute top-0 left-1/4 w-2 h-2 bg-purple-400 rounded-full opacity-40"></div>
                <div class="absolute top-20 right-1/3 w-3 h-3 bg-pink-400 rounded-full opacity-30"></div>
                <div class="absolute bottom-20 left-1/2 w-2 h-2 bg-purple-300 rounded-full opacity-40"></div>
                <div class="absolute top-1/2 right-1/4 w-2 h-2 bg-pink-300 rounded-full opacity-30"></div>
            </div>

            <div class="relative max-w-5xl mx-auto space-y-12">
                <!-- Título principal con estrellas -->
                <div class="space-y-4">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black">
                        <span class="text-purple-600">✨</span>
                        <span class="bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 bg-clip-text text-transparent px-3">
                            Nuestra Historia
                        </span>
                        <span class="text-purple-600">✨</span>
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                        Tejiendo piezas únicas hechas a mano con 
                        <span class="font-bold text-pink-600">amor</span> y 
                        <span class="font-bold text-purple-600">dedicación</span>
                    </p>
                </div>

                <!-- Stats section minimalista -->
                <div class="flex justify-center items-center gap-8 md:gap-16 flex-wrap pt-8">
                    <!-- Productos -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                            20+
                        </div>
                        <div class="text-sm md:text-base text-gray-700 font-semibold">
                            Productos
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="hidden md:block w-px h-12 bg-gradient-to-b from-transparent via-purple-300 to-transparent"></div>

                    <!-- Artesanal -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                            100%
                        </div>
                        <div class="text-sm md:text-base text-gray-700 font-semibold">
                            Artesanal
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="hidden md:block w-px h-12 bg-gradient-to-b from-transparent via-purple-300 to-transparent"></div>

                    <!-- Con Amor -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                            ♥
                        </div>
                        <div class="text-sm md:text-base text-gray-700 font-semibold">
                            Con Amor
                        </div>
                    </div>
                </div>

                <!-- Línea decorativa -->
                <div class="flex items-center justify-center gap-4 pt-8">
                    <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
                    <div class="text-purple-400">✦</div>
                    <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
                </div>
            </div>
        </section>

        <!-- About Content -->
        <section class="py-20 px-5 bg-gradient-to-b from-purple-50/30 to-white">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Misión -->
                <div class="group relative bg-white border-2 border-purple-100 p-10 rounded-3xl shadow-sm hover:shadow-xl hover:border-purple-300 transition-all duration-300 text-center">
                    <!-- Icono minimalista -->
                    <div class="relative w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-600">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl text-gray-900 mb-4 font-bold">
                        Mi Misión
                    </h2>
                    
                    <!-- Línea decorativa -->
                    <div class="w-12 h-1 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-400 to-pink-400"></div>
                    
                    <p class="text-gray-600 leading-relaxed">
                        Diseñar y elaborar productos tejidos a mano con altos estándares de calidad,
                        cuidando cada detalle para que cada pieza sea especial, duradera y significativa
                        para quien la recibe.
                    </p>
                </div>

                <!-- Visión -->
                <div class="group relative bg-white border-2 border-purple-100 p-10 rounded-3xl shadow-sm hover:shadow-xl hover:border-purple-300 transition-all duration-300 text-center">
                    <div class="relative w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-600">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl text-gray-900 mb-4 font-bold">
                        Mi Visión
                    </h2>
                    
                    <div class="w-12 h-1 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-400 to-pink-400"></div>
                    
                    <p class="text-gray-600 leading-relaxed">
                        Consolidar a CC Flowers como un emprendimiento de referencia en crochet
                        artesanal, iniciando con una colección pequeña de productos y creciendo de
                        forma sostenible, manteniendo siempre la cercanía con cada cliente.
                    </p>
                </div>

                <!-- Valores -->
                <div class="group relative bg-white border-2 border-purple-100 p-10 rounded-3xl shadow-sm hover:shadow-xl hover:border-purple-300 transition-all duration-300 text-center">
                    <div class="relative w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-600">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl text-gray-900 mb-4 font-bold">
                        Mis Valores
                    </h2>
                    
                    <div class="w-12 h-1 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-400 to-pink-400"></div>
                    
                    <p class="text-gray-600 leading-relaxed">
                        Honestidad, compromiso y respeto por el trabajo artesanal. Cada pieza refleja
                        el tiempo invertido, la dedicación al detalle y el deseo de ofrecer un producto
                        bien hecho, especialmente en esta etapa inicial del negocio.
                    </p>
                </div>
            </div>
        </section>

        <!-- Story Section - Rediseñada -->
        <section class="py-20 px-5 bg-white">
            <div class="max-w-7xl mx-auto">
                <!-- Header de sección -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black mb-4">
                        <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            ¿Quiénes Somos?
                        </span>
                    </h2>
                    <div class="flex items-center justify-center gap-4">
                        <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
                        <div class="text-purple-400 text-2xl">✦</div>
                        <div class="w-20 h-px bg-gradient-to-r from-transparent via-purple-300 to-transparent"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Emoji decorativo -->
                    <div class="flex justify-center items-center order-2 lg:order-1">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full blur-2xl opacity-60"></div>
                            <div class="relative text-8xl md:text-9xl animate-float">
                                🧶
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="order-1 lg:order-2 space-y-6">
                        <p class="text-gray-700 text-lg leading-relaxed">
                            <span class="font-bold text-purple-600">CC Flowers</span> es un emprendimiento estudiantil de crochet que nace del gusto
                            por crear piezas tejidas a mano. A lo largo del tiempo, esta afición se ha
                            convertido en un proyecto formal que hoy se presenta como una pequeña marca
                            en crecimiento.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Actualmente contamos con una colección inicial de aproximadamente <span class="font-bold text-pink-600">20 artículos
                            disponibles</span> y un número similar de ventas realizadas, lo que nos permite
                            aprender, mejorar y adaptar cada diseño a las preferencias de quienes confían
                            en nuestro trabajo.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Cada producto se elabora de manera artesanal desde <span class="font-bold text-purple-600">Puerto Vallarta, Jalisco</span>,
                            cuidando materiales, acabados y presentación. El objetivo es que cada pieza
                            llegue a su nuevo hogar transmitiendo calidez, dedicación y una experiencia
                            de compra cercana.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section - Rediseñada -->
        <section class="py-20 px-5 bg-gradient-to-b from-purple-50/50 to-white">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Stat 1 -->
                    <div class="text-center bg-white border-2 border-purple-100 rounded-2xl p-8 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                        <div class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                            20
                        </div>
                        <div class="text-gray-700 font-semibold">
                            Productos activos en catálogo
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="text-center bg-white border-2 border-purple-100 rounded-2xl p-8 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                        <div class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                            20+
                        </div>
                        <div class="text-gray-700 font-semibold">
                            Ventas realizadas
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="text-center bg-white border-2 border-purple-100 rounded-2xl p-8 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                        <div class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                            100%
                        </div>
                        <div class="text-gray-700 font-semibold">
                            Producción artesanal
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div class="text-center bg-white border-2 border-purple-100 rounded-2xl p-8 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                        <div class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                            1 a 1
                        </div>
                        <div class="text-gray-700 font-semibold">
                            Atención personalizada
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section - Rediseñada -->
        <section class="py-20 px-5 text-center bg-white">
            <div class="max-w-3xl mx-auto space-y-8">
                <div class="space-y-4">
                    <h2 class="text-4xl md:text-5xl font-black">
                        <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            ¿Te gustaría conocer nuestra colección?
                        </span>
                    </h2>
                    
                    <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto leading-relaxed">
                        Explora nuestra tienda en línea y descubre las piezas disponibles. 
                        Cada compra contribuye directamente al crecimiento del emprendimiento.
                    </p>
                </div>

                <div class="pt-4">
                    <Link
                        href="/tienda"
                        class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-10 rounded-full text-lg font-bold
                               hover:from-purple-700 hover:to-pink-700 hover:scale-105 hover:shadow-xl transition-all duration-300"
                    >
                        Ver tienda
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
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
    0%, 100% {
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