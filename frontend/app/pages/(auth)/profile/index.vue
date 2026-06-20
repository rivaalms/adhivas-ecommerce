<script setup lang="ts">
import { ConfirmationPrompt, FormAddress } from "#components"

const appStore = useAppStore()
const authStore = useAuthStore()

const {
   data: addresses,
   pending: pendingAddresses,
   refresh: refreshAddresses,
} = useApi("/api/addresses", {
   method: "get",
   query: { per_page: 50 },
   transform: (res) =>
      res.data.data.sort((a, b) => +b.is_default - +a.is_default),
})

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

const formLoading = shallowRef(false)

function openForm(data?: UserAddressDTO) {
   appStore.openModal(
      data ? "Edit Alamat" : "Tambah Alamat",
      h(FormAddress, {
         data,
         loading: formLoading,
         onSubmit: async (payload) => {
            formLoading.value = true
            try {
               const res = await $api(
                  data ? `/api/addresses/${data.id}` : `/api/addresses`,
                  {
                     method: data ? "PUT" : "POST",
                     body: payload,
                  }
               )
               appStore.notify({
                  title: "Berhasil",
                  description:
                     res.message ||
                     (data
                        ? "Alamat berhasil diperbarui"
                        : "Alamat berhasil ditambahkan"),
                  color: "success",
               })
               refreshAddresses()
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

function confirmDelete(address: UserAddressDTO) {
   appStore.openModal(
      "Konfirmasi Hapus",
      h(ConfirmationPrompt, {
         prompt: `Apakah Anda yakin ingin menghapus alamat ini?`,
         icon: "lucide:trash",
         positiveButtonProps: {
            color: "error",
            icon: "lucide:trash",
            label: "Hapus",
         },
         onPositive: async () => {
            try {
               const res = await $api(`/api/addresses/${address.id}`, {
                  method: "DELETE",
               })
               appStore.notify({
                  title: "Berhasil",
                  description: res.message || "Alamat berhasil dihapus",
                  color: "success",
               })
               refreshAddresses()
               appStore.closeModal()
            } catch (e) {
               $notifyError(e)
            }
         },
         onNegative: () => appStore.closeModal(),
      })
   )
}

async function setDefaultAddress(address: UserAddressDTO) {
   try {
      const res = await $api(`/api/addresses/${address.id}`, {
         method: "PUT",
         body: { is_default: true },
      })
      appStore.notify({
         title: "Berhasil",
         description: res.message || "Alamat utama berhasil diperbarui",
         color: "success",
      })
      refreshAddresses()
   } catch (e) {
      $notifyError(e)
   }
}
</script>

<template>
   <UPage>
      <UPageBody>
         <UPageHeader
            title="Profil Saya"
            description="Manajemen informasi detail akun dan alamat pengiriman Anda"
         />

         <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 items-start">
            <!-- Left Column: Account Information -->
            <div class="lg:col-span-1">
               <UCard title="Informasi Akun">
                  <div class="flex flex-col items-center text-center py-4">
                     <UAvatar
                        src="https://api.dicebear.com/9.x/thumbs/svg"
                        size="xl"
                        class="mb-4"
                     />
                     <h3 class="text-lg font-semibold text-default">
                        {{ authStore.user?.full_name }}
                     </h3>
                     <p class="text-sm text-muted mb-4">
                        {{ authStore.user?.email }}
                     </p>

                     <div class="w-full">
                        <USeparator class="my-4" />
                        <div class="flex justify-between text-sm py-2">
                           <span class="text-muted">Peran</span>
                           <span class="font-medium capitalize text-default">
                              {{
                                 authStore.user?.role === "customer"
                                    ? "Pelanggan"
                                    : authStore.user?.role
                              }}
                           </span>
                        </div>
                        <div class="flex justify-between text-sm py-2">
                           <span class="text-muted">Status Akun</span>
                           <span
                              class="font-medium text-success flex items-center gap-1"
                           >
                              <UIcon
                                 name="lucide:check-circle"
                                 class="size-4 text-success"
                              />
                              Aktif
                           </span>
                        </div>
                     </div>
                  </div>
               </UCard>
            </div>

            <!-- Right Column: Address Management -->
            <div class="lg:col-span-2 flex flex-col gap-6">
               <UCard>
                  <template #header>
                     <div class="flex items-center justify-between">
                        <div class="font-semibold text-highlighted">
                           Alamat Pengiriman
                        </div>
                        <UButton
                           icon="lucide:plus"
                           label="Tambah Alamat"
                           size="sm"
                           color="primary"
                           @click="openForm()"
                        />
                     </div>
                  </template>

                  <!-- Loading state -->
                  <div
                     v-if="pendingAddresses"
                     class="flex justify-center py-12"
                  >
                     <UIcon
                        name="lucide:loader-2"
                        class="animate-spin size-8 text-primary"
                     />
                  </div>

                  <!-- Empty state -->
                  <div
                     v-else-if="!addresses || addresses.length === 0"
                     class="py-12"
                  >
                     <UEmpty
                        icon="lucide:map-pin"
                        title="Belum ada alamat pengiriman"
                        description="Silakan tambahkan alamat pengiriman baru untuk mempermudah proses checkout."
                        variant="naked"
                     />
                  </div>

                  <!-- Addresses list -->
                  <div
                     v-else
                     class="flex flex-col gap-4"
                  >
                     <div
                        v-for="address in addresses"
                        :key="address.id"
                        class="border rounded-lg p-4 transition-all duration-200 hover:border-primary relative flex flex-col gap-3"
                        :class="[
                           address.is_default
                              ? 'border-primary bg-primary-50/10 dark:bg-primary-950/10'
                              : 'border-neutral-200 dark:border-neutral-800',
                        ]"
                     >
                        <div class="text-sm">
                           <div class="flex items-center gap-2">
                              <p class="font-medium text-default mb-1">
                                 {{ address.address_line }}
                              </p>
                              <UBadge
                                 v-if="address.is_default"
                                 label="Utama"
                                 size="xs"
                                 icon="lucide:check"
                                 variant="soft"
                              />
                              <div class="flex items-center gap-2 ms-auto">
                                 <UButton
                                    icon="lucide:edit"
                                    variant="ghost"
                                    color="neutral"
                                    size="sm"
                                    @click="openForm(address)"
                                 />
                                 <UButton
                                    icon="lucide:trash"
                                    variant="ghost"
                                    color="error"
                                    size="sm"
                                    :disabled="address.is_default"
                                    @click="confirmDelete(address)"
                                 />
                              </div>
                           </div>
                           <p class="text-muted">
                              {{ getAddressLine2(address) }}
                           </p>
                        </div>

                        <div
                           v-if="!address.is_default"
                           class="mt-2"
                        >
                           <UButton
                              label="Atur Sebagai Utama"
                              variant="soft"
                              color="neutral"
                              size="xs"
                              @click="setDefaultAddress(address)"
                           />
                        </div>
                     </div>
                  </div>
               </UCard>
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
