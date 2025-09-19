# Kode Nomor Permission Seeder

Seeder ini digunakan untuk menambahkan permission yang diperlukan untuk fitur Master Kode Nomor pada aplikasi AYPSIS.

## Permission yang Akan Ditambahkan

Seeder ini akan menambahkan permission berikut ke dalam database:

-   `master-kode-nomor` - Manajemen Kode Nomor
-   `master-kode-nomor.view` - Melihat data kode nomor
-   `master-kode-nomor.create` - Membuat kode nomor baru
-   `master-kode-nomor.store` - Menyimpan kode nomor baru
-   `master-kode-nomor.show` - Melihat detail kode nomor
-   `master-kode-nomor.edit` - Mengedit kode nomor
-   `master-kode-nomor.update` - Memperbarui kode nomor
-   `master-kode-nomor.delete` - Menghapus kode nomor

## Cara Menjalankan

### Opsi 1: Jalankan Bersama Semua Seeder

```bash
php artisan db:seed
```

### Opsi 2: Jalankan Seeder Tertentu Saja

```bash
php artisan db:seed --class=KodeNomorPermissionSeeder
```

### Opsi 3: Jalankan Seeder Secara Terpisah (Fresh Install)

```bash
php artisan db:seed --class=KodeNomorPermissionSeeder --force
```

## Verifikasi

Setelah menjalankan seeder, Anda dapat memverifikasi bahwa permission telah berhasil ditambahkan dengan menjalankan:

```bash
php artisan tinker
```

Kemudian di dalam tinker:

```php
Permission::where('name', 'like', '%kode-nomor%')->get()
```

## Catatan

-   Seeder menggunakan `firstOrCreate()` sehingga aman dijalankan berulang kali
-   Permission yang sudah ada tidak akan dibuat duplikat
-   Seeder ini sudah terdaftar di `DatabaseSeeder.php` sehingga akan dijalankan otomatis saat `php artisan db:seed`

## File Terkait

-   `database/seeders/KodeNomorPermissionSeeder.php` - File seeder utama
-   `database/seeders/DatabaseSeeder.php` - Sudah terdaftar di sini
-   `app/Http/Controllers/KodeNomorController.php` - Controller yang menggunakan permission ini
-   `routes/web.php` - Routes yang menggunakan middleware permission ini
