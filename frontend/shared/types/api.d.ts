/**
 * Example ApiResponse interface
 * If your API return different response structure,
 * change this interface accordingly
 */
interface ApiResponse<T> {
   status: "success" | "error"
   message: string
   data: T
}

/**
 * Example Pagination interface
 * If your API return different pagination structure,
 * change this interface accordingly
 */
interface Pagination<T> {
   data: T extends Array<unknown> ? T : T[]
   meta: {
      page: number
      per_page: number
      last_page: number
      total: number
   }
}
