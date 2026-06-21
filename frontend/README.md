# 🚀 Panduan Onboarding Frontend — Adhivas E-Commerce

Selamat datang di repositori frontend **Adhivas E-Commerce**! Repositori ini dibangun menggunakan **Nuxt v4** (Single Page Application, `ssr: false`) dan bertindak sebagai antarmuka pelanggan (Customer Portal) sekaligus Panel Administrasi (Admin Dashboard).

Frontend ini terhubung dengan Laravel API di backend melalui arsitektur **BFF (Backend-for-Frontend)** yang terintegrasi di dalam Nitro Server Nuxt.

---

## 🛠️ Stack Teknologi

- **Core Framework**: [Nuxt v4](https://nuxt.com/) (SPA Mode, `ssr: false`)
- **UI & Styling**: [Nuxt UI v4](https://ui.nuxt.com) & [Tailwind CSS v4](https://tailwindcss.com)
- **Data Visualization**: [@unovis/vue](https://unovis.dev/) & `@unovis/ts` (digunakan pada dashboard admin)
- **State Management**: [Pinia](https://pinia.vuejs.org) + [pinia-plugin-persistedstate](https://prazdevs.github.io/pinia-plugin-persistedstate/) (untuk menyimpan global state)
- **Validasi Skema**: [Zod](https://zod.dev) (skema validasi)
- **Date Utilities**: [Day.js](https://day.js.org) (timezone default `Asia/Jakarta`, locale `id`)
- **Composables**: [VueUse](https://vueuse.org)

---

## 📂 Struktur Direktori Utama

Arsitektur folder mengikuti konvensi **Nuxt 4** untuk kerapian dan keamanan kode:

```text
frontend/
├── app/                  # Direktori Utama Frontend (Vue App)
│   ├── assets/           # Global CSS (main.css) dan aset statis
│   ├── components/       # Komponen UI global (DataTable, ConfirmationPrompt)
│   │   └── form/         # Komponen form dinamis (FormAddress, FormProduct, dll.)
│   ├── composables/      # Reactivity helpers kustom (useApi, dll.)
│   ├── layouts/          # Tata letak halaman (default untuk Customer, admin untuk Admin)
│   ├── middleware/       # Route guards global (auth.global.ts untuk role check)
│   ├── pages/            # Routing berbasis berkas (Customer & Admin)
│   ├── stores/           # Pinia Stores (app, auth, cart)
│   └── utils/            # Utilitas helper client-side ($api)
├── server/               # Nitro Server (BFF Engine)
│   ├── api/              # API Endpoints lokal yang bertindak sebagai proxy
│   │   └── dashboard/    # Endpoints analitik (stats, status-breakdown, sales-trend)
│   └── utils/            # BFF core helper ($serverApi.ts untuk Laravel communication)
├── shared/               # Kode Bersama (Shared Code)
│   ├── types/dto/        # Tipe TypeScript DTO global (dashboard.d.ts, order.d.ts, dll.)
│   └── utils/            # Validasi bersama Zod (address.ts, auth.ts, dll.)
└── package.json          # Manajemen dependensi frontend
```

---

## 🚀 Langkah Memulai (Setup Cepat)

### 1. Prasyarat (Prerequisites)
- Gunakan Node.js versi terbaru (LTS direkomendasikan).
- Gunakan **`pnpm`** sebagai package manager utama.

### 2. Instalasi Dependensi
```bash
pnpm install
```

### 3. Konfigurasi Environment (`.env`)
Salin template `.env` (jika belum ada) dan sesuaikan URL API Laravel backend Anda:
```bash
cp .env.example .env
```
Isi konfigurasi dalam berkas `.env`:
```env
# URL Laravel Backend API
API_URL=http://localhost:8000/api
```

### 4. Menjalankan Server Development
```bash
pnpm dev
```
Aplikasi frontend Anda sekarang berjalan dan dapat diakses di `http://localhost:3000`.

### 5. Standardisasi Kode & Pemformatan (Format & Lint)
Kami menerapkan aturan format kode dan linter yang ketat:
```bash
# Memformat kode menggunakan Prettier
pnpm format

# Mengecek linter ESLint
pnpm lint
```

---

## 🎨 Konvensi Pengkodingan & Pola Penting

Untuk mempertahankan kebersihan dan integritas kode, harap patuhi pola-pola arsitektur berikut:

### 1. Pola BFF (Backend-For-Frontend) Proxy
Client di frontend **tidak pernah** menembak langsung ke port Laravel API (8000) untuk menjaga keamanan cookie & token. Semua request melewati Nuxt Server API (`server/api/`):
- Gunakan helper [$serverApi](/server/utils/$serverApi.ts) di server-side untuk mem-proxy request. Helper ini otomatis menyuntikkan token otorisasi Bearer dari cookie `auth-token` dan menghapusnya jika server merespons dengan `401 Unauthorized`.
- Setiap endpoint BFF wajib mengembalikan fungsi `toJSON()` agar TypeScript dapat secara otomatis mengenali DTO tipe data respons di sisi client.

### 2. Memanggil API di Client (`useApi` & `$api`)
- **`useApi` (Reaktif)**: Gunakan custom composable [useApi](/app/composables/useApi.ts) untuk `fetching` data yang membutuhkan reaktivitas (seperti pagination, filter, dll.). Secara default, `watch: false` untuk mencegah request duplikat. Anda dapat memantau parameter dengan memasukkannya ke properti `watch`.
- **`$api` (Imperatif)**: Gunakan `$api` langsung untuk aksi satu kali (seperti submit form POST/PUT/DELETE) tanpa membutuhkan state reaktif.

### 3. Validasi Bersama (Shared Validation)
Skema validasi didefinisikan menggunakan **Zod** di dalam folder `shared/utils/validations/` agar dapat digunakan bersama di client-side (untuk umpan balik instan di form input) dan server-side:
- **PENTING**: Sesuai mekanisme Nuxt 4, ekspor kembali skema validasi baru Anda di dalam [shared/utils/index.ts](/shared/utils/index.ts) agar ter-auto-import secara otomatis di mana saja tanpa import manual.

### 4. Sistem Modal Global
Modal form (seperti Tambah/Edit Produk, Kategori, atau Alamat) dikendalikan secara programatis menggunakan [appStore](/app/stores/app.ts):
- Anda tidak perlu memuat markup modal di setiap halaman. Cukup panggil `appStore.openModal(...)` dengan komponen form yang diinginkan.
- Komponen form yang akan dibuka melalui modal harus diimpor dari virtual package `#components` jika dipanggil via fungsi render `h()`.

---

## 📊 Halaman & Alur Utama Aplikasi

### 🔑 Autentikasi & Guarding
- Berkas [auth.global.ts](/app/middleware/auth.global.ts) bertindak sebagai route middleware global.
- Pengguna dengan peran `admin` otomatis diarahkan ke rute `/admin` dan diblokir dari rute customer.
- Pengguna dengan peran `customer` diarahkan ke rute `/` (Beranda) dan diblokir dari mengakses rute `/admin/*`.

### 🛒 Portal Customer
- **Katalog & Filter** (`/products`): Halaman listing produk lengkap dengan filter kategori reaktif dan sorting.
- **Manajemen Profil & Alamat** (`/profile`): Memuat informasi profil akun dan daftar alamat pengiriman. Form alamat terintegrasi dengan cascading dropdown searchable dari API publik `wilayah.id` (Provinsi -> Kota/Kabupaten -> Kecamatan -> Kelurahan).
- **Proses Order** (`/checkout` & `/orders`): Menyusun pesanan dari keranjang belanja utama dan melacak daftar pesanan yang pernah dibuat oleh pelanggan.

### 👑 Portal Admin (`/admin`)
- **Dashboard Analitik** (`/admin/index.vue`): Menyajikan statistik bisnis toko online:
  - **KPI Cards**: Menampilkan Total Pendapatan, Total Pesanan, dan Rata-rata Nilai Order (hanya menghitung order dengan status `completed`).
  - **Filter Range Tanggal**: Mendukung kueri filter reaktif (`start_date` & `end_date`) dengan opsi preset cepat.
  - **Line Chart**: Menampilkan distribusi status order (menggunakan komponen visualisasi Unovis).
  - **Donut Chart**: Menampilkan tren pendapatan harian dari order `completed` (menggunakan komponen visualisasi Unovis).
- **CRUD Panel**: Manajemen penuh atas data **Kategori Produk** (`/admin/categories`), **Daftar Produk** (`/admin/products`), dan **Daftar Order** (`/admin/orders`) termasuk pengubahan status pengiriman order.
