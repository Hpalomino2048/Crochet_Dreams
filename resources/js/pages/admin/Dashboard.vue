<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { 
  Package, 
  ShoppingCart, 
  AlertCircle,
  CheckCircle,
  Palette,
  ArrowRight,
  TrendingUp
} from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Panel de administrador', href: '/admin' }, 
]

interface DashboardProps {
  stats: {
    totalProducts: number
    totalOrders: number
    lowStockProducts: number
  }
  recentOrders?: Array<{
    id: string
    customer: string
    status: string
    amount: string
    date: string
  }>
  topProducts?: Array<{
    name: string
    stock: number
    price: string
  }>
  lowStockVariants?: Array<{
    product: string
    count: number
    variants: Array<{
      name: string
      stock: number
    }>
  }>
}

const props = defineProps<DashboardProps>()

const stats = [
  {
    title: 'Productos',
    value: props.stats.totalProducts.toString(),
    change: 'En inventario',
    changeType: 'neutral',
    icon: Package,
    description: 'Total activos'
  },
  {
    title: 'Pedidos',
    value: props.stats.totalOrders.toString(),
    change: 'Todos los tiempos',
    changeType: 'positive',
    icon: ShoppingCart,
    description: 'Total procesados'
  },
  {
    title: 'Stock Bajo',
    value: props.stats.lowStockProducts.toString(),
    change: 'Requieren atención',
    changeType: props.stats.lowStockProducts > 0 ? 'negative' : 'positive',
    icon: AlertCircle,
    description: 'Productos/Variantes < 10 unidades'
  }
]

const recentOrders = props.recentOrders || []
const topProducts = props.topProducts || []
const lowStockVariants = props.lowStockVariants || []

const getStatusClass = (status: string) => {
  const classes = {
    'Completado': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'En proceso': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'Cancelado': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  }
  return classes[status as keyof typeof classes] || ''
}
</script>

