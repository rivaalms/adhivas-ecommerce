import { z } from "zod"

/**
 * Order validation schemas
 */
export function $orderSchema() {
   const store = z.object({
      user_address_id: z.coerce.number().int().min(1, "Alamat wajib dipilih"),
      items: z
         .array(
            z.object({
               product_id: z.coerce
                  .number()
                  .int()
                  .min(1, "Produk wajib dipilih"),
               quantity: z.coerce.number().int().min(1, "Kuantitas minimal 1"),
            })
         )
         .min(1, "Keranjang belanja kosong"),
   })

   const update = z.object({
      user_address_id: z.coerce.number().int().min(1, "Alamat wajib dipilih"),
   })

   const updateStatus = z.object({
      status: z.string().nonempty("Status wajib diisi"),
   })

   return { store, update, updateStatus }
}
