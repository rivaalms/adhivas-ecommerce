<script setup lang="ts">
import type { DropdownMenuItem, NavigationMenuItem } from "@nuxt/ui"

const authStore = useAuthStore()
const appStore = useAppStore()
const cartStore = useCartStore()

const dayjs = useDayjs()

const items = computed(
   () =>
      [
         {
            label: "Beranda",
            icon: "lucide:home",
            to: "/",
         },
         {
            label: "Produk",
            icon: "lucide:shopping-bag",
            to: "/products",
         },
      ] satisfies NavigationMenuItem[]
)

const userMenuItems = computed(
   () =>
      [
         [
            {
               label: "Profile",
               icon: "lucide:user",
               onSelect: () => navigateTo("/profile"),
            },
            {
               label: "Pesanan Saya",
               icon: "lucide:shopping-bag",
               onSelect: () => navigateTo("/orders"),
            },
         ],
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
      ] satisfies DropdownMenuItem[][]
)

const mergedMenuItems = computed(() => {
   return [
      items.value,
      ...(authStore.isLoggedIn ? userMenuItems.value : []),
   ] satisfies NavigationMenuItem[][]
})

function selectAllCartItems() {
   cartStore.cart?.items.forEach((item) => {
      item.selected = true
   })
}

function checkoutItems() {
   const selected = cartStore.cart?.items.filter((item) => item.selected) || []
   if (selected.length === 0) {
      appStore.notify({
         title: "Checkout Gagal",
         description:
            "Silakan pilih minimal satu produk di keranjang belanja Anda.",
         color: "warning",
      })
      return
   }
   navigateTo("/checkout")
}

onMounted(() => {
   if (authStore.isLoggedIn) {
      cartStore.fetchCart()
   }
})
</script>

<template>
   <div class="bg-muted dark:bg-default">
      <UHeader
         title="Adhivas E-Commerce"
         to="/"
      >
         <template #title>
            <NuxtLink
               to="/"
               class="flex items-center gap-2"
            >
               <img
                  src="/logo.svg"
                  class="h-6 w-auto"
                  alt="Adhivas E-Commerce Logo"
               />
               <span class="text-lg font-bold tracking-tight text-primary">
                  Adhivas E-Commerce
               </span>
            </NuxtLink>
         </template>

         <UNavigationMenu
            :items="items"
            variant="link"
         />

         <template #right>
            <div class="flex items-center gap-4">
               <UColorModeButton />
               <template v-if="authStore.isLoggedIn">
                  <UPopover :ui="{ content: 'min-w-sm' }">
                     <UChip
                        :text="cartStore.cart?.items?.length ?? 0"
                        :show="!!cartStore.cart?.items.length"
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
                           <div class="flex items-center">
                              <UButton
                                 icon="lucide:check-check"
                                 label="Pilih Semua"
                                 size="sm"
                                 variant="ghost"
                                 color="neutral"
                                 @click="selectAllCartItems"
                              />
                           </div>
                           <USeparator class="my-2" />

                           <UEmpty
                              v-if="!cartStore.cart?.items.length"
                              icon="lucide:shopping-cart"
                              title="Keranjang belanjamu kosong"
                              description="Tambah produk pilihanmu untuk melengkapi pesananmu."
                              variant="naked"
                           />
                           <template v-else>
                              <div
                                 class="flex flex-col max-h-64 overflow-y-auto"
                              >
                                 <div
                                    v-for="item in cartStore.cart.items"
                                    :key="item.id"
                                    class="flex items-center gap-2 p-2 rounded-md hover:bg-muted transition"
                                 >
                                    <div class="self-start">
                                       <UCheckbox
                                          v-model="item.selected"
                                          class="z-10"
                                          @click.stop
                                       />
                                    </div>
                                    <NuxtLink
                                       class="flex-1 flex items-center gap-2"
                                       :to="`/products/${item.product?.id}`"
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
                                    </NuxtLink>
                                 </div>
                              </div>
                              <USeparator class="my-2" />
                              <div class="flex items-center">
                                 <UButton
                                    icon="lucide:shopping-cart"
                                    label="Checkout"
                                    class="ms-auto"
                                    size="sm"
                                    @click="checkoutItems"
                                 />
                              </div>
                           </template>
                        </div>
                     </template>
                  </UPopover>
                  <UDropdownMenu
                     :items="userMenuItems"
                     class="hidden lg:flex"
                  >
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
                     label="Login"
                  />
               </template>
            </div>
         </template>

         <template #body>
            <UNavigationMenu
               :items="mergedMenuItems"
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

      <USeparator />

      <UFooter>
         <template #left>
            <p class="text-sm text-muted">
               &copy; {{ dayjs().year() }} Adhivas E-Commerce. All rights
               reserved.
            </p>
         </template>
      </UFooter>
   </div>
</template>
