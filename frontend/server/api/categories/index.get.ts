export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   // Fetch paginated categories from the backend
   const response = await $api<ApiResponse<Pagination<CategoryDTO>>>(
      "/categories",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<Pagination<CategoryDTO>>
      },
   }
})
