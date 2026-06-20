export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Delete order in backend
   const response = await $api<ApiResponse<null>>(`/orders/${id}`, {
      method: "DELETE",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<null>
      },
   }
})
