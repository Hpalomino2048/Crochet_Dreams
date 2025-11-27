<script setup lang="ts">
import { ref, computed } from 'vue'

interface ProductColor {
  id: number
  name: string
  code: string
  stock: number
  is_default: boolean
}

interface Product {
  id: number
  sku: string
  name: string
  slug: string
  category?: string
  subcategory?: string
  price: number
  currency: string
  stock: number
  thumbnail?: string
  colors?: ProductColor[]
  created_at: string
}

interface ProductsProps {
  products: Product[]
  total: number
}

const props = defineProps<ProductsProps>()

// Estado para búsqueda y filtros
const searchQuery = ref('')
const filterCategory = ref('')

// Productos filtrados
const filteredProducts = computed(() => {
  let filtered = props.products

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.sku.toLowerCase().includes(query) ||
      product.category?.toLowerCase().includes(query)
    )
  }

  // Filtrar por categoría
  if (filterCategory.value) {
    filtered = filtered.filter(product => 
      product.category === filterCategory.value
    )
  }

  return filtered
})

// Categorías únicas para el filtro
const categories = computed(() => {
  const cats = props.products
    .map(p => p.category)
    .filter((cat): cat is string => !!cat)
  return [...new Set(cats)]
})

// Función para obtener clase de badge según stock
const getStockBadgeClass = (stock: number) => {
  if (stock === 0) return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  if (stock < 10) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
  return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
}

const getStockLabel = (stock: number) => {
  if (stock === 0) return 'Agotado'
  if (stock < 10) return 'Stock Bajo'
  return 'En Stock'
}

