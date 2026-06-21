export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   const response = await $api<ApiResponse<DashboardSalesTrendDTO>>(
      "/dashboard/sales-trend",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<DashboardSalesTrendDTO>
      },
   }
})
