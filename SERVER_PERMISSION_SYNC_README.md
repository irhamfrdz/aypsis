# Server Permission Synchronization Scripts

Scripts untuk menyamakan permission antara environment lokal dan server, khususnya untuk memperbaiki masalah Master Data menu yang tidak muncul di sidebar user_admin.

## 📋 Daftar Script

### 1. `sync_server_permissions.php`
**Tujuan**: Menyamakan permission di server dengan permission yang ada di lokal
**Fungsi**:
- Membaca permission yang ada di server
- Membandingkan dengan daftar permission yang lengkap
- Menambahkan permission yang hilang
- Melaporkan hasil sinkronisasi

### 2. `assign_all_permissions_to_user_admin.php`
**Tujuan**: Memastikan user_admin memiliki semua permission
**Fungsi**:
- Mencari user_admin
- Melepaskan semua permission lama
- Memberikan semua permission yang ada di sistem
- Memastikan user_admin memiliki role admin
- Verifikasi permission kritikal

### 3. `final_verification_and_cache_clear.php`
**Tujuan**: Verifikasi akhir dan membersihkan cache
**Fungsi**:
- Memverifikasi setup user_admin
- Mengecek permission dan role
- Membersihkan berbagai cache Laravel
- Memberikan instruksi akhir

## 🚀 Cara Penggunaan

### Langkah 1: Upload Script ke Server
Upload ketiga script ke folder root aplikasi Laravel di server:
```
scp sync_server_permissions.php user@server:/path/to/laravel/app/
scp assign_all_permissions_to_user_admin.php user@server:/path/to/laravel/app/
scp final_verification_and_cache_clear.php user@server:/path/to/laravel/app/
```

### Langkah 2: Jalankan Sinkronisasi Permission
```bash
php sync_server_permissions.php
```
Script ini akan:
- ✅ Menambahkan permission yang hilang
- 📊 Melaporkan berapa permission yang ditambahkan
- 🎯 Memverifikasi permission kritikal untuk Master Data

### Langkah 3: Assign Permission ke user_admin
```bash
php assign_all_permissions_to_user_admin.php
```
Script ini akan:
- 🔍 Mencari user_admin
- 🔧 Memberikan semua permission
- ✅ Memastikan role admin
- 📊 Verifikasi permission kritikal

### Langkah 4: Verifikasi Akhir & Clear Cache
```bash
php final_verification_and_cache_clear.php
```
Script ini akan:
- 🔍 Verifikasi setup lengkap
- 🔧 Clear semua cache Laravel
- 🎯 Memberikan instruksi testing

## 📊 Permission Kritikal untuk Master Data

Script akan memverifikasi permission berikut untuk memastikan Master Data muncul di sidebar:

- `master-karyawan-view`
- `master-user-view`
- `master-kontainer-view`
- `master-pricelist-sewa-kontainer-view`
- `master-tujuan-view`
- `master-kegiatan-view`
- `master-permission-view`
- `master-mobil-view`
- `master-divisi-view`
- `master-cabang-view`
- `master-pekerjaan-view`
- `master-pajak-view`
- `master-bank-view`
- `master-coa-view`

## ✅ Testing Setelah Menjalankan Script

1. **Login sebagai user_admin**
2. **Periksa sidebar** - Master Data menu harus muncul
3. **Klik menu Master Data** - harus bisa diakses
4. **Test submenu** - semua submenu harus bisa diakses
5. **Verifikasi halaman** - pastikan tidak ada error 403

## 🔧 Troubleshooting

### Jika Master Data masih tidak muncul:
1. Jalankan ulang `final_verification_and_cache_clear.php`
2. Periksa log Laravel untuk error
3. Pastikan user_admin ada dan aktif
4. Verifikasi permission di database

### Jika ada error saat menjalankan script:
1. Pastikan koneksi database benar
2. Periksa permission file PHP
3. Jalankan `composer install` jika perlu
4. Periksa versi PHP dan Laravel

## 📝 Catatan

- Script menggunakan model Laravel (User, Permission, Role)
- Pastikan `.env` sudah dikonfigurasi dengan benar
- Backup database sebelum menjalankan script
- Script aman untuk dijalankan berulang kali
- Semua permission akan di-sync tanpa menghapus permission existing

## 🎯 Hasil yang Diharapkan

Setelah menjalankan semua script:
- ✅ user_admin memiliki semua permission
- ✅ Master Data menu muncul di sidebar
- ✅ Semua cache sudah dibersihkan
- ✅ Aplikasi siap untuk testing

---

**Versi**: 1.0
**Tanggal**: $(date)
**Dibuat untuk**: Aplikasi AYPSIS - Permission Synchronization