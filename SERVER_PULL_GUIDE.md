# 🚀 PERINTAH PULL DARI GIT KE SERVER
# Jalankan perintah ini satu per satu di server

## 📋 LANGKAH-LANGKAH UPDATE SERVER:

### 1. 📁 Masuk ke direktori aplikasi
```bash
cd /path/to/your/aypsis
# Ganti /path/to/your/aypsis dengan path sebenarnya di server
```

### 2. 💾 Backup database (PENTING!)
```bash
# Backup database sebelum update
php artisan backup:run --only-db

# Atau manual backup:
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 3. 🚧 Aktifkan maintenance mode
```bash
php artisan down --message="System Update in Progress" --retry=60
```

### 4. 📥 Pull perubahan dari git
```bash
git fetch origin
git pull origin main
```

### 5. 📦 Update dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 6. 🗄️ Jalankan migrasi database
```bash
php artisan migrate --force
```

### 7. 🧹 Clear semua cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 8. ⚡ Optimize aplikasi
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. 🔐 Set permissions yang tepat
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 10. ✅ Nonaktifkan maintenance mode
```bash
php artisan up
```

---

## 🎯 PERINTAH RINGKAS (Copy-Paste):

```bash
# Quick update commands for server
cd /path/to/your/aypsis
php artisan down --message="System Update" --retry=60
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan up
```

---

## ⚠️ CATATAN PENTING:

1. **Backup Database:** Selalu backup database sebelum update
2. **Maintenance Mode:** Mencegah user mengakses saat update
3. **Permissions:** Pastikan web server memiliki akses ke storage dan cache
4. **Path:** Ganti `/path/to/your/aypsis` dengan path sebenarnya
5. **User:** Pastikan menjalankan sebagai user yang tepat (biasanya www-data)

---

## 🔍 VERIFIKASI SETELAH UPDATE:

```bash
# Cek status aplikasi
php artisan --version
php artisan route:list | head -5
php artisan migrate:status

# Cek log jika ada error
tail -f storage/logs/laravel.log
```

---

## 🆘 ROLLBACK JIKA TERJADI ERROR:

```bash
# Rollback git ke commit sebelumnya
git log --oneline -5  # Lihat commit history
git reset --hard <previous-commit-hash>

# Restore database dari backup
mysql -u username -p database_name < backup_file.sql

# Clear cache dan restart
php artisan config:clear && php artisan cache:clear
php artisan up
```
