import { z } from "zod"

/**
 * Cart validation schemas
 */
export function $cartSchema() {
   const store = z.object({
      product_id: z.coerce.number().int().min(1, "Produk wajib dipilih"),
      quantity: z.coerce.number().int().min(1, "Jumlah minimal 1"),
   })

   return { store }
}
