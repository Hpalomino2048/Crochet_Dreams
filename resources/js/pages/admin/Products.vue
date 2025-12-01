<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { 
  Package, 
  Search, 
  Plus, 
  Pencil, 
  Trash2, 
  ShoppingBag, 
  AlertCircle, 
  TrendingUp,
  Filter,
  X,
  Download,
  FileText,
  Calendar,
  DollarSign,
  Archive
} from 'lucide-vue-next'
import type { BreadcrumbItem } from '@/types'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

// Definición de tipos
interface ProductColor {
  id: number
  name: string
  code: string
  stock: number
  is_default: boolean
  image: string | null
}

interface Product {
  id: number
  sku: string
  name: string
  slug: string
  descriptions: string
  price: number
  currency: string
  stock: number
  thumbnail: string | null
  category: string | null
  subcategory: string | null
  tamaño: string | null
  created_at: string  // Tu backend ya lo formatea como string "d/m/Y"
  updated_at?: string
  colors: ProductColor[]
}

interface Props {
  products: {
    data: Product[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  flash?: {
    success?: string
    error?: string
  }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Panel', href: '/admin' },
  { title: 'Productos', href: '/admin/products' }
]

// Estado reactivo
const searchQuery = ref('')
const filterCategory = ref('')
const filterSubcategory = ref('')
const filterStockStatus = ref('')
const filterPriceMin = ref<number | null>(null)
const filterPriceMax = ref<number | null>(null)
const filterDateFrom = ref('')
const filterDateTo = ref('')
const filterTamaño = ref('')
const showAdvancedFilters = ref(false)

// Computadas
const allProducts = computed(() => props.products.data || [])

const filteredProducts = computed(() => {
  let filtered = allProducts.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product =>
      product.name.toLowerCase().includes(query) ||
      product.sku.toLowerCase().includes(query) ||
      product.descriptions?.toLowerCase().includes(query) ||
      (product.category && product.category.toLowerCase().includes(query))
    )
  }

  if (filterCategory.value) {
    filtered = filtered.filter(product => product.category === filterCategory.value)
  }

  if (filterSubcategory.value) {
    filtered = filtered.filter(product => product.subcategory === filterSubcategory.value)
  }

  if (filterStockStatus.value) {
    if (filterStockStatus.value === 'in-stock') {
      filtered = filtered.filter(p => p.stock >= 10)
    } else if (filterStockStatus.value === 'low-stock') {
      filtered = filtered.filter(p => p.stock > 0 && p.stock < 10)
    } else if (filterStockStatus.value === 'out-of-stock') {
      filtered = filtered.filter(p => p.stock === 0)
    }
  }

  if (filterPriceMin.value !== null) {
    filtered = filtered.filter(p => p.price >= filterPriceMin.value!)
  }
  if (filterPriceMax.value !== null) {
    filtered = filtered.filter(p => p.price <= filterPriceMax.value!)
  }

  if (filterTamaño.value) {
    filtered = filtered.filter(product => product.tamaño === filterTamaño.value)
  }

  if (filterDateFrom.value) {
    filtered = filtered.filter(p => {
      if (!p.created_at) return false
      // Convertir formato d/m/Y a Date para comparar
      const parts = p.created_at.split('/')
      const productDate = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]))
      return productDate >= new Date(filterDateFrom.value)
    })
  }
  if (filterDateTo.value) {
    filtered = filtered.filter(p => {
      if (!p.created_at) return false
      const parts = p.created_at.split('/')
      const productDate = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]))
      return productDate <= new Date(filterDateTo.value)
    })
  }

  return filtered
})

const categories = computed(() => {
  const cats = allProducts.value
    .map(p => p.category)
    .filter(Boolean) as string[]
  return [...new Set(cats)]
})

const subcategories = computed(() => {
  let products = allProducts.value
  if (filterCategory.value) {
    products = products.filter(p => p.category === filterCategory.value)
  }
  const subcats = products
    .map(p => p.subcategory)
    .filter(Boolean) as string[]
  return [...new Set(subcats)]
})

