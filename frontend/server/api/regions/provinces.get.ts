export default defineEventHandler(async () => {
   const response = await $fetch<RawRegionDTO>(
      "https://wilayah.id/api/provinces.json"
   )

   return {
      status: "success" as const,
      message: "Provinces retrieved successfully",
      data: response.data,
      toJSON() {
         return this as ApiResponse<ProvinceDTO[]>
      },
   }
})
