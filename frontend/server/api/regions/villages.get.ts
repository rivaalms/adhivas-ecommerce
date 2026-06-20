export default defineEventHandler(async (event) => {
   const query = getQuery(event)
   const districtId = query.district_id

   if (!districtId) {
      throw createError({
         statusCode: 400,
         message: "District ID is required",
      })
   }

   const response = await $fetch<RawRegionDTO>(
      `https://wilayah.id/api/villages/${districtId}.json`
   )

   return {
      status: "success" as const,
      message: "Villages retrieved successfully",
      data: response.data,
      toJSON() {
         return this as ApiResponse<VillageDTO[]>
      },
   }
})
