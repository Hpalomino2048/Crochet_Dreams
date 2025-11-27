<script setup>
import { Link } from '@inertiajs/vue3';
import AppHeader from './Components_Welcome/AppHeader.vue';
import AppFooter from './Components_Welcome/AppFooter.vue';
defineProps({
    user: Object
});

const goToShop = () => {
    window.location.href = '/tienda';
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-purple-50 to-white">
        <AppHeader :user="user" />
        
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-purple-500 to-pink-500 py-20 px-5 text-center text-white">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl font-bold mb-4">Nuestra Historia</h1>
                <p class="text-xl opacity-95">Tejiendo sueños con amor y dedicación</p>
            </div>
        </section>

        <!-- About Content -->
        <section class="py-20 px-5">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Mission -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl text-gray-800 mb-4">Mi Misión</h2>
                    <p class="text-gray-600 leading-relaxed">Crear productos únicos y especiales hechos a mano con amor. Cada amigurumi es cuidadosamente tejido para traer alegría y ternura a tu vida, ya sea como regalo o para tu colección personal.</p>
                </div>

                <!-- Vision -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl text-gray-800 mb-4">Mi Visión</h2>
                    <p class="text-gray-600 leading-relaxed">Seguir creciendo como artesana y llevar mis creaciones a más personas, manteniendo siempre la esencia artesanal y el amor en cada puntada. Quiero que CC Flowers sea sinónimo de calidad y ternura.</p>
                </div>

                <!-- Values -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl text-gray-800 mb-4">Mis Valores</h2>
                    <p class="text-gray-600 leading-relaxed">Autenticidad, dedicación y amor por lo que hago. Cada producto refleja mi pasión por el crochet y mi compromiso con quienes eligen mis creaciones para regalar o coleccionar.</p>
                </div>
            </div>
        </section>

        <!-- Story Section -->
        <section class="py-20 px-5 bg-white">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="flex justify-center items-center">
                    <div class="text-9xl animate-bounce">
                        🧶
                    </div>
                </div>
                <div>
                    <h2 class="text-4xl text-gray-800 mb-6">¿Quiénes Somos?</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-5">Hola, soy CC Flowers 💕 Tengo 21 años y hace 2 años decidí convertir mi pasión por el crochet en este hermoso emprendimiento estudiantil. Todo comenzó mientras estudiaba, buscando una forma de expresar mi creatividad y generar ingresos haciendo lo que amo.</p>
                    <p class="text-gray-600 text-lg leading-relaxed mb-5">Cada pieza es 100% artesanal y hecha con amor 🧶 Trabajo desde Puerto Vallarta, creando amigurumis únicos, personajes adorables, ropa y accesorios especiales. Cada puntada lleva mi dedicación y el cariño de quienes han confiado en este pequeño emprendimiento.</p>
                    <p class="text-gray-600 text-lg leading-relaxed">Lo que más me emociona es ver cómo mis creaciones traen sonrisas y calidez a las personas. Gracias a todos por su confianza en este camino 🌸✨</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-20 px-5 bg-gradient-to-br from-purple-100 to-pink-100">
            <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="text-center">
                    <h3 class="text-5xl font-bold bg-gradient-to-br from-purple-500 to-pink-500 bg-clip-text text-transparent mb-2">120+</h3>
                    <p class="text-gray-600 font-medium">Patrones de Amigurumis</p>
                </div>
                <div class="text-center">
                    <h3 class="text-5xl font-bold bg-gradient-to-br from-purple-500 to-pink-500 bg-clip-text text-transparent mb-2">85+</h3>
                    <p class="text-gray-600 font-medium">Diseños de Ropa</p>
                </div>
                <div class="text-center">
                    <h3 class="text-5xl font-bold bg-gradient-to-br from-purple-500 to-pink-500 bg-clip-text text-transparent mb-2">95+</h3>
                    <p class="text-gray-600 font-medium">Ideas de Accesorios</p>
                </div>
                <div class="text-center">
                    <h3 class="text-5xl font-bold bg-gradient-to-br from-purple-500 to-pink-500 bg-clip-text text-transparent mb-2">60+</h3>
                    <p class="text-gray-600 font-medium">Proyectos de Decoración</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-5 text-center">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-4xl text-gray-800 mb-4">¿Listo para comenzar tu proyecto?</h2>
                <p class="text-gray-600 text-lg mb-8">Explora nuestra tienda y encuentra todo lo que necesitas</p>
                <Link href="/tienda" class="inline-block bg-gradient-to-br from-purple-500 to-pink-500 text-white py-4 px-12 rounded-full text-lg font-semibold hover:scale-105 hover:shadow-2xl transition-all duration-300">
                    Ver Tienda
                </Link>
            </div>
        </section>
        <AppFooter />
    </div>
</template>

<style scoped>
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-bounce {
    animation: bounce 3s ease-in-out infinite;
}
</style>