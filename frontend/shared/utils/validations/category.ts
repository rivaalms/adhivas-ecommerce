import { z } from "zod"

/**
 * Category validation schemas
 */
export function $categorySchema() {
   const create = z.object({
      name: z
         .string()
         .min(1, "Nama kategori wajib diisi")
         .max(255, "Nama kategori maksimal 255 karakter"),
   })

   const update = z.object({
      name: z
         .string()
         .min(1, "Nama kategori wajib diisi")
         .max(255, "Nama kategori maksimal 255 karakter"),
   })

   return { create, update }
}
