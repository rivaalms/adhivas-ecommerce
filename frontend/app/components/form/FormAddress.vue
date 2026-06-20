<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"
import { z } from "zod"

const props = withDefaults(
   defineProps<{
      data?: UserAddressDTO
      loading?: Ref<boolean> | boolean
   }>(),
   {
      loading: () => shallowRef(false),
   }
)

const emit = defineEmits<{
   submit: [payload: any]
}>()

const schema = z.object({
   address_line: z.string().nonempty("Alamat lengkap wajib diisi"),
   province_name: z.string().nonempty("Provinsi wajib diisi"),
   regency_name: z.string().nonempty("Kota/Kabupaten wajib diisi"),
   district_name: z.string().nonempty("Kecamatan wajib diisi"),
   subdistrict_name: z.string().nonempty("Kelurahan/Desa wajib diisi"),
   postal_code: z
      .string()
      .nonempty("Kode Pos wajib diisi")
      .max(10, "Kode Pos tidak valid"),
   is_default: z.boolean().default(false),
})

type FormState = z.infer<typeof schema>

const state = reactive<FormState>({
   address_line: "",
   province_name: "",
   regency_name: "",
   district_name: "",
   subdistrict_name: "",
   postal_code: "",
   is_default: false,
})

const loading = computed(() => unref(props.loading))

function onSubmit(e: FormSubmitEvent<FormState>) {
   const data = e.data
   const payload = {
      address_line: data.address_line,
      province_name: data.province_name,
      province_id: data.province_name
         .toLowerCase()
         .trim()
         .replace(/[^a-z0-9]+/g, "-"),
      regency_name: data.regency_name,
      regency_id: data.regency_name
         .toLowerCase()
         .trim()
         .replace(/[^a-z0-9]+/g, "-"),
      district_name: data.district_name,
      district_id: data.district_name
         .toLowerCase()
         .trim()
         .replace(/[^a-z0-9]+/g, "-"),
      subdistrict_name: data.subdistrict_name,
      subdistrict_id: data.subdistrict_name
         .toLowerCase()
         .trim()
         .replace(/[^a-z0-9]+/g, "-"),
      postal_code: data.postal_code,
      is_default: !!data.is_default,
   }
   emit("submit", payload)
}

watchImmediate(
   () => props.data,
   (value) => {
      state.address_line = value?.address_line ?? ""
      state.province_name = value?.province_name ?? ""
      state.regency_name = value?.regency_name ?? ""
      state.district_name = value?.district_name ?? ""
      state.subdistrict_name = value?.subdistrict_name ?? ""
      state.postal_code = value?.postal_code ?? ""
      state.is_default = value?.is_default ?? false
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
         name="address_line"
         label="Alamat Lengkap"
         required
      >
         <UTextarea
            v-model="state.address_line"
            :disabled="loading"
            class="w-full"
            placeholder="Jalan, No. Rumah, RT/RW, Komplek, dll."
            :rows="3"
         />
      </UFormField>

      <div class="grid grid-cols-2 gap-4">
         <UFormField
            name="province_name"
            label="Provinsi"
            required
         >
            <UInput
               v-model="state.province_name"
               :disabled="loading"
               class="w-full"
               placeholder="Contoh: Jawa Barat"
            />
         </UFormField>

         <UFormField
            name="regency_name"
            label="Kota/Kabupaten"
            required
         >
            <UInput
               v-model="state.regency_name"
               :disabled="loading"
               class="w-full"
               placeholder="Contoh: Bandung"
            />
         </UFormField>
      </div>

      <div class="grid grid-cols-2 gap-4">
         <UFormField
            name="district_name"
            label="Kecamatan"
            required
         >
            <UInput
               v-model="state.district_name"
               :disabled="loading"
               class="w-full"
               placeholder="Contoh: Coblong"
            />
         </UFormField>

         <UFormField
            name="subdistrict_name"
            label="Kelurahan/Desa"
            required
         >
            <UInput
               v-model="state.subdistrict_name"
               :disabled="loading"
               class="w-full"
               placeholder="Contoh: Dago"
            />
         </UFormField>
      </div>

      <UFormField
         name="postal_code"
         label="Kode Pos"
         required
      >
         <UInput
            v-model="state.postal_code"
            :disabled="loading"
            class="w-full"
            placeholder="Contoh: 40135"
         />
      </UFormField>

      <UFormField name="is_default">
         <UCheckbox
            v-model="state.is_default"
            :disabled="loading || props.data?.is_default"
            label="Jadikan alamat utama (default)"
         />
      </UFormField>

      <div class="flex items-center justify-end gap-2 mt-4">
         <UButton
            type="submit"
            :label="props.data ? 'Perbarui Alamat' : 'Simpan Alamat'"
            :loading="loading"
         />
      </div>
   </UForm>
</template>
