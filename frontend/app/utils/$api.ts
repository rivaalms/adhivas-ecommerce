import type { H3Error } from "#build/types/nitro-imports"

export default $fetch.create({
   // disable auto retry
   retry: false,

   async onResponseError({ response }) {
      const err = response._data as H3Error

      if (err.statusCode == 401) {
         const authStore = useAuthStore()
         authStore.$reset()
         navigateTo("/login")
         return
      }

      // re-throw error to let nuxt handle it
      throw createError({
         ...err,
         statusText: err.statusMessage,
      })
   },
})
