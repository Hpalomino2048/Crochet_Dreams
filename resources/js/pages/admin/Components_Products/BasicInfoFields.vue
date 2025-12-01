<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import NameField from './Components_Basic_Info_Fields/NameField.vue'
import SkuField from './Components_Basic_Info_Fields/SkuField.vue'
import SlugField from './Components_Basic_Info_Fields/SlugField.vue'
import TamañoSelect from './Components_Basic_Info_Fields/SizeField.vue'
import CategoryField from './Components_Basic_Info_Fields/CategoryField.vue'
import DescriptionField from './Components_Basic_Info_Fields/DescriptionField.vue'

const props = defineProps<{
  form: InertiaForm<any>
  isEditing?: boolean
}>()

const manuallyEditedSlug = ref(false)
const manuallyEditedSku = ref(false)
</script>

<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Nombre -->
      <NameField 
        :model-value="form.name"
        @update:model-value="form.name = $event"
        :error="form.errors.name"
        @update:slug="form.slug = $event"
        @manual-edit="manuallyEditedSlug = $event"
      />
      
      <!-- SKU -->
      <SkuField
        :model-value="form.sku"
        @update:model-value="form.sku = $event"
        :error="form.errors.sku"
        :is-editing="isEditing"
        @manual-edit="manuallyEditedSku = $event"
      />
      
      <!-- Slug -->
      <SlugField
        :model-value="form.slug"
        @update:model-value="form.slug = $event"
        :error="form.errors.slug"
        :product-name="form.name"
        :manually-edited="manuallyEditedSlug"
      />
      
      <!-- Tamaño -->
      <TamañoSelect
        v-model="form.tamaño"
        :error="form.errors.tamaño"
      />
      
      <!-- Categoría -->
      <CategoryField
        v-model="form.category"                
        v-model:subcategoryValue="form.subcategory"  
      />
      <!-- Descripción -->
      <DescriptionField
        :model-value="form.descriptions"
        @update:model-value="form.descriptions = $event"
        :error="form.errors.descriptions"
      />
    </div>
  </div>
</template>