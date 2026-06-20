export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   // Fetch paginated orders from backend
   const response = await $api<ApiResponse<Pagination<OrderDTO>>>("/orders", {
      method: "GET",
      query,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<Pagination<OrderDTO>>
      },
   }
})
