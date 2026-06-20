<script setup lang="ts">
const authStore = useAuthStore()
const appStore = useAppStore()
const dayjs = useDayjs()

// Redirect to home if already logged in
if (authStore.isLoggedIn) {
   navigateTo("/")
}

const loading = shallowRef(false)

async function onSubmit(data: InferFnSchema<typeof $authSchema, "login">) {
   loading.value = true
   try {
      // Call the BFF login proxy API
      const response = await $api("/api/auth/login", {
         method: "POST",
         body: data,
      })

      authStore.setUser(response.data)

      appStore.notify({
         title: "Login Berhasil",
         description: response.message || "Selamat datang kembali!",
         color: "success",
      })

      if (authStore.getUserRole === "admin") {
         navigateTo("/admin")
      } else {
         navigateTo("/")
      }
   } catch (e) {
      $notifyError(e)
   } finally {
      loading.value = false
   }
}
</script>

<template>
   <div class="flex items-center h-screen justify-center">
      <div class="w-full max-w-md">
         <UCard>
            <template #header>
               <div class="text-center py-2">
                  <h1
                     class="bg-linear-to-r from-primary-500 to-indigo-500 bg-clip-text text-3xl font-extrabold tracking-tight text-transparent dark:from-primary-400 dark:to-indigo-400"
                  >
                     Adhivas E-Commerce
                  </h1>
                  <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                     Silakan masuk ke akun Anda untuk melanjutkan belanja
                  </p>
               </div>
            </template>

            <div class="py-4">
               <FormAuth
                  :loading="loading"
                  @submit="onSubmit"
               />
            </div>

            <template #footer>
               <div
                  class="text-center text-xs text-slate-400 dark:text-slate-500"
               >
                  &copy; {{ dayjs().year() }} Adhivas E-Commerce. All rights
                  reserved.
               </div>
            </template>
         </UCard>
      </div>
   </div>
</template>
