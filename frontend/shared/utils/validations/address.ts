import { z } from "zod"

/**
 * Address validation schemas
 */
export function $addressSchema() {
   const create = z.object({
      address_line: z.string().nonempty("Alamat lengkap wajib diisi"),
      province_name: z.string().nonempty("Provinsi wajib diisi"),
      regency_name: z.string().nonempty("Kota/Kabupaten wajib diisi"),
      district_name: z.string().nonempty("Kecamatan wajib diisi"),
      subdistrict_name: z.string().nonempty("Kelurahan/Desa wajib diisi"),
      postal_code: z
         .string()
         .nonempty("Kode Pos wajib diisi")
         .max(10, "Kode Pos tidak valid"),
      is_default: z.boolean().default(false),
   })

   const update = z.object({
      address_line: z.string().nonempty("Alamat lengkap wajib diisi"),
      province_name: z.string().nonempty("Provinsi wajib diisi"),
      regency_name: z.string().nonempty("Kota/Kabupaten wajib diisi"),
      district_name: z.string().nonempty("Kecamatan wajib diisi"),
      subdistrict_name: z.string().nonempty("Kelurahan/Desa wajib diisi"),
      postal_code: z
         .string()
         .nonempty("Kode Pos wajib diisi")
         .max(10, "Kode Pos tidak valid"),
      is_default: z.boolean().default(false),
   })

   return { create, update }
}
