export const useCartStore = defineStore("cart-store", () => {
   const cart = ref<CartDTO>()

   async function fetchCart() {
      try {
         const response = await $api(`/api/cart`, {
            method: "get",
         })

         cart.value = response.data
      } catch {
         cart.value = undefined
      }
   }

   async function addCartItem(
      payload: InferFnSchema<typeof $cartSchema, "store">
   ) {
      try {
         const response = await $api("/api/cart", {
            method: "post",
            body: payload,
         })

         cart.value = response.data

         return response
      } catch (e) {
         $notifyError(e)
      }
   }

   async function removeCartItem(productId: number) {
      try {
         const response = await $api(`/api/cart/items/${productId}`, {
            method: "delete",
            body: {
               product_id: productId,
            },
         })

         cart.value = response.data

         return response
      } catch (e) {
         $notifyError(e)
      }
   }

   return {
      cart,
      fetchCart,
      addCartItem,
      removeCartItem,
   }
})
