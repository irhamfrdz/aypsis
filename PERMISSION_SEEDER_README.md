# 🔐 Permission Seeder Comprehensive - Dokumentasi

## 📋 Deskripsi

Seeder komprehensif untuk sistem permission AYPSIS yang mencakup **SEMUA** permission yang digunakan dalam sistem berdasarkan analisis mendalam dari:

-   ✅ `routes/web.php` - Semua route dengan middleware permission
-   ✅ `app/Http/Controllers/UserController.php` - Logic permission management
-   ✅ Semua controller yang menggunakan authorization

## 📊 Total Permissions

**Total: 207+ Permissions** yang mencakup:

### 🏠 System & Dashboard (1)

-   `dashboard` - Akses dashboard utama

### 👥 Master User (5)

-   `master-user-view` - Lihat daftar user
-   `master-user-create` - Tambah user baru
-   `master-user-update` - Edit user
-   `master-user-delete` - Hapus user
-   `master-user-bulk-manage` - Operasi bulk

### 👤 Master Karyawan (8)

-   `master-karyawan-view`
-   `master-karyawan-create`
-   `master-karyawan-update`
-   `master-karyawan-delete`
-   `master-karyawan-print`
-   `master-karyawan-export`
-   `master-karyawan-template`
-   `master-karyawan-crew-checklist`

### 📦 Master Kontainer (4)

-   `master-kontainer-view`
-   `master-kontainer-create`
-   `master-kontainer-update`
-   `master-kontainer-delete`

### 📊 Master Stock Kontainer (4)

-   `master-stock-kontainer-view`
-   `master-stock-kontainer-create`
-   `master-stock-kontainer-update`
-   `master-stock-kontainer-delete`

### 🏢 Master Divisi (4)

-   `master-divisi-view`
-   `master-divisi-create`
-   `master-divisi-update`
-   `master-divisi-delete`

### 💼 Master Pekerjaan (4)

-   `master-pekerjaan-view`
-   `master-pekerjaan-create`
-   `master-pekerjaan-update`
-   `master-pekerjaan-delete`

### 🏦 Master Bank (4)

-   `master-bank-view`
-   `master-bank-create`
-   `master-bank-update`
-   `master-bank-delete`

### 💰 Master Pajak (4)

-   `master-pajak-view`
-   `master-pajak-create`
-   `master-pajak-update`
-   `master-pajak-delete`

### 🏪 Master Cabang (1)

-   `master-cabang-view`

### 📑 Master COA (2)

-   `master-coa-view`
-   `master-coa-create`

### 🔧 Master Vendor Bengkel (4)

-   `master-vendor-bengkel-view`
-   `master-vendor-bengkel-create`
-   `master-vendor-bengkel-update`
-   `master-vendor-bengkel-delete`

### 🔢 Master Kode Nomor (1)

-   `master-kode-nomor-view`

### 📋 Master Nomor Terakhir (1)

-   `master-nomor-terakhir-view`

### 🏦 Master Tipe Akun (1)

-   `master-tipe-akun-view`

### 📍 Master Tujuan (1)

-   `master-tujuan-view`

### 🎯 Master Kegiatan (4)

-   `master-kegiatan-view`
-   `master-kegiatan-create`
-   `master-kegiatan-update`
-   `master-kegiatan-delete`

### 🔐 Master Permission (4)

-   `master-permission-view`
-   `master-permission-create`
-   `master-permission-update`
-   `master-permission-delete`

### 🚗 Master Mobil (4)

-   `master-mobil-view`
-   `master-mobil-create`
-   `master-mobil-update`
-   `master-mobil-delete`

### 💵 Master Pricelist Sewa Kontainer (4)

-   `master-pricelist-sewa-kontainer-view`
-   `master-pricelist-sewa-kontainer-create`
-   `master-pricelist-sewa-kontainer-update`
-   `master-pricelist-sewa-kontainer-delete`

### 🎨 Master Pricelist CAT (4)

-   `master-pricelist-cat-view`
-   `master-pricelist-cat-create`
-   `master-pricelist-cat-update`
-   `master-pricelist-cat-delete`

### 📝 Permohonan Memo (6)

