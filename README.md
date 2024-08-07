
# Proyek Laravel dengan JWT

## Deskripsi

Ini adalah proyek Laravel yang menyediakan fitur otentikasi menggunakan JSON Web Tokens (JWT). Proyek ini dirancang untuk memberikan akses aman ke API dengan menggunakan token JWT.

## Prerequisites

Sebelum Anda mulai, pastikan Anda memiliki perangkat lunak berikut yang terpasang:

- PHP 8.3 atau versi lebih baru
- Composer
- PostgreSQL
- Nginx

## Clone Repository

Untuk menarik proyek dari GitHub, ikuti langkah-langkah berikut:

1. **Clone repository**

   ```bash
   git clone https://github.com/username/repository.git
   ```

Gantilah `username` dan `repository` dengan nama pengguna GitHub dan nama repository yang sesuai.

2. **Masuk ke direktori proyek**

   ```bash
   cd repository
   ```

## Setup Proyek

1. **Install dependencies**

   Pastikan Composer sudah terinstal, kemudian jalankan perintah berikut untuk menginstal semua dependensi:

   ```bash
   composer install
   ```

2. **Salin file `.env.example` ke `.env`**

   ```bash
   cp .env.example .env
   ```

3. **Generate kunci aplikasi**

   ```bash
   php artisan key:generate
   ```

4. **Konfigurasi JWT**

   Install paket JWT dengan Composer:

   ```bash
   composer require tymon/jwt-auth
   ```

   Publikasikan file konfigurasi JWT:

   ```bash
   php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   ```

   Tambahkan kunci JWT ke file `.env`:

   ```env
   JWT_SECRET=your_jwt_secret_key
   ```

   Generate kunci JWT:

   ```bash
   php artisan jwt:secret
   ```

5. **Konfigurasi lingkungan**

   Edit file `.env` sesuai kebutuhan Anda. Pastikan Anda mengkonfigurasi database dan pengaturan lainnya sesuai dengan lingkungan pengembangan atau produksi Anda.

6. **Migrasi database**

   Jalankan perintah berikut untuk menjalankan migrasi database:

   ```bash
   php artisan migrate
   ```

7. **Jalankan seeder (opsional)**

   Jika ada data awal yang perlu dimasukkan ke database, jalankan perintah berikut:

   ```bash
   php artisan db:seed
   ```

## Menjalankan Proyek

Setelah setup selesai, Anda bisa menjalankan server lokal menggunakan perintah berikut:

```bash
php artisan serve
```

Proyek akan tersedia di `http://localhost:8000`.

## Otentikasi JWT

Untuk menggunakan JWT dalam proyek ini:

1. **Mendaftar**

   Kirim permintaan POST ke endpoint `/api/register` dengan data pengguna. Setelah pendaftaran, Anda akan mendapatkan token JWT.

2. **Masuk**

   Kirim permintaan POST ke endpoint `/api/login` dengan kredensial pengguna. Anda akan menerima token JWT yang dapat digunakan untuk mengakses endpoint yang dilindungi.

3. **Akses Endpoint yang Dilindungi**

   Sertakan token JWT dalam header `Authorization` sebagai `Bearer {{token}}` untuk mengakses endpoint yang memerlukan otentikasi.

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini, silakan ikuti pedoman kontribusi yang terdapat di [CONTRIBUTING.md](CONTRIBUTING.md).

## Lisensi

Proyek ini dilisensikan di bawah [Lisensi MIT](LICENSE).

## Kontak

Untuk pertanyaan lebih lanjut, hubungi [email@domain.com](mailto:email@domain.com).
```

Silakan sesuaikan bagian seperti `your_jwt_secret_key` dengan nilai kunci JWT yang sesuai, dan tambahkan informasi tambahan yang relevan dengan proyek Anda.
