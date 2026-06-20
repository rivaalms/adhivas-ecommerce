export const useAuthStore = defineStore(
   "auth-store",
   () => {
      const user = ref<UserDTO | null>(null)
      const isLoggedIn = computed(() => !!user.value)

      function setUser(val: UserDTO | null) {
         user.value = val
      }

      function logout() {
         user.value = null
      }

      return {
         user,
         isLoggedIn,
         setUser,
         logout,
      }
   },
   {
      persist: true,
   }
)
