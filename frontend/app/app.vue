<script setup lang="ts">
const appStore = useAppStore()

useHead({
   titleTemplate: (chunk) =>
      chunk ? `${chunk} - Adhivas Ecommerce` : "Adhivas Ecommerce",
   link: [{ rel: "icon", type: "image/svg+xml", href: "/logo.svg" }],
})
</script>

<template>
   <UApp>
      <NuxtRouteAnnouncer />
      <NuxtLoadingIndicator color="var(--ui-primary)" />
      <NuxtLayout>
         <NuxtPage />
      </NuxtLayout>
      <UModal
         v-model:open="appStore.modal.show"
         :title="appStore.modal.title"
         :ui="{
            content: `max-w-${appStore.modal.width ?? 'auto'}`,
         }"
      >
         <template #body>
            <component :is="appStore.modal.component ?? 'div'" />
         </template>
      </UModal>
   </UApp>
</template>
