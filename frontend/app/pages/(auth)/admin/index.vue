<script setup lang="ts">
const authStore = useAuthStore()
</script>

<template>
   <div class="flex flex-col gap-6">
      <!-- Admin Header Banner -->
      <section
         class="rounded-2xl bg-linear-to-br from-rose-500/10 via-amber-500/5 to-transparent p-8 border border-rose-500/10"
      >
         <h1
            class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl"
         >
            Panel Administrasi
         </h1>
         <p class="mt-2 text-lg text-slate-600 dark:text-slate-400">
            Selamat datang,
            <span class="font-bold text-rose-600 dark:text-rose-400">{{
               authStore.user?.full_name
            }}</span
            >. Anda memiliki akses penuh ke sistem manajemen e-commerce.
         </p>
      </section>
   </div>
</template>
