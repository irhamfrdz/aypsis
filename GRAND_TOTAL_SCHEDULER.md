# Auto Grand Total Recalculation

## 📋 Overview

Command `tagihan:recalculate-grand-total` akan dijalankan secara otomatis **setiap 1 jam sekali** untuk memastikan semua grand_total di database selalu akurat.

## ⚙️ Setup (di Server)

### 1. Setup Laravel Scheduler

Jalankan script setup cron:

```bash
chmod +x setup-cron.sh
./setup-cron.sh
```

Atau manual setup cron job:

```bash
crontab -e
```

Tambahkan baris ini:

```cron
* * * * * cd /path/to/aypsis && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Verifikasi Cron Job

```bash
# Lihat crontab yang aktif
crontab -l

# Test manual
php artisan schedule:run
```

## 🕐 Jadwal Otomatis

| Task                          | Jadwal            | Deskripsi                                   |
| ----------------------------- | ----------------- | ------------------------------------------- |
| **Grand Total Recalculation** | Setiap 1 jam      | Recalculate grand_total untuk semua tagihan |
| Periode Sync                  | Setiap hari 02:10 | Sync periode tagihan                        |
| Create Next Periode           | Setiap hari 03:00 | Buat periode baru                           |
| Update Container Periods      | Setiap hari 01:00 | Update periode kontainer                    |
| Validate Duplicates           | Setiap hari 04:00 | Validasi duplikat kontainer                 |

## 📊 Monitoring

### 1. Cek Log

```bash
# Log grand total recalculation
tail -f storage/logs/grand-total-recalculation.log

# Log Laravel
tail -f storage/logs/laravel.log
```

### 2. Manual Run

```bash
# Run semua scheduled tasks
php artisan schedule:run

# Run specific command
php artisan tagihan:recalculate-grand-total --force

# Run dengan output verbose
php artisan tagihan:recalculate-grand-total
```

## 🔍 Troubleshooting

### Scheduler Tidak Berjalan

1. **Cek cron service**:

    ```bash
    systemctl status cron  # Ubuntu/Debian
    systemctl status crond # CentOS/RHEL
    ```

2. **Cek log cron**:

    ```bash
    sudo tail -f /var/log/syslog | grep CRON  # Ubuntu
    sudo tail -f /var/log/cron                # CentOS
    ```

3. **Cek permission**:
    ```bash
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    ```

### Command Gagal

1. **Cek manual**:

    ```bash
    php artisan tagihan:recalculate-grand-total --force
    ```

2. **Cek database connection**:

    ```bash
    php artisan tinker
    >>> \DB::connection()->getPdo();
    ```

3. **Cek log error**:
    ```bash
    tail -f storage/logs/grand-total-recalculation.log
    ```

## 📈 Performance

-   **Proses per chunk**: 100 records
-   **Average time**: ~10 seconds untuk 765 records
-   **Memory usage**: < 50MB
-   **Database transactions**: Yes (rollback on error)

## 🔐 Security

-   Command menggunakan `withoutOverlapping()` untuk mencegah concurrent execution
-   Database transactions untuk data integrity
-   Logging semua aktivitas

## 💡 Tips

1. **Monitoring**: Setup monitoring alert jika ada error di log
2. **Backup**: Backup database sebelum first run
3. **Testing**: Test di staging environment dulu
4. **Schedule Time**: Jadwal di jam non-peak hours jika perlu

## 📝 Notes

-   Scheduler membutuhkan cron job yang running every minute
-   Command akan skip jika tidak ada perubahan data
-   Log disimpan di `storage/logs/grand-total-recalculation.log`
-   Dapat disesuaikan jadwalnya di `app/Console/Kernel.php`

## 🔄 Update Schedule

Untuk mengubah jadwal, edit file `app/Console/Kernel.php`:

```php
// Setiap 1 jam
$schedule->command('tagihan:recalculate-grand-total --force')->hourly();

// Setiap 30 menit
$schedule->command('tagihan:recalculate-grand-total --force')->everyThirtyMinutes();

// Setiap hari jam 2 pagi
$schedule->command('tagihan:recalculate-grand-total --force')->dailyAt('02:00');

// Setiap 6 jam
$schedule->command('tagihan:recalculate-grand-total --force')->everySixHours();
```

## 🆘 Support

Jika ada masalah, hubungi tim development atau cek:

-   Laravel Logs: `storage/logs/laravel.log`
-   Scheduler Logs: `storage/logs/grand-total-recalculation.log`
-   Audit Logs: Check `audit_logs` table
