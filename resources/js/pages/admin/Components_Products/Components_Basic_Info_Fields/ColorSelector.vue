<script setup lang="ts">
import { computed } from 'vue'
import type { ColorVariant } from './Components_ColorSelector/ColorVariantTypes'
import { AVAILABLE_COLORS } from './Components_ColorSelector/ColorVariantTypes'
import ColorVariantHeader from './Components_ColorSelector/ColorVariantHeader.vue'
import StockSummary from './Components_ColorSelector/StockSummary.vue'
import EmptyState from './Components_ColorSelector/EmptyState.vue'
import ColorVariantCard from './Components_ColorSelector/ColorVariantCard.vue'

const props = defineProps<{
  modelValue: ColorVariant[]
  errors?: Record<string, string>
}>()

const emit = defineEmits<{
  'update:modelValue': [value: ColorVariant[]]
}>()

// Computed
const hasVariants = computed(() => props.modelValue.length > 0)

const totalStock = computed(() => {
  return props.modelValue.reduce((sum, variant) => sum + (variant.stock || 0), 0)
})

// Crear nueva variante vacía
const createEmptyVariant = (isDefault = false): ColorVariant => ({
  name: '',
  code: '',
  stock: 0,
  is_default: isDefault,
  image: null,
  imagePreview: '',
  gallery: []
})

// Agregar nuevo color
const addColor = () => {
  const isFirstVariant = props.modelValue.length === 0
  const newColor = createEmptyVariant(isFirstVariant)
  emit('update:modelValue', [...props.modelValue, newColor])
}

// Eliminar color
const removeColor = (index: number) => {
  const updated = props.modelValue.filter((_, i) => i !== index)
  
  // Si quedan variantes y ninguna es default, hacer default a la primera
  if (updated.length > 0 && !updated.some(c => c.is_default)) {
    updated[0].is_default = true
  }
  
  emit('update:modelValue', updated)
}

// Actualizar campo de una variante
const updateColor = (index: number, field: keyof ColorVariant, value: any) => {
  const updated = [...props.modelValue]
  updated[index] = { ...updated[index], [field]: value }
  
  // Si marca como default, desmarcar los demás
  if (field === 'is_default' && value === true) {
    updated.forEach((color, i) => {
      if (i !== index) color.is_default = false
    })
  }
  
  // Auto-completar código hexadecimal al seleccionar nombre
  if (field === 'name' && typeof value === 'string') {
    const colorData = AVAILABLE_COLORS.find(c => c.name === value)
    if (colorData) {
      updated[index].code = colorData.hex
    }
  }
  
  emit('update:modelValue', updated)
}

// Manejar carga de imagen
const handleImageUpload = (index: number, event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (!file) return
  
  // Validar tamaño
  if (file.size > 4 * 1024 * 1024) {
    alert('La imagen no debe superar 4MB')
    target.value = ''
    return
  }
  
  const updated = [...props.modelValue]
  updated[index].image = file
  
  // Crear preview
  const reader = new FileReader()
  reader.onload = (e) => {
    updated[index].imagePreview = e.target?.result as string
    emit('update:modelValue', updated)
  }
  reader.readAsDataURL(file)
}

// Remover imagen
const removeImage = (index: number) => {
  const updated = [...props.modelValue]
  updated[index].image = null
  updated[index].imagePreview = ''
  emit('update:modelValue', updated)
}

// Obtener error de una variante específica
const getVariantError = (index: number): string | undefined => {
  return props.errors?.[`colors.${index}`]
}
</script>

<template>
  <div class="space-y-4">
    <!-- Header con información -->
    <ColorVariantHeader @add-variant="addColor" />

    <!-- Resumen de stock total -->
    <StockSummary
      v-if="hasVariants"
      :total-stock="totalStock"
      :variants-count="modelValue.length"
    />

    <!-- Estado vacío -->
    <EmptyState v-if="!hasVariants" />

    <!-- Lista de variantes -->
    <TransitionGroup name="list" tag="div" class="space-y-4">
      <ColorVariantCard
        v-for="(color, index) in modelValue"
        :key="`variant-${index}`"
        :variant="color"
        :index="index"
        :error="getVariantError(index)"
        @update="(field, value) => updateColor(index, field, value)"
        @remove="removeColor(index)"
        @upload-image="(event) => handleImageUpload(index, event)"
        @remove-image="removeImage(index)"
      />
    </TransitionGroup>
  </div>
</template>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-leave-active {
  position: absolute;
  width: 100%;
}
</style>