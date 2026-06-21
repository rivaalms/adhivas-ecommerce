<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"

const props = withDefaults(
   defineProps<{
      imageUrl?: string | null
      loading?: Ref<boolean> | boolean
   }>(),
   {
      loading: () => shallowRef(false),
   }
)

const emit = defineEmits<{
   submit: [file: File]
}>()

const schema = $productSchema().image

const state = reactive({
   image: undefined as File | undefined,
})

const loading = computed(() => unref(props.loading))

function onSubmit(e: FormSubmitEvent<InferSchema<typeof schema>>) {
   if (e.data.image) {
      emit("submit", e.data.image)
   }
}
</script>

<template>
   <UForm
      :schema="schema"
      :state="state"
      class="flex flex-col gap-4"
      @submit="onSubmit"
   >
      <!-- Current Image Preview if available -->
      <div
         v-if="imageUrl"
         class="flex flex-col gap-2"
      >
         <span class="text-xs font-semibold text-muted-foreground"
            >Gambar Saat Ini</span
         >
         <div
            class="h-40 w-40 rounded-md overflow-hidden border border-default bg-muted"
         >
            <img
               :src="imageUrl"
               alt="Product Image"
               class="h-full w-full object-cover"
            />
         </div>
      </div>

      <UFormField
         name="image"
         label="Gambar Baru"
         required
      >
         <UFileUpload
            v-model="state.image"
            accept="image/png, image/jpeg, image/webp"
            icon="lucide:image"
            label="Pilih atau seret gambar ke sini"
            description="Format PNG, JPG, WEBP (maks. 2MB)"
            :disabled="loading"
            class="w-full min-h-32"
         />
      </UFormField>

      <div class="flex items-center justify-end mt-4">
         <UButton
            type="submit"
            label="Unggah Gambar"
            :loading="loading"
         />
      </div>
   </UForm>
</template>
