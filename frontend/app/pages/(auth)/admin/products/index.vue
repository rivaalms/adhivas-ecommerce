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
   search: undefined as string | undefined,
   category_id: undefined as number | undefined,
   sort_by: undefined as string | undefined,
   sort_dir: undefined as string | undefined,
})

const sorting = ref<ColumnSorting[]>([])

const categorySearch = shallowRef("")
const { data: categories, refresh: fetchCategories } = useApi(
   "/api/categories",
   {
      method: "get",
      query: {
         search: categorySearch,
         per_page: 10,
      },
      transform: (res) => res.data.data,
      default: () => [] as CategoryDTO[],
   }
)

watchDebounced(categorySearch, () => fetchCategories(), {
   debounce: 800,
   maxWait: 1000,
})

const { data, pending, refresh } = useApi(`/api/products`, {
   method: "get",
   query,
   transform: (res) => res.data,
})

watch(
   () => sorting.value,
   (newSorting) => {
      if (newSorting && newSorting.length > 0) {
         query.sort_by = newSorting[0]!.id
         query.sort_dir = newSorting[0]!.desc ? "desc" : "asc"
      } else {
         query.sort_by = undefined
         query.sort_dir = undefined
      }
      query.page = 1
   },
   { deep: true }
)

watchDebounced(
   () => query.search,
   () => {
      query.page = 1
      refresh()
   },
   {
      debounce: 800,
      maxWait: 1000,
   }
)

watch(
   () => query.category_id,
   () => {
      query.page = 1
      refresh()
   }
)

watchExcludable(
   () => query,
   () => refresh(),
   {
      exclude: ["search", "category_id"],
   }
)

const columns: TableColumn<ProductDTO>[] = [
   {
      accessorKey: "name",
      header: "Nama Produk",
   },
   {
      accessorKey: "price",
      header: ({ column }) => {
         const isSorted = column.getIsSorted()
         return h(UButton, {
            color: "neutral",
            variant: "ghost",
            label: "Harga",
            icon: isSorted
               ? isSorted === "desc"
                  ? "lucide:arrow-down-narrow-wide"
                  : "lucide:arrow-up-narrow-wide"
               : "lucide:arrow-up-down",
            onClick: () =>
               column.getIsSorted() == "desc"
                  ? column.clearSorting()
                  : column.toggleSorting(column.getIsSorted() === "asc"),
         })
      },
      cell: ({ row }) =>
         $formatNumber(row.original.price, {
            style: "currency",
            currency: "IDR",
            currencyDisplay: "narrowSymbol",
         }),
   },
   {
      accessorKey: "stock_quantity",
      header: "Stok",
   },
   {
      accessorKey: "created_at",
      header: ({ column }) => {
         const isSorted = column.getIsSorted()
         return h(UButton, {
            color: "neutral",
            variant: "ghost",
            label: "Dibuat Pada",
            icon: isSorted
               ? isSorted === "desc"
                  ? "lucide:arrow-down-narrow-wide"
                  : "lucide:arrow-up-narrow-wide"
               : "lucide:arrow-up-down",
            onClick: () =>
               column.getIsSorted() == "desc"
                  ? column.clearSorting()
                  : column.toggleSorting(column.getIsSorted() === "asc"),
         })
      },
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
         v-model:sorting="sorting"
         :data="data?.data"
         :columns="columns"
         :total="data?.meta.total"
         :loading="pending"
      >
         <template #header>
            <div
               class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full"
            >
               <div class="flex flex-wrap items-center gap-2.5">
                  <UInput
                     v-model="query.search"
                     placeholder="Cari produk..."
                     icon="lucide:search"
                     class="w-64"
                  />
                  <USelectMenu
                     v-model="query.category_id"
                     v-model:search-term="categorySearch"
                     :items="categories"
                     value-key="id"
                     label-key="name"
                     placeholder="Pilih Kategori"
                     ignore-filter
                     clear
                     class="w-48"
                  />
               </div>
               <UButton
                  label="Produk Baru"
                  icon="lucide:plus"
                  class="shrink-0 sm:ms-auto"
                  @click="() => openForm()"
               />
            </div>
         </template>
      </DataTable>
   </UPage>
</template>
