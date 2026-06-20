export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Fetch a category by its ID from the backend
   const response = await $api<ApiResponse<CategoryDTO>>(`/categories/${id}`, {
      method: "GET",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CategoryDTO>
      },
   }
})
