# 🔑 QUICK GUIDE: Menjalankan Permission Seeder di Server

# ======================================================

## 🚀 CARA CEPAT (Recommended)

```bash
# 1. Masuk ke direktori aplikasi
cd /path/to/your/aypsis

# 2. Set maintenance mode (opsional tapi direkomendasikan)
php artisan down --message="Installing Permissions" --retry=60

# 3. Pull latest changes dari git
git pull origin main

# 4. Update composer jika diperlukan
composer install --no-dev --optimize-autoloader

# 5. 🔥 JALANKAN SEEDER PERMISSION
php artisan db:seed --class=ComprehensiveSystemPermissionSeeder

# 6. Matikan maintenance mode
php artisan up

# 7. Verifikasi hasil
php artisan tinker --execute="echo 'Total permissions: ' . \App\Models\Permission::count();"
```

## 📋 VERIFIKASI DETAIL

```bash
# Cek total permissions
php artisan tinker
>>> App\Models\Permission::count()

# Cek sample permissions
>>> App\Models\Permission::whereIn('name', ['master-user-view', 'approval-dashboard', 'supir-dashboard'])->pluck('name', 'description')

# Cek permission by category
>>> App\Models\Permission::where('name', 'like', 'master-%')->count()
>>> App\Models\Permission::where('name', 'like', 'approval-%')->count()
>>> App\Models\Permission::where('name', 'like', 'tagihan-%')->count()

>>> exit
```

## ⚠️ TROUBLESHOOTING

**Jika error "Class not found":**

```bash
composer dump-autoload
php artisan config:clear
php artisan db:seed --class=ComprehensiveSystemPermissionSeeder
```

**Jika ingin reset permissions (HATI-HATI!):**

```bash
php artisan tinker
>>> App\Models\Permission::truncate();
>>> DB::table('user_permissions')->truncate();
>>> exit
php artisan db:seed --class=ComprehensiveSystemPermissionSeeder
```

## 🎯 YANG AKAN DITAMBAHKAN

-   ✅ **400+ permissions** lengkap untuk seluruh sistem
-   ✅ **14 kategori** utama (Master Data, Business Process, Approval, dll)
-   ✅ **Dual format** support (dash & dot notation)
-   ✅ **CRUD complete** untuk semua modul
-   ✅ **Auto duplicate check** - tidak akan menimpa yang sudah ada

## 📊 EXPECTED RESULTS

Setelah seeder berhasil, Anda akan memiliki permission untuk:

-   Master Data (User, Karyawan, Kontainer, dll)
-   Business Process (Pranota, Tagihan, Pembayaran)
-   Approval System (Multi-level approval)
-   System Functions (Dashboard, Profile, Admin)
-   Driver Functions (Supir dashboard & checkpoint)
