<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { urlIsActive } from '@/lib/utils'
import type { NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'

defineProps<{ items: NavItem[] }>()
const page = usePage()
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>

    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <SidebarMenuButton
          as-child
          :is-active="urlIsActive(item.href, page.url)"
          :tooltip="item.title"
          :class="[
            item.level === 1 && 'pl-8',
            item.level === 2 && 'pl-12'
          ]"
        >
          <Link :href="item.href" class="gap-2">
            <!-- Si no hay icono, ponemos un “dot” chiquito para simular subitem -->
            <template v-if="item.icon">
              <component :is="item.icon" />
            </template>
            <template v-else>
              <span class="ml-1 block h-2 w-2 rounded-full bg-foreground/60" aria-hidden="true"></span>
            </template>

            <span :class="item.level ? 'text-sm' : ''" class="truncate">
              {{ item.title }}
            </span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
