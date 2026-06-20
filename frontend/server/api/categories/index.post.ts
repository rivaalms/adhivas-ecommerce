export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   // Proxy post category to the backend
   const response = await $api<ApiResponse<CategoryDTO>>("/categories", {
      method: "POST",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CategoryDTO>
      },
   }
})
