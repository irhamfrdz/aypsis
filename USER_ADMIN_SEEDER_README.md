# User Admin Seeder

Seeder khusus untuk membuat user `user_admin` dengan semua permission yang tersedia di sistem.

## Deskripsi

`UserAdminSeeder` akan membuat user administrator dengan akses penuh ke semua fitur dan modul dalam sistem AYP SIS. User ini memiliki semua permission yang ada di database.

## Fitur

- ✅ Membuat user `user_admin` dengan semua permission
- ✅ Verifikasi otomatis jumlah permission yang di-assign
- ✅ Login credentials yang jelas dan mudah diingat
- ✅ Script runner untuk Linux/Mac dan Windows
- ✅ Integrasi dengan DatabaseSeeder utama

## Cara Penggunaan

### 1. Menggunakan Script Runner (Direkomendasikan)

#### Linux/Mac:
```bash
./run_user_admin_seeder.sh
```

#### Windows:
```batch
run_user_admin_seeder.bat
```

### 2. Menggunakan Artisan Command

```bash
php artisan db:seed --class=UserAdminSeeder
```

### 3. Melalui DatabaseSeeder

UserAdminSeeder sudah terintegrasi dengan `DatabaseSeeder` utama, sehingga akan otomatis dijalankan saat menjalankan:

```bash
php artisan db:seed
```

## Login Credentials

Setelah seeder berhasil dijalankan, Anda dapat login dengan:

- **Username:** `user_admin`
- **Password:** `admin123`
- **Status:** `approved` (auto-approved)

> ⚠️ **PENTING:** Segera ubah password default setelah login pertama kali untuk alasan keamanan!

## Prerequisites

Pastikan permission sudah ada di database sebelum menjalankan seeder ini. Jalankan seeder berikut terlebih dahulu jika belum:

```bash
php artisan db:seed --class=ComprehensivePermissionsSeeder
```

## Output yang Diharapkan

```
🚀 Starting user_admin seeding...
📊 Found 440 permissions in system
👤 Creating user_admin...
✅ user_admin created/updated successfully!
🔑 Login credentials:
   Username: user_admin
   Password: admin123
   Email: user_admin@aypsis.com
🔐 Permissions assigned: 440 (ALL permissions)
✅ Verification successful: All permissions assigned correctly
🎉 user_admin seeding completed successfully!
💡 Note: You can change the default password after first login for security.
```

## Troubleshooting

### Error: "No permissions found!"
**Solusi:** Jalankan `ComprehensivePermissionsSeeder` terlebih dahulu:
```bash
php artisan db:seed --class=ComprehensivePermissionsSeeder
```

### Error: "Class UserAdminSeeder not found"
**Solusi:** Pastikan file `database/seeders/UserAdminSeeder.php` ada dan Composer autoload sudah di-regenerate:
```bash
composer dump-autoload
```

### Permission tidak ter-assign dengan benar
**Solusi:** Periksa apakah model `User` dan `Permission` memiliki relasi `belongsToMany` yang benar, dan tabel pivot `user_permissions` ada.

## Keamanan

- Password default adalah `admin123`
- **WAJIB** mengubah password setelah login pertama
- User ini memiliki akses penuh ke semua modul sistem
- Pertimbangkan untuk membuat user admin terpisah untuk development dan production

## File yang Dibuat

1. `database/seeders/UserAdminSeeder.php` - Seeder utama
2. `run_user_admin_seeder.sh` - Script runner untuk Linux/Mac
3. `run_user_admin_seeder.bat` - Script runner untuk Windows
4. `USER_ADMIN_SEEDER_README.md` - Dokumentasi ini

## Integrasi dengan Sistem

UserAdminSeeder terintegrasi dengan:
- `DatabaseSeeder` utama
- Model `User` dan `Permission`
- Sistem permission matrix yang ada
- Script runner lainnya

## Lisensi

Seeder ini dibuat khusus untuk proyek AYP SIS.