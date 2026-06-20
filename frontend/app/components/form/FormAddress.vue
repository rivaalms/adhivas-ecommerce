<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui"

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

const schema = $addressSchema().create

type FormState = InferSchema<typeof schema>

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

const provinces = ref<ProvinceDTO[]>([])
const regencies = ref<RegencyDTO[]>([])
const districts = ref<DistrictDTO[]>([])
const villages = ref<VillageDTO[]>([])

// Region Selection States (Object binding)
const selectedProvince = ref<ProvinceDTO>()
const selectedRegency = ref<RegencyDTO>()
const selectedDistrict = ref<DistrictDTO>()
const selectedVillage = ref<VillageDTO>()

const loadingProvinces = ref(false)
const loadingRegencies = ref(false)
const loadingDistricts = ref(false)
const loadingVillages = ref(false)

const isInitializing = ref(false)

// API Fetch Helpers
async function loadProvinces() {
   loadingProvinces.value = true
   try {
      const res = await $api<ApiResponse<ProvinceDTO[]>>(
         "/api/regions/provinces"
      )
      provinces.value = res.data || []
   } catch (e) {
      $notifyError(e)
   } finally {
      loadingProvinces.value = false
   }
}

async function loadRegencies(provinceId: string) {
   loadingRegencies.value = true
   try {
      const res = await $api<ApiResponse<RegencyDTO[]>>(
         "/api/regions/regencies",
         {
            query: { province_id: provinceId },
         }
      )
      regencies.value = res.data || []
   } catch (e) {
      $notifyError(e)
   } finally {
      loadingRegencies.value = false
   }
}

async function loadDistricts(regencyId: string) {
   loadingDistricts.value = true
   try {
      const res = await $api<ApiResponse<DistrictDTO[]>>(
         "/api/regions/districts",
         {
            query: { regency_id: regencyId },
         }
      )
      districts.value = res.data || []
   } catch (e) {
      $notifyError(e)
   } finally {
      loadingDistricts.value = false
   }
}

async function loadVillages(districtId: string) {
   loadingVillages.value = true
   try {
      const res = await $api<ApiResponse<VillageDTO[]>>(
         "/api/regions/villages",
         {
            query: { district_id: districtId },
         }
      )
      villages.value = res.data || []
   } catch (e) {
      $notifyError(e)
   } finally {
      loadingVillages.value = false
   }
}

// Watchers for cascading dropdowns using Object comparison
watch(selectedProvince, async (newVal, oldVal) => {
   if (newVal?.code === oldVal?.code) return

   if (!isInitializing.value) {
      state.province_name = newVal ? newVal.name : ""

      selectedRegency.value = undefined
      state.regency_name = ""
      regencies.value = []

      selectedDistrict.value = undefined
      state.district_name = ""
      districts.value = []

      selectedVillage.value = undefined
      state.subdistrict_name = ""
      villages.value = []

      if (newVal?.code) {
         await loadRegencies(newVal.code)
      }
   } else {
      state.province_name = newVal ? newVal.name : ""
   }
})

watch(selectedRegency, async (newVal, oldVal) => {
   if (newVal?.code === oldVal?.code) return

   if (!isInitializing.value) {
      state.regency_name = newVal ? newVal.name : ""

      selectedDistrict.value = undefined
      state.district_name = ""
      districts.value = []

      selectedVillage.value = undefined
      state.subdistrict_name = ""
      villages.value = []

      if (newVal?.code) {
         await loadDistricts(newVal.code)
      }
   } else {
      state.regency_name = newVal ? newVal.name : ""
   }
})

watch(selectedDistrict, async (newVal, oldVal) => {
   if (newVal?.code === oldVal?.code) return

   if (!isInitializing.value) {
      state.district_name = newVal ? newVal.name : ""

      selectedVillage.value = undefined
      state.subdistrict_name = ""
      villages.value = []

      if (newVal?.code) {
         await loadVillages(newVal.code)
      }
   } else {
      state.district_name = newVal ? newVal.name : ""
   }
})

watch(selectedVillage, (newVal, oldVal) => {
   if (newVal?.code === oldVal?.code) return
   state.subdistrict_name = newVal ? newVal.name : ""
})

function onSubmit(e: FormSubmitEvent<FormState>) {
   const data = e.data
   const payload = {
      address_line: data.address_line,
      province_name: data.province_name,
      province_id: selectedProvince.value?.code || "",
      regency_name: data.regency_name,
      regency_id: selectedRegency.value?.code || "",
      district_name: data.district_name,
      district_id: selectedDistrict.value?.code || "",
      subdistrict_name: data.subdistrict_name,
      subdistrict_id: selectedVillage.value?.code || "",
      postal_code: data.postal_code,
      is_default: !!data.is_default,
   }
   emit("submit", payload)
}

// Initial hydration from props data
watchImmediate(
   () => props.data,
   async (value) => {
      isInitializing.value = true

      state.address_line = value?.address_line ?? ""
      state.postal_code = value?.postal_code ?? ""
      state.is_default = value?.is_default ?? false

      state.province_name = value?.province_name ?? ""
      state.regency_name = value?.regency_name ?? ""
      state.district_name = value?.district_name ?? ""
      state.subdistrict_name = value?.subdistrict_name ?? ""

      selectedProvince.value = value?.province_id
         ? { code: value.province_id, name: value.province_name }
         : undefined
      selectedRegency.value = value?.regency_id
         ? { code: value.regency_id, name: value.regency_name }
         : undefined
      selectedDistrict.value = value?.district_id
         ? { code: value.district_id, name: value.district_name }
         : undefined
      selectedVillage.value = value?.subdistrict_id
         ? { code: value.subdistrict_id, name: value.subdistrict_name }
         : undefined

      await loadProvinces()

      if (value) {
         if (value.province_id) await loadRegencies(value.province_id)
         if (value.regency_id) await loadDistricts(value.regency_id)
         if (value.district_id) await loadVillages(value.district_id)
      }

      isInitializing.value = false
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
            <USelectMenu
               v-model="selectedProvince"
               :items="provinces"
               label-key="name"
               :loading="loadingProvinces || loading"
               :disabled="loadingProvinces || loading"
               placeholder="Pilih Provinsi"
               class="w-full"
            />
         </UFormField>

         <UFormField
            name="regency_name"
            label="Kota/Kabupaten"
            required
         >
            <USelectMenu
               v-model="selectedRegency"
               :items="regencies"
               label-key="name"
               :loading="loadingRegencies || loading"
               :disabled="loadingRegencies || !selectedProvince || loading"
               placeholder="Pilih Kota/Kabupaten"
               class="w-full"
            />
         </UFormField>
      </div>

      <div class="grid grid-cols-2 gap-4">
         <UFormField
            name="district_name"
            label="Kecamatan"
            required
         >
            <USelectMenu
               v-model="selectedDistrict"
               :items="districts"
               label-key="name"
               :loading="loadingDistricts || loading"
               :disabled="loadingDistricts || !selectedRegency || loading"
               placeholder="Pilih Kecamatan"
               class="w-full"
            />
         </UFormField>

         <UFormField
            name="subdistrict_name"
            label="Kelurahan/Desa"
            required
         >
            <USelectMenu
               v-model="selectedVillage"
               :items="villages"
               label-key="name"
               :loading="loadingVillages || loading"
               :disabled="loadingVillages || !selectedDistrict || loading"
               placeholder="Pilih Kelurahan/Desa"
               class="w-full"
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
            :maxlength="5"
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
