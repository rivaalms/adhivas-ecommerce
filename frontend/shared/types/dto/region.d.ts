interface RawRegionDTO {
   data: {
      code: string
      name: string
   }[]
   meta: {
      administrative_area_level: number
      updated_at: string
   }
}

type RegionDTO = RawRegionDTO["data"][number]

/**
 * Province DTO interface from wilayah.id
 */
type ProvinceDTO = RegionDTO

/**
 * Regency DTO interface from wilayah.id
 */
type RegencyDTO = RegionDTO

/**
 * District DTO interface from wilayah.id
 */
type DistrictDTO = RegionDTO

/**
 * Village (Subdistrict) DTO interface from wilayah.id
 */
type VillageDTO = RegionDTO
