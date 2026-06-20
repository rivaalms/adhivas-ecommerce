export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Fetch a single order from backend
   const response = await $api<ApiResponse<OrderDTO>>(`/orders/${id}`, {
      method: "GET",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<OrderDTO>
      },
   }
})
