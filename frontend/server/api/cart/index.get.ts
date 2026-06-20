export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)

   // Fetch cart from backend
   const response = await $api<ApiResponse<CartDTO>>("/cart", {
      method: "GET",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CartDTO>
      },
   }
})
