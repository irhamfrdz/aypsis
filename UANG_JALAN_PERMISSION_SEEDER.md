# Uang Jalan Permission Seeder

## 📋 Overview

Seeder ini dibuat untuk menambahkan permission uang jalan ke sistem yang sudah ada di laptop/server lain.

## 🚀 Cara Menjalankan

### Step 1: Pastikan file seeder sudah ada

Pastikan file-file berikut sudah ada di project:

-   `database/seeders/UangJalanPermissionSeeder.php`
-   `database/seeders/AssignUangJalanPermissionsToAdminSeeder.php`

### Step 2: Jalankan seeder permission (WAJIB)

```bash
php artisan db:seed --class=UangJalanPermissionSeeder
```

### Step 3: Jalankan seeder assign ke admin (OPSIONAL)

```bash
php artisan db:seed --class=AssignUangJalanPermissionsToAdminSeeder
```

### Step 4: Atau jalankan semua seeder sekaligus

Jika ingin menjalankan kedua seeder sekaligus, edit file `DatabaseSeeder.php`:

```php
public function run()
{
    $this->call([
        // ... seeder lain yang sudah ada
        UangJalanPermissionSeeder::class,
        AssignUangJalanPermissionsToAdminSeeder::class,
    ]);
}
```

Lalu jalankan:

```bash
php artisan db:seed
```

## 📝 Permission yang Ditambahkan

### Uang Jalan (7 permissions):

-   `uang-jalan-view` - Melihat data uang jalan
-   `uang-jalan-create` - Membuat data uang jalan baru
-   `uang-jalan-update` - Mengubah data uang jalan
-   `uang-jalan-delete` - Menghapus data uang jalan
-   `uang-jalan-approve` - Menyetujui data uang jalan
-   `uang-jalan-print` - Mencetak data uang jalan
-   `uang-jalan-export` - Mengexport data uang jalan

### Pranota Uang Jalan (7 permissions):

-   `pranota-uang-jalan-view` - Melihat data pranota uang jalan
-   `pranota-uang-jalan-create` - Membuat pranota uang jalan baru
-   `pranota-uang-jalan-update` - Mengubah data pranota uang jalan
-   `pranota-uang-jalan-delete` - Menghapus data pranota uang jalan
-   `pranota-uang-jalan-approve` - Menyetujui pranota uang jalan
-   `pranota-uang-jalan-print` - Mencetak pranota uang jalan
-   `pranota-uang-jalan-export` - Mengexport data pranota uang jalan

**Total: 14 permissions**

## 🛡️ Admin User Detection

Seeder `AssignUangJalanPermissionsToAdminSeeder` akan mencari user dengan username:

-   `admin`
-   `administrator`
-   `superadmin`

Jika username admin Anda berbeda, edit file seeder atau jalankan manual assignment.

## ⚠️ Troubleshooting

### Error: Permission already exists

Seeder sudah menangani duplikasi, jadi aman dijalankan berkali-kali.

### Error: No admin users found

Edit file `AssignUangJalanPermissionsToAdminSeeder.php` dan ubah query pencarian admin user:

```php
$adminUsers = User::where('username', 'nama_admin_anda')->get();
```

### Error: Migration belum dijalankan

Pastikan migration untuk tabel `uang_jalans` dan `pranota_uang_jalans` sudah dijalankan:

```bash
php artisan migrate
```

## 🔧 Verifikasi

Setelah menjalankan seeder:

1. **Cek di database:**

    ```sql
    SELECT * FROM permissions WHERE name LIKE '%uang-jalan%';
    ```

2. **Cek di User Management:**

    - Login sebagai admin
    - Buka Menu User → Edit User
    - Lihat section "Operational" → "Uang Jalan" & "Pranota Uang Jalan"

3. **Test akses menu:**
    - Coba akses menu Uang Jalan
    - Coba akses menu Pranota Uang Jalan

## 📞 Support

Jika ada masalah:

1. Cek log Laravel: `storage/logs/laravel.log`
2. Jalankan dengan verbose: `php artisan db:seed --class=UangJalanPermissionSeeder -v`
3. Cek struktur database apakah tabel permissions ada dan benar

---

_Generated: November 6, 2025_