// Formatear precio
const formatPrice = (price: number, currency: string) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: currency || 'MXN'
  }).format(price)
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <div class="space-y-4 md:space-y-6 p-3 sm:p-4 md:p-6 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
          <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight">Productos</h1>
          <p class="text-xs sm:text-sm md:text-base text-muted-foreground">
            Gestiona tu inventario de productos
          </p>
        </div>
        <a href="/admin/products/create" class="sm:flex-shrink-0">
          <button class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            <span class="hidden xs:inline">Agregar Producto</span>
            <span class="xs:hidden">Agregar</span>
          </button>
        </a>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-3 md:gap-4 grid-cols-1 xs:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
          <div class="flex flex-row items-center justify-between space-y-0 pb-2 p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-medium">Total Productos</h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-muted-foreground"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
          </div>
          <div class="p-4 pt-0 sm:p-6 sm:pt-0">
            <div class="text-xl sm:text-2xl font-bold">{{ props.total }}</div>
            <p class="text-[10px] sm:text-xs text-muted-foreground">En tu inventario</p>
          </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
          <div class="flex flex-row items-center justify-between space-y-0 pb-2 p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-medium">En Stock</h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-green-600"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
          </div>
          <div class="p-4 pt-0 sm:p-6 sm:pt-0">
            <div class="text-xl sm:text-2xl font-bold">
              {{ props.products.filter(p => p.stock >= 10).length }}
            </div>
            <p class="text-[10px] sm:text-xs text-muted-foreground">Con stock suficiente</p>
          </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm xs:col-span-2 lg:col-span-1">
          <div class="flex flex-row items-center justify-between space-y-0 pb-2 p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-medium">Stock Bajo</h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-yellow-600"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          </div>
          <div class="p-4 pt-0 sm:p-6 sm:pt-0">
            <div class="text-xl sm:text-2xl font-bold">
              {{ props.products.filter(p => p.stock < 10 && p.stock > 0).length }}
            </div>
            <p class="text-[10px] sm:text-xs text-muted-foreground">Requieren atención</p>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <Card>
        <CardHeader class="p-4 sm:p-6">
          <CardTitle class="text-base sm:text-lg md:text-xl font-bold">Buscar y Filtrar</CardTitle>
        </CardHeader>
        <CardContent class="p-4 pt-0 sm:p-6 sm:pt-0">
          <div class="flex flex-col gap-3">
            <!-- Search -->
            <div class="relative flex-1">
              <Search class="absolute left-2.5 sm:left-3 top-1/2 h-3.5 w-3.5 sm:h-4 sm:w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                v-model="searchQuery"
                placeholder="Buscar por nombre, SKU..."
                class="pl-8 sm:pl-9 text-sm h-9 sm:h-10"
              />
            </div>

            <!-- Category Filter -->
            <div class="flex flex-col xs:flex-row gap-2">
              <select
                v-model="filterCategory"
                class="flex h-9 sm:h-10 w-full xs:w-auto xs:flex-1 sm:flex-initial sm:w-[200px] rounded-md border border-input bg-background px-3 py-2 text-xs sm:text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
              >
                <option value="">Todas las categorías</option>
                <option v-for="cat in categories" :key="cat" :value="cat">
                  {{ cat }}
                </option>
              </select>
              
              <Button
                v-if="searchQuery || filterCategory"
                variant="outline"
                size="sm"
                @click="searchQuery = ''; filterCategory = ''"
                class="w-full xs:w-auto text-xs sm:text-sm h-9 sm:h-10"
              >
                Limpiar
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Products Table -->
      <Card>
        <CardHeader class="p-4 sm:p-6">
          <CardTitle class="text-base sm:text-lg md:text-xl font-bold">Lista de Productos</CardTitle>
          <CardDescription class="text-xs sm:text-sm">
            {{ filteredProducts.length }} producto{{ filteredProducts.length !== 1 ? 's' : '' }} encontrado{{ filteredProducts.length !== 1 ? 's' : '' }}
          </CardDescription>
        </CardHeader>
        <CardContent class="p-0">
          <div v-if="filteredProducts.length > 0">
            <!-- Vista Desktop - Tabla -->
            <div class="hidden lg:block overflow-x-auto">
              <div class="inline-block min-w-full align-middle">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead class="w-[60px] xl:w-[80px]">Imagen</TableHead>
                      <TableHead class="min-w-[200px]">Producto</TableHead>
                      <TableHead class="min-w-[100px]">SKU</TableHead>
                      <TableHead class="min-w-[120px]">Categoría</TableHead>
                      <TableHead class="min-w-[100px]">Precio</TableHead>
                      <TableHead class="min-w-[150px]">Stock</TableHead>
                      <TableHead class="min-w-[120px]">Variantes</TableHead>
                      <TableHead class="text-right w-[80px]">Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="product in filteredProducts" :key="product.id">
                      <!-- Imagen -->
                      <TableCell>
                        <div class="h-10 w-10 xl:h-12 xl:w-12 rounded-md bg-muted flex items-center justify-center overflow-hidden">
                          <img
                            v-if="product.thumbnail"
                            :src="product.thumbnail"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                          />
                          <Package v-else class="h-5 w-5 xl:h-6 xl:w-6 text-muted-foreground" />
                        </div>
                      </TableCell>

                      <!-- Nombre -->
                      <TableCell class="font-medium">
                        <div class="max-w-[200px] xl:max-w-[250px]">
                          <p class="truncate text-sm">{{ product.name }}</p>
                          <p class="text-xs text-muted-foreground truncate">{{ product.subcategory }}</p>
                        </div>
                      </TableCell>

                      <!-- SKU -->
                      <TableCell>
                        <code class="text-xs bg-muted px-2 py-1 rounded whitespace-nowrap">{{ product.sku }}</code>
                      </TableCell>

                      <!-- Categoría -->
                      <TableCell>
                        <Badge variant="outline" class="text-xs whitespace-nowrap">{{ product.category || 'Sin categoría' }}</Badge>
                      </TableCell>

                      <!-- Precio -->
                      <TableCell class="font-semibold text-sm whitespace-nowrap">
                        {{ formatPrice(product.price, product.currency) }}
                      </TableCell>

                      <!-- Stock -->
                      <TableCell>
                        <div class="flex items-center gap-2">
                          <Badge :class="getStockBadgeClass(product.stock)" class="text-xs whitespace-nowrap">
                            {{ getStockLabel(product.stock) }}
                          </Badge>
                          <span class="text-sm text-muted-foreground">{{ product.stock }}</span>
                        </div>
                      </TableCell>

                      <!-- Variantes (Colores) -->
                      <TableCell>
                        <div v-if="product.colors && product.colors.length > 0" class="flex items-center gap-1">
                          <div
                            v-for="color in product.colors.slice(0, 3)"
                            :key="color.id"
                            class="h-5 w-5 xl:h-6 xl:w-6 rounded-full border-2 border-white dark:border-gray-800 shadow-sm flex-shrink-0"
                            :style="{ backgroundColor: color.code }"
                            :title="`${color.name} (${color.stock})`"
                          ></div>
                          <span v-if="product.colors.length > 3" class="text-xs text-muted-foreground ml-1 whitespace-nowrap">
                            +{{ product.colors.length - 3 }}
                          </span>
                        </div>
                        <span v-else class="text-xs text-muted-foreground whitespace-nowrap">Sin variantes</span>
                      </TableCell>

                      <!-- Acciones -->
                      <TableCell class="text-right">
                        <DropdownMenu>
                          <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon" class="h-8 w-8">
                              <MoreVertical class="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem>Ver detalles</DropdownMenuItem>
                            <DropdownMenuItem>Editar</DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem class="text-red-600 focus:text-red-600">
                              Eliminar
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>
            </div>

            <!-- Vista Mobile/Tablet - Cards -->
            <div class="lg:hidden divide-y">
              <div v-for="product in filteredProducts" :key="product.id" class="p-3 sm:p-4 hover:bg-muted/50 transition-colors">
                <div class="flex gap-3">
                  <!-- Imagen -->
                  <div class="flex-shrink-0">
                    <div class="h-16 w-16 xs:h-20 xs:w-20 rounded-lg bg-muted flex items-center justify-center overflow-hidden">
                      <img
                        v-if="product.thumbnail"
                        :src="product.thumbnail"
                        :alt="product.name"
                        class="h-full w-full object-cover"
                      />
                      <Package v-else class="h-7 w-7 xs:h-8 xs:w-8 text-muted-foreground" />
                    </div>
                  </div>

                  <!-- Info -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                      <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm xs:text-base truncate leading-tight">{{ product.name }}</h3>
                        <p class="text-xs text-muted-foreground truncate mt-0.5">{{ product.subcategory }}</p>
                      </div>
                      <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                          <Button variant="ghost" size="icon" class="flex-shrink-0 h-7 w-7 xs:h-8 xs:w-8 -mr-2">
                            <MoreVertical class="h-3.5 w-3.5 xs:h-4 xs:w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                          <DropdownMenuSeparator />
                          <DropdownMenuItem>Ver detalles</DropdownMenuItem>
                          <DropdownMenuItem>Editar</DropdownMenuItem>
                          <DropdownMenuSeparator />
                          <DropdownMenuItem class="text-red-600 focus:text-red-600">
                            Eliminar
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>

                    <div class="space-y-2">
                      <!-- SKU y Categoría -->
                      <div class="flex flex-wrap items-center gap-1.5 xs:gap-2">
                        <code class="bg-muted px-1.5 xs:px-2 py-0.5 xs:py-1 rounded text-[10px] xs:text-xs">{{ product.sku }}</code>
                        <Badge variant="outline" class="text-[10px] xs:text-xs h-5 xs:h-auto">
                          {{ product.category || 'Sin categoría' }}
                        </Badge>
                      </div>

                      <!-- Precio y Stock -->
                      <div class="flex items-center justify-between gap-2">
                        <span class="font-bold text-sm xs:text-base">
                          {{ formatPrice(product.price, product.currency) }}
                        </span>
                        <div class="flex items-center gap-1.5 xs:gap-2">
                          <Badge :class="getStockBadgeClass(product.stock)" class="text-[10px] xs:text-xs h-5 xs:h-auto">
                            {{ getStockLabel(product.stock) }}
                          </Badge>
                          <span class="text-xs text-muted-foreground">{{ product.stock }}</span>
                        </div>
                      </div>

                      <!-- Variantes -->
                      <div v-if="product.colors && product.colors.length > 0" class="flex items-center gap-2">
                        <span class="text-[10px] xs:text-xs text-muted-foreground flex-shrink-0">Variantes:</span>
                        <div class="flex items-center gap-1">
                          <div
                            v-for="color in product.colors.slice(0, 5)"
                            :key="color.id"
                            class="h-4 w-4 xs:h-5 xs:w-5 rounded-full border-2 border-white dark:border-gray-800 shadow-sm"
                            :style="{ backgroundColor: color.code }"
                            :title="`${color.name} (${color.stock})`"
                          ></div>
                          <span v-if="product.colors.length > 5" class="text-[10px] xs:text-xs text-muted-foreground ml-0.5">
                            +{{ product.colors.length - 5 }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="flex flex-col items-center justify-center py-10 sm:py-12 md:py-16 text-center px-4">
            <div class="p-3 sm:p-4 rounded-full bg-muted/50 mb-3 sm:mb-4">
              <Package class="h-8 w-8 sm:h-10 sm:w-10 text-muted-foreground/50" />
            </div>
            <p class="text-sm sm:text-base font-medium text-foreground">No hay productos</p>
            <p class="text-xs sm:text-sm text-muted-foreground mt-1 max-w-sm">
              {{ searchQuery || filterCategory ? 'No se encontraron productos con los filtros aplicados' : 'Comienza agregando tu primer producto' }}
            </p>
            <Link href="/admin/products/create" v-if="!searchQuery && !filterCategory">
              <Button class="mt-3 sm:mt-4 gap-2 text-sm h-9 sm:h-10">
                <Plus class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                Agregar Producto
              </Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>