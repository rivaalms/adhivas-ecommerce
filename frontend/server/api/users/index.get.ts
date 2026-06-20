export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const query = getQuery(event)

   // Fetch paginated users from the backend
   const response = await $api<ApiResponse<Pagination<UserDTO>>>("/users", {
      method: "GET",
      query,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<Pagination<UserDTO>>
      },
   }
})
