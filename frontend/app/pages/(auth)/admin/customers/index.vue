<script setup lang="ts">
import type { DropdownMenuItem, TableColumn } from "@nuxt/ui"
import { DetailCustomer, UButton, UDropdownMenu } from "#components"

const appStore = useAppStore()

const query = reactive({
   page: 1,
   per_page: 10,
   search: undefined as string | undefined,
   role: "customer",
})

const { data, pending, refresh } = useApi("/api/users", {
   method: "get",
   query,
   transform: (res) => res.data,
})

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

watchDeep(
   () => [query.page, query.per_page],
   () => refresh()
)

const columns: TableColumn<UserDTO>[] = [
   {
      accessorKey: "full_name",
      header: "Nama Lengkap",
   },
   {
      accessorKey: "email",
      header: "Email",
   },
   {
      accessorKey: "created_at",
      header: "Tanggal Bergabung",
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
                        onSelect: () => showDetail(row.original),
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

function showDetail(user: UserDTO) {
   appStore.openModal(
      "Detail Customer",
      h(DetailCustomer, {
         data: user,
      }),
      {
         width: "md",
      }
   )
}
</script>

<template>
   <UPage>
      <UPageHeader
         title="Daftar Customer"
         description="Kelola dan lihat data customer terdaftar"
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
            <div
               class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full"
            >
               <div class="flex items-center gap-2.5">
                  <UInput
                     v-model="query.search"
                     placeholder="Cari customer..."
                     icon="lucide:search"
                     class="w-64"
                  />
               </div>
            </div>
         </template>
      </DataTable>
   </UPage>
</template>
