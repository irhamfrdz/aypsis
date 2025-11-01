# Panduan Mengatasi Error Migration di Server

## Error yang Muncul
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'surat_jalan_approvals' already exists
```

## Solusi yang Sudah Diterapkan

Saya telah memperbaiki 3 migration files dengan menambahkan pengecekan tabel:

1. **2025_10_14_120000_create_surat_jalan_approvals_table.php**
   - Menambahkan `if (!Schema::hasTable('surat_jalan_approvals'))`
   - Tabel hanya dibuat jika belum ada

2. **2025_10_14_121000_add_checkpoint_status_to_surat_jalans.php**
   - Menambahkan try-catch untuk menangani error jika kolom sudah diupdate
   - Tidak akan error jika status sudah berisi nilai yang sama

3. **2025_10_14_122000_create_pranota_surat_jalans_table.php**
   - Menambahkan `if (!Schema::hasTable('pranota_surat_jalans'))`
   - Tabel hanya dibuat jika belum ada

## Langkah-Langkah di Server

### Opsi 1: Pull Update Terbaru (Recommended)

```bash
# 1. Pull perubahan terbaru dari GitHub
git pull origin main

# 2. Jalankan migration lagi
php artisan migrate

# Migration sekarang akan skip tabel yang sudah ada
```

### Opsi 2: Reset Migration (Jika Opsi 1 Gagal)

```bash
# 1. Cek migration yang sudah dijalankan
php artisan migrate:status

# 2. Tandai migration bermasalah sebagai sudah dijalankan (tanpa execute)
php artisan db:seed --class=MarkMigrationAsRunSeeder

# 3. Atau rollback migration tertentu
php artisan migrate:rollback --step=1

# 4. Jalankan migrate lagi
php artisan migrate
```

### Opsi 3: Manual Skip Migration

Jika masih error, tandai migration sebagai sudah dijalankan:

```bash
# Masuk ke MySQL
mysql -u your_username -p your_database

# Tambahkan record ke tabel migrations
INSERT INTO migrations (migration, batch) VALUES 
('2025_10_14_120000_create_surat_jalan_approvals_table', 1),
('2025_10_14_121000_add_checkpoint_status_to_surat_jalans', 1),
('2025_10_14_122000_create_pranota_surat_jalans_table', 1);

# Keluar
exit;

# Jalankan migration sisanya
php artisan migrate
```

## Verifikasi

Setelah berhasil migrate, cek:

```bash
# Cek status semua migrations
php artisan migrate:status

# Semua harus menunjukkan status "Ran"
```

## Catatan Penting

- ✅ Migration files sudah diperbaiki dan di-push ke GitHub
- ✅ Tidak akan error lagi jika tabel sudah ada
- ✅ Aman untuk dijalankan berulang kali
- ⚠️  Jangan drop tabel yang sudah ada kecuali diperlukan
- ⚠️  Backup database sebelum melakukan operasi migration

## Troubleshooting

### Jika masih error setelah pull:

```bash
# Clear cache composer
composer dump-autoload

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Coba migrate lagi
php artisan migrate
```

### Jika perlu rollback total:

```bash
# ⚠️ HATI-HATI: Ini akan menghapus semua data!
php artisan migrate:fresh

# Atau rollback semua dan migrate ulang
php artisan migrate:reset
php artisan migrate
```

## Kontak

Jika masih ada masalah, hubungi developer atau cek log error di:
- `storage/logs/laravel.log`
- Server error logs

---
**Last Updated**: October 31, 2025
**Commit**: 214ae89
