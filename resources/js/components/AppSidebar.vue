<script setup lang="ts">
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import type { NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import AppLogo from './AppLogo.vue'

// Íconos correctos
import {
  LayoutGrid,
  Package, PackagePlus, PackageMinus, PackageCheck,
  ShoppingCart, ShoppingBag, Truck, CheckCheck,
  Megaphone, Upload, Trash2,
  // nuevos para user
  Heart, Bookmark,
  ShoppingCart as Cart, Store, History, PackageSearch,
  MapPin, Truck as DeliveryTruck, Undo2,
} from 'lucide-vue-next'

// obtenemos el usuario desde inertia
const page = usePage()
const user = page.props.auth?.user as { role?: string } | null

// Helpers para definir items sin repetir
const sec = (title: string, href: NavItem['href'], icon: NavItem['icon']): NavItem => ({
  title, href, icon, level: 0,
})
const sub = (title: string, href: NavItem['href'], icon?: NavItem['icon']): NavItem => ({
  title, href, icon, level: 1,
})

// items para admin
const adminNavItems: NavItem[] = [
  // Secciones
  sec('Admin Dashboard', '/admin', LayoutGrid),
  sec('Productos', '/admin/products', Package),
  sub('Crear Producto', '/admin/Create_Product', PackagePlus),
  sub('Eliminar Producto', '/admin/Delete_Product', PackageMinus),
  sub('Actualizar Producto', '/admin/Update_Product', PackageCheck),

  sec('Pedidos', '/admin/Pedidos/Pedidos', ShoppingCart),
  sub('Nuevos pedidos', '#', ShoppingBag),
  sub('Pedidos en proceso', '#', Truck),
  sub('Pedidos entregados', '#', CheckCheck),

  sec('Novedades', '#', Megaphone),
  sub('Subir Novedad', '#', Upload),
  sub('Eliminar Novedad', '#', Trash2),
]

// items para user normal
const userNavItems: NavItem[] = [
  // Inicio
  sec('Dashboard', '/dashboard', LayoutGrid),

  // Compras
  sec('Mis Compras', '/mis-compras', ShoppingCart),
  sub('Carrito', '/carrito', Cart),
  sub('Pedidos', '/pedidos', PackageSearch),
  sub('Historial', '/pedidos/historial', History),
  sub('Seguimiento', '/pedidos/seguimiento', DeliveryTruck),
  sub('Devoluciones', '/pedidos/devoluciones', Undo2),

  // Favoritos
  sec('Favoritos', '/favoritos', Heart),
  sub('Lista de deseos', '/favoritos/wishlist', Bookmark),

  // Envíos y direcciones
  sec('Direcciones', '/cuenta/direcciones', MapPin),
  sub('Mis direcciones', '/cuenta/direcciones', MapPin),
]

// según el rol asignamos el menú
const mainNavItems = user?.role === 'admin' ? adminNavItems : userNavItems
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link href="/">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
