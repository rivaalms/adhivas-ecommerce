export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   // Add or update cart item in backend
   const response = await $api<ApiResponse<CartDTO>>("/cart", {
      method: "POST",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CartDTO>
      },
   }
})
