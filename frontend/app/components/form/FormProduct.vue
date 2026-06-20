<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"

const props = withDefaults(
   defineProps<{
      data?: ProductDTO
      /**
       * Loading prop accepts both primitive `boolean` and `ref()`
       * since this component can be rendered on template or using render function.
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

const schema = $productSchema().create

const state = reactive<DeepPartial<InferSchema<typeof schema>>>({
   name: undefined,
   description: undefined,
   price: undefined,
   stock_quantity: undefined,
   image_url: undefined,
   categories: [],
})

const loading = computed(() => unref(props.loading))

// Fetch available categories for check selection
const { data: categoriesData } = useApi("/api/categories", {
   method: "get",
   query: { per_page: 100 },
   transform: (res) => res.data,
})
const categories = computed(() => categoriesData.value?.data || [])

/**
 * Emit `submit` event with schema-compliant data
 */
function onSubmit(e: FormSubmitEvent<InferSchema<typeof schema>>) {
   emit("submit", e.data)
}

watchImmediate(
   () => props.data,
   (value) => {
      state.name = value?.name ?? undefined
      state.description = value?.description ?? undefined
      state.price = value?.price ?? undefined
      state.stock_quantity = value?.stock_quantity ?? undefined
      state.image_url = value?.image_url ?? undefined
      state.categories = value?.categories?.map((c) => c.id) ?? []
   }
)
</script>

<template>
   <UForm
      :schema="schema"
      :state="state"
      class="flex flex-col gap-4"
      @submit="onSubmit"
   >
      <UFormField
         name="name"
         label="Nama Produk"
         required
      >
         <UInput
            v-model="state.name"
            :disabled="loading"
            class="w-full"
            placeholder="Masukkan nama produk"
         />
      </UFormField>

      <UFormField
         name="description"
         label="Deskripsi"
         required
      >
         <UTextarea
            v-model="state.description"
            :disabled="loading"
            class="w-full"
            placeholder="Masukkan deskripsi produk"
            :rows="3"
         />
      </UFormField>

      <div class="grid grid-cols-2 gap-4">
         <UFormField
            name="price"
            label="Harga (IDR)"
            required
         >
            <UInputNumber
               v-model="state.price"
               :min="0"
               :format-options="{
                  style: 'currency',
                  currency: 'IDR',
                  currencyDisplay: 'narrowSymbol',
               }"
               :disabled="loading"
               :increment="false"
               :decrement="false"
               class="w-full"
               placeholder="Harga produk"
            />
         </UFormField>

         <UFormField
            name="stock_quantity"
            label="Stok"
            required
         >
            <UInputNumber
               v-model="state.stock_quantity"
               :min="0"
               :disabled="loading"
               :increment="false"
               :decrement="false"
               class="w-full"
               placeholder="Jumlah Stok"
            />
         </UFormField>
      </div>

      <UFormField
         name="image_url"
         label="URL Gambar Produk"
      >
         <UInput
            v-model.nullable="state.image_url"
            type="url"
            :disabled="loading"
            class="w-full"
            placeholder="https://example.com/image.jpg"
         />
      </UFormField>

      <UFormField
         name="categories"
         label="Kategori Produk"
         required
      >
         <div class="max-h-64 overflow-y-auto">
            <UCheckboxGroup
               v-model="state.categories"
               :items="categories"
               label-key="name"
               value-key="id"
               variant="card"
               :ui="{
                  fieldset: 'grid grid-cols-2 gap-x-2 gap-y-2',
               }"
            />
         </div>
      </UFormField>

      <div class="flex items-center justify-end mt-4">
         <UButton
            type="submit"
            label="Simpan Produk"
            :loading="loading"
         />
      </div>
   </UForm>
</template>
