<script setup lang="ts">
const route = useRoute()
const appStore = useAppStore()
const cartStore = useCartStore()
const authStore = useAuthStore()

definePageMeta({
   auth: false,
})

const id = route.params.id

// Fetch product details
const { data: product, pending } = useApi(`/api/products/${id}`, {
   method: "get",
   transform: (res) => ({ ...res.data, add_to_cart_qty: 1 }),
   deep: true,
})

const subtotal = computed(() => {
   const price = product.value?.price ?? 0
   const cartQty = product.value?.add_to_cart_qty ?? 0
   return $formatNumber(price * cartQty, {
      locale: "id-ID",
      style: "currency",
      currency: "IDR",
      currencyDisplay: "narrowSymbol",
   })
})

// Get stock status
async function addToCart() {
   if (!authStore.isLoggedIn) {
      navigateTo("/login")
      return
   }

   if (!product.value) return

   const response = await cartStore.addCartItem({
      product_id: product.value.id,
      quantity: product.value.add_to_cart_qty,
   })

   if (response) {
      appStore.notify({
         title: "Sukses",
         description: response.message,
         color: "success",
      })
   }
}
</script>

<template>
   <UPage>
      <UPageBody>
         <UPageHeader
            title="Detail Produk"
            description="Informasi lengkap mengenai produk ini"
         >
            <template #headline>
               <UButton
                  icon="lucide:arrow-left"
                  label="Kembali"
                  variant="ghost"
                  color="neutral"
                  size="sm"
                  to="/products"
                  aria-label="Kembali"
               />
            </template>
         </UPageHeader>

         <!-- Loading State -->
         <div
            v-if="pending"
            class="flex h-64 items-center justify-center"
         >
            <UIcon
               name="lucide:loader-2"
               class="size-8 animate-spin text-primary"
            />
         </div>

         <!-- Detail View Layout -->
         <div
            v-else-if="product"
            class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start"
         >
            <!-- Left Column: Product Image -->
            <div class="lg:col-span-1 flex flex-col gap-4">
               <div
                  class="aspect-square w-full rounded-md bg-muted flex items-center justify-center overflow-hidden border border-muted"
               >
                  <img
                     v-if="product.image_url"
                     :src="product.image_url"
                     :alt="product.name"
                     class="h-full w-full object-cover"
                  />
                  <UEmpty
                     v-else
                     title="Tidak ada gambar"
                     icon="lucide:shopping-bag"
                     variant="naked"
                  />
               </div>
            </div>

            <!-- Right Column: Detail Information -->
            <div class="lg:col-span-2 flex flex-col">
               <h1 class="text-highlighted text-2xl font-semibold">
                  {{ product.name }}
               </h1>
               <div class="text-3xl font-extrabold text-primary mt-4">
                  {{
                     $formatNumber(product.price, {
                        locale: "id-ID",
                        style: "currency",
                        currency: "IDR",
                        currencyDisplay: "narrowSymbol",
                     })
                  }}
               </div>

               <USeparator class="my-6" />

               <div
                  class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 py-2 items-start"
               >
                  <div class="sm:col-span-2 flex flex-col gap-6">
                     <div class="">
                        <span class="block text-sm text-muted mb-1"
                           >Kategori</span
                        >
                        <div class="flex flex-wrap gap-2">
                           <template
                              v-if="
                                 product.categories &&
                                 product.categories.length > 0
                              "
                           >
                              <UBadge
                                 v-for="cat in product.categories"
                                 :key="cat.id"
                                 color="neutral"
                                 variant="subtle"
                              >
                                 {{ cat.name }}
                              </UBadge>
                           </template>
                           <span
                              v-else
                              class="text-sm text-muted italic"
                           >
                              Tanpa kategori
                           </span>
                        </div>
                     </div>

                     <div class="col-span-full">
                        <span class="block text-sm text-muted mb-2"
                           >Deskripsi Produk</span
                        >
                        <div
                           class="text-default leading-relaxed whitespace-pre-wrap"
                        >
                           {{ product.description }}
                        </div>
                     </div>
                  </div>
                  <!-- Cart Actions -->
                  <div
                     class="bg-default p-4 rounded-lg flex flex-col gap-4 border border-muted"
                  >
                     <div class="flex items-center gap-4">
                        <UInputNumber
                           v-model="product.add_to_cart_qty"
                           size="md"
                           :min="1"
                           :max="product.stock_quantity"
                           :ui="{ root: 'w-28' }"
                        />
                        <span class="text-sm">
                           Stok:
                           <span class="text-highlighted font-semibold">{{
                              product.stock_quantity
                           }}</span>
                        </span>
                     </div>
                     <USeparator />
                     <div class="flex flex-col gap-2">
                        <div class="flex items-end justify-between">
                           <span class="text-sm text-muted"> Subtotal </span>
                           <span
                              class="text-lg text-highlighted font-semibold leading-snug"
                           >
                              {{ subtotal }}
                           </span>
                        </div>
                     </div>
                     <USeparator />
                     <UButton
                        icon="lucide:shopping-cart"
                        label="Masukkan ke Keranjang"
                        color="primary"
                        size="lg"
                        block
                        :disabled="product.stock_quantity === 0"
                        @click="addToCart"
                     />
                  </div>
               </div>
            </div>
         </div>

         <div
            v-else
            class="flex flex-col items-center justify-center py-20"
         >
            <UEmpty
               title="Produk Tidak Ditemukan"
               description="Produk yang Anda cari mungkin telah dihapus atau tidak tersedia."
               icon="lucide:package-x"
               variant="naked"
            />
            <UButton
               label="Kembali ke Katalog"
               to="/products"
               color="primary"
               class="mt-4"
               variant="soft"
            />
         </div>
      </UPageBody>
   </UPage>
</template>
