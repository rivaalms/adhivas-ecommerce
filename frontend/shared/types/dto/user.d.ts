/**
 * User Role type
 */
type UserRole = "admin" | "customer"

/**
 * User DTO interface
 */
interface UserDTO {
   id: number
   full_name: string
   email: string
   role: UserRole
   created_at: string
   updated_at: string
}
