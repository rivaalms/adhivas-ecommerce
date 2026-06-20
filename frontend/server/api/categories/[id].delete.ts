export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Proxy delete category to the backend
   const response = await $api<ApiResponse<null>>(`/categories/${id}`, {
      method: "DELETE",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<null>
      },
   }
})
