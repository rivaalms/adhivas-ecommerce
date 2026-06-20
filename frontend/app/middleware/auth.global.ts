export default defineNuxtRouteMiddleware((to) => {
   const authStore = useAuthStore()

   // Check if the route is inside the (auth) route group
   const isProtectedRoute = to.meta.groups?.includes("auth")

   const isLoginPage = to.path === "/login"

   // 1. If user is NOT logged in and tries to access a protected route
   if (isProtectedRoute && !authStore.isLoggedIn) {
      return navigateTo("/login")
   }

   // 2. If user IS logged in and tries to access the login page
   if (isLoginPage && authStore.isLoggedIn) {
      if (authStore.getUserRole === "admin") {
         return navigateTo("/admin")
      }
      return navigateTo("/")
   }

   // 3. If user IS logged in but tries to access admin routes without being an admin
   if (
      authStore.isLoggedIn &&
      isProtectedRoute &&
      to.path.startsWith("/admin") &&
      authStore.getUserRole !== "admin"
   ) {
      return navigateTo("/")
   }

   // 4. If user IS logged in but tries to access customer routes without being a customer
   if (
      authStore.isLoggedIn &&
      isProtectedRoute &&
      !to.path.startsWith("/admin") &&
      authStore.getUserRole !== "customer"
   ) {
      return navigateTo("/admin")
   }
})
