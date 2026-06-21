# 🐘 Panduan Onboarding Backend — Adhivas E-Commerce API

Selamat datang di repositori backend **Adhivas E-Commerce**! Repositori ini dibangun menggunakan **Laravel v13.x** dan bertindak sebagai RESTful API Server yang melayani kebutuhan otentikasi JWT, pengelolaan produk, kategori, keranjang, pesanan, alamat, dan analitik dashboard.

---

## 🛠️ Stack Teknologi

- **Framework**: [Laravel v13.x](https://laravel.com/) (PHP ^8.3)
- **Database**: PostgreSQL (`pgsql`)
- **Authentication**: JWT Auth via [php-open-source-saver/jwt-auth](https://github.com/PHP-Open-Source-Saver/jwt-auth)
- **Testing**: [Pest PHP](https://pestphp.com/) (Pest v4+)
- **Code Style (Linter)**: [Laravel Pint](https://laravel.com/docs/11.x/pint)
- **PHP Static Analysis**: [PHPStan](https://phpstan.org/) (Larastan)

---

## 🚀 Langkah Memulai (Setup Cepat)

### 1. Prasyarat (Prerequisites)
- PHP >= 8.3 terinstal secara lokal.
- [Composer](https://getcomposer.org/) terinstal secara lokal.
- Server database PostgreSQL aktif dan berjalan.

### 2. Instalasi Dependensi
Jalankan perintah berikut di folder `backend/`:
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database Anda di dalam berkas `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=adhivas_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 4. Generasi Kunci Enkripsi & JWT Secret
```bash
# Membuat key Laravel APP_KEY
php artisan key:generate

# Membuat JWT Secret untuk otentikasi token
php artisan jwt:secret
```

### 5. Migrasi Database & Seeding Mock Data
Buat skema tabel dan isi database Anda dengan data uji awal:
```bash
php artisan migrate --seed
```

---

## 👥 Akun Demo Uji Coba (Seeders)

Setelah menjalankan seeder, akun berikut siap digunakan untuk uji coba autentikasi:

| Peran (Role) | Email | Password | Nama Lengkap |
| :--- | :--- | :--- | :--- |
| **Admin** | `john.doe@example.com` | `password` | John Doe |
| **Customer** | `customer@example.com` | `password` | Customer 1 |

---

## 💻 Menjalankan Server Lokal

Ada dua cara untuk menjalankan backend secara lokal:

### Opsi A: Mode Konkuren (Direkomendasikan)
Menjalankan server API, queue listener, dan pail log parser secara bersamaan:
```bash
composer dev
```

### Opsi B: Mode Standar
Hanya menjalankan web server API lokal pada port `http://localhost:8000`:
```bash
php artisan serve
```

---

## 📜 Dokumentasi Swagger

Anda dapat mengakses dokumentasi Swagger API pada endpoint berikut:

- [JSON](#) (`/docs?api-docs.json`)
- [Swagger UI](#) (`/api/documentation`)

---

## 🎨 Konvensi Pengkodingan & Pola Penting

Harap ikuti standar koding berikut saat menambahkan fitur baru ke backend:

### 1. PHP Attributes untuk Eloquent Models
Untuk menjaga kebersihan model, kami menggunakan PHP Attributes bawaan Laravel 11/12+ daripada mendefinisikan properti kelas secara tradisional:
- Gunakan `#[Fillable([...])]` di atas deklarasi kelas model untuk menentukan mass-assignable fields.
- Contoh model `Product`:
  ```php
  use Illuminate\Database\Eloquent\Attributes\Fillable;

  #[Fillable(['name', 'description', 'price', 'stock_quantity'])]
  class Product extends Model { ... }
  ```

### 2. Format Response API yang Terstandardisasi
Seluruh controller harus memperpanjang base controller `App\Http\Controllers\Controller` dan menggunakan helper `$this->response()` untuk konsistensi keluaran API:
```php
return $this->response($data, 'Pesan sukses berhasil dikembalikan', 200);
```

Format respons JSON yang dihasilkan akan selalu berupa:
```json
{
  "status": "success", // atau "error" jika status code >= 400
  "message": "Pesan sukses/error berhasil dikembalikan",
  "data": ...
}
```

### 3. Middleware & Keamanan Peran (Role-based Gate)
- Rute-rute API yang dilindungi harus dibungkus dengan middleware `auth:api` (menggunakan JWT guard).
- Pengecekan otorisasi peran pengguna di dalam controller menggunakan Laravel Gates:
  ```php
  // Membatasi akses hanya untuk pengguna Admin
  Gate::authorize('role:admin', Auth::user());

  // Membatasi akses hanya untuk pengguna Customer
  Gate::authorize('role:customer', Auth::user());
  ```
- Definisi Gate ini diregistrasikan di dalam `App\Providers\AppServiceProvider.php`.


### 4. Format & Gaya Kode (Laravel Pint)
Sebelum membuat pull request / commit, pastikan untuk memformat kode PHP Anda menggunakan Pint:
```bash
# Memformat seluruh berkas kode PHP
./vendor/bin/pint
```
