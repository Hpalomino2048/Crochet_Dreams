<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import FormSection from '../admin/Components_Products/FormSection.vue'
import BasicInfoFields from '../admin/Components_Products/BasicInfoFields.vue'
import PricingFields from '../admin/Components_Products/PricingFields.vue'
import ImageUploader from '../admin/Components_Products/ImageUploader.vue'
import ColorVariantsFields from '../admin/Components_Products/Components_Basic_Info_Fields/ColorSelector.vue'
import SuccessModal from '../admin/Components_Products/SuccesModal.vue'
import FlashMessage from '../admin/Components_Products/FlashMessage.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

interface ColorVariant {
  id?: number
  name: string
  code: string
  stock: number
  is_default: boolean
  image: File | string | null
  imagePreview?: string
  gallery: (File | string)[]
}

interface FormDefaults {
  sku: string
  name: string
  slug: string
  stock: number | null
  tamaño: string | null
  descriptions: string
  price: number | null
  currency: string
  thumbnail: File | string | null
  category: string | null
  subcategory: string | null
  colors: ColorVariant[]
}

interface Props {
  formDefaults: FormDefaults
  flash?: { success?: string; error?: string }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Productos', href: '/admin/products' },
  { title: 'Crear Producto', href: '/admin/Create_Product' }
]

const showSuccessAnimation = ref(false)

const form = useForm({
  sku: props.formDefaults.sku,
  name: props.formDefaults.name,
  slug: props.formDefaults.slug,
  stock: props.formDefaults.stock,
  tamaño: props.formDefaults.tamaño,
  descriptions: props.formDefaults.descriptions,
  price: props.formDefaults.price,
  currency: props.formDefaults.currency,
  thumbnail: null as File | string | null,
  category: props.formDefaults.category || null,
  subcategory: props.formDefaults.subcategory || null,
  colors: props.formDefaults.colors || [] as ColorVariant[],
})

// 👉 Debe tener al menos 1 variante
const hasVariants = computed(() => form.colors.length > 0)

// Stock total de variantes
const totalVariantsStock = computed(() => {
  if (!hasVariants.value) return 0
  return form.colors.reduce((total, color) => total + (color.stock || 0), 0)
})

// Imagen de la variante por defecto
const defaultVariantImage = computed(() => {
  if (!hasVariants.value) return null
  const defaultVariant = form.colors.find(c => c.is_default)
  return defaultVariant?.image || null
})

// Sincronizar stock cuando hay variantes
watch(() => form.colors, () => {
  if (hasVariants.value) {
    form.stock = totalVariantsStock.value
  }
}, { deep: true })

// Sincronizar thumbnail con la variante predeterminada
watch(() => [form.colors, hasVariants.value], () => {
  if (hasVariants.value && defaultVariantImage.value) {
    form.thumbnail = defaultVariantImage.value
  }
}, { deep: true })

watch(() => form.name, (newName) => {
  if (!form.slug || form.slug === slugify(form.name)) {
    form.slug = slugify(newName)
  }
})

function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

function submit() {
  // ⛔ Bloquea si no hay variantes
  if (!hasVariants.value) {
    form.setError('colors', 'Debes agregar al menos una variante de color antes de crear el producto.')
    return
  }

  // ⛔ Al menos una variante con stock
  const totalStock = totalVariantsStock.value
  if (totalStock <= 0) {
    form.setError('colors', 'Las variantes deben tener stock mayor a 0.')
    return
  }

  // ⛔ Asegurar variante por defecto
  const hasDefault = form.colors.some(c => c.is_default)
  if (!hasDefault) {
    form.setError('colors', 'Selecciona una variante como predeterminada.')
    return
  }

  form.post('/admin/products', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showSuccessAnimation.value = true
      setTimeout(() => {}, 2000)
    },
    onError: (errors) => console.error('Errores de validación:', errors)
  })
}
</script>

<template>
  <Head title="Crear Producto" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <SuccessModal :show="showSuccessAnimation" />

    <div class="px-4 py-6">
      <FlashMessage :flash="flash" />

      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Crear Nuevo Producto</h1>
        <p class="mt-1 text-sm text-gray-600">Complete la información del producto</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Información Básica -->
        <FormSection title="Información Básica">
          <BasicInfoFields :form="form" :has-variants="hasVariants" />
        </FormSection>

        <!-- Variantes de Color -->
        <FormSection title="Variantes de Color">
          <ColorVariantsFields
            v-model="form.colors"
            :errors="form.errors"
          />
          <!-- Mensaje de error global para variantes -->
          <p
            v-if="form.errors.colors"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.colors }}
          </p>
        </FormSection>

        <!-- Precio -->
        <FormSection title="Precio">
          <PricingFields :form="form" />
        </FormSection>

        <!-- Info de stock e imagen cuando hay variantes -->
        <div
          v-if="hasVariants"
          class="bg-purple-50 border border-purple-200 rounded-lg p-4"
        >
          <div class="flex items-start gap-3">
            <svg
              class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
            <div class="flex-1">
              <h4 class="text-sm font-medium text-purple-900 mb-1">
                Producto con variantes
              </h4>
              <ul class="text-sm text-purple-700 space-y-1">
                <li>• El stock total se calculará automáticamente sumando el stock de cada variante</li>
                <li>• La imagen principal será la imagen de la variante marcada como predeterminada</li>
                <li>
                  • Stock total calculado:
                  <span class="font-semibold">
                    {{ totalVariantsStock }} unidades
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-xs text-gray-600">
              <span class="text-red-600">*</span>
              Campos obligatorios
            </p>
            <div class="flex gap-2 w-full sm:w-auto">
              <a
                href="/admin/products"
                class="flex-1 sm:flex-none px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 font-medium hover:bg-gray-50 text-center"
              >
                Cancelar
              </a>
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 sm:flex-none px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg text-sm font-medium hover:from-purple-700 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
              >
                <span
                  v-if="form.processing"
                  class="flex items-center justify-center"
                >
                  <svg
                    class="animate-spin -ml-1 mr-2 h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    ></circle>
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  Guardando...
                </span>
                <span v-else>Crear Producto</span>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
*:focus-visible {
  outline: 2px solid #9333ea;
  outline-offset: 2px;
}
</style>
