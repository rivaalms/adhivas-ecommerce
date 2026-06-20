export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Proxy delete product request to backend
   const response = await $api<ApiResponse<null>>(`/products/${id}`, {
      method: "DELETE",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<null>
      },
   }
})
