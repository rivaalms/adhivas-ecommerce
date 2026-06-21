<script setup lang="ts">
const authStore = useAuthStore()
const appStore = useAppStore()
const dayjs = useDayjs()

// Redirect to home if already logged in
if (authStore.isLoggedIn) {
   navigateTo("/")
}

const loading = shallowRef(false)

async function onSubmit(data: InferFnSchema<typeof $authSchema, "register">) {
   loading.value = true
   try {
      // Call the BFF register proxy API
      const response = await $api("/api/auth/register", {
         method: "POST",
         body: data,
      })

      authStore.setUser(response.data)

      appStore.notify({
         title: "Registrasi Berhasil",
         description:
            response.message || "Selamat bergabung di Adhivas E-Commerce!",
         color: "success",
      })

      navigateTo("/")
   } catch (e) {
      $notifyError(e)
   } finally {
      loading.value = false
   }
}
</script>

<template>
   <UPage>
      <UPageBody class="h-[calc(100vh-var(--ui-header-height))] mt-0">
         <div class="flex items-center h-full justify-center bg-muted/30">
            <div class="w-full max-w-md">
               <UCard>
                  <template #header>
                     <div class="text-center py-2">
                        <img
                           src="/logo.svg"
                           class="h-12 mx-auto w-auto mb-2"
                           alt="Adhivas E-Commerce Logo"
                        />
                        <h1
                           class="text-primary text-2xl font-bold tracking-tight"
                        >
                           Adhivas E-Commerce
                        </h1>
                        <p class="mt-2 text-sm text-muted">
                           Daftar akun baru untuk mulai berbelanja
                        </p>
                     </div>
                  </template>

                  <div class="py-4">
                     <FormRegister
                        :loading="loading"
                        @submit="onSubmit"
                     />

                     <div class="mt-6 text-center text-sm text-muted">
                        Sudah punya akun?
                        <NuxtLink
                           to="/login"
                           class="text-primary hover:underline"
                        >
                           Login disini
                        </NuxtLink>
                     </div>
                  </div>

                  <template #footer>
                     <div class="text-center text-xs text-muted">
                        &copy; {{ dayjs().year() }} Adhivas E-Commerce. All
                        rights reserved.
                     </div>
                  </template>
               </UCard>
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
