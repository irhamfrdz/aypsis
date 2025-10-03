# 🧹 PEMBERSIHAN PERMISSION DATABASE - DOKUMENTASI

## 📊 Hasil Analisis

Berdasarkan analisis yang dilakukan pada **3 Oktober 2025**, ditemukan bahwa:

-   **Total Permission di Database**: 296
-   **Permission yang DIGUNAKAN**: 189 (63.85%)
-   **Permission yang TIDAK DIGUNAKAN**: 107 (36.15%)

## 🔍 Metodologi Analisis

Script analisis memeriksa penggunaan permission di:

1. **Routes** - Middleware `can:` dan `permission:`
2. **Views** - Directive `@can()`, method `hasPermissionTo()`, dan `can()`
3. **Controllers** - Authorization checks `$this->authorize()`, `Gate::authorize()`, dan `hasPermissionTo()`

## ❌ Daftar Permission yang Tidak Digunakan

### 📂 Admin Module (3 permissions)

| ID   | Nama Permission     | Keterangan |
| ---- | ------------------- | ---------- |
| 1369 | admin-debug         | -          |
| 1368 | admin-features      | -          |
| 1370 | admin-user-approval | -          |

### 📂 Master Module (64 permissions)

**Master Bank**

-   `master-bank-destroy` (ID: 471)
-   `master-bank-edit` (ID: 469)
-   `master-bank-index` (ID: 749)

**Master Cabang**

-   `master-cabang-create` (ID: 626)
-   `master-cabang-delete` (ID: 631)
-   `master-cabang-edit` (ID: 629)
-   `master-cabang-index` (ID: 1382)
-   `master-cabang-show` (ID: 628)
-   `master-cabang-update` (ID: 630)

**Master COA**

-   `master-coa-delete` (ID: 643)
-   `master-coa-destroy` (ID: 1043)
-   `master-coa-edit` (ID: 1042)
-   `master-coa-index` (ID: 1039)
-   `master-coa-update` (ID: 477)

**Master Karyawan**

-   `master-karyawan-destroy` (ID: 46)
-   `master-karyawan-edit` (ID: 44)
-   `master-karyawan-index` (ID: 40)

**Master Kegiatan**

-   `master-kegiatan-destroy` (ID: 74)
-   `master-kegiatan-edit` (ID: 72)
-   `master-kegiatan-index` (ID: 68)

**Master Kode Nomor**

-   `master-kode-nomor-create` (ID: 884)
-   `master-kode-nomor-delete` (ID: 889)
-   `master-kode-nomor-index` (ID: 1383)
-   `master-kode-nomor-update` (ID: 888)

**Master Kontainer**

-   `master-kontainer-destroy` (ID: 60)
-   `master-kontainer-edit` (ID: 58)
-   `master-kontainer-index` (ID: 54)

**Master Mobil**

-   `master-mobil-destroy` (ID: 90)
-   `master-mobil-edit` (ID: 88)
-   `master-mobil-index` (ID: 84)

**Master Nomor Terakhir**

-   `master-nomor-terakhir-create` (ID: 1324)
-   `master-nomor-terakhir-delete` (ID: 1329)
-   `master-nomor-terakhir-edit` (ID: 1327)
-   `master-nomor-terakhir-index` (ID: 1384)
-   `master-nomor-terakhir-show` (ID: 1326)
-   `master-nomor-terakhir-store` (ID: 1325)
-   `master-nomor-terakhir-update` (ID: 1328)

**Master Pajak**

-   `master-pajak-destroy` (ID: 621)
-   `master-pajak-edit` (ID: 620)
-   `master-pajak-show` (ID: 619)

**Master Pekerjaan**

-   `master-pekerjaan-destroy` (ID: 463)
-   `master-pekerjaan-edit` (ID: 461)
-   `master-pekerjaan-export` (ID: 395)
-   `master-pekerjaan-index` (ID: 457)
-   `master-pekerjaan-print` (ID: 394)

**Master Permission**

-   `master-permission-destroy` (ID: 83)
-   `master-permission-edit` (ID: 81)
-   `master-permission-index` (ID: 77)

**Master Pricelist Sewa Kontainer**

-   `master-pricelist-sewa-kontainer-destroy` (ID: 97)
-   `master-pricelist-sewa-kontainer-edit` (ID: 95)
-   `master-pricelist-sewa-kontainer-index` (ID: 91)

**Master Tipe Akun**

-   `master-tipe-akun-create` (ID: 902)
-   `master-tipe-akun-delete` (ID: 904)
-   `master-tipe-akun-index` (ID: 1385)
-   `master-tipe-akun-update` (ID: 903)

