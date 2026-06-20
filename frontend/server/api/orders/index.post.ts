export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   // Store order in backend
   const response = await $api<ApiResponse<OrderDTO>>("/orders", {
      method: "POST",
      body,
   })

   return {
      ...response,
      toJSON() {
         return this as ApiResponse<OrderDTO>
      },
   }
})
