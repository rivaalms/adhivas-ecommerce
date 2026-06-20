type CartData = Omit<CartDTO, "items"> & {
   items: (CartItemDTO & { selected: boolean })[]
}

export const useCartStore = defineStore("cart-store", () => {
   const cart = ref<CartData>()

   async function fetchCart() {
      try {
         const response = await $api(`/api/cart`, {
            method: "get",
         })

         cart.value = {
            ...response.data,
            items: response.data.items.map((item) => ({
               ...item,
               selected: false,
            })),
         }
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

         cart.value = {
            ...response.data,
            items: response.data.items.map((item) => ({
               ...item,
               selected: false,
            })),
         }

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

         cart.value = {
            ...response.data,
            items: response.data.items.map((item) => ({
               ...item,
               selected: false,
            })),
         }

         return response
      } catch (e) {
         $notifyError(e)
      }
   }

   async function checkoutItems(
      payload: InferFnSchema<typeof $orderSchema, "store">
   ) {
      const response = await $api(`/api/orders`, {
         method: "post",
         body: payload,
      })

      fetchCart()
      return response
   }

   return {
      cart,
      fetchCart,
      addCartItem,
      removeCartItem,
      checkoutItems,
   }
})