**Master Tujuan**

-   `master-tujuan-create` (ID: 62)
-   `master-tujuan-delete` (ID: 330)
-   `master-tujuan-destroy` (ID: 67)
-   `master-tujuan-edit` (ID: 65)
-   `master-tujuan-index` (ID: 61)
-   `master-tujuan-update` (ID: 66)

**Master User**

-   `master.user.destroy` (ID: 560)
-   `master.user.edit` (ID: 558)
-   `master.user.index` (ID: 562)

### 📂 Pembayaran Module (12 permissions)

**Pembayaran Pranota CAT**

-   `pembayaran-pranota-cat-approve` (ID: 1371)
-   `pembayaran-pranota-cat-create` (ID: 1214)
-   `pembayaran-pranota-cat-delete` (ID: 1216)
-   `pembayaran-pranota-cat-export` (ID: 1218)
-   `pembayaran-pranota-cat-print` (ID: 1217)
-   `pembayaran-pranota-cat-update` (ID: 1215)

**Pembayaran Pranota Kontainer**

-   `pembayaran-pranota-kontainer-approve` (ID: 1372)
-   `pembayaran-pranota-kontainer-export` (ID: 360)

**Pembayaran Pranota Perbaikan Kontainer**

-   `pembayaran-pranota-perbaikan-kontainer-approve` (ID: 1373)
-   `pembayaran-pranota-perbaikan-kontainer-export` (ID: 411)

**Pembayaran Pranota Supir**

-   `pembayaran-pranota-supir-approve` (ID: 308)
-   `pembayaran-pranota-supir-export` (ID: 310)

### 📂 Perbaikan Module (1 permission)

-   `perbaikan-kontainer-create` (ID: 397)

### 📂 Pranota Module (13 permissions)

-   `pranota-approve` (ID: 351)
-   `pranota-cat-approve` (ID: 1375)
-   `pranota-cat-export` (ID: 999)
-   `pranota-destroy` (ID: 429)
-   `pranota-export` (ID: 353)
-   `pranota-index` (ID: 424)
-   `pranota-kontainer-sewa-approve` (ID: 1376)
-   `pranota-kontainer-sewa-export` (ID: 1211)
-   `pranota-perbaikan-kontainer-approve` (ID: 1377)
-   `pranota-perbaikan-kontainer-export` (ID: 405)
-   `pranota-perbaikan-kontainer.export` (ID: 1225)
-   `pranota-supir-approve` (ID: 276)
-   `pranota-supir-export` (ID: 278)

### 📂 Tagihan Module (14 permissions)

**Tagihan CAT**

-   `tagihan-cat-approve` (ID: 1236)
-   `tagihan-cat-destroy` (ID: 1235)
-   `tagihan-cat-edit` (ID: 1234)
-   `tagihan-cat-export` (ID: 925)
-   `tagihan-cat-index` (ID: 1232)
-   `tagihan-cat-print` (ID: 924)

**Tagihan Kontainer**

-   `tagihan-kontainer-export` (ID: 271)
-   `tagihan-kontainer-print` (ID: 270)
-   `tagihan-kontainer-view` (ID: 265)

**Tagihan Kontainer Sewa**

-   `tagihan-kontainer-sewa-approve` (ID: 1378)
-   `tagihan-kontainer-sewa-export` (ID: 1380)
-   `tagihan-kontainer-sewa-print` (ID: 1379)

**Tagihan Perbaikan Kontainer**

-   `tagihan-perbaikan-kontainer-approve` (ID: 1319)
-   `tagihan-perbaikan-kontainer-export` (ID: 1321)

## 🗑️ Cara Pembersihan

### Opsi 1: Menggunakan Script Otomatis (Recommended)

```bash
php cleanup_unused_permissions_auto.php
```

Script ini akan:

1. ✅ Membuat backup otomatis dalam format JSON
2. ✅ Menampilkan preview permission yang akan dihapus
3. ✅ Meminta konfirmasi sebelum menghapus
4. ✅ Menghapus relasi di `user_permissions`
5. ✅ Menghapus relasi di `permission_role`
6. ✅ Menghapus permission dari tabel `permissions`
7. ✅ Menampilkan ringkasan hasil pembersihan

### Opsi 2: Manual via SQL

Jika ingin lebih kontrol, Anda bisa:

1. Backup database terlebih dahulu:

```bash
mysqldump -u root aypsis > backup_aypsis_before_cleanup.sql
```

2. Jalankan query SQL berikut:

