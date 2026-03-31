# Flexible POS Base Module

Aplikasi POS berbasis Laravel yang dapat dikonfigurasi untuk berbagai jenis bisnis: apotek, retail, F&B, atau bisnis umum lainnya.

## Persyaratan

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Node.js & NPM

## Instalasi

```bash
# Clone dan masuk ke direktori
cd apotek-laravel

# Install dependensi PHP
composer install

# Install dependensi JS
npm install && npm run build

# Salin file environment
cp .env.example .env

# Generate app key
php artisan key:generate

# Konfigurasi database di .env
# DB_DATABASE=nama_database
# DB_USERNAME=user
# DB_PASSWORD=password

# Jalankan migration
php artisan migrate

# Jalankan seeder default
php artisan db:seed
```

## Konfigurasi Jenis Bisnis via ENV

Tambahkan variabel berikut di `.env` sebelum menjalankan seeder:

```env
APP_BUSINESS_TYPE=apotek   # apotek | retail | fnb | general (default)
```

## Seeder

### Seeder Default (General)
```bash
php artisan db:seed
```
Mengisi konfigurasi bisnis dengan preset `general` dan `is_setup_complete = false`. Setup wizard akan muncul saat pertama login.

### Seeder Per Jenis Bisnis
```bash
# Apotek
php artisan db:seed --class=ApotekPresetSeeder

# Retail
php artisan db:seed --class=RetailPresetSeeder

# F&B (Restoran/Kafe)
php artisan db:seed --class=FnbPresetSeeder
```
Seeder ini langsung mengisi konfigurasi lengkap dan men-set `is_setup_complete = true`, sehingga setup wizard tidak akan muncul.

## Setup Wizard

Saat pertama kali login (sebelum konfigurasi bisnis diisi), aplikasi akan otomatis redirect ke `/setup`. Di sini Anda dapat mengisi:
- Nama bisnis
- Jenis bisnis (apotek / retail / fnb / general)
- Alamat & telepon

Setelah setup selesai, label-label di seluruh aplikasi akan menyesuaikan dengan jenis bisnis yang dipilih.

## Konfigurasi Bisnis

Setelah setup, konfigurasi dapat diubah kapan saja melalui menu **Pengaturan → Konfigurasi Bisnis**. Tersedia opsi:
- Jenis bisnis & preset label
- Label kustom untuk setiap entitas (produk, lokasi, supplier, pelanggan, dll)
- Prefix kode transaksi
- Threshold stok rendah
- Footer struk

## Property-Based Tests

```bash
# Install eris/eris terlebih dahulu
composer require --dev eris/eris

# Jalankan property tests
php artisan test tests/Property
```

## Struktur Konfigurasi

Preset bisnis tersimpan di `config/business_presets.php`. Konfigurasi aktif tersimpan di tabel `business_configs` dan di-cache selama 1 jam.
