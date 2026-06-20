<script setup lang="ts">
import type { DropdownMenuItem, TableColumn } from "@nuxt/ui"
import {
   ConfirmationPrompt,
   FormProduct,
   UButton,
   UDropdownMenu,
} from "#components"

const appStore = useAppStore()

const query = reactive({
   page: 1,
   per_page: 10,
})

const { data, pending, refresh } = useApi(`/api/products`, {
   method: "get",
   query,
   transform: (res) => res.data,
})

watchDeep(
   () => query,
   () => refresh()
)

const columns: TableColumn<ProductDTO>[] = [
   {
      accessorKey: "name",
      header: "Nama Produk",
   },
   {
      accessorKey: "price",
      header: "Harga",
      cell: ({ row }) =>
         $formatNumber(row.original.price, {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
         }),
   },
   {
      accessorKey: "stock_quantity",
      header: "Stok",
   },
   {
      accessorKey: "created_at",
      header: "Dibuat Pada",
      cell: ({ row }) => $formatDate(row.original.created_at),
   },
   {
      id: "actions",
      meta: {
         class: {
            td: "text-end",
         },
      },
      cell: ({ row }) =>
         h(
            UDropdownMenu,
            {
               items: [
                  [
                     {
                        label: "Detail",
                        icon: "lucide:eye",
                        onSelect: () =>
                           navigateTo(`/admin/products/${row.original.id}`),
                     },
                     {
                        label: "Edit",
                        icon: "lucide:edit",
                        onSelect: () => openForm(row.original),
                     },
                     {
                        label: "Hapus",
                        icon: "lucide:trash",
                        color: "error",
                        onSelect: () =>
                           appStore.openModal(
                              "Konfirmasi Hapus",
                              h(ConfirmationPrompt, {
                                 prompt: `Apakah kamu yakin ingin menghapus produk ${row.original.name}?`,
                                 icon: "lucide:trash",
                                 positiveButtonProps: {
                                    color: "error",
                                    icon: "lucide:trash",
                                    label: "Hapus",
                                 },
                                 onPositive: async () => {
                                    try {
                                       const response = await $api(
                                          `/api/products/${row.original.id}`,
                                          {
                                             method: "delete",
                                          }
                                       )
                                       appStore.notify({
                                          title: "Berhasil",
                                          description: response.message,
                                          color: "info",
                                       })
                                       appStore.closeModal()
                                       refresh()
                                    } catch (e) {
                                       $notifyError(e)
                                    }
                                 },
                              })
                           ),
                     },
                  ],
               ] satisfies DropdownMenuItem[][],
            },
            [
               h(UButton, {
                  icon: "lucide:ellipsis-vertical",
                  variant: "ghost",
                  color: "neutral",
               }),
            ]
         ),
   },
]

const formLoading = shallowRef(false)
function openForm(data?: ProductDTO) {
   appStore.openModal(
      "Form Produk",
      h(FormProduct, {
         data,
         loading: formLoading,
         onSubmit: async (payload) => {
            try {
               const response = await $api(
                  data ? `/api/products/${data.id}` : `/api/products`,
                  {
                     method: data ? "patch" : "post",
                     body: payload,
                  }
               )
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
</script>

<template>
   <UPage>
      <UPageHeader
         title="Daftar Produk"
         description="Kelola katalog produk e-commerce"
      />
      <DataTable
         v-model:page="query.page"
         v-model:per-page="query.per_page"
         :data="data?.data"
         :columns="columns"
         :total="data?.meta.total"
         :loading="pending"
      >
         <template #header>
            <div class="flex items-center gap-2">
               <UButton
                  label="Produk Baru"
                  icon="lucide:plus"
                  class="ms-auto"
                  @click="() => openForm()"
               />
            </div>
         </template>
      </DataTable>
   </UPage>
</template>
