export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   const response = await $api<ApiResponse<DashboardStatsDTO>>(
      "/dashboard/stats",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<DashboardStatsDTO>
      },
   }
})
