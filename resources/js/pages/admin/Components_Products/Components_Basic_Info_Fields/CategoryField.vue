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
    name: 'Muñecos y personajes',
    slug: 'muñecos-personajes',
    subcategories: [
      { id: 101, name: 'Personajes personalizados',              slug: 'personajes-personalizados' },
      { id: 102, name: 'Películas y series',                     slug: 'peliculas-series' },
      { id: 103, name: 'Anime y caricaturas',                    slug: 'anime-caricaturas' },
      { id: 104, name: 'Deportivos (equipos y jugadores)',       slug: 'deportivos' },
      { id: 105, name: 'Animales y criaturas (patos, osos, etc.)', slug: 'animales-criaturas' }
    ]
  },
  {
    id: 2,
    name: 'Ramos tejidos',
    slug: 'ramos-tejidos',
    subcategories: [
      { id: 201, name: 'Ramos de flores (solo flores)',          slug: 'ramos-flores' },
      { id: 202, name: 'Ramos con muñeco',                       slug: 'ramos-con-muneco' },
      { id: 203, name: 'Ramos mini / detalle',                   slug: 'ramos-mini' },
      { id: 204, name: 'Ramos temáticos (graduación, cumple, etc.)', slug: 'ramos-tematicos' }
    ]
  },
  {
    id: 3,
    name: 'Llaveros y minis',
    slug: 'llaveros-minis',
    subcategories: [
      { id: 301, name: 'Llaveros personajes',                    slug: 'llaveros-personajes' },
      { id: 302, name: 'Llaveros animales',                      slug: 'llaveros-animales' },
      { id: 303, name: 'Llaveros flores / otros',                slug: 'llaveros-flores-otros' },
      { id: 304, name: 'Mini amigurumis',                        slug: 'mini-amigurumis' }
    ]
  },
  {
    id: 4,
    name: 'Flores y plantas',
    slug: 'flores-plantas',
    subcategories: [
      { id: 401, name: 'Tulipanes',                              slug: 'tulipanes' },
      { id: 402, name: 'Rosas',                                  slug: 'rosas' },
      { id: 403, name: 'Margaritas / girasoles / similares',     slug: 'margaritas-girasoles' },
      { id: 404, name: 'Otros arreglos florales pequeños',       slug: 'otros-arreglos' }
    ]
  },
  {
    id: 5,
    name: 'Temporada y especiales',
    slug: 'temporada-especiales',
    subcategories: [
      { id: 501, name: 'San Valentín / Amor y amistad',          slug: 'san-valentin' },
      { id: 502, name: 'Navidad',                                slug: 'navidad' },
      { id: 503, name: 'Halloween / Otoño',                      slug: 'halloween-otono' },
      { id: 504, name: 'Día de las Madres / Graduación / Otros', slug: 'madres-graduacion-otros' }
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
