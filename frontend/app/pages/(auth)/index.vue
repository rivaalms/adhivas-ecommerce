<script setup lang="ts">
const appStore = useAppStore()
const authStore = useAuthStore()
const cartStore = useCartStore()

const { data: products } = useApi(`/api/products`, {
   method: "get",
   query: {
      per_page: 5,
   },
   transform: (res) =>
      res.data.data.map((item) => ({ ...item, add_to_cart_qty: 1 })),
   deep: true,
})

const { data: categories } = useApi(`/api/categories`, {
   method: "get",
   query: {
      per_page: 10,
   },
   transform: (res) => res.data.data,
})

async function addToCart(product: ProductDTO & { add_to_cart_qty: number }) {
   const response = await cartStore.addCartItem({
      product_id: product.id,
      quantity: product.add_to_cart_qty,
   })

   if (response) {
      appStore.notify({
         title: "Sukses",
         description: response.message,
      })
   }
}
</script>

<template>
   <UPage>
      <UPageBody>
         <!-- Welcome Banner Section -->
         <UPageCard
            :title="`Selamat datang, ${authStore.user?.full_name}`"
            description="Belanja di Adhivas Ecommerce, produk dijamin original!"
            spotlight
            :ui="{
               title: 'text-2xl tracking-wide',
               description: 'leading-relaxed text-default text-lg',
            }"
         />
         <div class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold text-highlighted tracking-wide">
               Kategori
            </h2>
            <div class="flex items-center gap-6 overflow-x-auto p-0.5">
               <UPageCard
                  v-for="category in categories"
                  :key="category.id"
                  :title="category.name"
                  :ui="{
                     root: 'shrink-0 w-full max-w-2xs',
                     title: 'text-base font-medium line-clamp-2',
                  }"
               />
            </div>
         </div>
         <div class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold text-highlighted tracking-wide">
               Produk
            </h2>
            <div class="flex items-center gap-6 overflow-x-auto p-0.5">
               <UPageCard
                  v-for="product in products"
                  :key="product.id"
                  :ui="{
                     root: 'shrink-0 w-full max-w-2xs',
                     container: 'sm:p-4',
                  }"
                  :to="`/products/${product.id}`"
               >
                  <div class="flex flex-col gap-4">
                     <div
                        class="aspect-square w-full rounded-md overflow-hidden"
                     >
                        <img
                           src="https://picsum.photos/seed/picsum/200/200"
                           class="object-cover h-full w-full"
                        />
                     </div>
                     <div class="flex flex-col">
                        <div class="font-medium line-clamp-2">
                           {{ product.name }}
                        </div>
                        <div class="text-lg font-semibold">
                           {{
                              $formatNumber(product.price, {
                                 style: "currency",
                                 currency: "IDR",
                                 currencyDisplay: "narrowSymbol",
                              })
                           }}
                        </div>
                        <div
                           class="mt-4 flex items-center pointer-events-auto z-10"
                        >
                           <UInputNumber
                              v-model="product.add_to_cart_qty"
                              size="sm"
                              :min="1"
                              :max="product.stock_quantity"
                              :ui="{ root: 'max-w-24' }"
                           />
                           <div class="text-sm ms-auto text-muted font-medium">
                              Stok: {{ product.stock_quantity }}
                           </div>
                        </div>
                        <UButton
                           icon="lucide:plus"
                           label="Keranjang"
                           color="primary"
                           variant="soft"
                           block
                           class="mt-4 z-10"
                           @click.stop="addToCart(product)"
                        />
                     </div>
                  </div>
               </UPageCard>
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
