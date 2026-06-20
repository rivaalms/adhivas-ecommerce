export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const id = getRouterParam(event, "id")

   // Remove item from cart in backend
   const response = await $api<ApiResponse<CartDTO>>(`/cart/items/${id}`, {
      method: "DELETE",
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<CartDTO>
      },
   }
})
