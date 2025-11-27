<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Publicar productos', href: '/admin' }, 
]
</script>

<template>
  <Head title="Publicar productos" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="box1">
        <h1>Seccion de pedidos</h1>
    </div>
  </AppLayout>
</template>
