<script setup lang="ts">
const route = useRoute()
const appStore = useAppStore()

const id = route.params.id

// Fetch order details
const {
   data: order,
   pending,
   refresh,
} = useApi(`/api/orders/${id}`, {
   method: "get",
   transform: (res) => res.data,
})

const statusLoading = shallowRef(false)

function getStatusBadge(status: OrderStatus) {
   switch (status) {
      case "pending":
         return { label: "Pending", color: "warning" as const }
      case "processing":
         return { label: "Diproses", color: "info" as const }
      case "shipped":
         return { label: "Dikirim", color: "neutral" as const }
      case "delivered":
         return { label: "Sampai", color: "success" as const }
      case "completed":
         return { label: "Selesai", color: "success" as const }
      case "cancelled":
         return { label: "Dibatalkan", color: "error" as const }
      default:
         return { label: status, color: "neutral" as const }
   }
}

async function updateOrderStatus(status: OrderStatus) {
   statusLoading.value = true
   try {
      const response = await $api(`/api/orders/${id}/status`, {
         method: "patch",
         body: { status },
      })
      appStore.notify({
         title: "Berhasil",
         description: response.message || "Status order berhasil diperbarui.",
         color: "success",
      })
      refresh()
   } catch (e) {
      $notifyError(e)
   } finally {
      statusLoading.value = false
   }
}

function getFullAddress(addr?: UserAddressDTO) {
   if (!addr) return "-"
   return [
      addr.address_line,
      addr.subdistrict_name,
      addr.district_name,
      addr.regency_name,
      addr.province_name,
      addr.postal_code,
   ]
      .filter(Boolean)
      .join(", ")
}
</script>

<template>
   <UPage>
      <UPageBody>
         <UPageHeader
            :title="`Detail Order #${id}`"
            description="Informasi rincian pesanan Anda"
         >
            <template #headline>
               <UButton
                  icon="lucide:arrow-left"
                  label="Kembali"
                  variant="ghost"
                  color="neutral"
                  size="sm"
                  to="/orders"
                  aria-label="Kembali"
               />
            </template>
            <template #links>
               <UBadge
                  v-if="order"
                  :color="getStatusBadge(order.status).color"
                  variant="subtle"
               >
                  {{ getStatusBadge(order.status).label }}
               </UBadge>
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
            v-else-if="order"
            class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start"
         >
            <!-- Left Column: Order Info, Shipping & Actions -->
            <div class="lg:col-span-2 flex flex-col gap-6">
               <!-- Order Details Card -->
               <UCard title="Informasi Pesanan">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                     <div>
                        <span class="block text-xs text-muted"
                           >Tanggal Pesan</span
                        >
                        <span class="text-highlighted">
                           {{ $formatDate(order.order_date) }}
                        </span>
                     </div>
                     <div>
                        <span class="block text-xs text-muted"
                           >Terakhir Diperbarui</span
                        >
                        <span class="text-highlighted">
                           {{ $formatDate(order.updated_at) }}
                        </span>
                     </div>
                     <div class="col-span-full">
                        <span class="block text-xs text-muted"
                           >Alamat Pengiriman</span
                        >
                        <span class="text-highlighted">
                           {{ getFullAddress(order.user_address) }}
                        </span>
                     </div>
                  </div>

                  <!-- Customer Action Panel -->
                  <div
                     v-if="
                        order.status === 'pending' || order.status === 'shipped'
                     "
                     class="flex gap-4 items-center mt-8"
                  >
                     <UButton
                        v-if="order.status === 'pending'"
                        label="Batalkan Pesanan"
                        color="error"
                        variant="ghost"
                        icon="lucide:x"
                        :loading="statusLoading"
                        @click="updateOrderStatus('cancelled')"
                     />
                     <UButton
                        v-if="order.status === 'shipped'"
                        label="Tandai Diterima"
                        icon="lucide:check-circle"
                        color="success"
                        :loading="statusLoading"
                        class="ms-auto"
                        @click="updateOrderStatus('delivered')"
                     />
                  </div>
               </UCard>
            </div>

            <!-- Right Column: Order Items Summary -->
            <div class="lg:col-span-1 flex flex-col gap-6">
               <UCard title="Rincian Produk">
                  <div class="flex flex-col divide-y divide-default mb-4">
                     <div
                        v-for="detail in order.order_details"
                        :key="detail.id"
                        class="py-3 first:pt-0 last:pb-0"
                     >
                        <div class="flex gap-4 items-center">
                           <div
                              class="size-16 rounded-md overflow-hidden bg-muted shrink-0 border border-default"
                           >
                              <img
                                 v-if="detail.product?.image_url"
                                 :src="detail.product.image_url"
                                 :alt="detail.product.name"
                                 class="h-full w-full object-cover"
                              />
                              <div
                                 v-else
                                 class="h-full w-full flex items-center justify-center text-muted"
                              >
                                 <UIcon
                                    name="lucide:shopping-bag"
                                    class="size-6"
                                 />
                              </div>
                           </div>
                           <div class="flex-1 flex flex-col justify-between">
                              <div>
                                 <h3
                                    class="font-medium text-highlighted line-clamp-1 text-sm"
                                 >
                                    {{ detail.product?.name }}
                                 </h3>
                              </div>
                              <div
                                 class="flex justify-between items-center text-sm mt-1"
                              >
                                 <div class="text-xs">
                                    <span class="text-default">
                                       {{
                                          $formatNumber(
                                             detail.product?.price || 0,
                                             {
                                                style: "currency",
                                                currency: "IDR",
                                                currencyDisplay: "narrowSymbol",
                                             }
                                          )
                                       }}
                                    </span>
                                    <span class="text-muted">
                                       &times; {{ detail.quantity }}
                                    </span>
                                 </div>
                                 <span class="font-semibold text-highlighted">
                                    {{
                                       $formatNumber(
                                          detail.price * detail.quantity,
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

                  <USeparator class="my-4" />

                  <div class="flex flex-col gap-1 text-sm">
                     <div class="flex justify-between">
                        <span class="text-muted">Biaya Pengiriman</span>
                        <span class="font-medium text-primary">Gratis</span>
                     </div>
                     <div class="flex justify-between text-base font-bold mt-2">
                        <span class="text-highlighted">Total Tagihan</span>
                        <span class="text-highlighted">
                           {{
                              $formatNumber(order.total_amount, {
                                 style: "currency",
                                 currency: "IDR",
                                 maximumFractionDigits: 0,
                              })
                           }}
                        </span>
                     </div>
                  </div>
               </UCard>
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
