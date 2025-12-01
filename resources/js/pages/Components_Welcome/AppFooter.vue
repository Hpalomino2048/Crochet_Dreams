<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- Brand -->
        <div>
          <div class="flex items-center space-x-2 mb-4">
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
              <span class="text-white font-bold">🧸</span>
            </div>
            <span class="text-xl font-bold text-white">Crochet Dreams</span>
          </div>
          <p class="text-sm text-gray-400">
            Tu destino para patrones únicos y creativos de crochet
          </p>
        </div>

        <!-- Enlaces Rápidos -->
        <div>
          <h4 class="font-semibold text-white mb-4">Enlaces Rápidos</h4>
          <ul class="space-y-2 text-sm">
            <li><Link href="/shop" class="hover:text-purple-400 transition-colors">Tienda</Link></li>
            <li><Link href="/about" class="hover:text-purple-400 transition-colors">Nosotros</Link></li>
            <li><Link href="/season" class="hover:text-purple-400 transition-colors">Temporada</Link></li>
          </ul>
        </div>

        <!-- Ayuda -->
        <div>
          <h4 class="font-semibold text-white mb-4">Ayuda</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-purple-400 transition-colors">FAQ</a></li>
            <li><a href="#" class="hover:text-purple-400 transition-colors">Envíos</a></li>
            <li><a href="#" class="hover:text-purple-400 transition-colors">Devoluciones</a></li>
          </ul>
        </div>

        <!-- Redes Sociales -->
        <div>
          <h4 class="font-semibold text-white mb-4">Síguenos</h4>
          <div class="flex space-x-3">
            <a href="#" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center transition-colors">
              <span class="text-white text-sm">📘</span>
            </a>
            <a href="#" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center transition-colors">
              <span class="text-white text-sm">📷</span>
            </a>
            <a href="#" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center transition-colors">
              <span class="text-white text-sm">🐦</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
        <p>&copy; 2024 Crochet Dreams. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</template>