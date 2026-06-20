import { z } from "zod"

/**
 * Example auth schema
 */
export function $authSchema() {
   const login = z.object({
      email: z.string().email({ message: "Format email tidak valid" }),
      password: z.string().min(6, { message: "Password minimal 6 karakter" }),
   })

   const register = z
      .object({
         full_name: z.string().min(1, { message: "Nama lengkap wajib diisi" }),
         email: z.string().email({ message: "Format email tidak valid" }),
         password: z
            .string()
            .min(6, { message: "Password minimal 6 karakter" }),
         password_confirmation: z
            .string()
            .min(6, { message: "Konfirmasi password minimal 6 karakter" }),
      })
      .refine((data) => data.password === data.password_confirmation, {
         message: "Konfirmasi password tidak cocok",
         path: ["password_confirmation"],
      })

   return { login, register }
}
