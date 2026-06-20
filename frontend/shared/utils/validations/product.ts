import { z } from "zod"

/**
 * Product validation schemas
 */
export function $productSchema() {
   const create = z.object({
      name: z.string().nonempty("Nama produk wajib diisi"),
      description: z.string().nonempty("Deskripsi wajib diisi"),
      price: z.coerce.number().min(0, "Harga minimal 0"),
      stock_quantity: z.coerce.number().int().min(0, "Stok minimal 0"),
      image_url: z
         .url("URL gambar tidak valid")
         .or(z.literal(""))
         .nullable()
         .optional(),
      categories: z
         .array(z.coerce.number())
         .min(1, "Pilih minimal satu kategori"),
   })

   const update = z.object({
      name: z.string().nonempty("Nama produk wajib diisi"),
      description: z.string().nonempty("Deskripsi wajib diisi"),
      price: z.coerce.number().min(0, "Harga minimal 0"),
      stock_quantity: z.coerce.number().int().min(0, "Stok minimal 0"),
      image_url: z
         .url("URL gambar tidak valid")
         .or(z.literal(""))
         .nullable()
         .optional(),
      categories: z
         .array(z.coerce.number())
         .min(1, "Pilih minimal satu kategori"),
   })

   return { create, update }
}
