import { defineAppConfig } from "#imports"

export default defineAppConfig({
   ui: {
      pageHeader: {
         slots: {
            root: "pt-0 border-b-0",
            title: "text-lg sm:text-2xl",
            description: "text-base",
         },
         variants: {
            title: {
               true: {
                  description: "mt-1",
               },
            },
         },
      },
   },
})
