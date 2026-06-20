export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   // Proxy product storage to backend
   const response = await $api<ApiResponse<ProductDTO>>("/products", {
      method: "POST",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<ProductDTO>
      },
   }
})
