<script setup lang="ts">
import {
   VisXYContainer,
   VisStackedBar,
   VisAxis,
   VisSingleContainer,
   VisDonut,
} from "@unovis/vue"

const authStore = useAuthStore()

// Reactive filter state (start_date, end_date)
const filter = reactive({
   start_date: undefined as string | undefined,
   end_date: undefined as string | undefined,
})

// Query endpoints using Nuxt useApi utility
const { data: statsData, pending: statsPending } = useApi(
   "/api/dashboard/stats",
   {
      query: filter,
      watch: [() => filter.start_date, () => filter.end_date],
      transform: (res) => res.data,
   }
)

const { data: breakdownData, pending: breakdownPending } = useApi(
   "/api/dashboard/status-breakdown",
   {
      query: filter,
      watch: [() => filter.start_date, () => filter.end_date],
      transform: (res) => res.data,
   }
)

const { data: salesTrendData, pending: salesTrendPending } = useApi(
   "/api/dashboard/sales-trend",
   {
      query: filter,
      watch: [() => filter.start_date, () => filter.end_date],
      transform: (res) => res.data,
   }
)

// Loading indicator combining all pending states
const isLoading = computed(
   () => statsPending.value || breakdownPending.value || salesTrendPending.value
)

// Helpers for formatted display
const formatCurrency = (val: number | undefined) => {
   return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
   }).format(val ?? 0)
}

const formatDate = (dateStr: string | null | undefined) => {
   if (!dateStr) return "-"
   return new Date(dateStr).toLocaleDateString("id-ID", {
      year: "numeric",
      month: "short",
      day: "numeric",
   })
}

// Order Status Breakdown accessors and chart configurations (Line Chart)
const breakdownList = computed(
   () => breakdownData.value?.order_status_breakdown || []
)
const lineX = (_d: any, i: number) => i
const lineY = (d: any) => d.count
const tickValues = computed(() => breakdownList.value.map((_, i) => i))
const xTickFormat = (i: number) => {
   const status = breakdownList.value[i]?.status
   return status ? status.charAt(0).toUpperCase() + status.slice(1) : ""
}

// Sales Trend accessors and chart configurations (Donut Chart)
// Filter out dates with 0 revenue to keep the donut chart visually clean and informative
const activeSalesTrend = computed(() => {
   return salesTrendData.value?.sales_trend.filter((d) => d.revenue > 0) || []
})
const donutValue = (d: any) => d.revenue
const donutColor = (_d: any, i: number) => {
   const palette = [
      "var(--color-lime-500)",
      "var(--ui-primary)",
      "var(--ui-success)",
      "var(--ui-info)",
      "var(--ui-warning)",
      "var(--ui-error)",
   ]
   return palette[i % palette.length]
}

// Compute total sales in trend for proportion percentages
const totalSalesInTrend = computed(() => {
   return activeSalesTrend.value.reduce((acc, curr) => acc + curr.revenue, 0)
})
</script>

