export default defineEventHandler(async (event) => {
   const query = getQuery(event)
   const regencyId = query.regency_id

   if (!regencyId) {
      throw createError({
         statusCode: 400,
         message: "Regency ID is required",
      })
   }

   const response = await $fetch<RawRegionDTO>(
      `https://wilayah.id/api/districts/${regencyId}.json`
   )

   return {
      status: "success" as const,
      message: "Districts retrieved successfully",
      data: response.data,
      toJSON() {
         return this as ApiResponse<DistrictDTO[]>
      },
   }
})
