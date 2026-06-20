export default defineEventHandler(async (event) => {
   const $api = $serverApi(event)

   try {
      // Call backend API to invalidate token
      await $api("/auth/logout", {
         method: "POST",
      })
   } catch {
      // Silently ignore logout errors (e.g., if the token is already expired)
   } finally {
      // Always delete the cookie on the client side
      deleteCookie(event, "auth-token", {
         path: "/",
      })
   }

   return {
      status: "success",
      message: "Successfully logged out",
      data: null,
      toJSON() {
         return this as ApiResponse<null>
      },
   }
})
