# Database Synchronization Seeder

Seeder ini digunakan untuk menyamakan database server dengan database laptop yang sudah lengkap.

## 📋 **Persiapan Sebelum Menjalankan**

1. **Backup database server** terlebih dahulu
2. **Pastikan koneksi database** sudah benar di file `.env`
3. **Test koneksi database** dengan `php artisan migrate:status`

## 🚀 **Cara Menjalankan Seeder**

### Opsi 1: Jalankan Semua Seeder Sekaligus (Direkomendasikan)

```bash
php artisan db:seed --class=DatabaseSyncSeeder
```

### Opsi 2: Jalankan Seeder Satu per Satu

```bash
# 1. Bersihkan data lama
php artisan db:seed --class=DatabaseCleanerSeeder

# 2. Tambahkan permissions lengkap
php artisan db:seed --class=CompletePermissionSeeder

# 3. Sync users
php artisan db:seed --class=SyncUserSeeder

# 4. Sync user permissions
php artisan db:seed --class=SyncUserPermissionSeeder
```

## 📊 **Yang Akan Dilakukan Seeder**

### 1. DatabaseCleanerSeeder

-   ✅ Hapus permissions lama yang tidak ada di laptop
-   ✅ Hapus user_permissions yang tidak valid
-   ✅ Hapus users yang tidak ada di laptop database

### 2. CompletePermissionSeeder

-   ✅ Tambahkan 381 permissions lengkap dari laptop
-   ✅ Permissions dengan format baru dan terstruktur
-   ✅ Semua permissions untuk master data, operational, dan system

### 3. SyncUserSeeder

-   ✅ Sync 7 users dari laptop database
-   ✅ User admin, staff, test, kiky123, dan users tambahan
-   ✅ Password sudah di-hash dengan bcrypt

### 4. SyncUserPermissionSeeder

-   ✅ Bersihkan semua user_permissions lama
-   ✅ Tambahkan user_permissions sesuai laptop
-   ✅ User admin memiliki semua 381 permissions
-   ✅ User lain memiliki permissions sesuai role

## 🔍 **Verifikasi Setelah Menjalankan**

### Cek Jumlah Data

```bash
# Cek jumlah permissions
php artisan tinker
>>> DB::table('permissions')->count()
>>> DB::table('users')->count()
>>> DB::table('user_permissions')->count()
```

### Cek Permissions User Admin

```bash
php artisan tinker
>>> $adminPermissions = DB::table('user_permissions')->where('user_id', 1)->count()
>>> echo "Admin has {$adminPermissions} permissions"
```

### Test Login

-   ✅ Login sebagai admin → harus memiliki semua akses
-   ✅ Login sebagai staff → harus memiliki akses terbatas
-   ✅ Login sebagai kiky123 → harus memiliki akses terbatas

## ⚠️ **Peringatan Penting**

1. **Backup wajib** sebelum menjalankan seeder
2. **Test di staging** terlebih dahulu jika memungkinkan
3. **Monitor performa** setelah migrasi karena data cukup besar
4. **Update kode aplikasi** jika ada perubahan struktur permissions

## 🔧 **Troubleshooting**

### Jika Seeder Gagal

```bash
# Cek error logs
tail -f storage/logs/laravel.log

# Reset dan jalankan ulang
php artisan migrate:reset
php artisan migrate
php artisan db:seed --class=DatabaseSyncSeeder
```

### Jika Permissions Tidak Muncul

```bash
# Clear cache aplikasi
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📞 **Support**

Jika ada masalah atau pertanyaan:

1. Cek log file Laravel
2. Pastikan database connection benar
3. Verifikasi versi PHP dan Laravel
4. Pastikan semua dependencies terinstall

---

**Seeder ini akan membuat database server 100% sama dengan database laptop!** 🎉