const tamaños = computed(() => {
  const sizes = allProducts.value
    .map(p => p.tamaño)
    .filter(Boolean) as string[]
  return [...new Set(sizes)]
})

const stats = computed(() => ({
  total: allProducts.value.length,
  inStock: allProducts.value.filter(p => p.stock >= 10).length,
  lowStock: allProducts.value.filter(p => p.stock < 10 && p.stock > 0).length,
  outOfStock: allProducts.value.filter(p => p.stock === 0).length
}))

const filteredStats = computed(() => ({
  total: filteredProducts.value.length,
  totalValue: filteredProducts.value.reduce((sum, p) => sum + (p.price * p.stock), 0),
  averagePrice: filteredProducts.value.length > 0 
    ? filteredProducts.value.reduce((sum, p) => sum + p.price, 0) / filteredProducts.value.length 
    : 0,
  totalStock: filteredProducts.value.reduce((sum, p) => sum + p.stock, 0)
}))

const hasActiveFilters = computed(() => 
  searchQuery.value || 
  filterCategory.value || 
  filterSubcategory.value ||
  filterStockStatus.value ||
  filterPriceMin.value !== null ||
  filterPriceMax.value !== null ||
  filterDateFrom.value ||
  filterDateTo.value ||
  filterTamaño.value
)

const activeFiltersCount = computed(() => {
  let count = 0
  if (searchQuery.value) count++
  if (filterCategory.value) count++
  if (filterSubcategory.value) count++
  if (filterStockStatus.value) count++
  if (filterPriceMin.value !== null) count++
  if (filterPriceMax.value !== null) count++
  if (filterDateFrom.value) count++
  if (filterDateTo.value) count++
  if (filterTamaño.value) count++
  return count
})

// Métodos
const getStockBadgeClass = (stock: number): string => {
  if (stock === 0) return 'bg-red-50 text-red-700 border border-red-200'
  if (stock < 10) return 'bg-yellow-50 text-yellow-700 border border-yellow-200'
  return 'bg-green-50 text-green-700 border border-green-200'
}

const getStockLabel = (stock: number): string => {
  if (stock === 0) return 'Agotado'
  if (stock < 10) return 'Stock Bajo'
  return 'En Stock'
}

const formatPrice = (price: number, currency: string = 'MXN'): string => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency
  }).format(price)
}

const clearFilters = (): void => {
  searchQuery.value = ''
  filterCategory.value = ''
  filterSubcategory.value = ''
  filterStockStatus.value = ''
  filterPriceMin.value = null
  filterPriceMax.value = null
  filterDateFrom.value = ''
  filterDateTo.value = ''
  filterTamaño.value = ''
}

const toggleAdvancedFilters = (): void => {
  showAdvancedFilters.value = !showAdvancedFilters.value
}

const handleAddProduct = (): void => {
  router.visit('/admin/products/create')
}

const handleEditProduct = (product: Product): void => {
  router.visit(`/admin/products/${product.id}/edit`)
}

