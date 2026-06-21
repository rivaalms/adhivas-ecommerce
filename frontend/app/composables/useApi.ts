import type { H3Error } from "#build/types/nitro-imports"

export default createUseFetch((opts) => {
   return {
      ...opts,
      watch: opts.watch ?? false,
      $fetch: $api,
      onResponseError({ response }) {
         const err = response._data as H3Error

         if (err.statusCode == 401) {
            const authStore = useAuthStore()
            authStore.$reset()
            navigateTo("/login")
            return
         }

         $notifyError(response._data)
      },
   }
})
