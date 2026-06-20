export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")
   const body = await readBody(event)

   // Update order status in backend
   const response = await $api<ApiResponse<OrderDTO>>(`/orders/${id}/status`, {
      method: "PATCH",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<OrderDTO>
      },
   }
})
