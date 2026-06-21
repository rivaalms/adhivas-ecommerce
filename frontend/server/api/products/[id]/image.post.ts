export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readFormData(event)
   const id = getRouterParam(event, "id")

   const response = await $api<ApiResponse<ProductDTO>>(
      `/products/${id}/upload`,
      {
         method: "POST",
         body,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<ProductDTO>
      },
   }
})
