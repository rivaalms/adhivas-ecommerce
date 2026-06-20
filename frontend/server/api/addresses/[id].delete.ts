export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Proxy delete user address in backend
   const response = await $api<ApiResponse<null>>(`/addresses/${id}`, {
      method: "DELETE",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<null>
      },
   }
})
