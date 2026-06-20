import { z } from "zod"

/**
 * Category validation schemas
 */
export function $categorySchema() {
   const create = z.object({
      name: z.string().nonempty(),
   })

   const update = z.object({
      name: z.string().nonempty(),
   })

   return { create, update }
}
