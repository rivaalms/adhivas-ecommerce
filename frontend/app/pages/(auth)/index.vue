<script setup lang="ts">
const authStore = useAuthStore()
</script>

<template>
   <div class="flex flex-col gap-8">
      <!-- Welcome Banner Section -->
      <section
         class="rounded-2xl bg-linear-to-br from-primary-500/10 via-indigo-500/5 to-transparent p-8 border border-primary-500/10"
      >
         <div class="max-w-3xl">
            <template v-if="authStore.isLoggedIn">
               <h1
                  class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl"
               >
                  Selamat datang kembali,
                  <span class="text-primary-600 dark:text-primary-400">{{
                     authStore.user?.full_name
                  }}</span
                  >!
               </h1>
               <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">
                  Anda telah masuk sebagai
                  <span
                     class="font-semibold capitalize text-indigo-600 dark:text-indigo-400"
                     >{{ authStore.user?.role }}</span
                  >. Anda dapat mengeksplorasi halaman admin maupun berbelanja
                  produk sesuai dengan hak akses Anda.
               </p>
            </template>
            <template v-else>
               <h1
                  class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl"
               >
                  Selamat Datang di Adhivas E-Commerce
               </h1>
               <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">
                  Platform e-commerce terbaik untuk kebutuhan belanja Anda.
                  Silakan masuk untuk mulai berbelanja dan melacak pesanan Anda
                  secara real-time.
               </p>
               <div class="mt-6 flex items-center gap-4">
                  <UButton
                     to="/login"
                     size="lg"
                     color="primary"
                     label="Masuk Sekarang"
                     icon="i-heroicons-arrow-right-on-rectangle"
                  />
               </div>
            </template>
         </div>
      </section>
   </div>
</template>