```sql
-- Hapus relasi user_permissions
DELETE FROM user_permissions WHERE permission_id IN (
    1369,1368,1370,471,469,749,626,631,629,1382,628,630,643,1043,1042,
    1039,477,46,44,40,74,72,68,884,889,1383,888,60,58,54,90,88,84,
    1324,1329,1327,1384,1326,1325,1328,621,620,619,463,461,395,457,394,
    83,81,77,97,95,91,902,904,1385,903,62,330,67,65,61,66,560,558,562,
    1371,1214,1216,1218,1217,1215,1372,360,1373,411,308,310,
    397,
    351,1375,999,429,353,424,1376,1211,1377,405,276,278,1225,
    1236,1235,1234,925,1232,924,271,270,1378,1380,1379,265,1319,1321
);

-- Hapus relasi permission_role
DELETE FROM permission_role WHERE permission_id IN (
    1369,1368,1370,471,469,749,626,631,629,1382,628,630,643,1043,1042,
    1039,477,46,44,40,74,72,68,884,889,1383,888,60,58,54,90,88,84,
    1324,1329,1327,1384,1326,1325,1328,621,620,619,463,461,395,457,394,
    83,81,77,97,95,91,902,904,1385,903,62,330,67,65,61,66,560,558,562,
    1371,1214,1216,1218,1217,1215,1372,360,1373,411,308,310,
    397,
    351,1375,999,429,353,424,1376,1211,1377,405,276,278,1225,
    1236,1235,1234,925,1232,924,271,270,1378,1380,1379,265,1319,1321
);

-- Hapus permissions
DELETE FROM permissions WHERE id IN (
    1369,1368,1370,471,469,749,626,631,629,1382,628,630,643,1043,1042,
    1039,477,46,44,40,74,72,68,884,889,1383,888,60,58,54,90,88,84,
    1324,1329,1327,1384,1326,1325,1328,621,620,619,463,461,395,457,394,
    83,81,77,97,95,91,902,904,1385,903,62,330,67,65,61,66,560,558,562,
    1371,1214,1216,1218,1217,1215,1372,360,1373,411,308,310,
    397,
    351,1375,999,429,353,424,1376,1211,1377,405,276,278,1225,
    1236,1235,1234,925,1232,924,271,270,1378,1380,1379,265,1319,1321
);
```

## ⚠️ Penting!

### Sebelum Menjalankan Pembersihan:

1. ✅ **BACKUP DATABASE** - Sangat penting!
2. ✅ Pastikan aplikasi sedang tidak digunakan
3. ✅ Review daftar permission yang akan dihapus
4. ✅ Pastikan tidak ada permission kustom yang Anda butuhkan

### Setelah Pembersihan:

1. ✅ Test semua fitur aplikasi
2. ✅ Verifikasi user masih bisa akses menu yang seharusnya
3. ✅ Cek log error untuk memastikan tidak ada masalah
4. ✅ Simpan file backup dengan aman

## 🔄 Restore Jika Terjadi Masalah

Jika terjadi masalah setelah pembersihan:

### Dari JSON Backup (dibuat oleh script):

```php
php restore_permissions_from_backup.php backup_permissions_2025-10-03_XXXXXX.json
```

### Dari SQL Backup:

```bash
mysql -u root aypsis < backup_aypsis_before_cleanup.sql
```

## 📈 Manfaat Pembersihan

1. ✅ Database lebih bersih dan terorganisir
2. ✅ Mengurangi kebingungan dalam permission management
3. ✅ Meningkatkan performa query (sedikit)
4. ✅ Memudahkan maintenance ke depan
5. ✅ Menghilangkan permission duplikat/tidak terpakai

## 📝 Catatan Tambahan

### Permission dengan Format Ganda

Beberapa permission memiliki format ganda (dot notation vs dash notation):

-   `master-karyawan-view` vs `master.karyawan.index`
-   `master-user-view` vs `master.user.index`

Script hanya menghapus yang benar-benar tidak digunakan di routes, views, dan controllers.

### Permission yang Dipertahankan

Permission yang masih digunakan dan TIDAK akan dihapus:

-   Semua permission dengan prefix `master-*-view`, `master-*-create`, `master-*-update`, `master-*-delete` yang aktif digunakan
-   Permission untuk dashboard dan authentication
-   Permission untuk approval system
-   Permission yang digunakan dalam middleware routes

---

**Generated by**: AYP SIS Permission Cleanup Script  
**Date**: 3 Oktober 2025  
**Total Permissions to be Cleaned**: 107 (36.15%)  
**Backup File**: backup*permissions*\*.json
