<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"

const props = withDefaults(
   defineProps<{
      loading?: Ref<boolean> | boolean
   }>(),
   {
      loading: () => shallowRef(false),
   }
)

const emit = defineEmits<{
   submit: [data: InferSchema<typeof schema>]
}>()

const schema = $authSchema().register

const state = reactive<InferSchema<typeof schema>>({
   full_name: "",
   email: "",
   password: "",
   password_confirmation: "",
})

const loading = computed(() => unref(props.loading))

function onSubmit(e: FormSubmitEvent<InferSchema<typeof schema>>) {
   emit("submit", e.data)
}
</script>

<template>
   <UForm
      :schema="schema"
      :state="state"
      class="flex flex-col gap-4"
      @submit="onSubmit"
   >
      <UFormField
         name="full_name"
         label="Nama Lengkap"
         required
      >
         <UInput
            v-model="state.full_name"
            type="text"
            :disabled="loading"
            placeholder="Nama lengkap Anda"
            class="w-full"
         />
      </UFormField>
      <UFormField
         name="email"
         label="Email"
         required
      >
         <UInput
            v-model="state.email"
            type="email"
            :disabled="loading"
            placeholder="Alamat email Anda"
            class="w-full"
         />
      </UFormField>
      <UFormField
         name="password"
         label="Password"
         required
      >
         <UInput
            v-model="state.password"
            type="password"
            :disabled="loading"
            placeholder="Password minimal 6 karakter"
            class="w-full"
         />
      </UFormField>
      <UFormField
         name="password_confirmation"
         label="Konfirmasi Password"
         required
      >
         <UInput
            v-model="state.password_confirmation"
            type="password"
            :disabled="loading"
            placeholder="Ulangi password Anda"
            class="w-full"
         />
      </UFormField>
      <div class="flex items-center justify-end mt-2">
         <UButton
            type="submit"
            label="Daftar Sekarang"
            :loading="loading"
            class="px-6"
         />
      </div>
   </UForm>
</template>
