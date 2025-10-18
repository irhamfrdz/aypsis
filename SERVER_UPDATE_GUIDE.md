# 🚀 PANDUAN SERVER UPDATE - MENGATASI MIGRATION CONFLICTS

## ⚠️ MASALAH YANG DISELESAIKAN

### Migration Conflicts Fixed:
1. ✅ **kontainer_sewas table** - Migration duplikat dihapus
2. ✅ **vendor_kontainer_sewas table** - Migration duplikat dihapus

### Migration Files Removed:
- `2025_10_17_100910_create_kontainer_sewas_table.php`
- `2025_10_17_100954_create_kontainer_sewas_table.php`
- `2025_10_17_101113_create_vendor_kontainer_sewas_table.php`
- `2025_10_17_101847_create_vendor_kontainer_sewas_table.php`

## 🔧 LANGKAH UNTUK SERVER

### 1. PULL LATEST CHANGES (MANUAL)
```bash
# Masuk ke direktori project Laravel
cd /path/to/your/laravel/project

# Pull latest changes
git pull origin main
```

### 2. MENGGUNAKAN SCRIPT OTOMATIS (RECOMMENDED)
```bash
# Upload script server_pull_and_migrate.sh ke server
# Berikan permission execute
chmod +x server_pull_and_migrate.sh

# Jalankan script
./server_pull_and_migrate.sh
```

### 3. MANUAL MIGRATION (Jika Script Tidak Digunakan)
```bash
# Cek status migration
php artisan migrate:status

# Jalankan migration
php artisan migrate --force

# Jika masih ada error "table already exists", lakukan:
# - Cek table mana yang bermasalah
# - Skip migration yang conflict dengan menandai sebagai "migrated"

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 🛠️ TROUBLESHOOTING

### Jika Masih Ada Error "Table Already Exists":

#### Opsi 1: Manual Skip Migration
```bash
# Cek migration mana yang bermasalah
php artisan migrate:status

# Insert manual ke migrations table untuk skip
# Contoh untuk migration yang bermasalah:
php artisan tinker

# Di dalam tinker:
DB::table('migrations')->insert([
    'migration' => '2025_XX_XX_XXXXXX_nama_migration_bermasalah',
    'batch' => DB::table('migrations')->max('batch') + 1
]);
exit
```

#### Opsi 2: Reset Specific Migration
```bash
# Rollback migration tertentu (HATI-HATI!)
php artisan migrate:rollback --step=1

# Atau rollback ke batch tertentu
php artisan migrate:rollback --batch=X
```

#### Opsi 3: Fresh Migration (DANGER - HAPUS SEMUA DATA!)
```bash
# HANYA untuk development/testing - AKAN HAPUS SEMUA DATA!
php artisan migrate:fresh

# Dengan seeder jika ada
php artisan migrate:fresh --seed
```

## ✅ VERIFIKASI SETELAH MIGRATION

### 1. Cek Migration Status
```bash
php artisan migrate:status
# Pastikan semua migration status: "Ran"
```

### 2. Test Database Connection
```bash
php artisan tinker
# Test: DB::table('users')->count();
```

### 3. Test Audit Trail (Jika Sudah Implement)
```bash
# Setup audit permissions
php setup_audit_permissions_server.php

# Test audit functionality
php test_audit_log_implementation.php
```

### 4. Test Website
- Login sebagai admin
- Cek menu "Audit Log" (jika sudah implement)
- Test CRUD operations
- Verify tombol "Riwayat" muncul di halaman master data

## 📋 CHECKLIST SERVER UPDATE

- [ ] Pull latest changes dari repository
- [ ] Check migration status: `php artisan migrate:status`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear all caches
- [ ] Set proper file permissions (775 untuk storage/)
- [ ] Test database connection
- [ ] Test website functionality
- [ ] Test audit trail features (jika sudah implement)
- [ ] Verify no errors in Laravel logs

## 🆘 EMERGENCY ROLLBACK

Jika update menyebabkan masalah besar:

```bash
# Rollback ke commit sebelumnya
git log --oneline -5  # Lihat 5 commit terakhir
git reset --hard COMMIT_HASH_SEBELUMNYA

# Restore database dari backup (jika ada)
mysql -u username -p database_name < backup_file.sql

# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 📞 SUPPORT

Jika masih ada masalah:
1. Cek Laravel logs: `tail -f storage/logs/laravel.log`
2. Cek web server error logs
3. Pastikan file permissions correct
4. Verify database connection settings di .env

---

**Last Updated**: 18 Oktober 2025  
**Status**: Migration conflicts resolved  
**Ready for Production**: ✅ Yes