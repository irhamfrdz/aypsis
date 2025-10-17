# Setup Permissions Vendor Kontainer Sewa

Script ini digunakan untuk menambahkan permissions Master Vendor Kontainer Sewa ke database dan assign ke user admin.

## 📋 Daftar Permissions

Script ini akan membuat dan assign 4 permissions berikut:

1. `vendor-kontainer-sewa-view` - Melihat data vendor kontainer sewa
2. `vendor-kontainer-sewa-create` - Menambah data vendor kontainer sewa
3. `vendor-kontainer-sewa-edit` - Mengedit data vendor kontainer sewa
4. `vendor-kontainer-sewa-delete` - Menghapus data vendor kontainer sewa

## 🚀 Cara Menjalankan

### Di Server Development (Local)

```bash
php run_vendor_kontainer_sewa_permissions.php
```

### Di Server Production

```bash
# Masuk ke direktori aplikasi
cd /path/to/your/laravel/app

# Jalankan script
php run_vendor_kontainer_sewa_permissions.php
```

## 📊 Output Script

Script akan menampilkan log yang detail seperti ini:

```
[2025-10-17 10:25:32] [INFO] 🚀 Memulai setup permissions Vendor Kontainer Sewa...
[2025-10-17 10:25:32] [INFO] 📋 Mengecek dan menambahkan permissions...
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-view' berhasil ditambahkan
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-create' berhasil ditambahkan
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-edit' berhasil ditambahkan
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-delete' berhasil ditambahkan
[2025-10-17 10:25:32] [INFO] 📊 Permissions baru: 4, sudah ada: 0
[2025-10-17 10:25:32] [INFO] 👤 Mencari user admin...
[2025-10-17 10:25:32] [SUCCESS] ✅ User admin ditemukan: admin (ID: 1)
[2025-10-17 10:25:32] [INFO] 🔐 Assign permissions ke user admin...
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-view' berhasil di-assign ke admin
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-create' berhasil di-assign ke admin
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-edit' berhasil di-assign ke admin
[2025-10-17 10:25:32] [SUCCESS] ✅ Permission 'vendor-kontainer-sewa-delete' berhasil di-assign ke admin
[2025-10-17 10:25:32] [INFO] 📊 Assignments baru: 4, sudah ada: 0
[2025-10-17 10:25:32] [INFO] 🔍 Melakukan verifikasi final...
[2025-10-17 10:25:32] [INFO] 📝 Permissions vendor kontainer sewa yang berhasil di-assign:
[2025-10-17 10:25:32] [SUCCESS]    ✓ vendor-kontainer-sewa-view
[2025-10-17 10:25:32] [SUCCESS]    ✓ vendor-kontainer-sewa-create
[2025-10-17 10:25:32] [SUCCESS]    ✓ vendor-kontainer-sewa-edit
[2025-10-17 10:25:32] [SUCCESS]    ✓ vendor-kontainer-sewa-delete
[2025-10-17 10:25:32] [SUCCESS] 🎉 Setup permissions vendor kontainer sewa berhasil lengkap!
[2025-10-17 10:25:32] [INFO] 📈 Total permissions: 4
[2025-10-17 10:25:32] [INFO] 🔓 User admin sekarang memiliki akses penuh ke menu Vendor Kontainer Sewa

======================================================================
🚀 SETUP BERHASIL DISELESAIKAN!
Menu Vendor Kontainer Sewa sudah siap digunakan oleh user admin.
URL: http://your-domain.com/vendor-kontainer-sewa
======================================================================
[2025-10-17 10:25:32] [INFO] ✅ Script selesai dijalankan.
```

## ⚡ Fitur Script

-   **✅ Idempotent**: Script dapat dijalankan berulang kali tanpa menimbulkan error
-   **📊 Logging Detail**: Menampilkan log lengkap dengan timestamp dan status
-   **🔍 Verifikasi**: Melakukan verifikasi final untuk memastikan semua permissions berhasil di-assign
-   **⚠️ Error Handling**: Menangani error dengan baik dan memberikan pesan yang jelas
-   **🎯 User Detection**: Mencari user admin dengan username 'admin' atau email 'admin@admin.com'

## ❗ Troubleshooting

### User Admin Tidak Ditemukan

Jika mendapat error "User admin tidak ditemukan!", pastikan:

1. Ada user dengan username 'admin', atau
2. Ada user dengan email 'admin@admin.com'

### Permission Sudah Ada

Jika permission sudah ada, script akan skip dan menampilkan pesan "sudah ada". Ini normal dan tidak menimbulkan error.

### Database Connection Error

Pastikan:

1. File `.env` sudah dikonfigurasi dengan benar
2. Database server sedang berjalan
3. Koneksi database dapat diakses

## 🗂️ Files Terkait

-   `run_vendor_kontainer_sewa_permissions.php` - Script utama
-   `app/Models/VendorKontainerSewa.php` - Model
-   `app/Http/Controllers/VendorKontainerSewaController.php` - Controller
-   `resources/views/vendor-kontainer-sewa/` - Views directory
-   `database/migrations/*_create_vendor_kontainer_sewas_table.php` - Migration

## 🔗 Setelah Setup

Setelah script berhasil dijalankan:

1. **Login sebagai admin** ke aplikasi
2. **Akses menu** "Master Vendor Kontainer Sewa" di sidebar
3. **URL direct**: `http://your-domain.com/vendor-kontainer-sewa`
4. **Test CRUD operations**: Create, Read, Update, Delete

## 📞 Support

Jika ada masalah dengan script ini, silakan check:

1. Log output script untuk detail error
2. Laravel log di `storage/logs/laravel.log`
3. Database permissions dan connection
