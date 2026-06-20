interface LoginDTO {
   access_token: string
   token_type: string
   expires_in: number
   user: UserDTO
}

interface RegisterPayloadDTO {
   full_name: string
   email: string
   password: string
   password_confirmation: string
}

interface RegisterResponseDTO {
   user: UserDTO
   access_token: string
   token_type: string
   expires_in: number
}
