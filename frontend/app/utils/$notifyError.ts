import type { Toast } from "@nuxt/ui/composables"

export default function (e: unknown) {
   const appStore = useAppStore()
   const toast = resolveErrorToast(e)
   appStore.notify(toast)
}

/** @internal */
function resolveErrorToast(e: unknown): Partial<Toast> {
   const title =
      typeof e === "object" &&
      e !== null &&
      "statusMessage" in e &&
      typeof e.statusMessage === "string"
         ? e.statusMessage
         : isGeneralError(e)
           ? e.name
           : "Error"

   const description =
      typeof e === "object" &&
      e !== null &&
      "message" in e &&
      typeof e.message === "string"
         ? e.message
         : "An unexpected error occurred"

   return {
      title,
      description,
      color: "error",
   }
}

/** @internal */
function isGeneralError(e: unknown): e is Error {
   return e instanceof Error
}
