# Pranota Kontainer Sewa Edit Permission Seeder

Seeder ini dibuat untuk memastikan permission `pranota-kontainer-sewa-edit` tersedia di sistem dan diberikan ke user admin.

## File yang Dibuat

### 1. `database/seeders/PranotaKontainerSewaEditPermissionSeeder.php`
Seeder khusus untuk menambahkan permission edit pranota kontainer sewa.

**Fungsi:**
- Memastikan permission `pranota-kontainer-sewa-edit` ada di database
- Memberikan permission tersebut ke semua user admin
- Menggunakan Spatie Laravel Permission package

### 2. Update `database/seeders/DatabaseSeeder.php`
Menambahkan `PranotaKontainerSewaEditPermissionSeeder::class` ke dalam daftar seeder yang akan dijalankan.

## Permission yang Ditambahkan

```
pranota-kontainer-sewa-edit - Edit Pranota Kontainer Sewa
```

## Cara Menjalankan

### Jalankan seeder spesifik:
```bash
php artisan db:seed --class=PranotaKontainerSewaEditPermissionSeeder
```

### Jalankan semua seeder (termasuk yang baru):
```bash
php artisan db:seed
```

## Verifikasi

Untuk memverifikasi bahwa permission sudah diberikan dengan benar:

```bash
php check_current_user_permission.php
```

Masukkan username `admin` saat diminta, dan pastikan permission `pranota-kontainer-sewa-edit` muncul dalam daftar permissions user.

## Route yang Menggunakan Permission Ini

- `GET pranota-kontainer-sewa/{pranota}/edit` - Menampilkan form edit pranota kontainer sewa
- `PUT pranota-kontainer-sewa/{pranota}` - Menyimpan perubahan pranota kontainer sewa

## Status

✅ **Permission sudah tersedia dan diberikan ke user admin**
✅ **Seeder berhasil dibuat dan terdaftar**
✅ **Verifikasi berhasil - permission aktif**