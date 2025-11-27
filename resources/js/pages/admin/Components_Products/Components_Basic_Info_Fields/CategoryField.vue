<script setup lang="ts">
import { computed } from 'vue'

interface Subcategory {
  id: number
  name: string
  slug: string
}

interface Category {
  id: number
  name: string
  slug: string
  subcategories: Subcategory[]
}

// modelValue = slug de categoría
// subcategoryValue = slug de subcategoría
const props = defineProps<{
  modelValue: string | null
  subcategoryValue?: string | null
  error?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | null]
  'update:subcategoryValue': [value: string | null]
}>()

const categories: Category[] = [
  {
    id: 1,
    name: 'Anime',
    slug: 'Anime',
    subcategories: [
      { id: 101, name: 'Shōnen',            slug: 'shonen' },
      { id: 102, name: 'Shōjo',             slug: 'shojo' },
      { id: 103, name: 'Seinen',            slug: 'seinen' },
      { id: 104, name: 'Josei',             slug: 'josei' },
      { id: 105, name: 'Isekai',            slug: 'isekai' },
      { id: 106, name: 'Slice of Life',     slug: 'slice-of-life' },
      { id: 107, name: 'Fantasía',          slug: 'fantasia' },
      { id: 108, name: 'Deportes',          slug: 'deportes' },
      { id: 109, name: 'Mecha',             slug: 'mecha' },
      { id: 110, name: 'Terror / Suspenso', slug: 'terror-suspenso' },
      { id: 111, name: 'Magia',             slug: 'magia' },
      { id: 112, name: 'Romance',           slug: 'romance' }
    ]
  },
  {
    id: 2,
    name: 'Películas',
    slug: 'Peliculas',
    subcategories: [
      { id: 201, name: 'Acción',            slug: 'accion' },
      { id: 202, name: 'Fantasía',          slug: 'fantasia' },
      { id: 203, name: 'Animadas',          slug: 'animadas' },
      { id: 204, name: 'Terror',            slug: 'terror' },
      { id: 205, name: 'Ciencia Ficción',   slug: 'ciencia-ficcion' },
      { id: 206, name: 'Romance',           slug: 'romance' },
      { id: 207, name: 'Superhéroes',       slug: 'superheroes' }
    ]
  },
  {
    id: 3,
    name: 'Series de TV',
    slug: 'Series-tv',
    subcategories: [
      { id: 301, name: 'Live Action',       slug: 'live-action' },
      { id: 302, name: 'Sitcom',            slug: 'sitcom' },
      { id: 303, name: 'Drama',             slug: 'drama' },
      { id: 304, name: 'Fantasia / Sci-Fi', slug: 'fantasia-sci-fi' }
    ]
  },
  {
    id: 4,
    name: 'Peluches',
    slug: 'Peluches',
    subcategories: [
      { id: 401, name: 'Amigurumi',         slug: 'amigurumi' },
      { id: 402, name: 'Chibi',             slug: 'chibi' },
      { id: 403, name: 'Kawaii',            slug: 'kawaii' },
      { id: 404, name: 'Realistas',         slug: 'realistas' },
      { id: 405, name: 'Mini',              slug: 'mini' },
      { id: 406, name: 'Gigantes',          slug: 'gigantes' }
    ]
  },
  {
    id: 5,
    name: 'Decoración',
    slug: 'Decoracion',
    subcategories: [
      { id: 501, name: 'Escritorio',        slug: 'escritorio' },
      { id: 502, name: 'Habitación',        slug: 'habitacion' },
      { id: 503, name: 'Pared',             slug: 'pared' },
      { id: 504, name: 'Cocina',            slug: 'cocina' },
      { id: 505, name: 'Auto',              slug: 'auto' },
      { id: 506, name: 'Oficina',           slug: 'oficina' }
    ]
  },
  {
    id: 6,
    name: 'Adornos',
    slug: 'Adornos',
    subcategories: [
      { id: 601, name: 'Llaveros',          slug: 'llaveros' },
      { id: 602, name: 'Colgantes',         slug: 'colgantes' },
      { id: 603, name: 'Figuras',           slug: 'figuras' },
      { id: 604, name: 'Centros de mesa',   slug: 'centros-de-mesa' },
      { id: 605, name: 'Temporada',         slug: 'temporada' },
      { id: 606, name: 'Pulsera',         slug: 'pulsera' }
    
    
    ]
  },
  {
    id: 7,
    name: 'Ropa',
    slug: 'Ropa',
    subcategories: [
      { id: 701, name: 'Playeras',          slug: 'playeras' },
      { id: 702, name: 'Sudaderas',         slug: 'sudaderas' },
      { id: 703, name: 'Gorras',            slug: 'gorras' },
      { id: 704, name: 'Calcetas',          slug: 'calcetas' },
      { id: 705, name: 'Suéteres',          slug: 'sueteres' },
      { id: 706, name: 'Bufandas',          slug: 'bufandas' }
    ]
  },
  {
    id: 8,
    name: 'Cojines',
    slug: 'Cojines',
    subcategories: [
      { id: 801, name: 'Cojín básico',      slug: 'cojin-basico' },
      { id: 802, name: 'Cojín personaje',   slug: 'cojin-personaje' },
      { id: 803, name: 'Mini cojines',      slug: 'mini-cojines' },
      { id: 804, name: 'Fundas decorativas',slug: 'fundas-decorativas' }
    ]
  }
]

// cambio de categoría (slug)
const handleCategoryChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const value = target.value || null
  emit('update:modelValue', value)
  // reset subcategoría al cambiar categoría
  emit('update:subcategoryValue', null)
}

// cambio de subcategoría (slug)
const handleSubcategoryChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const value = target.value || null
  emit('update:subcategoryValue', value)
}

// categoría actual según slug
const selectedCategory = computed(() =>
  categories.find(c => c.slug === props.modelValue) ?? null
)

const subcategoriesForSelected = computed<Subcategory[]>(() =>
  selectedCategory.value ? selectedCategory.value.subcategories : []
)
</script>

<template>
  <div class="md:col-span-2 space-y-3">
    <!-- Categoría -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Categoría <span class="text-red-600">*</span>
      </label>

      <select
        :value="modelValue ?? ''"
        @change="handleCategoryChange"
        required
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
      >
        <option value="">
          Seleccionar categoría
        </option>

        <option
          v-for="category in categories"
          :key="category.id"
          :value="category.slug"
        >
          {{ category.name }}
        </option>
      </select>
    </div>

    <!-- Subcategoría -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Subcategoría
      </label>

      <select
        :value="subcategoryValue ?? ''"
        @change="handleSubcategoryChange"
        :disabled="!selectedCategory"
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-400"
      >
        <option value="">
          {{ selectedCategory ? 'Seleccionar subcategoría' : 'Selecciona una categoría primero' }}
        </option>

        <option
          v-for="subcategory in subcategoriesForSelected"
          :key="subcategory.id"
          :value="subcategory.slug"
        >
          {{ subcategory.name }}
        </option>
      </select>
    </div>

    <p
      v-if="error"
      class="mt-1 text-xs text-red-600"
    >
      {{ error }}
    </p>
  </div>
</template>