const handleDeleteProduct = (product: Product): void => {
  if (confirm(`¿Estás seguro de eliminar "${product.name}"?`)) {
    router.delete(`/admin/products/${product.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        // Flash message handled automatically
      }
    })
  }
}

const exportToCSV = (): void => {
  const headers = ['SKU', 'Nombre', 'Categoría', 'Subcategoría', 'Precio', 'Stock', 'Variante', 'Color', 'Stock Variante', 'Fecha Creación']
  const rows: any[] = []

  filteredProducts.value.forEach(p => {
    if (p.colors && p.colors.length > 0) {
      p.colors.forEach(color => {
        rows.push([
          p.sku,
          p.name,
          p.category || '',
          p.subcategory || '',
          p.price,
          p.stock,
          color.name,
          color.code,
          color.stock,
          p.created_at || '-'
        ])
      })
    } else {
      rows.push([
        p.sku,
        p.name,
        p.category || '',
        p.subcategory || '',
        p.price,
        p.stock,
        '-',
        '-',
        '-',
        p.created_at || '-'
      ])
    }
  })

  const csvContent = [
    headers.join(','),
    ...rows.map(row => row.map((cell: any) => `"${cell}"`).join(','))
  ].join('\n')

  const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  
  link.setAttribute('href', url)
  link.setAttribute('download', `productos_reporte_${new Date().toISOString().split('T')[0]}.csv`)
  link.style.visibility = 'hidden'
  
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const exportToPDF = (): void => {
  const doc = new jsPDF('l', 'mm', 'a4')
  
  // ===== ENCABEZADO FORMAL =====
  doc.setFillColor(30, 41, 59)
  doc.rect(0, 0, 297, 25, 'F')
  
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(20)
  doc.setFont('helvetica', 'bold')
  doc.text('REPORTE DE INVENTARIO DE PRODUCTOS', 14, 12)
  
  doc.setFontSize(9)
  doc.setFont('helvetica', 'normal')
  doc.text(`Generado el ${new Date().toLocaleDateString('es-MX', { 
    weekday: 'long',
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })}`, 14, 19)
  
  doc.setFontSize(8)
  doc.text(`Documento N°: RPT-${new Date().getTime()}`, 297 - 14, 12, { align: 'right' })
  doc.text(`Página 1 de ${Math.ceil(filteredProducts.value.length / 15)}`, 297 - 14, 17, { align: 'right' })
  
  // ===== RESUMEN EJECUTIVO =====
  doc.setTextColor(30, 41, 59)
  doc.setFontSize(11)
  doc.setFont('helvetica', 'bold')
  doc.text('I. RESUMEN EJECUTIVO', 14, 35)
  
  doc.setDrawColor(203, 213, 225)
  doc.setLineWidth(0.3)
  doc.line(14, 37, 283, 37)
  
  const summaryData = [
    ['Productos Filtrados', filteredStats.value.total.toString()],
    ['Valor Total Inventario', formatPrice(filteredStats.value.totalValue)],
    ['Precio Promedio', formatPrice(filteredStats.value.averagePrice)],
    ['Stock Total', `${filteredStats.value.totalStock} unidades`]
  ]
  
  autoTable(doc, {
    startY: 40,
    head: [['Concepto', 'Valor']],
    body: summaryData,
    theme: 'plain',
    styles: {
      fontSize: 9,
      cellPadding: 3,
    },
    headStyles: {
      fillColor: [241, 245, 249],
      textColor: [30, 41, 59],
      fontStyle: 'bold',
      lineWidth: 0.1,
      lineColor: [203, 213, 225]
    },
    columnStyles: {
      0: { cellWidth: 60, fontStyle: 'bold', textColor: [71, 85, 105] },
      1: { cellWidth: 50, halign: 'right' }
    },
    margin: { left: 14, right: 14 }
  })
  
  let currentY = (doc as any).lastAutoTable.finalY + 8
  
  // ===== FILTROS APLICADOS =====
  if (hasActiveFilters.value) {
    doc.setFontSize(11)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(30, 41, 59)
    doc.text('II. FILTROS APLICADOS', 14, currentY)
    
    doc.setDrawColor(203, 213, 225)
    doc.line(14, currentY + 2, 283, currentY + 2)
    
    currentY += 6
    
    const filtersData: string[][] = []
    
    if (filterCategory.value) {
      filtersData.push(['Categoría', filterCategory.value])
    }
    if (filterSubcategory.value) {
      filtersData.push(['Subcategoría', filterSubcategory.value])
    }
    if (filterStockStatus.value) {
      const statusLabels: Record<string, string> = {
        'in-stock': 'En Stock (≥10 unidades)',
        'low-stock': 'Stock Bajo (1-9 unidades)',
        'out-of-stock': 'Agotado (0 unidades)'
      }
      filtersData.push(['Estado de Stock', statusLabels[filterStockStatus.value]])
    }
    if (filterPriceMin.value !== null || filterPriceMax.value !== null) {
      const min = filterPriceMin.value !== null ? formatPrice(filterPriceMin.value) : '$0.00'
      const max = filterPriceMax.value !== null ? formatPrice(filterPriceMax.value) : 'Sin límite'
      filtersData.push(['Rango de Precio', `${min} - ${max}`])
    }
    if (filterDateFrom.value || filterDateTo.value) {
      const from = filterDateFrom.value ? new Date(filterDateFrom.value).toLocaleDateString('es-MX') : 'Inicio'
      const to = filterDateTo.value ? new Date(filterDateTo.value).toLocaleDateString('es-MX') : 'Hoy'
      filtersData.push(['Rango de Fechas', `${from} - ${to}`])
    }
    
    autoTable(doc, {
      startY: currentY,
      body: filtersData,
      theme: 'plain',
      styles: {
        fontSize: 8,
        cellPadding: 2,
      },
      columnStyles: {
        0: { cellWidth: 40, fontStyle: 'bold', textColor: [71, 85, 105] },
        1: { cellWidth: 80 }
      },
      margin: { left: 14 }
    })
    
    currentY = (doc as any).lastAutoTable.finalY + 8
  }
  
  // ===== DETALLE DE PRODUCTOS =====
  doc.setFontSize(11)
  doc.setFont('helvetica', 'bold')
  doc.setTextColor(30, 41, 59)
  doc.text('III. DETALLE DE PRODUCTOS', 14, currentY)
  
  doc.setDrawColor(203, 213, 225)
  doc.line(14, currentY + 2, 283, currentY + 2)
  
  currentY += 5
  
  const tableData: any[] = []
  
  filteredProducts.value.forEach(p => {
    if (p.colors && p.colors.length > 0) {
      tableData.push([
        { content: p.sku, styles: { fontStyle: 'bold' } },
        { content: p.name, styles: { fontStyle: 'bold' } },
        p.category || '-',
        p.subcategory || '-',
        { content: formatPrice(p.price), styles: { halign: 'right' } },
        { content: p.stock.toString(), styles: { halign: 'center' } },
        { content: p.colors.length.toString(), styles: { halign: 'center', fontStyle: 'bold' } },
        '-',
        '-',
        { content: p.created_at || '-', styles: { fontSize: 6, halign: 'center' } }
      ])
      
      p.colors.forEach((color) => {
        tableData.push([
          '',
          { content: `  └─ ${color.name}`, styles: { textColor: [100, 116, 139], fontSize: 7 } },
          '',
          '',
          '',
          '',
          '',
          { content: color.code, styles: { fontSize: 7, halign: 'center' } },
          { content: color.stock.toString(), styles: { halign: 'center', fontSize: 7 } },
          ''
        ])
      })
    } else {
      tableData.push([
        { content: p.sku, styles: { fontStyle: 'bold' } },
        { content: p.name, styles: { fontStyle: 'bold' } },
        p.category || '-',
        p.subcategory || '-',
        { content: formatPrice(p.price), styles: { halign: 'right' } },
        { content: p.stock.toString(), styles: { halign: 'center' } },
        { content: '0', styles: { halign: 'center', textColor: [148, 163, 184] } },
        '-',
        '-',
        { content: p.created_at || '-', styles: { fontSize: 6, halign: 'center' } }
      ])
    }
  })
  
  autoTable(doc, {
    startY: currentY,
    head: [['SKU', 'Producto', 'Categoría', 'Subcategoría', 'Precio', 'Stock', 'Vars.', 'Color', 'Stock V.', 'F. Creación']],
    body: tableData,
    theme: 'grid',
    styles: {
      fontSize: 7,
      cellPadding: 2,
      overflow: 'linebreak',
      lineColor: [203, 213, 225],
      lineWidth: 0.1,
      textColor: [30, 41, 59]
    },
    headStyles: {
      fillColor: [241, 245, 249],
      textColor: [30, 41, 59],
      fontStyle: 'bold',
      fontSize: 7,
      cellPadding: 3,
      halign: 'center',
      lineWidth: 0.2,
      lineColor: [148, 163, 184]
    },
    columnStyles: {
      0: { cellWidth: 22 },
      1: { cellWidth: 50 },
      2: { cellWidth: 25 },
      3: { cellWidth: 25 },
      4: { cellWidth: 22, halign: 'right' },
      5: { cellWidth: 15, halign: 'center' },
      6: { cellWidth: 13, halign: 'center' },
      7: { cellWidth: 20, halign: 'center' },
      8: { cellWidth: 15, halign: 'center' },
      9: { cellWidth: 22, halign: 'center' }
    },
    alternateRowStyles: {
      fillColor: [248, 250, 252]
    },
    didParseCell: function(data) {
      if (data.section === 'body' && data.column.index === 1) {
        const cellText = String(data.cell.text)
        if (cellText.includes('└─')) {
          data.cell.styles.fillColor = [241, 245, 249]
        }
      }
    }
  })
  
  // ===== PIE DE PÁGINA =====
  const pageCount = (doc as any).internal.getNumberOfPages()
  const pageWidth = doc.internal.pageSize.getWidth()
  const pageHeight = doc.internal.pageSize.getHeight()
  
  for (let i = 1; i <= pageCount; i++) {
    doc.setPage(i)
    
    doc.setDrawColor(148, 163, 184)
    doc.setLineWidth(0.5)
    doc.line(14, pageHeight - 18, pageWidth - 14, pageHeight - 18)
    
    doc.setFontSize(7)
    doc.setTextColor(100, 116, 139)
    doc.setFont('helvetica', 'normal')
    
    doc.text('Documento generado automáticamente por el sistema', 14, pageHeight - 12)
    doc.text(`Fecha y hora: ${new Date().toLocaleString('es-MX')}`, 14, pageHeight - 8)
    
    doc.setFont('helvetica', 'italic')
    doc.text('DOCUMENTO CONFIDENCIAL - USO INTERNO', pageWidth / 2, pageHeight - 10, { align: 'center' })
    
    doc.setFont('helvetica', 'bold')
    doc.text(`Página ${i} de ${pageCount}`, pageWidth - 14, pageHeight - 10, { align: 'right' })
  }
  
  doc.save(`reporte_inventario_${new Date().toISOString().split('T')[0]}.pdf`)
}
</script>

<template>
  <Head title="Productos" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="w-full px-6 py-8">
      <!-- Flash Messages -->
      <div v-if="flash?.success" class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
        <p class="text-sm text-green-800 font-medium">{{ flash.success }}</p>
      </div>
      
      <div v-if="flash?.error" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
        <p class="text-sm text-red-800 font-medium">{{ flash.error }}</p>
      </div>

      <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">
              Productos
            </h1>
            <p class="mt-1 text-sm text-gray-600">
              Gestiona tu inventario de productos
            </p>
          </div>
          <div class="flex gap-2">
            <button 
              @click="exportToCSV"
              class="inline-flex items-center justify-center gap-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 h-10 px-4 transition-colors"
              title="Exportar a CSV"
            >
              <Download class="h-4 w-4" />
              <span class="hidden sm:inline">CSV</span>
            </button>
            <button 
              @click="exportToPDF"
              class="inline-flex items-center justify-center gap-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 h-10 px-4 transition-colors"
              title="Exportar a PDF"
            >
              <FileText class="h-4 w-4" />
              <span class="hidden sm:inline">PDF</span>
            </button>
            <button 
              @click="handleAddProduct"
              class="inline-flex items-center justify-center gap-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 h-10 px-5 transition-colors"
            >
              <Plus class="h-4 w-4" />
              Agregar
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100">
                <ShoppingBag class="h-5 w-5 text-gray-600" />
              </div>
              <Package class="h-4 w-4 text-gray-400" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Total Productos</p>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ stats.total }}</p>
            <p class="text-xs text-gray-500">En tu inventario</p>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100">
                <TrendingUp class="h-5 w-5 text-green-600" />
              </div>
              <Package class="h-4 w-4 text-green-400" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">En Stock</p>
            <p class="text-2xl font-bold text-green-600 mb-1">{{ stats.inStock }}</p>
            <p class="text-xs text-gray-500">Stock suficiente</p>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100">
                <AlertCircle class="h-5 w-5 text-yellow-600" />
              </div>
              <AlertCircle class="h-4 w-4 text-yellow-400" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Stock Bajo</p>
            <p class="text-2xl font-bold text-yellow-600 mb-1">{{ stats.lowStock }}</p>
            <p class="text-xs text-gray-500">Requieren atención</p>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100">
                <AlertCircle class="h-5 w-5 text-red-600" />
              </div>
              <AlertCircle class="h-4 w-4 text-red-400" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Agotados</p>
            <p class="text-2xl font-bold text-red-600 mb-1">{{ stats.outOfStock }}</p>
            <p class="text-xs text-gray-500">Sin inventario</p>
          </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <h3 class="text-base font-bold text-gray-900">
                Filtros de Búsqueda
              </h3>
              <button
                @click="toggleAdvancedFilters"
                class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
              >
                <Filter class="h-4 w-4" />
                {{ showAdvancedFilters ? 'Ocultar' : 'Mostrar' }} filtros avanzados
                <span v-if="activeFiltersCount > 0" class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-600 text-white text-xs font-bold">
                  {{ activeFiltersCount }}
                </span>
              </button>
            </div>
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-medium"
            >
              <X class="h-4 w-4" />
              Limpiar todo
            </button>
          </div>

          <div class="p-6">
            <div class="mb-4">
              <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Buscar por nombre, SKU, descripción..."
                  class="w-full h-11 pl-10 pr-4 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>

            <transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div v-if="showAdvancedFilters" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <Archive class="h-3 w-3 inline mr-1" />
                      Categoría
                    </label>
                    <select
                      v-model="filterCategory"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="">Todas las categorías</option>
                      <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <Package class="h-3 w-3 inline mr-1" />
                      Subcategoría
                    </label>
                    <select
                      v-model="filterSubcategory"
                      :disabled="!filterCategory"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="">Todas las subcategorías</option>
                      <option v-for="subcat in subcategories" :key="subcat" :value="subcat">{{ subcat }}</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <TrendingUp class="h-3 w-3 inline mr-1" />
                      Estado de Stock
                    </label>
                    <select
                      v-model="filterStockStatus"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="">Todos los estados</option>
                      <option value="in-stock">En Stock (≥10)</option>
                      <option value="low-stock">Stock Bajo (1-9)</option>
                      <option value="out-of-stock">Agotado (0)</option>
                    </select>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <DollarSign class="h-3 w-3 inline mr-1" />
                      Precio Mínimo
                    </label>
                    <input
                      v-model.number="filterPriceMin"
                      type="number"
                      placeholder="$0.00"
                      min="0"
                      step="0.01"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <DollarSign class="h-3 w-3 inline mr-1" />
                      Precio Máximo
                    </label>
                    <input
                      v-model.number="filterPriceMax"
                      type="number"
                      placeholder="$9999.99"
                      min="0"
                      step="0.01"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <Package class="h-3 w-3 inline mr-1" />
                      Tamaño
                    </label>
                    <select
                      v-model="filterTamaño"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="">Todos los tamaños</option>
                      <option v-for="size in tamaños" :key="size" :value="size">{{ size }}</option>
                    </select>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <Calendar class="h-3 w-3 inline mr-1" />
                      Fecha Desde
                    </label>
                    <input
                      v-model="filterDateFrom"
                      type="date"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                      <Calendar class="h-3 w-3 inline mr-1" />
                      Fecha Hasta
                    </label>
                    <input
                      v-model="filterDateTo"
                      type="date"
                      class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-200">
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                      <p class="text-xs text-blue-700 font-medium mb-1">Productos Filtrados</p>
                      <p class="text-xl font-bold text-blue-900">{{ filteredStats.total }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                      <p class="text-xs text-green-700 font-medium mb-1">Valor Total</p>
                      <p class="text-xl font-bold text-green-900">{{ formatPrice(filteredStats.totalValue) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                      <p class="text-xs text-purple-700 font-medium mb-1">Precio Promedio</p>
                      <p class="text-xl font-bold text-purple-900">{{ formatPrice(filteredStats.averagePrice) }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                      <p class="text-xs text-orange-700 font-medium mb-1">Stock Total</p>
                      <p class="text-xl font-bold text-orange-900">{{ filteredStats.totalStock }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-900">
              Lista de Productos
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              {{ filteredProducts.length }} producto{{ filteredProducts.length !== 1 ? 's' : '' }} encontrado{{ filteredProducts.length !== 1 ? 's' : '' }}
            </p>
          </div>

          <template v-if="filteredProducts.length > 0">
            <div class="hidden lg:block overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Imagen</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Variantes</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <template v-for="product in filteredProducts" :key="product.id">
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td class="px-6 py-5 w-24">
                        <div class="h-14 w-14 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-200">
                          <img 
                            v-if="product.thumbnail" 
                            :src="product.thumbnail" 
                            :alt="product.name" 
                            class="h-full w-full object-cover"
                          />
                          <Package v-else class="h-6 w-6 text-gray-400" />
                        </div>
                      </td>

                      <td class="px-6 py-5">
                        <div class="min-w-[200px] max-w-[350px]">
                          <p class="text-sm font-semibold text-gray-900 truncate leading-tight">{{ product.name }}</p>
                          <p class="text-xs text-gray-500 truncate mt-1">{{ product.subcategory }}</p>
                        </div>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <code class="text-xs bg-gray-50 text-gray-700 px-2.5 py-1.5 rounded-md border border-gray-200 font-mono">
                          {{ product.sku }}
                        </code>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                          {{ product.category || 'Sin categoría' }}
                        </span>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900">
                          {{ formatPrice(product.price, product.currency) }}
                        </span>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center gap-2.5">
                          <span :class="['inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold', getStockBadgeClass(product.stock)]">
                            {{ getStockLabel(product.stock) }}
                          </span>
                          <span class="text-sm font-medium text-gray-900">{{ product.stock }}</span>
                        </div>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <span v-if="product.colors?.length" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                          {{ product.colors.length }} variante{{ product.colors.length !== 1 ? 's' : '' }}
                        </span>
                        <span v-else class="text-xs text-gray-400">Sin variantes</span>
                      </td>

                      <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-xs text-gray-700 font-medium">
                          {{ product.created_at || '-' }}
                        </div>
                      </td>
                    </tr>

                    <tr 
                      v-for="(color, index) in product.colors" 
                      :key="`${product.id}-color-${color.id}`"
                      class="bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                      <td class="px-6 py-3"></td>
                      <td class="px-6 py-3" colspan="2">
                        <div class="flex items-center gap-3 pl-6">
                          <div 
                            class="h-6 w-6 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-300"
                            :style="{ backgroundColor: color.code }"
                          />
                          <span class="text-sm text-gray-700 font-medium">{{ color.name }}</span>
                          <code class="text-xs text-gray-500 font-mono">{{ color.code }}</code>
                        </div>
                      </td>
                      <td class="px-6 py-3"></td>
                      <td class="px-6 py-3"></td>
                      <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                          <span :class="['inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold', getStockBadgeClass(color.stock)]">
                            {{ getStockLabel(color.stock) }}
                          </span>
                          <span class="text-sm font-medium text-gray-700">{{ color.stock }}</span>
                        </div>
                      </td>
                      <td class="px-6 py-3"></td>
                      <td class="px-6 py-3"></td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </template>

          <div v-else class="flex flex-col items-center justify-center py-20 px-4">
            <div class="p-4 rounded-xl bg-gray-50 mb-4">
              <Package class="h-12 w-12 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay productos</h3>
            <p class="text-sm text-gray-600 mb-6 text-center max-w-sm">
              {{ hasActiveFilters ? 'No se encontraron productos con los filtros aplicados' : 'Comienza agregando tu primer producto al inventario' }}
            </p>
            <button 
              v-if="!hasActiveFilters"
              @click="handleAddProduct"
              class="inline-flex items-center gap-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 h-10 px-5 transition-colors"
            >
              <Plus class="h-4 w-4" />
              Agregar Producto
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>