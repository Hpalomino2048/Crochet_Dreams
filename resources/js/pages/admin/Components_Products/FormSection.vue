<script setup lang="ts">
defineProps<{
  title: string
}>()
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
      <h2 class="text-base font-semibold text-gray-900">{{ title }}</h2>
    </div>
    <div class="p-4">
      <slot />
    </div>
  </div>
</template>