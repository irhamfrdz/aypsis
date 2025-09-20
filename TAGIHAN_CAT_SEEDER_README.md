# Tagihan CAT Permissions Seeder

Seeder lengkap untuk membuat semua permission yang diperlukan untuk modul **Tagihan CAT**.

## 📋 Permission yang Dibuat

### Permission Utama

-   `tagihan-cat` - Akses modul Tagihan CAT
-   `tagihan-cat-view` - Melihat daftar Tagihan CAT
-   `tagihan-cat-create` - Membuat Tagihan CAT baru
-   `tagihan-cat-update` - Mengupdate data Tagihan CAT
-   `tagihan-cat-delete` - Menghapus data Tagihan CAT
-   `tagihan-cat-show` - Melihat detail Tagihan CAT

### Permission Route-Based

-   `tagihan-cat-index` - Akses halaman index
-   `tagihan-cat-store` - Menyimpan data baru
-   `tagihan-cat-edit` - Akses halaman edit
-   `tagihan-cat-destroy` - Menghapus data

### Permission Tambahan

-   `tagihan-cat-print` - Mencetak Tagihan CAT
-   `tagihan-cat-export` - Mengekspor data
-   `tagihan-cat-import` - Mengimpor data
-   `tagihan-cat-approve` - Menyetujui Tagihan CAT
-   `tagihan-cat-reject` - Menolak Tagihan CAT
-   `tagihan-cat-history` - Melihat riwayat
-   `tagihan-cat-template` - Menggunakan template
-   `tagihan-cat-single` - Cetak tunggal
-   `tagihan-cat-bulk` - Operasi bulk

### Permission Massal

-   `tagihan-cat-mass-approve` - Persetujuan massal
-   `tagihan-cat-mass-reject` - Penolakan massal
-   `tagihan-cat-mass-print` - Pencetakan massal
-   `tagihan-cat-mass-export` - Ekspor massal

### Permission Berdasarkan Status

-   `tagihan-cat-status-pending` - Mengelola status pending
-   `tagihan-cat-status-approved` - Mengelola status approved
-   `tagihan-cat-status-rejected` - Mengelola status rejected
-   `tagihan-cat-status-paid` - Mengelola status paid
-   `tagihan-cat-status-cancelled` - Mengelola status cancelled

### Permission Advanced

-   `tagihan-cat-admin` - Akses admin
-   `tagihan-cat-supervisor` - Akses supervisor
-   `tagihan-cat-manager` - Akses manager
-   `tagihan-cat-audit` - Akses audit
-   `tagihan-cat-report` - Akses laporan
-   `tagihan-cat-dashboard` - Akses dashboard
-   `tagihan-cat-analytics` - Akses analitik
-   `tagihan-cat-settings` - Akses pengaturan

## 🚀 Cara Menjalankan Seeder

### Jalankan seeder spesifik:

```bash
php artisan db:seed --class=TagihanCatPermissionsSeeder
```

### Atau jalankan semua seeder:

```bash
php artisan db:seed
```

### Jalankan dengan force (untuk production):

```bash
php artisan db:seed --class=TagihanCatPermissionsSeeder --force
```

## ✅ Fitur Seeder

-   ✅ **Anti-duplikasi**: Tidak akan membuat permission yang sudah ada
-   ✅ **Format konsisten**: Menggunakan format dash (-) untuk konsistensi
-   ✅ **Auto-assign**: Otomatis assign ke user admin jika ada
-   ✅ **Informative output**: Menampilkan progress dan hasil
-   ✅ **Error handling**: Menangani kasus edge case

## 🔧 Konfigurasi

Seeder ini akan:

1. Mengecek permission yang sudah ada
2. Membuat permission baru yang belum ada
3. Menampilkan daftar permission yang dibuat
4. Assign semua permission ke user admin
5. Menampilkan total permission Tagihan CAT

## 📊 Output Contoh

```
Created 25 new Tagihan CAT permissions:
  - tagihan-cat-view: Melihat daftar Tagihan CAT
  - tagihan-cat-create: Membuat Tagihan CAT baru
  - ...

Total Tagihan CAT permissions in database: 33
Assigned 33 Tagihan CAT permissions to admin user.
Tagihan CAT permissions seeder completed successfully!
```

## ⚠️ Catatan Penting

-   Seeder menggunakan format **dash (-)** untuk konsistensi dengan sistem permission yang ada
-   Permission akan di-assign ke user dengan username `admin` jika ada
-   Seeder aman untuk dijalankan berulang kali (tidak akan membuat duplikasi)
-   Pastikan model `Permission` dan `User` sudah ada sebelum menjalankan seeder

## 🔗 Integration

Seeder ini terintegrasi dengan:

-   **UserController**: Untuk konversi permission matrix
-   **Permission Matrix**: Di halaman edit user
-   **Middleware**: Untuk authorization
-   **Blade Templates**: Untuk conditional display

## 📝 Changelog

### v2.0.0 (Current)

-   Menggunakan format dash (-) untuk konsistensi
-   Menambahkan 25+ permission baru
-   Improved error handling
-   Better output formatting
-   Auto-assign ke admin user

### v1.0.0

-   Basic CRUD permissions
-   Route-based permissions
-   Simple assignment logic
