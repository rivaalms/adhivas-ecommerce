<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"

const props = withDefaults(
   defineProps<{
      data?: CategoryDTO
      /**
       * Loading prop accepts both primitive `boolean` and `ref()`
       * since this component can be rendered on template or using render function.
       * We provide `ref()` value to maintain reactivity when user is rendering
       * this component using render function
       */
      loading?: Ref<boolean> | boolean
   }>(),
   {
      loading: () => shallowRef(false),
   }
)

const emit = defineEmits<{
   submit: [data: InferSchema<typeof schema>]
}>()

const schema = $categorySchema().create

const state = reactive<DeepPartial<InferSchema<typeof schema>>>({
   name: undefined,
})

/**
 * This will handle both primitive `boolean` and `ref()` type
 * when `props.loading` type is `Ref<boolean>` this will return `ref().value`
 * but if `props.loading` type is `boolean` this will return `boolean`
 */
const loading = computed(() => unref(props.loading))

/**
 * Emit `submit` event with `InferSchema<typeof schema>` data
 */
function onSubmit(e: FormSubmitEvent<InferSchema<typeof schema>>) {
   emit("submit", e.data)
}

watchImmediate(
   () => props.data,
   (value) => {
      state.name = value?.name ?? undefined
   }
)
</script>

<template>
   <!-- Example of form implementation -->
   <UForm
      :schema="schema"
      :state="state"
      class="flex flex-col gap-4"
      @submit="onSubmit"
   >
      <UFormField
         name="name"
         label="Nama Kategori"
         required
      >
         <UInput
            v-model="state.name"
            :disabled="loading"
            class="w-full"
         />
      </UFormField>
      <div class="flex items-center justify-end">
         <UButton
            type="submit"
            label="Submit"
            :loading="loading"
         />
      </div>
   </UForm>
</template>
