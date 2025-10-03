# 🧹 Panduan Pembersihan Database Daftar Tagihan Kontainer Sewa

## ⚠️ PERINGATAN PENTING

**BACKUP DATABASE TERLEBIH DAHULU!**
Script ini akan menghapus **SEMUA DATA** dari tabel `daftar_tagihan_kontainer_sewa`. Pastikan Anda sudah membackup database sebelum melanjutkan.

## 📁 File Pembersihan yang Tersedia

Saya telah menyiapkan **3 metode** untuk membersihkan database:

### 1. 🔧 **Script PHP** - `clean_database_tagihan.php`

-   **Fitur**: Interactive script dengan konfirmasi
-   **Keamanan**: Meminta konfirmasi 'YES' sebelum menghapus
-   **Output**: Menampilkan progress dan hasil pembersihan

### 2. ⚡ **Artisan Command** - `CleanTagihanKontainerSewa.php`

-   **Fitur**: Laravel command dengan backup otomatis
-   **Keamanan**: Konfirmasi built-in dan opsi force
-   **Output**: Structured logging dan error handling

### 3. 🗃️ **Script SQL** - `clean_database.sql`

-   **Fitur**: Direct SQL execution
-   **Keamanan**: Manual execution dengan review
-   **Output**: Query results dengan verifikasi

## 🚀 Cara Menggunakan

### Metode 1: Script PHP (Direkomendasikan)

```bash
# Masuk ke direktori project
cd c:\folder_kerjaan\aypsis

# Jalankan script
php clean_database_tagihan.php
```

**Proses yang terjadi:**

1. ✅ Menampilkan jumlah data saat ini
2. ⚠️ Meminta konfirmasi dengan mengetik 'YES'
3. 🔄 Menghapus semua data dengan transaction
4. 🔄 Reset auto increment ID ke 1
5. ✅ Verifikasi hasil pembersihan

### Metode 2: Artisan Command

```bash
# Command dasar dengan konfirmasi
php artisan tagihan:clean

# Command dengan backup otomatis
php artisan tagihan:clean --backup

# Command tanpa konfirmasi (hati-hati!)
php artisan tagihan:clean --force

# Command lengkap dengan backup dan force
php artisan tagihan:clean --backup --force
```

**Opsi yang tersedia:**

-   `--backup`: Membuat backup SQL sebelum pembersihan
-   `--force`: Skip konfirmasi (gunakan dengan hati-hati)

### Metode 3: Script SQL

1. **Buka phpMyAdmin** atau MySQL client
2. **Pilih database** aypsis
3. **Copy-paste** isi file `clean_database.sql`
4. **Review query** sebelum menjalankan
5. **Execute** script SQL

## 📊 Yang Akan Terjadi Setelah Pembersihan

### ✅ Database Bersih:

-   **Jumlah records**: 0
-   **Auto increment**: Reset ke 1
-   **Table structure**: Tetap utuh
-   **Foreign keys**: Tetap aktif

### 🎯 Siap untuk Import:

-   Import CSV baru akan dimulai dari ID 1
-   Tidak ada konflik data lama
-   Performance optimal
-   Grouping fresh dari awal

## 🛡️ Safety Features

### Script PHP & Artisan:

-   ✅ **Transaction support** - Rollback jika error
-   ✅ **Confirmation prompt** - Mencegah penghapusan tidak sengaja
-   ✅ **Error handling** - Menampilkan pesan error yang jelas
-   ✅ **Verification** - Mengecek hasil pembersihan
-   ✅ **Backup option** - Membuat backup otomatis (Artisan)

### Script SQL:

-   ✅ **Manual review** - Anda bisa review query sebelum execute
-   ✅ **Step by step** - Setiap langkah terlihat jelas
-   ✅ **Verification queries** - Menampilkan hasil sebelum dan sesudah

## 💡 Rekomendasi Workflow

### Untuk Development:

```bash
# 1. Backup (opsional untuk dev)
php artisan tagihan:clean --backup

# 2. Import data baru
# Gunakan fitur import di web interface
```

### Untuk Production:

```bash
# 1. WAJIB backup database lengkap terlebih dahulu
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Bersihkan dengan backup
php artisan tagihan:clean --backup

# 3. Verifikasi hasil
php artisan tinker
>>> App\Models\DaftarTagihanKontainerSewa::count()
```

## 🔍 Troubleshooting

### Error: "Foreign key constraint fails"

```bash
# Jalankan manual dengan disable foreign key
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM daftar_tagihan_kontainer_sewa;
SET FOREIGN_KEY_CHECKS = 1;
```

### Error: "Permission denied"

```bash
# Pastikan permission file
chmod +x clean_database_tagihan.php

# Atau jalankan dengan sudo jika perlu
sudo php clean_database_tagihan.php
```

### Error: "Table doesn't exist"

```bash
# Cek apakah tabel ada
php artisan tinker
>>> Schema::hasTable('daftar_tagihan_kontainer_sewa')
```

## ✅ Checklist Sebelum Pembersihan

-   [ ] ✅ Database sudah dibackup
-   [ ] ✅ Pastikan tidak ada user yang sedang menggunakan sistem
-   [ ] ✅ Verifikasi environment (dev/staging/production)
-   [ ] ✅ Siapkan data import baru jika diperlukan
-   [ ] ✅ Informasikan ke tim jika production

## 🎯 Setelah Pembersihan

1. **Verifikasi** database sudah bersih
2. **Test import** dengan file CSV kecil terlebih dahulu
3. **Import data** yang sudah disiapkan
4. **Verify hasil** import dan grouping
5. **Test functionality** yang berkaitan dengan tagihan

**Database siap untuk import data fresh!** 🚀
