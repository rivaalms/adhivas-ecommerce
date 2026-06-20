export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   // Proxy user addresses fetching from backend
   const response = await $api<ApiResponse<Pagination<UserAddressDTO>>>(
      "/addresses",
      {
         method: "GET",
         query,
      }
   )

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<Pagination<UserAddressDTO>>
      },
   }
})