<template>
   <UPage>
      <UPageHeader :title="`Selamat datang, ${authStore.user?.full_name}.`">
         <template #links>
            <UFormField label="Tanggal Mulai">
               <UInput
                  v-model="filter.start_date"
                  type="date"
                  class="w-full"
                  icon="lucide:calendar"
               />
            </UFormField>
            <UFormField label="Tanggal Selesai">
               <UInput
                  v-model="filter.end_date"
                  type="date"
                  class="w-full"
                  icon="lucide:calendar"
               />
            </UFormField>
         </template>
      </UPageHeader>
      <div class="flex flex-col gap-6">
         <!-- Loading State Overlay -->
         <div
            v-if="isLoading"
            class="flex items-center justify-center min-h-[300px] rounded-2xl bg-slate-500/5 backdrop-blur-xs"
         >
            <div class="flex flex-col items-center gap-2">
               <UIcon
                  name="lucide:loader-2"
                  class="animate-spin text-primary text-3xl"
               />
               <span class="text-sm text-muted">Memuat data dashboard...</span>
            </div>
         </div>

         <template v-else>
            <!-- Summary Metric Cards (using only completed orders) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
               <!-- Total Revenue -->
               <UCard class="shadow-sm relative overflow-hidden group">
                  <div class="flex items-center justify-between">
                     <div>
                        <span
                           class="text-xs font-semibold text-muted uppercase tracking-wider"
                        >
                           Total Pendapatan
                        </span>
                        <h2 class="text-2xl font-bold mt-2 text-highlighted">
                           {{ formatCurrency(statsData?.total_revenue) }}
                        </h2>
                     </div>
                     <div
                        class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 group-hover:scale-110 transition duration-300 size-12"
                     >
                        <UIcon
                           name="lucide:banknote"
                           class="text-2xl"
                        />
                     </div>
                  </div>
               </UCard>

               <!-- Total Orders -->
               <UCard class="shadow-sm relative overflow-hidden group">
                  <div class="flex items-center justify-between">
                     <div>
                        <span
                           class="text-xs font-semibold text-muted uppercase tracking-wider"
                        >
                           Total Pesanan
                        </span>
                        <h2 class="text-2xl font-bold mt-2 text-highlighted">
                           {{ statsData?.total_orders }}
                        </h2>
                     </div>
                     <div
                        class="p-3 bg-indigo-500/10 rounded-xl text-indigo-500 group-hover:scale-110 transition duration-300 size-12"
                     >
                        <UIcon
                           name="lucide:package-check"
                           class="text-2xl"
                        />
                     </div>
                  </div>
               </UCard>

               <!-- Average Order Value -->
               <UCard class="shadow-sm relative overflow-hidden group">
                  <div class="flex items-center justify-between">
                     <div>
                        <span
                           class="text-xs font-semibold text-muted uppercase tracking-wider"
                        >
                           Rata-rata Nilai Order
                        </span>
                        <h2 class="text-2xl font-bold mt-2 text-highlighted">
                           {{ formatCurrency(statsData?.avg_order_value) }}
                        </h2>
                     </div>
                     <div
                        class="p-3 bg-violet-500/10 rounded-xl text-violet-500 group-hover:scale-110 transition duration-300 size-12"
                     >
                        <UIcon
                           name="lucide:shopping-bag"
                           class="text-2xl"
                        />
                     </div>
                  </div>
               </UCard>
            </div>

            <!-- Charts section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
               <!-- Order Status Breakdown (Line Chart) -->
               <UCard
                  title="Distribusi Status Pesanan (Line Chart)"
                  class="lg:col-span-2"
               >
                  <div class="flex flex-col gap-4">
                     <!-- Unovis Line Chart container -->
                     <div class="h-64 w-full flex items-center justify-center">
                        <VisXYContainer
                           v-if="breakdownList.length"
                           :data="breakdownList"
                           class="w-full h-full"
                        >
                           <VisStackedBar
                              :x="lineX"
                              :y="lineY"
                              color="var(--ui-primary)"
                           />
                           <VisAxis
                              type="x"
                              :tick-values="tickValues"
                              :tick-format="xTickFormat"
                           />
                           <VisAxis type="y" />
                        </VisXYContainer>
                        <div
                           v-else
                           class="text-sm text-muted"
                        >
                           Tidak ada data status pesanan pada periode ini.
                        </div>
                     </div>

                     <!-- Legend/Details -->
                     <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <UPageCard
                           v-for="item in breakdownList"
                           :key="item.status"
                           :title="item.status"
                           :description="`${item.count} order`"
                           variant="soft"
                           :ui="{
                              container: 'p-3 sm:p-3',
                              title: 'text-xs capitalize uppercase text-muted tracking-wide',
                              description: 'text-default mt-0 text-sm',
                           }"
                        >
                           <template #footer>
                              <span class="text-sm font-medium text-success">
                                 {{ formatCurrency(item.total_revenue) }}
                              </span>
                           </template>
                        </UPageCard>
                     </div>
                  </div>
               </UCard>

               <!-- Sales Trend (Donut Chart) -->
               <UCard title="Tren Penjualan Harian (Donut Chart)">
                  <div class="flex flex-col items-center gap-6">
                     <!-- Unovis Donut Chart container -->
                     <div
                        class="shrink-0 flex items-center justify-center relative"
                     >
                        <VisSingleContainer
                           v-if="activeSalesTrend.length"
                           :data="activeSalesTrend"
                           class="w-full h-full"
                        >
                           <VisDonut
                              :value="donutValue"
                              :color="donutColor"
                              :arc-width="40"
                              :pad-angle="0.03"
                           />
                        </VisSingleContainer>

                        <div
                           v-if="activeSalesTrend.length"
                           class="absolute flex flex-col items-center justify-center text-center inset-0 pointer-events-none"
                        >
                           <span
                              class="text-xs uppercase font-bold text-muted tracking-wider"
                           >
                              Total Sales
                           </span>
                           <span
                              class="text-sm font-extrabold text-highlighted"
                           >
                              {{ formatCurrency(totalSalesInTrend) }}
                           </span>
                        </div>

                        <div
                           v-else
                           class="text-sm text-muted"
                        >
                           Tidak ada transaksi completed pada periode ini.
                        </div>
                     </div>

                     <!-- Details Legend Table -->
                     <div class="flex-1 w-full max-h-56 overflow-y-auto pr-1">
                        <table class="w-full text-xs">
                           <thead>
                              <tr class="border-b border-default text-muted">
                                 <th class="text-left py-2 font-semibold">
                                    Tanggal
                                 </th>
                                 <th class="text-right py-2 font-semibold">
                                    Pendapatan
                                 </th>
                                 <th class="text-right py-2 font-semibold">
                                    Rasio
                                 </th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr
                                 v-for="(item, index) in activeSalesTrend"
                                 :key="item.date"
                                 class="border-b border-default transition"
                              >
                                 <td
                                    class="py-2 text-toned flex items-center gap-2"
                                 >
                                    <span
                                       class="h-2 w-2 rounded-full inline-block"
                                       :style="{
                                          backgroundColor: donutColor(
                                             item,
                                             index
                                          ),
                                       }"
                                    />
                                    {{ formatDate(item.date) }}
                                 </td>
                                 <td
                                    class="py-2 text-right font-bold text-highlighted"
                                 >
                                    {{ formatCurrency(item.revenue) }}
                                 </td>
                                 <td
                                    class="py-2 text-right text-muted font-semibold"
                                 >
                                    {{
                                       totalSalesInTrend > 0
                                          ? (
                                               (item.revenue /
                                                  totalSalesInTrend) *
                                               100
                                            ).toFixed(1)
                                          : 0
                                    }}%
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </UCard>
            </div>
         </template>
      </div>
   </UPage>
</template>

<style scoped>
.unovis-xy-container {
   --vis-axis-grid-color: var(--ui-border);
   --vis-axis-tick-color: var(--ui-border);
   --vis-axis-tick-label-color: var(--ui-text-dimmed);

   --vis-tooltip-background-color: var(--ui-bg);
   --vis-tooltip-border-color: var(--ui-border);
   --vis-tooltip-text-color: var(--ui-text-highlighted);
}
</style>
