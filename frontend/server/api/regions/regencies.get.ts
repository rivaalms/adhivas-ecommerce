export default defineEventHandler(async (event) => {
   const query = getQuery(event)
   const provinceId = query.province_id

   if (!provinceId) {
      throw createError({
         statusCode: 400,
         message: "Province ID is required",
      })
   }

   const response = await $fetch<RawRegionDTO>(
      `https://wilayah.id/api/regencies/${provinceId}.json`
   )

   return {
      status: "success" as const,
      message: "Regencies retrieved successfully",
      data: response.data,
      toJSON() {
         return this as ApiResponse<RegencyDTO[]>
      },
   }
})
