<script setup lang="ts" generic="T">
import type { TableColumn } from "#ui/types"

const props = withDefaults(
   defineProps<{
      data?: T[]
      columns?: TableColumn<T>[]
      total?: number
      from?: number
      to?: number
      loading?: boolean
   }>(),
   {
      data: () => [],
      columns: () => [],
      total: 0,
      loading: false,
   }
)

const page = defineModel<number>("page", { required: false, default: 1 })
const perPage = defineModel<number>("perPage", { required: false, default: 10 })
const expanded = defineModel<Record<number, boolean> | undefined>("expanded", {
   required: false,
   default: undefined,
})

const showCountInfo = computed(() => {
   return props.from !== undefined && props.to !== undefined
})
</script>

<template>
   <div class="flex flex-col gap-2">
      <slot name="header" />
      <UTable
         v-model:expanded="expanded"
         :data="props.data"
         :columns="props.columns"
         class="w-full"
         :ui="{
            base: 'border-b border-default',
            tr: 'data-[expanded=true]:bg-elevated/50',
         }"
      >
         <template #empty>
            <slot name="empty">
               <UEmpty
                  title="No Data"
                  icon="lucide:x"
                  variant="naked"
               />
            </slot>
         </template>
         <template #expanded="{ row }">
            <slot
               name="expanded"
               :row="row"
            />
         </template>
      </UTable>
      <slot name="footer">
         <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
         >
            <span
               v-show="showCountInfo"
               class="text-muted order-2 text-xs sm:order-1"
            >
               {{ props.from }} &ndash; {{ props.to }} of
               {{ props.total }} entries
            </span>
            <div
               class="sm:ms-auto order-1 flex flex-wrap items-center justify-between gap-4 sm:order-2 sm:justify-start sm:gap-2.5"
            >
               <USelect
                  v-model="perPage"
                  :items="[5, 10, 25, 50, 100]"
                  class="w-32"
               >
                  <template #item-label="{ item }">
                     {{ item }} / page
                  </template>
                  <template #default="{ modelValue }">
                     {{ modelValue }} / page
                  </template>
               </USelect>
               <UPagination
                  v-model:page="page"
                  :items-per-page="perPage"
                  :total="props.total"
                  :sibling-count="1"
                  :ui="{
                     root: 'flex-wrap justify-center',
                  }"
               />
            </div>
         </div>
      </slot>
   </div>
</template>
