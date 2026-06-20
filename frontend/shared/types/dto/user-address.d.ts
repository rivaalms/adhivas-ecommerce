/**
 * UserAddress DTO interface
 */
interface UserAddressDTO {
   id: number
   user_id: number
   address_line: string
   subdistrict_id: string
   subdistrict_name: string
   district_id: string
   district_name: string
   regency_id: string
   regency_name: string
   province_id: string
   province_name: string
   postal_code: string
   is_default: boolean
   created_at: string
   updated_at: string
}