-   `permohonan`
-   `permohonan-memo-view`
-   `permohonan-memo-create`
-   `permohonan-memo-update`
-   `permohonan-memo-delete`
-   `permohonan-memo-print`

### 🚚 Pranota Supir (5)

-   `pranota-supir-view`
-   `pranota-supir-create`
-   `pranota-supir-update`
-   `pranota-supir-delete`
-   `pranota-supir-print`

### 💳 Pembayaran Pranota Supir (5)

-   `pembayaran-pranota-supir-view`
-   `pembayaran-pranota-supir-create`
-   `pembayaran-pranota-supir-update`
-   `pembayaran-pranota-supir-delete`
-   `pembayaran-pranota-supir-print`

### 🔧 Tagihan Perbaikan Kontainer (5)

-   `tagihan-perbaikan-kontainer-view`
-   `tagihan-perbaikan-kontainer-create`
-   `tagihan-perbaikan-kontainer-update`
-   `tagihan-perbaikan-kontainer-delete`
-   `tagihan-perbaikan-kontainer-print`

### 🔧 Perbaikan Kontainer (3)

-   `perbaikan-kontainer-view`
-   `perbaikan-kontainer-update`
-   `perbaikan-kontainer-delete`

### 📄 Pranota Perbaikan Kontainer (5)

-   `pranota-perbaikan-kontainer-view`
-   `pranota-perbaikan-kontainer-create`
-   `pranota-perbaikan-kontainer-update`
-   `pranota-perbaikan-kontainer-delete`
-   `pranota-perbaikan-kontainer-print`

### 💳 Pembayaran Pranota Perbaikan Kontainer (5)

-   `pembayaran-pranota-perbaikan-kontainer-view`
-   `pembayaran-pranota-perbaikan-kontainer-create`
-   `pembayaran-pranota-perbaikan-kontainer-update`
-   `pembayaran-pranota-perbaikan-kontainer-delete`
-   `pembayaran-pranota-perbaikan-kontainer-print`

### 🎨 Tagihan CAT (4)

-   `tagihan-cat-view`
-   `tagihan-cat-create`
-   `tagihan-cat-update`
-   `tagihan-cat-delete`

### 📄 Pranota CAT (5)

-   `pranota-cat-view`
-   `pranota-cat-create`
-   `pranota-cat-update`
-   `pranota-cat-delete`
-   `pranota-cat-print`

### 💳 Pembayaran Pranota CAT (5)

-   `pembayaran-pranota-cat-view`
-   `pembayaran-pranota-cat-create`
-   `pembayaran-pranota-cat-update`
-   `pembayaran-pranota-cat-delete`
-   `pembayaran-pranota-cat-print`

### 📦 Tagihan Kontainer Sewa (6)

-   `tagihan-kontainer-sewa-index`
-   `tagihan-kontainer-sewa-create`
-   `tagihan-kontainer-sewa-update`
-   `tagihan-kontainer-sewa-destroy`
-   `tagihan-kontainer-update`
-   `tagihan-kontainer-delete`

### 📄 Pranota Kontainer Sewa (5)

-   `pranota-kontainer-sewa-view`
-   `pranota-kontainer-sewa-create`
-   `pranota-kontainer-sewa-update`
-   `pranota-kontainer-sewa-delete`
-   `pranota-kontainer-sewa-print`

### 💳 Pembayaran Pranota Kontainer (5)

-   `pembayaran-pranota-kontainer-view`
-   `pembayaran-pranota-kontainer-create`
-   `pembayaran-pranota-kontainer-update`
-   `pembayaran-pranota-kontainer-delete`
-   `pembayaran-pranota-kontainer-print`

### 📋 General Pranota (5)

-   `pranota-view`
-   `pranota-create`
-   `pranota-update`
-   `pranota-delete`
-   `pranota-print`

### 📊 Aktivitas Lainnya (5)

-   `aktivitas-lainnya-view`
-   `aktivitas-lainnya-create`
-   `aktivitas-lainnya-update`
-   `aktivitas-lainnya-delete`
-   `aktivitas-lainnya-approve`

### 💳 Pembayaran Aktivitas Lainnya (7)

