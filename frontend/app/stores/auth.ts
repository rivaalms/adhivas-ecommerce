export const useAuthStore = defineStore(
   "auth-store",
   () => {
      const user = ref<UserDTO | null>(null)
      const isLoggedIn = computed(() => !!user.value)
      const getUserRole = computed(() => user.value && user.value.role)

      function setUser(val: UserDTO | null) {
         user.value = val
      }

      async function logout() {
         const response = await $api(`/api/auth/logout`, { method: "POST" })
         user.value = null
         return response
      }

      return {
         user,
         isLoggedIn,
         getUserRole,
         setUser,
         logout,
      }
   },
   {
      persist: true,
   }
)
