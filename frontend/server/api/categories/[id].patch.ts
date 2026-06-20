export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")
   const body = await readBody(event)

   // Proxy patch category to the backend
   const response = await $api<ApiResponse<CategoryDTO>>(`/categories/${id}`, {
      method: "PATCH",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CategoryDTO>
      },
   }
})