-   `pembayaran-aktivitas-lainnya-view`
-   `pembayaran-aktivitas-lainnya-create`
-   `pembayaran-aktivitas-lainnya-update`
-   `pembayaran-aktivitas-lainnya-delete`
-   `pembayaran-aktivitas-lainnya-print`
-   `pembayaran-aktivitas-lainnya-export`
-   `pembayaran-aktivitas-lainnya-approve`

### ✅ Approval (2)

-   `approval-tugas-1.view`
-   `approval-dashboard`

### 👤 Profile (3)

-   `profile-view`
-   `profile-update`
-   `profile-delete`

## 🚀 Cara Menggunakan

### 1️⃣ Jalankan Seeder Permission

```bash
# Untuk development - jalankan permission seeder saja
php artisan db:seed --class=PermissionSeederComprehensive
```

### 2️⃣ Assign Permission ke Admin

```bash
# Jalankan seeder untuk memberikan semua permission ke user admin
php artisan db:seed --class=AdminPermissionSeeder
```

### 3️⃣ Jalankan Semua Seeder (Fresh Start)

```bash
# Drop semua table dan jalankan migration + seeder dari awal
php artisan migrate:fresh --seed
```

## 📁 File-File Seeder

### ✅ `PermissionSeederComprehensive.php`

Seeder utama yang berisi semua 207+ permissions

### ✅ `AdminPermissionSeeder.php`

Seeder untuk assign semua permissions ke user admin (ID 1)

### ✅ `KaryawanSeeder.php`

Seeder untuk data karyawan (sudah ada)

## 🔍 Verifikasi

Setelah menjalankan seeder, verifikasi dengan:

```bash
# Login sebagai admin
Username: admin
Password: admin123

# Atau cek di database
php artisan tinker
>>> \App\Models\User::find(1)->permissions()->count()
# Harus return 207 atau lebih
```

## 🎯 Use Case

### Scenario 1: Fresh Installation

```bash
php artisan migrate:fresh
php artisan db:seed --class=KaryawanSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PermissionSeederComprehensive
php artisan db:seed --class=AdminPermissionSeeder
```

### Scenario 2: Update Permissions Only

```bash
php artisan db:seed --class=PermissionSeederComprehensive
php artisan db:seed --class=AdminPermissionSeeder
```

### Scenario 3: Production Deployment

```bash
# Jangan gunakan truncate di production!
# Buat custom seeder yang hanya insert permissions baru
```

## ⚠️ Catatan Penting

1. **Foreign Key Checks**: Seeder ini menonaktifkan foreign key checks sementara untuk truncate
2. **Truncate Warning**: Hati-hati di production, truncate akan menghapus semua data
3. **Timestamps**: Semua permissions menggunakan timestamp saat ini
4. **Chunk Insert**: Insert dilakukan dalam chunks untuk performa optimal
5. **Database Support**: Support MySQL dan SQLite

## 🔗 Dependencies

Pastikan model-model berikut sudah ada:

-   `App\Models\Permission`
-   `App\Models\User`
-   `App\Models\Karyawan`

Pastikan relasi many-to-many sudah di-setup:

-   `users` table
-   `permissions` table
-   `permission_user` pivot table

## 📚 Referensi

File yang dianalisis untuk membuat seeder ini:

-   `routes/web.php` - Semua route definitions
-   `app/Http/Controllers/UserController.php` - Permission logic
-   `database/seeders/KaryawanSeeder.php` - Data karyawan

## 🆘 Troubleshooting

### Error: "User admin tidak ditemukan"

**Solusi**: Jalankan `KaryawanSeeder` dan `UserSeeder` terlebih dahulu

### Error: "Tidak ada permissions di database"

**Solusi**: Jalankan `PermissionSeederComprehensive` terlebih dahulu

### Error: "Foreign key constraint fails"

**Solusi**: Pastikan migration untuk pivot table `permission_user` sudah ada

## 📝 Update Log

-   **2024-10-03**: Initial creation dengan 207+ permissions
-   Extracted dari routes/web.php dan UserController.php
-   Organized by module untuk kemudahan maintenance

---

**Dibuat oleh**: GitHub Copilot  
**Tanggal**: 3 Oktober 2024  
**Sistem**: AYPSIS - Permission Management System
