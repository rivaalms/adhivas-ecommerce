<script setup lang="ts">
interface Props {
   data: UserDTO
}

defineProps<Props>()
</script>

<template>
   <div class="flex flex-col gap-6 py-2">
      <!-- Header section with Avatar and Name -->
      <div class="flex items-center gap-4">
         <UAvatar
            :src="`https://api.dicebear.com/9.x/thumbs/svg?seed=${data.email}`"
            size="xl"
            :alt="data.full_name"
            class="border border-default"
         />
         <div class="flex flex-col">
            <h3 class="text-lg font-bold text-highlighted">
               {{ data.full_name }}
            </h3>
            <span class="text-sm text-muted">{{ data.email }}</span>
         </div>
      </div>

      <USeparator />

      <!-- Details List -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
         <div class="col-span-full">
            <span class="block text-xs text-muted mb-1">ID Customer</span>
            <span class="font-medium text-sm text-highlighted"
               >#{{ data.id }}</span
            >
         </div>

         <div>
            <span class="block text-xs text-muted mb-1">Tanggal Bergabung</span>
            <span class="font-medium text-sm text-highlighted">
               {{ $formatDate(data.created_at) }}
            </span>
         </div>

         <div>
            <span class="block text-xs text-muted mb-1"
               >Terakhir Diperbarui</span
            >
            <span class="font-medium text-sm text-highlighted">
               {{ $formatDate(data.updated_at) }}
            </span>
         </div>
      </div>
   </div>
</template>
