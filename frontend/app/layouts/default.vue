<script setup lang="ts">
import type { NavigationMenuItem } from "@nuxt/ui"

const authStore = useAuthStore()
const appStore = useAppStore()

const items = computed<NavigationMenuItem[]>(() => [
   {
      label: "Home",
      to: "/",
   },
   {
      label: "Form Handling",
      to: "/form-handling",
   },
   {
      label: "Modal Form",
      to: "/modal-form",
   },
])

async function handleLogout() {
   try {
      await $api("/api/auth/logout", {
         method: "POST",
      })

      // Clear user in store
      authStore.setUser(null)

      appStore.notify({
         title: "Logout Berhasil",
         description: "Anda telah keluar dari akun Anda.",
         color: "success",
      })

      // Redirect to login page
      navigateTo("/login")
   } catch (e) {
      $notifyError(e)
   }
}
</script>

<template>
   <div
      class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-550"
   >
      <!-- UHeader from Nuxt UI -->
      <UHeader
         title="Adhivas E-Commerce"
         to="/"
      >
         <!-- Left / Title Slot -->
         <template #title>
            <span
               class="bg-linear-to-r from-primary-500 to-indigo-500 bg-clip-text text-xl font-bold tracking-tight text-transparent dark:from-primary-400 dark:to-indigo-400"
            >
               Adhivas E-Commerce
            </span>
         </template>

         <!-- Center / Navigation Menu -->
         <UNavigationMenu :items="items" />

         <!-- Right Slot -->
         <template #right>
            <div class="flex items-center gap-4">
               <template v-if="authStore.isLoggedIn">
                  <div class="flex items-center gap-3">
                     <div class="text-right hidden sm:block">
                        <p
                           class="text-xs font-semibold text-slate-700 dark:text-slate-300"
                        >
                           {{ authStore.user?.full_name }}
                        </p>
                        <p
                           class="text-[10px] text-slate-400 dark:text-slate-500 capitalize"
                        >
                           {{ authStore.user?.role }}
                        </p>
                     </div>
                     <UButton
                        color="error"
                        variant="subtle"
                        size="sm"
                        label="Logout"
                        @click="handleLogout"
                     />
                  </div>
               </template>
               <template v-else>
                  <UButton
                     to="/login"
                     color="primary"
                     variant="solid"
                     size="sm"
                     label="Login"
                  />
               </template>
            </div>
         </template>

         <!-- Mobile Menu Body -->
         <template #body>
            <UNavigationMenu
               :items="items"
               orientation="vertical"
               class="-mx-2.5"
            />
         </template>
      </UHeader>

      <!-- Main Content -->
      <UMain>
         <slot />
      </UMain>
   </div>
</template>
