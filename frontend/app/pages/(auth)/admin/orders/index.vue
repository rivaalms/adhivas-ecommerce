<script setup lang="ts">
import type { DropdownMenuItem, SelectMenuItem, TableColumn } from "@nuxt/ui"
import { UBadge, UButton, UDropdownMenu } from "#components"

const query = reactive({
   page: 1,
   per_page: 10,
   user_id: undefined as number | undefined,
   status: undefined as string | undefined,
   start_date: undefined as string | undefined,
   end_date: undefined as string | undefined,
   sort_dir: undefined as string | undefined,
})

const sorting = ref<ColumnSorting[]>([])

const userSearch = shallowRef("")
const { data: usersData, refresh: fetchUsers } = useApi("/api/users", {
   method: "get",
   query: {
      search: userSearch,
      per_page: 10,
      role: "customer",
   },
   transform: (res) =>
      res.data.data.map(
         (u) =>
            ({
               ...u,
               label: u.full_name,
               description: u.email,
            }) satisfies SelectMenuItem
      ),
   default: () => [],
})
const users = computed(() => usersData.value || [])

watchDebounced(userSearch, () => fetchUsers(), {
   debounce: 800,
   maxWait: 1000,
})

const { data, pending, refresh } = useApi(`/api/orders`, {
   method: "get",
   query,
   transform: (res) => res.data,
})

watch(
   () => sorting.value,
   (newSorting) => {
      if (newSorting && newSorting.length > 0) {
         query.sort_dir = newSorting[0]!.desc ? "desc" : "asc"
      } else {
         query.sort_dir = undefined
      }
      query.page = 1
      refresh()
   },
   { deep: true }
)

watchExcludable(
   () => query,
   (value) => {
      if (value.page > 1) {
         query.page = 1
         return
      }
      refresh()
   },
   {
      exclude: ["sort_dir"],
   }
)

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

const statusOptions = [
   { label: "Pending", value: "pending" },
   { label: "Diproses", value: "processing" },
   { label: "Dikirim", value: "shipped" },
   { label: "Sampai", value: "delivered" },
   { label: "Selesai", value: "completed" },
   { label: "Dibatalkan", value: "cancelled" },
]

const columns: TableColumn<OrderDTO>[] = [
   {
      accessorKey: "id",
      header: "ID Order",
      cell: ({ row }) => `#${row.original.id}`,
   },
   {
      accessorKey: "user.full_name",
      header: "Pelanggan",
      cell: ({ row }) =>
         row.original.user?.full_name || row.original.user?.email || "-",
   },
   {
      accessorKey: "order_date",
      header: ({ column }) => {
         const isSorted = column.getIsSorted()
         return h(UButton, {
            color: "neutral",
            variant: "ghost",
            label: "Tanggal Order",
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
      cell: ({ row }) => $formatDate(row.original.order_date),
   },
   {
      accessorKey: "total_amount",
      header: "Total Tagihan",
      cell: ({ row }) =>
         $formatNumber(row.original.total_amount, {
            style: "currency",
            currency: "IDR",
            currencyDisplay: "narrowSymbol",
         }),
   },
   {
      accessorKey: "status",
      header: "Status",
      cell: ({ row }) => {
         const badge = getStatusBadge(row.original.status)
         return h(
            UBadge,
            {
               color: badge.color,
               variant: "subtle",
            },
            () => badge.label
         )
      },
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
                           navigateTo(`/admin/orders/${row.original.id}`),
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
</script>

<template>
   <UPage>
      <UPageHeader
         title="Daftar Order"
         description="Kelola dan pantau pesanan pelanggan"
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
                  <USelectMenu
                     v-model="query.user_id"
                     v-model:search-term="userSearch"
                     :items="users"
                     value-key="id"
                     label-key="label"
                     placeholder="Pilih Pelanggan"
                     ignore-filter
                     clear
                     class="w-48"
                  />
                  <USelectMenu
                     v-model.optional="query.status"
                     clear
                     value-key="value"
                     placeholder="Pilih Status"
                     :items="statusOptions"
                     class="w-40"
                  />
                  <div class="flex items-center gap-1.5">
                     <UInput
                        v-model="query.start_date"
                        type="date"
                        class="w-40"
                        :max="query.end_date"
                     />
                     <span class="text-muted text-xs">s.d.</span>
                     <UInput
                        v-model="query.end_date"
                        type="date"
                        class="w-40"
                        :min="query.start_date"
                     />
                  </div>
               </div>
            </div>
         </template>
      </DataTable>
   </UPage>
</template>
