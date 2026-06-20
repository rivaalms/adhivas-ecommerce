export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   // Proxy product list fetching from backend
   const response = await $api<ApiResponse<Pagination<ProductDTO>>>(
      "/products",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<Pagination<ProductDTO>>
      },
   }
})
