<script setup lang="ts">
import type { DropdownMenuItem, TableColumn } from "@nuxt/ui"
import { UBadge, UButton, UDropdownMenu } from "#components"

const query = reactive({
   page: 1,
   per_page: 10,
})

const { data, pending, refresh } = useApi(`/api/orders`, {
   method: "get",
   query,
   transform: (res) => res.data,
})

watchDeep(
   () => query,
   () => refresh()
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

const columns: TableColumn<OrderDTO>[] = [
   {
      accessorKey: "id",
      header: "ID Order",
      cell: ({ row }) => `#${row.original.id}`,
   },
   {
      accessorKey: "order_date",
      header: "Tanggal Order",
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
                           navigateTo(`/orders/${row.original.id}`),
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
      <UPageBody>
         <UPageHeader
            title="Pesanan Saya"
            description="Riwayat belanja dan status pengiriman pesanan Anda"
         />
         <UCard>
            <DataTable
               v-model:page="query.page"
               v-model:per-page="query.per_page"
               :data="data?.data"
               :columns="columns"
               :total="data?.meta.total"
               :loading="pending"
            />
         </UCard>
      </UPageBody>
   </UPage>
</template>
