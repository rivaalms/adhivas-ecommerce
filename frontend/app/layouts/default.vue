<script setup lang="ts">
import type { DropdownMenuItem, NavigationMenuItem } from "@nuxt/ui"

const authStore = useAuthStore()
const appStore = useAppStore()
const cartStore = useCartStore()

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

onMounted(() => {
   cartStore.fetchCart()
})
</script>

<template>
   <div class="bg-muted">
      <UHeader
         title="Adhivas E-Commerce"
         to="/"
      >
         <template #title>
            <span
               class="bg-linear-to-r from-primary-500 to-indigo-500 bg-clip-text text-xl font-bold tracking-tight text-transparent dark:from-primary-400 dark:to-indigo-400"
            >
               Adhivas E-Commerce
            </span>
         </template>

         <UNavigationMenu
            :items="items"
            variant="link"
         />

         <template #right>
            <div class="flex items-center gap-4">
               <template v-if="authStore.isLoggedIn">
                  <UPopover
                     mode="hover"
                     :ui="{ content: 'min-w-sm' }"
                  >
                     <UChip
                        :text="cartStore.cart?.items?.length ?? 0"
                        color="error"
                        variant="solid"
                        size="xl"
                        inset
                        position="top-right"
                     >
                        <UButton
                           icon="lucide:shopping-cart"
                           variant="ghost"
                           color="neutral"
                        />
                     </UChip>
                     <template #content>
                        <div class="p-2">
                           <UEmpty
                              v-if="!cartStore.cart?.items.length"
                              icon="lucide:shopping-cart"
                              title="Keranjang belanjamu kosong"
                              description="Tambah produk pilihanmu untuk melengkapi pesananmu."
                              variant="naked"
                           />
                           <template v-else>
                              <div
                                 class="flex flex-col max-h-40 overflow-y-auto"
                              >
                                 <NuxtLink
                                    v-for="item in cartStore.cart.items"
                                    :key="item.id"
                                    :to="`/products/${item.product?.id}`"
                                    class="group rounded-md overflow-hidden"
                                 >
                                    <div
                                       class="flex items-center gap-2 group-hover:bg-muted transition p-2"
                                    >
                                       <div
                                          class="bg-accented rounded-md aspect-square size-12"
                                       >
                                          <img
                                             :src="
                                                item.product?.image_url ??
                                                'https://picsum.photos/seed/picsum/200/200'
                                             "
                                             class="h-12 w-12 rounded-md object-cover aspect-square"
                                          />
                                       </div>
                                       <div class="flex flex-col flex-1">
                                          <div class="line-clamp-1 text-sm">
                                             {{ item.product?.name }}
                                          </div>
                                          <div class="flex items-center">
                                             <span
                                                class="font-semibold text-sm"
                                             >
                                                {{
                                                   $formatNumber(
                                                      item.product?.price ?? 0,
                                                      {
                                                         style: "currency",
                                                         currency: "IDR",
                                                         currencyDisplay:
                                                            "narrowSymbol",
                                                      }
                                                   )
                                                }}
                                             </span>
                                             <span
                                                class="ms-auto text-xs text-muted"
                                             >
                                                Qty: {{ item.quantity }}
                                             </span>
                                          </div>
                                       </div>
                                    </div>
                                 </NuxtLink>
                              </div>
                              <USeparator class="my-2" />
                              <div class="flex items-center">
                                 <UButton
                                    icon="lucide:shopping-cart"
                                    label="Checkout"
                                    class="ms-auto"
                                    size="sm"
                                 />
                              </div>
                           </template>
                        </div>
                     </template>
                  </UPopover>
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

         <template #body>
            <UNavigationMenu
               :items="items"
               orientation="vertical"
               class="-mx-2.5"
            />
         </template>
      </UHeader>

      <UMain>
         <UContainer>
            <slot />
         </UContainer>
      </UMain>
   </div>
</template>
