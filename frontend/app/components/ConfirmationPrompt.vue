<script setup lang="ts">
import type { ButtonProps } from "#ui/types"

const props = withDefaults(
   defineProps<{
      prompt?: string
      positiveButtonProps?: Omit<ButtonProps, "onClick" | "block">
      negativeButtonProps?: Omit<ButtonProps, "onClick" | "block">
      icon?: string
   }>(),
   {
      prompt: "Confirm actions?",
      icon: "lucide:help-circle",
   }
)

const emit = defineEmits<{
   positive: []
   negative: []
}>()

const positiveButtonProps = computed<ButtonProps>(() => {
   return {
      label: "Konfirmasi",
      color: "primary",
      icon: "lucide:check",
      ...props.positiveButtonProps,
      block: true,
      onClick: () => emit("positive"),
   }
})

const negativeButtonProps = computed<ButtonProps>(() => {
   return {
      label: "Batal",
      color: "neutral",
      variant: "outline",
      icon: "lucide:x",
      ...props.negativeButtonProps,
      block: true,
      onClick: () => emit("negative"),
   }
})
</script>

<template>
   <div>
      <div class="my-20 flex flex-col items-center">
         <UIcon
            :name="props.icon"
            class="text-dimmed mb-8 size-12"
         />
         <div>
            <p class="text-center font-medium">
               {{ props.prompt }}
            </p>
         </div>
      </div>
      <div class="flex gap-4">
         <UButton v-bind="negativeButtonProps" />
         <UButton v-bind="positiveButtonProps" />
      </div>
   </div>
</template>
