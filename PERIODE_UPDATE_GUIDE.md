# Sistem Update Periode Kontainer Otomatis

## 📝 Deskripsi

Sistem ini secara otomatis mengupdate periode untuk kontainer yang masih berjalan (kontainer yang belum memiliki tanggal akhir). Periode akan dihitung berdasarkan berapa bulan sudah berjalan sejak tanggal awal kontainer.

## 🚀 Cara Kerja

### Logika Perhitungan Periode:

1. **Kontainer dengan tanggal akhir**: Periode tetap sesuai database (tidak berubah)
2. **Kontainer tanpa tanggal akhir**: Periode dihitung otomatis dengan rumus:
    ```
    Periode = (Bulan selisih dari tanggal awal ke sekarang) + 1
    ```

### Contoh:

-   **Tanggal awal**: 2025-01-24
-   **Tanggal sekarang**: 2025-09-01
-   **Selisih**: 7 bulan
-   **Periode**: 7 + 1 = **8**

## 💻 Command Manual

### Menjalankan Update Manual:

```bash
php artisan kontainer:update-periods
```

### Preview Tanpa Menyimpan (Dry Run):

```bash
php artisan kontainer:update-periods --dry-run
```

## ⏰ Jadwal Otomatis

Command akan berjalan otomatis setiap hari pada **01:00** untuk memastikan periode selalu up-to-date.

### Jadwal Lengkap:

-   **01:00**: Update periode kontainer
-   **02:10**: Sync periode tagihan
-   **03:00**: Buat periode selanjutnya

## 📊 Output Example

```
🔄 Starting container period update...
📦 Found 607 ongoing containers

📊 CBHU5911444: 1 -> 8 (Start: 2025-01-24, 7 months)
📊 RXTU4540180: 1 -> 6 (Start: 2025-03-04, 5 months)
⏭️  GESU5114504: Period 18 unchanged (calculated: 18)

✅ UPDATE COMPLETED:
  📈 Updated: 504 containers
  ⏭️  Skipped: 103 containers
  ❌ Errors: 0 containers
```

## 🔍 Fitur Keamanan

-   **Hanya update jika periode baru lebih besar**: Tidak akan mengurangi periode yang sudah ada
-   **Dry run mode**: Preview changes sebelum apply
-   **Error handling**: Skip container dengan error tanpa stop proses
-   **Detailed logging**: Menampilkan detail setiap perubahan

## 📈 Monitoring

### Cek Command Terakhir:

```bash
php artisan schedule:list
```

### Cek Log (jika diaktifkan):

```bash
tail -f storage/logs/laravel.log
```

## ⚠️ Important Notes

1. **Backup Data**: Selalu backup database sebelum run manual pertama kali
2. **Test Mode**: Gunakan `--dry-run` untuk preview perubahan
3. **Database Cast**: Field `periode` di-cast sebagai integer di model
4. **Timezone**: Calculation menggunakan timezone server

## 🛠️ Troubleshooting

### Error "No containers found":

-   Pastikan ada kontainer dengan `tanggal_akhir = null`
-   Check format tanggal di database

### Period tidak berubah:

-   Cek apakah periode calculated > periode current
-   Gunakan `--dry-run` untuk debug

### Command tidak terdaftar:

```bash
php artisan cache:clear
composer dump-autoload
```
