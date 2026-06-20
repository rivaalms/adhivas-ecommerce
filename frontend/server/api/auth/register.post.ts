export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)
   const body = await readBody(event)

   const response = await $api<ApiResponse<RegisterResponseDTO>>(
      "/auth/register",
      {
         method: "POST",
         body,
      }
   )

   if (response.status === "success" && response.data?.access_token) {
      setCookie(event, "auth-token", response.data.access_token, {
         path: "/",
         httpOnly: true,
         sameSite: "lax",
         maxAge: response.data.expires_in,
      })
   }

   return {
      ...response,
      data: response.data?.user,
      toJSON() {
         return this as ApiResponse<UserDTO>
      },
   }
})
