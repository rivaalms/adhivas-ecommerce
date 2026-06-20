export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")
   const body = await readBody(event)

   // Proxy update user address to backend
   const response = await $api<ApiResponse<UserAddressDTO>>(
      `/addresses/${id}`,
      {
         method: "PUT",
         body,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<UserAddressDTO>
      },
   }
})
