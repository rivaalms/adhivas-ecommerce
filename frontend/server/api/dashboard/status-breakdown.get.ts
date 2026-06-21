export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   const response = await $api<ApiResponse<DashboardOrderStatusBreakdownDTO>>(
      "/dashboard/status-breakdown",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<DashboardOrderStatusBreakdownDTO>
      },
   }
})
