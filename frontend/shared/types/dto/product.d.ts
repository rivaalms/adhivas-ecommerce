/**
 * Product DTO interface
 */
interface ProductDTO {
   id: number
   name: string
   description: string
   price: number
   stock_quantity: number
   image_url: string | null
   categories?: CategoryDTO[]
   created_at: string
   updated_at: string
}
