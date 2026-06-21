<script setup lang="ts">
const appStore = useAppStore()
const cartStore = useCartStore()
const authStore = useAuthStore()

const sortOptions = [
   { label: "Terbaru", sort_by: "created_at", sort_dir: "desc" },
   { label: "Terlama", sort_by: "created_at", sort_dir: "asc" },
   { label: "Harga Terendah", sort_by: "price", sort_dir: "asc" },
   { label: "Harga Tertinggi", sort_by: "price", sort_dir: "desc" },
]

const query = reactive({
   page: 1,
   per_page: 18,
   search: undefined as string | undefined,
   category_id: undefined as number | undefined,
   sort_by: "created_at",
   sort_dir: "desc",
})

const selectedSort = ref(sortOptions[0])

const categorySearch = shallowRef("")
const { data: categoriesData, refresh: fetchCategories } = useApi(
   "/api/categories",
   {
      method: "get",
      query: {
         search: categorySearch,
         per_page: 10,
      },
      transform: (res) => res.data.data,
      default: () => [] as CategoryDTO[],
   }
)
const categories = computed(() => categoriesData.value || [])

watchDebounced(categorySearch, () => fetchCategories(), {
   debounce: 800,
   maxWait: 1000,
})

const { data, pending, refresh } = useApi(`/api/products`, {
   method: "get",
   query,
   transform: (res) => {
      return {
         data: res.data.data.map((item: any) => ({
            ...item,
            add_to_cart_qty: 1,
         })),
         meta: res.data.meta,
      }
   },
   deep: true,
})

watch(
   () => selectedSort.value,
   (val) => {
      if (val) {
         query.sort_by = val.sort_by
         query.sort_dir = val.sort_dir
      } else {
         query.sort_by = "created_at"
         query.sort_dir = "desc"
      }
      query.page = 1
      refresh()
   }
)

watchDebounced(
   () => query.search,
   () => {
      query.page = 1
      refresh()
   },
   { debounce: 800, maxWait: 1000 }
)

watch(
   () => query.category_id,
   () => {
      query.page = 1
      refresh()
   }
)

watch(
   () => query.page,
   () => refresh()
)

watchExcludable(
   () => query,
   (value) => {
      if (value.page > 1) {
         query.page = 1
         return
      }
      refresh()
   },
   {
      exclude: ["page", "search", "category_id", "sort_by", "sort_dir"],
   }
)

async function addToCart(product: ProductDTO & { add_to_cart_qty: number }) {
   if (!authStore.isLoggedIn) {
      return navigateTo("/login")
   }

   const response = await cartStore.addCartItem({
      product_id: product.id,
      quantity: product.add_to_cart_qty,
   })

   if (response) {
      appStore.notify({
         title: "Sukses",
         description: response.message,
      })
   }
}
</script>

<template>
   <UPage>
      <UPageBody>
         <UPageHeader
            title="Katalog Produk"
            description="Temukan berbagai macam produk original terbaik hanya di Adhivas Ecommerce"
         />
         <!-- Filter Section -->
         <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full mb-6"
         >
            <div class="flex flex-wrap items-center gap-2.5">
               <UInput
                  v-model="query.search"
                  placeholder="Cari produk..."
                  icon="lucide:search"
                  class="w-64"
               />
               <USelectMenu
                  v-model="query.category_id"
                  v-model:search-term="categorySearch"
                  :items="categories"
                  value-key="id"
                  label-key="name"
                  placeholder="Semua Kategori"
                  ignore-filter
                  clear
                  class="w-48"
               />
               <USelectMenu
                  v-slot="{ modelValue }"
                  v-model="selectedSort"
                  :items="sortOptions"
                  placeholder="Urutkan"
                  class="w-48"
               >
                  <span>{{ modelValue ? modelValue.label : "Urutkan" }}</span>
               </USelectMenu>
            </div>
         </div>

         <!-- Product Grid -->
         <div
            v-if="pending && !data?.data.length"
            class="flex h-64 items-center justify-center"
         >
            <UIcon
               name="lucide:loader-2"
               class="size-8 animate-spin text-primary"
            />
         </div>
         <div
            v-else-if="!data?.data.length"
            class="flex flex-col items-center justify-center py-20"
         >
            <UEmpty
               title="Produk Tidak Ditemukan"
               description="Coba ubah kata kunci pencarian atau filter kategori Anda."
               icon="lucide:shopping-bag"
               variant="naked"
            />
         </div>
         <div
            v-else
            class="flex flex-col gap-8"
         >
            <div
               class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
            >
               <UPageCard
                  v-for="product in data.data"
                  :key="product.id"
                  :ui="{
                     container: 'sm:p-4',
                  }"
                  :to="`/products/${product.id}`"
               >
                  <div class="flex flex-col gap-4">
                     <div
                        class="aspect-square w-full rounded-md overflow-hidden bg-muted"
                     >
                        <img
                           :src="product.image_url"
                           class="object-cover h-full w-full"
                           :alt="product.name"
                        />
                     </div>
                     <div class="flex flex-col flex-1">
                        <div class="font-medium line-clamp-2 min-h-10">
                           {{ product.name }}
                        </div>
                        <div class="text-lg font-semibold mt-1">
                           {{
                              $formatNumber(product.price, {
                                 style: "currency",
                                 currency: "IDR",
                                 currencyDisplay: "narrowSymbol",
                              })
                           }}
                        </div>
                        <div
                           class="mt-4 flex items-center pointer-events-auto z-10"
                        >
                           <UInputNumber
                              v-model="product.add_to_cart_qty"
                              size="sm"
                              :min="1"
                              :max="product.stock_quantity"
                              :ui="{ root: 'max-w-24' }"
                           />
                           <div class="text-sm ms-auto text-muted font-medium">
                              Stok: {{ product.stock_quantity }}
                           </div>
                        </div>
                        <UButton
                           icon="lucide:plus"
                           label="Keranjang"
                           color="primary"
                           block
                           variant="soft"
                           class="mt-4 z-10"
                           @click.stop="addToCart(product)"
                        />
                     </div>
                  </div>
               </UPageCard>
            </div>

            <USeparator />

            <!-- Pagination -->
            <div
               class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
               <span class="text-muted text-xs">
                  Menampilkan
                  {{ (query.page - 1) * query.per_page + 1 }} &ndash;
                  {{
                     Math.min(
                        query.page * query.per_page,
                        data?.meta?.total || 0
                     )
                  }}
                  dari {{ data?.meta?.total || 0 }} produk
               </span>
               <UPagination
                  v-model:page="query.page"
                  :items-per-page="query.per_page"
                  :total="data?.meta?.total || 0"
                  :sibling-count="1"
               />
            </div>
         </div>
      </UPageBody>
   </UPage>
</template>
