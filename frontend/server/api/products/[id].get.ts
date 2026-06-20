export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Proxy product show request to backend
   const response = await $api<ApiResponse<ProductDTO>>(`/products/${id}`, {
      method: "GET",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<ProductDTO>
      },
   }
})
