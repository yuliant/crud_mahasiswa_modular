# CRUD Mahasiswa

Aplikasi **Laravel 10** dengan arsitektur **modular monolith** menggunakan
[`nwidart/laravel-modules` versi 10](https://laravelmodules.com/docs/10).
Satu aplikasi, satu database MySQL, dan dua modul: **Prodi** dan **Mahasiswa**.

## Fitur

- CRUD prodi (`id`, `nama_prodi`).
- CRUD mahasiswa (`id`, `id_prodi`, `nama`, `nim`).
- Setiap mahasiswa wajib terhubung dengan satu prodi; satu prodi dapat memiliki banyak mahasiswa.
- NIM wajib unik dan disimpan sebagai teks agar angka nol di depan tetap tersimpan.
- Prodi yang masih digunakan mahasiswa tidak dapat dihapus.
- Validasi formulir, pesan hasil operasi, dan paginasi 10 data per halaman.
- Bootstrap 5 lokal, navbar, tabel, dan halaman konfirmasi hapus.
  Tanpa JavaScript, DataTables, CDN, atau build frontend Node/Vite.

## Persyaratan

- PHP 8.2+ sesuai dependensi yang dikunci di `composer.lock`, beserta `pdo_mysql`, `mbstring`,
  `openssl`, `fileinfo`, `xml`, `dom`, `ctype`, `tokenizer`, dan `curl`.
- Composer 2.
- MySQL 5.7+ atau MariaDB yang kompatibel.
- Git untuk mengambil repositori.

## Instalasi setelah clone dari GitHub

### 1. Clone dan pasang dependensi

```bash
git clone https://github.com/yuliant/crud_mahasiswa_modular.git
cd crud_mahasiswa_modular
composer install
```

Gunakan `composer install` agar versi dependensi mengikuti `composer.lock` yang
disertakan. Paket modul sudah terpasang melalui Composer; tidak perlu menjalankan
`composer require`, membuat ulang modul, atau memublikasikan konfigurasi paket.

### 2. Buat konfigurasi lokal

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

Edit `.env` sesuai akun MySQL lokal:

```dotenv
APP_NAME="CRUD Mahasiswa"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_mahasiswa
DB_USERNAME=root
DB_PASSWORD=
```

`root` tanpa password hanyalah contoh konfigurasi lokal. Sesuaikan username,
password, host, dan port dengan server Anda. File `.env` tidak disertakan ke Git.

### 3. Buat database

Jalankan MySQL/MariaDB terlebih dahulu melalui Laragon, XAMPP, atau service MySQL.
Kemudian jalankan SQL berikut melalui phpMyAdmin atau klien MySQL:

```sql
CREATE DATABASE IF NOT EXISTS crud_mahasiswa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Untuk klien terminal, masuk menggunakan `mysql -u root -p`, lalu jalankan SQL di
atas. Gunakan database kosong saat instalasi pertama; jangan menimpa tabel dari
proyek lain yang kebetulan memakai nama database sama.

### 4. Buat application key dan jalankan migrasi

```bash
php artisan key:generate
php artisan optimize:clear
php artisan module:list
php artisan migrate
```

Pastikan modul **Prodi** dan **Mahasiswa** berstatus aktif. Status keduanya sudah
disimpan pada `modules_statuses.json` dalam repositori.

Migrasi di dalam modul dimuat oleh masing-masing service provider, sehingga cukup
menggunakan `php artisan migrate`. Laravel membuat tabel `prodi` lebih dahulu,
kemudian `mahasiswa` dengan foreign key `id_prodi`, beserta tabel `migrations`.
Tidak ada tabel autentikasi atau data contoh yang wajib diimpor.

### 5. Jalankan aplikasi

```bash
php artisan serve
```

Buka [http://127.0.0.1:8000](http://127.0.0.1:8000). Halaman awal mengarah ke daftar
mahasiswa. Tambahkan prodi terlebih dahulu melalui navbar **Prodi**, kemudian
tambahkan mahasiswa melalui navbar **Mahasiswa**.

Tidak perlu menjalankan `npm install`, `npm run dev`, atau `npm run build`.
Bootstrap tersedia di `public/css/bootstrap.min.css` sehingga tampilan tetap
berfungsi tanpa akses internet setelah dependensi PHP terpasang.

Jika menggunakan Apache/Nginx atau virtual host Laragon, arahkan document root ke
direktori `public`, lalu sesuaikan `APP_URL`.

## Struktur modular monolith

```text
Modules/
├── Prodi/
│   ├── Database/Migrations/
│   ├── Entities/Prodi.php
│   ├── Http/Controllers/ProdiController.php
│   ├── Providers/ProdiServiceProvider.php
│   ├── Resources/views/
│   ├── Routes/web.php
│   └── module.json
└── Mahasiswa/
    ├── Database/Migrations/
    ├── Entities/Mahasiswa.php
    ├── Http/Controllers/MahasiswaController.php
    ├── Providers/MahasiswaServiceProvider.php
    ├── Resources/views/
    ├── Routes/web.php
    └── module.json
```

Paket modul menemukan modul melalui `module.json` dan mendaftarkan service
provider-nya. Namespace `Modules\` diarahkan ke folder `Modules/` melalui PSR-4 di
`composer.json`. Tiap provider memuat route, view, dan migrasi modulnya. Layout navbar
dan halaman konfirmasi hapus dipakai bersama dari `resources/views`.

Integrasi kedua modul menggunakan relasi Eloquent secara eksplisit:
`Mahasiswa::prodi()` adalah `belongsTo`, sedangkan `Prodi::mahasiswa()` adalah
`hasMany`. Kedua modul dipakai bersama; modul Prodi dibutuhkan oleh Mahasiswa.
Tidak ada panggilan HTTP antarfitur atau service terpisah.

Kolom data mengikuti kebutuhan aplikasi, tanpa `created_at` dan `updated_at`.
Nama prodi dan nama mahasiswa maksimal 100 karakter; NIM maksimal 30 karakter.
Penghapusan prodi dilindungi oleh pemeriksaan aplikasi dan foreign key database
dengan aturan `RESTRICT`. Pindahkan atau hapus mahasiswa terkait sebelum menghapus
prodinya.

## Pengujian

Pengujian menggunakan SQLite **di memori**, terpisah dari database MySQL
`crud_mahasiswa`. Aktifkan ekstensi `pdo_sqlite` pada PHP CLI sebelum menjalankan:

```bash
php artisan test
```

Jika memakai PHP Laragon yang memiliki DLL SQLite tetapi belum mengaktifkannya,
jalankan pengujian langsung di Windows tanpa mengubah `php.ini`:

```powershell
php -d extension=pdo_sqlite vendor/bin/phpunit
```

Pengujian mencakup pemuatan modul, halaman daftar dan formulir, CRUD kedua entitas,
validasi, keunikan NIM, relasi prodi, paginasi, dan larangan menghapus prodi yang
masih memiliki mahasiswa. Untuk memeriksa route dan migrasi:

```bash
php artisan route:list
php artisan migrate:status
```

## Pemecahan masalah

- **Connection refused:** jalankan MySQL dan cocokkan host/port di `.env`.
- **Unknown database:** buat database `crud_mahasiswa` terlebih dahulu.
- **Access denied:** sesuaikan `DB_USERNAME` dan `DB_PASSWORD`.
- **Could not find driver:** aktifkan `pdo_mysql` untuk aplikasi atau `pdo_sqlite`
  untuk pengujian. Periksa PHP CLI dengan `php --ini` dan `php -m`.
- **Kelas modul tidak ditemukan:** jalankan `composer dump-autoload`, lalu
  `php artisan optimize:clear`. Pastikan folder `Modules` ikut ter-clone.
- **Perubahan `.env` belum terbaca:** jalankan `php artisan config:clear`.
- **Tampilan tanpa Bootstrap:** pastikan `public/css/bootstrap.min.css` ada dan
  document root web server mengarah ke `public`.

## Catatan versi framework

Laravel 10 digunakan sesuai kebutuhan proyek. Pada pemeriksaan dependensi saat
pembuatan proyek, `composer audit` masih melaporkan advisory pada Laravel 10.
Sebelum penggunaan produksi, tinjau hasil audit dan rencanakan pembaruan framework
ke versi yang memperoleh perbaikan keamanan.
