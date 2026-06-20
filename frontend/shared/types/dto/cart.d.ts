/**
 * Cart Item DTO interface
 */
interface CartItemDTO {
   id: number
   cart_id: number
   product_id: number
   product?: ProductDTO
   quantity: number
   created_at: string
   updated_at: string
}

/**
 * Cart DTO interface
 */
interface CartDTO {
   id: number
   user_id: number
   items: CartItemDTO[]
}