<template>
  <Head title="Panel de Administrador" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Panel de Administrador</h1>
        <p class="text-muted-foreground mt-2">
          Bienvenido de nuevo. Aquí está el resumen de tu tienda.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="stat in stats" :key="stat.title" class="hover:shadow-lg transition-shadow duration-200">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">
              {{ stat.title }}
            </CardTitle>
            <div :class="[
              'p-2 rounded-lg',
              stat.changeType === 'positive' ? 'bg-green-100 dark:bg-green-900/20' : 
              stat.changeType === 'negative' ? 'bg-red-100 dark:bg-red-900/20' : 
              'bg-muted'
            ]">
              <component 
                :is="stat.icon" 
                :class="[
                  'h-5 w-5',
                  stat.changeType === 'positive' ? 'text-green-600 dark:text-green-400' : 
                  stat.changeType === 'negative' ? 'text-red-600 dark:text-red-400' : 
                  'text-muted-foreground'
                ]"
              />
            </div>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ stat.value }}</div>
            <p class="text-xs text-muted-foreground mt-2">
              <span 
                :class="[
                  'font-medium',
                  stat.changeType === 'positive' ? 'text-green-600 dark:text-green-400' : 
                  stat.changeType === 'negative' ? 'text-red-600 dark:text-red-400' : 
                  'text-muted-foreground'
                ]"
              >
                {{ stat.change }}
              </span>
              <span class="mx-1">•</span>
              <span>{{ stat.description }}</span>
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Main Content Grid -->
      <div class="grid gap-4 lg:grid-cols-7">
        <!-- Recent Orders -->
        <Card class="lg:col-span-4 hover:shadow-lg transition-shadow duration-200">
          <CardHeader class="flex flex-row items-center justify-between">
            <div>
              <CardTitle class="text-xl font-bold">Pedidos Recientes</CardTitle>
              <CardDescription class="mt-1">
                Últimos pedidos realizados en tu tienda
              </CardDescription>
            </div>
            <Link href="/admin/orders" v-if="recentOrders.length > 0">
              <Button variant="ghost" size="sm" class="gap-2">
                Ver todos
                <ArrowRight class="h-4 w-4" />
              </Button>
            </Link>
          </CardHeader>
          <CardContent>
            <div v-if="recentOrders.length > 0" class="space-y-4">
              <div 
                v-for="order in recentOrders.slice(0, 5)" 
                :key="order.id"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-lg hover:bg-muted/50 transition-colors border border-transparent hover:border-border"
              >
                <div class="space-y-1 flex-1">
                  <p class="text-sm font-medium leading-none">{{ order.customer }}</p>
                  <p class="text-xs text-muted-foreground">{{ order.id }} • {{ order.date }}</p>
                </div>
                <div class="flex items-center gap-3 sm:flex-shrink-0">
                  <span 
                    :class="[
                      'px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap',
                      getStatusClass(order.status)
                    ]"
                  >
                    {{ order.status }}
                  </span>
                  <p class="text-sm font-semibold min-w-[80px] text-right">{{ order.amount }}</p>
                </div>
              </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-16 text-center">
              <div class="p-4 rounded-full bg-muted/50 mb-4">
                <ShoppingCart class="h-10 w-10 text-muted-foreground/50" />
              </div>
              <p class="text-base font-medium text-foreground">No hay pedidos aún</p>
              <p class="text-sm text-muted-foreground mt-1 max-w-sm">Los pedidos aparecerán aquí cuando se realicen compras en tu tienda</p>
            </div>
          </CardContent>
        </Card>

        <!-- Top Products -->
        <Card class="lg:col-span-3 hover:shadow-lg transition-shadow duration-200">
          <CardHeader>
            <div>
              <CardTitle class="text-xl font-bold">Productos con Mayor Stock</CardTitle>
              <CardDescription class="mt-1">
                Inventario disponible
              </CardDescription>
            </div>
          </CardHeader>
          <CardContent>
            <div v-if="topProducts.length > 0" class="space-y-3">
              <div 
                v-for="(product, index) in topProducts.slice(0, 5)" 
                :key="product.name"
                class="flex items-center gap-4 p-2 rounded-lg hover:bg-muted/50 transition-colors"
              >
                <div 
                  class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary/20 to-primary/10 text-primary font-bold text-sm"
                >
                  {{ index + 1 }}
                </div>
                <div class="flex-1 space-y-1 min-w-0">
                  <p class="text-sm font-medium leading-none truncate">{{ product.name }}</p>
                  <div class="flex items-center gap-2">
                    <TrendingUp class="h-3 w-3 text-muted-foreground" />
                    <p class="text-xs text-muted-foreground">{{ product.stock }} unidades</p>
                  </div>
                </div>
                <div class="text-sm font-semibold flex-shrink-0">{{ product.price }}</div>
              </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-16 text-center">
              <div class="p-4 rounded-full bg-muted/50 mb-4">
                <Package class="h-10 w-10 text-muted-foreground/50" />
              </div>
              <p class="text-base font-medium text-foreground">No hay productos</p>
              <p class="text-sm text-muted-foreground mt-1">Agrega productos para ver el inventario</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Alerts Section -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Stock Bajo General -->
        <Card 
          v-if="props.stats.lowStockProducts > 0"
          class="border-2 border-yellow-200 dark:border-yellow-900/50 bg-yellow-50/50 dark:bg-yellow-900/5 hover:shadow-lg transition-all duration-200"
        >
          <CardHeader class="pb-3">
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                <AlertCircle class="h-5 w-5 text-yellow-600 dark:text-yellow-500" />
              </div>
              <CardTitle class="text-base font-semibold">Alerta de Stock</CardTitle>
            </div>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground leading-relaxed">
              Hay <span class="font-bold text-yellow-700 dark:text-yellow-500">{{ props.stats.lowStockProducts }} items</span> con menos de 10 unidades en inventario
            </p>
          </CardContent>
        </Card>

        <!-- Variantes con Stock Bajo -->
        <Card 
          v-if="lowStockVariants.length > 0"
          class="border-2 border-orange-200 dark:border-orange-900/50 bg-orange-50/50 dark:bg-orange-900/5 hover:shadow-lg transition-all duration-200"
        >
          <CardHeader class="pb-3">
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <Palette class="h-5 w-5 text-orange-600 dark:text-orange-500" />
              </div>
              <CardTitle class="text-base font-semibold">Variantes Afectadas</CardTitle>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-3 max-h-52 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-transparent">
              <div 
                v-for="item in lowStockVariants" 
                :key="item.product"
                class="space-y-2 pb-3 border-b last:border-0 last:pb-0"
              >
                <div class="flex justify-between items-start gap-2">
                  <span class="font-medium text-sm text-foreground line-clamp-2">{{ item.product }}</span>
                  <span class="text-xs font-semibold bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400 px-2.5 py-1 rounded-full whitespace-nowrap">
                    {{ item.count }} variante{{ item.count > 1 ? 's' : '' }}
                  </span>
                </div>
                <div class="pl-3 space-y-1.5">
                  <div 
                    v-for="variant in item.variants" 
                    :key="variant.name"
                    class="flex justify-between items-center text-xs"
                  >
                    <span class="text-muted-foreground flex items-center gap-1">
                      <span class="w-1 h-1 rounded-full bg-orange-400"></span>
                      {{ variant.name }}
                    </span>
                    <span class="font-semibold text-orange-600 dark:text-orange-500">{{ variant.stock }} unid.</span>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Sistema OK -->
        <Card class="border-2 border-green-200 dark:border-green-900/50 bg-green-50/50 dark:bg-green-900/5 hover:shadow-lg transition-all duration-200">
          <CardHeader class="pb-3">
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30">
                <CheckCircle class="h-5 w-5 text-green-600 dark:text-green-500" />
              </div>
              <CardTitle class="text-base font-semibold">Sistema Actualizado</CardTitle>
            </div>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground leading-relaxed">
              Todos los sistemas funcionando correctamente
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>