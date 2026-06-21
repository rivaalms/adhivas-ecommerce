# 🛍️ Adhivas E-Commerce (Monorepo)

Selamat datang di repositori monorepo **Adhivas E-Commerce**! Proyek ini terdiri dari aplikasi belanja online lengkap untuk pelanggan (Customer Portal) dan panel manajemen dashboard analitik (Admin Panel).

Repositori ini menggabungkan frontend modern berbasis **Nuxt** dengan server API yang tangguh berbasis **Laravel**.

---

## 📂 Struktur Repositori

Proyek ini disusun secara modular sebagai monorepo:

- **[`/frontend`](/frontend)**: Aplikasi client-side Vue/Nuxt v4 (Single Page Application, `ssr: false`) dengan server Backend-for-Frontend (BFF) lokal.
- **[`/backend`](/backend)**: Server RESTful API berbasis Laravel v13.x untuk mengelola database, autentikasi JWT, keranjang, pesanan, dan data statistik.
- **[`compose.yaml`](/compose.yaml)**: Orkestrasi Docker Compose untuk menjalankan database PostgreSQL, backend, dan frontend di dalam container.

---

## 🛠️ Stack Teknologi

| Komponen | Teknologi Utama |
| :--- | :--- |
| **Frontend** | Nuxt v4, Nuxt UI v4, Tailwind CSS v4, Pinia, Unovis (Charts), Zod, Day.js |
| **Backend** | Laravel v13.x, PHP ^8.3, JWT Auth, Laravel Pint, PHPStan |
| **Database** | PostgreSQL 15 |
| **Containers** | Docker & Docker Compose |

---

## 🚀 Memulai Cepat (Quick Start)

Metode tercepat untuk menjalankan seluruh sistem secara lokal adalah dengan menggunakan Docker Compose.

### Cara A: Menjalankan dengan Docker Compose (Direkomendasikan)

#### Prasyarat:
- Pastikan [Docker](https://www.docker.com/) dan Docker Compose terinstal di mesin Anda.

#### Langkah-langkah:
1. Buat file `.env` di direktori `backend/` berdasarkan `.env.example` dan pastikan kunci enkripsi terisi:
   ```bash
   cp backend/.env.example backend/.env
   # Silakan isi APP_KEY dan JWT_SECRET di backend/.env
   ```
2. Jalankan perintah orkestrasi di root direktori proyek:
   ```bash
   docker compose up --build -d
   ```
3. Layanan akan aktif secara otomatis pada port berikut:
   - **Frontend (Nuxt App):** [http://localhost:3000](http://localhost:3000)
   - **Backend (Laravel API):** [http://localhost:8000](http://localhost:8000)
   - **Database (PostgreSQL):** `localhost:5432` (Username/Password: `postgres`/`postgres`)

---

### Cara B: Menjalankan Secara Manual (Bare Metal)

Jika Anda ingin menjalankan frontend dan backend secara manual tanpa Docker, ikuti langkah berikut:

#### 1. Setup Backend (Laravel API)
1. Pindah ke direktori `/backend`.
2. Jalankan instalasi dependensi, migrasi skema database, dan seeding mock data:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate --seed
   ```
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   *(Untuk dokumentasi setup backend selengkapnya, baca [Panduan Backend](/backend/README.md))*

#### 2. Setup Frontend (Nuxt App)
1. Pindah ke direktori `/frontend`.
2. Jalankan instalasi modul node dan konfigurasi env:
   ```bash
   pnpm install
   cp .env.example .env # Pastikan API_URL mengarah ke backend (http://localhost:8000/api)
   ```
3. Jalankan server development:
   ```bash
   pnpm dev
   ```
   *(Untuk dokumentasi setup frontend selengkapnya, baca [Panduan Frontend](/frontend/README.md))*

---

## 👥 Akun Demo untuk Pengujian

Gunakan kredensial akun bawaan berikut untuk menguji coba autentikasi sistem:

| Peran (Role) | Email | Password | Kegunaan |
| :--- | :--- | :--- | :--- |
| **Admin** | `john.doe@example.com` | `password` | Mengakses dashboard analitik & CRUD panel di `/admin` |
| **Customer** | `customer@example.com` | `password` | Belanja, manajemen alamat, melacak keranjang & checkout |

---

## 📚 Dokumentasi API

Anda dapat mengakses dokumentasi API pada endpoint berikut di Backend:

- [Swagger UI](#) (`/api/documentation`)
- [OpenAPI Specification](#) (`/docs?api-docs.json`)

## 📖 Panduan Pengembangan Lanjutan

Untuk kontributor/developer baru, harap membaca dokumentasi onboarding khusus di masing-masing sub-proyek untuk memahami konvensi koding (seperti pola BFF Proxy, Shared Validations, dan model attribute-based Eloquent):

- 👉 **[PANDUAN ONBOARDING FRONTEND (Nuxt)](/frontend/README.md)**
- 👉 **[PANDUAN ONBOARDING BACKEND (Laravel)](/backend/README.md)**
