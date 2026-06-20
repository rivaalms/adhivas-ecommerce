<script setup lang="ts">
const cartStore = useCartStore()
const appStore = useAppStore()

// Filter selected items from the cart store
const selectedItems = computed(() => {
   return cartStore.cart?.items.filter((item) => item.selected) || []
})

// Calculate total payment amount
const totalPrice = computed(() => {
   return selectedItems.value.reduce((total, item) => {
      return total + (item.product?.price || 0) * item.quantity
   }, 0)
})

// Redirect back to home/cart if no items are selected
onMounted(() => {
   if (selectedItems.value.length === 0) {
      appStore.notify({
         title: "Keranjang Kosong",
         description: "Silakan pilih produk terlebih dahulu sebelum checkout.",
         color: "warning",
      })
      navigateTo("/")
   }
})

// Fetch user addresses from BFF
const { data: addresses, pending: pendingAddresses } = useApi(
   "/api/addresses",
   {
      method: "get",
      query: { per_page: 50 },
      transform: (res) => res.data.data,
   }
)

const selectedAddressId = shallowRef<number>()

function getAddressLine2(item: UserAddressDTO) {
   return (
      [
         item.subdistrict_name,
         item.district_name,
         item.regency_name,
         item.province_name,
      ].join(", ") + ` ${item.postal_code}`
   ).trim()
}

// Auto-select default address on load
watchImmediate(
   () => addresses.value,
   (list) => {
      if (list && list.length > 0 && !selectedAddressId.value) {
         const def = list.find((addr) => addr.is_default) || list[0]
         selectedAddressId.value = def?.id
      }
   }
)

const checkoutLoading = shallowRef(false)

async function placeOrder() {
   if (!selectedAddressId.value) {
      appStore.notify({
         title: "Alamat Wajib",
         description: "Silakan pilih alamat pengiriman terlebih dahulu.",
         color: "warning",
      })
      return
   }

   checkoutLoading.value = true
   try {
      const payload = {
         user_address_id: selectedAddressId.value,
         items: selectedItems.value.map((item) => ({
            product_id: item.product_id,
            quantity: item.quantity,
         })),
      }

      const response = await cartStore.checkoutItems(payload)

      appStore.notify({
         title: "Pesanan Berhasil",
         description: response.message || "Pesanan Anda berhasil diproses!",
         color: "success",
      })

      // Redirect user back to home
      navigateTo("/")
   } catch (e) {
      $notifyError(e)
   } finally {
      checkoutLoading.value = false
   }
}
</script>

<template>
   <UPage>
      <UPageBody>
         <UPageHeader
            title="Checkout"
            description="Tinjau pesanan Anda dan lengkapi detail pengiriman"
         >
            <template #headline>
               <UButton
                  icon="lucide:arrow-left"
                  label="Kembali"
                  variant="ghost"
                  color="neutral"
                  size="sm"
                  to="/"
                  aria-label="Kembali"
               />
            </template>
         </UPageHeader>

         <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start mt-6">
            <!-- Left Side: Order Items and Address Selection -->
            <div class="lg:col-span-2 flex flex-col gap-6">
               <!-- Address Selection Card -->
               <UCard title="Alamat Pengiriman">
                  <div
                     v-if="pendingAddresses"
                     class="flex justify-center py-6"
                  >
                     <UIcon
                        name="lucide:loader-2"
                        class="animate-spin size-6 text-primary"
                     />
                  </div>

                  <div
                     v-else-if="!addresses || addresses.length === 0"
                     class="py-6"
                  >
                     <UEmpty
                        icon="lucide:map-pin"
                        title="Belum ada alamat pengiriman"
                        description="Tambahkan alamat pengiriman di menu profil/pengaturan Anda."
                        variant="naked"
                     />
                  </div>

                  <div v-else>
                     <URadioGroup
                        v-model="selectedAddressId"
                        :items="addresses"
                        value-key="id"
                        variant="table"
                     >
                        <template #label="{ item }">
                           <div class="flex items-center gap-2">
                              <span>
                                 {{ item.address_line }}
                              </span>
                              <UBadge
                                 v-if="item.is_default"
                                 label="Utama"
                                 size="sm"
                                 variant="subtle"
                                 color="neutral"
                              />
                           </div>
                        </template>
                        <template #description="{ item }">
                           <p class="text-muted leading-relaxed">
                              {{ getAddressLine2(item) }}
                           </p>
                        </template>
                     </URadioGroup>
                  </div>
               </UCard>

               <!-- Order Items Detail Card -->
               <UCard title="Rincian Produk">
                  <div class="flex flex-col divide-y divide-default">
                     <div
                        v-for="item in selectedItems"
                        :key="item.id"
                     >
                        <div
                           class="flex gap-4 p-2 rounded-lg hover:bg-muted transition"
                        >
                           <div
                              class="size-16 rounded-md overflow-hidden bg-muted shrink-0"
                           >
                              <img
                                 :src="
                                    item.product?.image_url ||
                                    'https://picsum.photos/seed/picsum/200/200'
                                 "
                                 :alt="item.product?.name"
                                 class="h-full w-full object-cover"
                              />
                           </div>
                           <div class="flex-1 flex flex-col justify-between">
                              <div>
                                 <h3
                                    class="font-medium text-highlighted line-clamp-1"
                                 >
                                    {{ item.product?.name }}
                                 </h3>
                              </div>
                              <div
                                 class="flex justify-between items-center text-sm mt-1"
                              >
                                 <div class="">
                                    <span class="text-default">
                                       {{
                                          $formatNumber(
                                             item.product?.price || 0,
                                             {
                                                style: "currency",
                                                currency: "IDR",
                                                currencyDisplay: "narrowSymbol",
                                             }
                                          )
                                       }}
                                    </span>
                                    <span class="text-muted">
                                       &times; {{ item.quantity }}
                                    </span>
                                 </div>
                                 <span class="font-semibold text-highlighted">
                                    {{
                                       $formatNumber(
                                          (item.product?.price || 0) *
                                             item.quantity,
                                          {
                                             style: "currency",
                                             currency: "IDR",
                                             currencyDisplay: "narrowSymbol",
                                          }
                                       )
                                    }}
                                 </span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </UCard>
            </div>

            <!-- Right Side: Order Summary Card -->
            <div class="lg:col-span-1">
               <UCard title="Ringkasan Belanja">
                  <div class="flex flex-col gap-2 py-1 text-sm">
                     <div class="flex justify-between">
                        <span class="text-muted">
                           Total Harga ({{ selectedItems.length }} produk)
                        </span>
                        <span class="font-medium text-highlighted">
                           {{
                              $formatNumber(totalPrice, {
                                 style: "currency",
                                 currency: "IDR",
                                 currencyDisplay: "narrowSymbol",
                              })
                           }}
                        </span>
                     </div>
                     <div class="flex justify-between">
                        <span class="text-muted">Biaya Pengiriman</span>
                        <span class="font-medium text-primary">Gratis</span>
                     </div>

                     <USeparator class="my-2" />

                     <div class="flex justify-between text-base font-bold">
                        <span class="text-highlighted">Total Tagihan</span>
                        <span class="text-highlighted">
                           {{
                              $formatNumber(totalPrice, {
                                 style: "currency",
                                 currency: "IDR",
                                 currencyDisplay: "narrowSymbol",
                              })
                           }}
                        </span>
                     </div>

                     <UButton
                        label="Buat Pesanan"
                        color="primary"
                        block
                        size="lg"
                        class="mt-4"
                        icon="lucide:check-circle"
                        :loading="checkoutLoading"
                        @click="placeOrder"
                     />
                  </div>
               </UCard>
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
