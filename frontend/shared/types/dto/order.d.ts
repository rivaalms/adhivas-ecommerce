type OrderStatus =
   | "pending"
   | "processing"
   | "shipped"
   | "delivered"
   | "completed"
   | "cancelled"

/**
 * OrderDetail DTO interface
 */
interface OrderDetailDTO {
   id: number
   order_id: number
   product_id: number
   product?: ProductDTO
   quantity: number
   price: number
   created_at: string
   updated_at: string
}

/**
 * Order DTO interface
 */
interface OrderDTO {
   id: number
   user_id: number
   user?: UserDTO
   user_address_id: number
   user_address?: UserAddressDTO
   order_date: string
   total_amount: number
   status: OrderStatus
   order_details?: OrderDetailDTO[]
   created_at: string
   updated_at: string
}
