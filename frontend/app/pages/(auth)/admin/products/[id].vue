<script setup lang="ts">
import { ConfirmationPrompt, FormProduct, UButton } from "#components"

const route = useRoute()
const router = useRouter()
const appStore = useAppStore()

const id = route.params.id

// Fetch product details
const {
   data: product,
   pending,
   refresh,
} = useApi(`/api/products/${id}`, {
   method: "get",
   transform: (res) => res.data,
})

// Get stock status
const stockStatus = computed(() => {
   if (!product.value) return { label: "Unknown", color: "neutral" as const }
   const stock = product.value.stock_quantity
   if (stock === 0) {
      return { label: "Stok Habis", color: "error" as const }
   } else if (stock <= 10) {
      return { label: "Stok Terbatas", color: "warning" as const }
   } else {
      return { label: "Stok Ready", color: "success" as const }
   }
})

const formLoading = shallowRef(false)

function openEditForm() {
   if (!product.value) return
   appStore.openModal(
      "Form Produk",
      h(FormProduct, {
         data: product.value,
         loading: formLoading,
         onSubmit: async (payload) => {
            try {
               const response = await $api(`/api/products/${id}`, {
                  method: "patch",
                  body: payload,
               })
               appStore.notify({
                  title: "Berhasil",
                  description: response.message,
                  color: "success",
               })
               refresh()
               appStore.closeModal()
            } catch (e) {
               $notifyError(e)
            } finally {
               formLoading.value = false
            }
         },
      })
   )
}

function confirmDelete() {
   if (!product.value) return
   appStore.openModal(
      "Konfirmasi Hapus",
      h(ConfirmationPrompt, {
         prompt: `Apakah kamu yakin ingin menghapus produk ${product.value.name}?`,
         icon: "lucide:trash",
         positiveButtonProps: {
            color: "error",
            icon: "lucide:trash",
            label: "Hapus",
         },
         onPositive: async () => {
            try {
               const response = await $api(`/api/products/${id}`, {
                  method: "delete",
               })
               appStore.notify({
                  title: "Berhasil",
                  description: response.message,
                  color: "info",
               })
               appStore.closeModal()
               router.push("/admin/products")
            } catch (e) {
               $notifyError(e)
            }
         },
      })
   )
}
</script>

<template>
   <UPage>
      <!-- Page Header with Back button and Actions -->
      <UPageHeader
         title="Detail Produk"
         description="Detail lengkap data produk"
      >
         <template #headline>
            <UButton
               icon="lucide:arrow-left"
               label="Kembali"
               variant="ghost"
               color="neutral"
               size="sm"
               to="/admin/products"
               aria-label="Kembali"
            />
         </template>
         <template #links>
            <div class="flex items-center gap-2">
               <UButton
                  label="Edit Produk"
                  icon="lucide:edit"
                  color="neutral"
                  variant="outline"
                  @click="openEditForm"
               />
               <UButton
                  label="Hapus"
                  icon="lucide:trash"
                  color="error"
                  variant="outline"
                  @click="confirmDelete"
               />
            </div>
         </template>
      </UPageHeader>

      <!-- Loading State -->
      <div
         v-if="pending"
         class="flex h-64 items-center justify-center"
      >
         <UIcon
            name="lucide:loader-2"
            class="size-8 animate-spin text-primary-500"
         />
      </div>

      <!-- Detail View Layout -->
      <div
         v-else-if="product"
         class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start"
      >
         <!-- Left Column: Product Image -->
         <div class="lg:col-span-1">
            <div
               class="aspect-square w-full rounded-md bg-muted flex items-center justify-center overflow-hidden border border-muted border-dashed"
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
            <h1 class="text-highlighted text-xl font-semibold">
               {{ product.name }}
            </h1>
            <div class="text-2xl font-extrabold text-highlighted mt-4">
               {{
                  $formatNumber(product.price, {
                     style: "currency",
                     currency: "IDR",
                     maximumFractionDigits: 0,
                  })
               }}
            </div>

            <USeparator class="my-6" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 py-2">
               <div>
                  <span class="block text-xs text-muted mb-1"> Stok </span>
                  <div class="flex items-center gap-2">
                     <span class="font-bold text-highlighted text-lg">
                        {{ product.stock_quantity }}
                     </span>
                     <UBadge
                        :color="stockStatus.color"
                        variant="subtle"
                        size="xs"
                     >
                        {{ stockStatus.label }}
                     </UBadge>
                  </div>
               </div>

               <div class="sm:col-span-2">
                  <span class="block text-xs text-muted mb-1">Kategori</span>
                  <div class="flex flex-wrap gap-2">
                     <template
                        v-if="
                           product.categories && product.categories.length > 0
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
                        class="text-sm text-slate-500 dark:text-slate-400 italic"
                     >
                        Belum ada kategori
                     </span>
                  </div>
               </div>

               <div>
                  <span class="block text-xs text-muted"> Tanggal Dibuat </span>
                  <span
                     class="font-medium text-slate-800 dark:text-slate-200 text-sm"
                  >
                     {{ $formatDate(product.created_at) }}
                  </span>
               </div>

               <div>
                  <span class="block text-xs text-muted">
                     Terakhir Diperbarui
                  </span>
                  <span
                     class="font-medium text-slate-800 dark:text-slate-200 text-sm"
                  >
                     {{ $formatDate(product.updated_at) }}
                  </span>
               </div>

               <div class="col-span-full">
                  <span class="block text-xs text-muted"> Deskripsi </span>
                  <span
                     class="font-medium text-slate-800 dark:text-slate-200 text-sm"
                  >
                     {{ product.description }}
                  </span>
               </div>
            </div>
         </div>
      </div>
   </UPage>
</template>
