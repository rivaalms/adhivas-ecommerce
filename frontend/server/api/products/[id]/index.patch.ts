export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")
   const body = await readBody(event)

   // Proxy update product to backend
   const response = await $api<ApiResponse<ProductDTO>>(`/products/${id}`, {
      method: "PATCH",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<ProductDTO>
      },
   }
})
