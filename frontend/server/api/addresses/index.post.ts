export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   // Proxy store user address to backend
   const response = await $api<ApiResponse<UserAddressDTO>>("/addresses", {
      method: "POST",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<UserAddressDTO>
      },
   }
})
