type DashboardMeta = {
   start_date: string | null
   end_date: string | null
}

interface DashboardStatsDTO {
   total_revenue: number
   total_orders: number
   avg_order_value: number
   meta: DashboardMeta
}

interface DashboardOrderStatusBreakdownDTO {
   order_status_breakdown: {
      status: OrderStatus
      count: number
      total_revenue: number
   }[]
   meta: DashboardMeta
}

interface DashboardSalesTrendDTO {
   sales_trend: {
      date: string
      revenue: number
      order_count: number
   }[]
   meta: DashboardMeta
}
