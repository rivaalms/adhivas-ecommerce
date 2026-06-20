<script setup lang="ts">
import type { DropdownMenuItem, NavigationMenuItem } from "@nuxt/ui"

const authStore = useAuthStore()
const appStore = useAppStore()

const userMenuItems = computed<DropdownMenuItem[][]>(() => [
   [{ label: "Profile", icon: "lucide:user" }],
   [
      {
         label: "Logout",
         icon: "lucide:log-out",
         color: "error",
         onSelect: async () => {
            try {
               const response = await authStore.logout()
               appStore.notify({
                  title: response.message,
               })
               navigateTo("/login")
            } catch (e) {
               $notifyError(e)
            }
         },
      },
   ],
])

const navigationItem = [
   [
      {
         label: "Dashboard",
         to: "/admin",
         icon: "lucide:home",
      },
      {
         label: "Kategori Produk",
         to: "/admin/categories",
         icon: "lucide:layers",
      },
      {
         label: "Daftar Produk",
         to: "/admin/products",
         icon: "lucide:shopping-bag",
      },
      {
         label: "Daftar Order",
         to: "/admin/orders",
         icon: "lucide:shopping-cart",
      },
   ],
] satisfies NavigationMenuItem[][]
</script>

<template>
   <UDashboardGroup
      unit="rem"
      storage-key="layout"
   >
      <UDashboardSidebar
         collapsible
         :default-size="16"
         :min-size="16"
      >
         <template #header="{ collapsed }">
            <NuxtLink
               to="/admin"
               class="flex items-center justify-center gap-2"
            >
               <img
                  src="/logo.svg"
                  class="h-6 w-auto"
                  alt="Adhivas E-Commerce Logo"
               />
               <span
                  v-show="!collapsed"
                  class="font-bold tracking-tight text-primary"
               >
                  Adhivas E-Commerce
               </span>
            </NuxtLink>
         </template>
         <template #default="{ collapsed }">
            <UNavigationMenu
               :items="navigationItem"
               :collapsed="collapsed"
               orientation="vertical"
            />
         </template>
      </UDashboardSidebar>
      <UDashboardPanel>
         <template #header>
            <UDashboardNavbar>
               <template #left>
                  <UTooltip text="Toggle Sidebar">
                     <UDashboardSidebarCollapse />
                  </UTooltip>
               </template>
               <template #right>
                  <UDropdownMenu :items="userMenuItems">
                     <div class="group flex items-center justify-between gap-2">
                        <UUser
                           :avatar="{
                              src: 'https://api.dicebear.com/9.x/thumbs/svg',
                           }"
                           :name="authStore.user?.full_name"
                           :ui="{
                              root: 'select-none cursor-pointer',
                              name: 'text-muted group-hover:text-default transition',
                           }"
                        />
                        <UIcon
                           name="lucide:chevron-down"
                           class="text-muted group-hover:text-default transition"
                        />
                     </div>
                  </UDropdownMenu>
               </template>
            </UDashboardNavbar>
         </template>
         <template #body>
            <slot />
         </template>
      </UDashboardPanel>
   </UDashboardGroup>
</template>
