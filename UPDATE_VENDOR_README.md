# Update Vendor Invoice dari CSV

Script ini digunakan untuk mengupdate nomor invoice vendor di database berdasarkan data dari file CSV.

## File yang Dibuat

1. **`update_vendor_from_csv.php`** - Script standalone untuk update vendor
2. **`app/Console/Commands/UpdateVendorFromCsv.php`** - Artisan command untuk update vendor
3. **`backup_vendor_data.php`** - Script backup data sebelum update
4. **`UPDATE_VENDOR_README.md`** - File dokumentasi ini

## Struktur CSV yang Dibutuhkan

File CSV harus memiliki kolom-kolom berikut (delimiter `;`):

```csv
Group;Kontainer;Awal;Akhir;Ukuran;Harga;Periode;Status;Hari;DPP;Keterangan;QTY Disc;adjustment;Pembulatan;ppn;pph;grand_total;No.InvoiceVendor;Tgl.InvVendor;No.Bank;Tgl.Bank;OK;Approval;x;Group;;;;;;;;x
```

**Kolom penting:**
- **Kontainer** - Nomor kontainer yang akan dicocokkan dengan database
- **No.InvoiceVendor** - Nomor invoice vendor yang akan diupdate
- **Tgl.InvVendor** - Tanggal invoice vendor (opsional)

## Cara Penggunaan

### 1. Backup Data (WAJIB)

Sebelum melakukan update, **WAJIB** backup data terlebih dahulu:

```bash
php backup_vendor_data.php
```

Script ini akan membuat:
- File SQL backup untuk restore: `storage/backups/vendor_backup_YYYY_MM_DD_HH_MM_SS.sql`
- File CSV backup untuk referensi: `storage/backups/vendor_backup_YYYY_MM_DD_HH_MM_SS.csv`

### 2. Upload File CSV ke Server (Jika di Server)

**Opsi A - Manual Upload:**
```bash
# Upload via SCP
scp Zona.csv user@server:/var/www/aypsis/

# Upload via SFTP
sftp user@server
put Zona.csv /var/www/aypsis/
```

**Opsi B - Menggunakan Helper Script:**
```bash
# Edit konfigurasi di upload_csv_to_server.sh terlebih dahulu
./upload_csv_to_server.sh upload
```

### 3. Update Vendor (Pilihan A - Artisan Command)

Menggunakan Laravel Artisan command (RECOMMENDED):

```bash
# Local (Windows) - dengan file default
php artisan vendor:update-from-csv

# Local (Windows) - dengan file custom
php artisan vendor:update-from-csv "C:\path\to\your\file.csv"

# Server (Linux) - dengan file yang sudah diupload
php artisan vendor:update-from-csv /var/www/aypsis/Zona.csv

# Server (Linux) - akan mencari file di lokasi default
php artisan vendor:update-from-csv
```

**Lokasi default yang dicari (otomatis):**
- `C:\Users\amanda\Downloads\Zona.csv` (Windows)
- `/var/www/aypsis/storage/app/Zona.csv` (Linux)
- `/var/www/aypsis/Zona.csv` (Linux)
- `/tmp/Zona.csv` (Linux)
- `./Zona.csv` (Current directory)

**Keuntungan Artisan command:**
- Progress bar
- Konfirmasi sebelum update
- Output yang lebih rapi
- Terintegrasi dengan Laravel
- Auto-detect path untuk berbagai environment

### 4. Update Vendor (Pilihan B - Script Standalone)

Menggunakan script standalone:

```bash
php update_vendor_from_csv.php
```

## Proses Update

Script akan melakukan:

1. **Validasi file CSV** - Memastikan file ada dan dapat dibaca
2. **Mapping kolom** - Mencari kolom yang diperlukan dalam CSV
3. **Konfirmasi** - Menampilkan mapping dan meminta konfirmasi (untuk Artisan command)
4. **Update database** - Mencocokkan nomor kontainer dan update invoice vendor
5. **Laporan hasil** - Menampilkan statistik update

## Mapping Database

Script akan mengupdate tabel `daftar_tagihan_kontainer_sewa`:

```sql
UPDATE daftar_tagihan_kontainer_sewa 
SET 
    invoice_vendor = 'ZONA23.07.20493',
    tanggal_vendor = '2023-07-13'
WHERE nomor_kontainer = 'BMOU2495277';
```

**Pencocokan data:**
- CSV `Kontainer` → Database `nomor_kontainer`
- CSV `No.InvoiceVendor` → Database `invoice_vendor`
- CSV `Tgl.InvVendor` → Database `tanggal_vendor`

## Format Tanggal yang Didukung

Script dapat membaca berbagai format tanggal:
- `13 Jul 23` (format CSV saat ini)
- `13-Jul-23`
- `13/07/2023`
- `13-07-2023`
- `2023-07-13`
- `13 Jul 2023`

## Output Script

### Contoh Output Sukses:
```
====================================================
UPDATE VENDOR INVOICE DARI CSV
====================================================
File CSV: C:\Users\amanda\Downloads\Zona.csv
Waktu mulai: 2025-01-15 10:30:00

Header CSV ditemukan:
  [0] Group
  [1] Kontainer
  [17] No.InvoiceVendor
  [18] Tgl.InvVendor

Mapping kolom:
  - Kontainer: kolom [1] Kontainer
  - Invoice Vendor: kolom [17] No.InvoiceVendor
  - Tanggal Vendor: kolom [18] Tgl.InvVendor

✅ Updated 3 record(s) untuk kontainer 'BMOU2495277' dengan invoice 'ZONA23.07.20493' tanggal 2023-07-13

====================================================
HASIL PEMROSESAN
====================================================
✅ Total record berhasil diupdate: 156
⚠️  Total kontainer tidak ditemukan: 12
❌ Total error: 0
Waktu selesai: 2025-01-15 10:32:15
====================================================
```

## Error Handling

Script menangani berbagai kondisi error:

1. **File tidak ditemukan** - Script akan berhenti dengan pesan error
2. **Kontainer tidak ditemukan** - Dicatat sebagai "not found", tidak menghentikan proses
3. **Format tanggal invalid** - Tanggal diabaikan, hanya invoice vendor yang diupdate
4. **Database error** - Transaksi di-rollback, tidak ada perubahan tersimpan

## Restore Data

Jika terjadi kesalahan, gunakan file backup SQL:

```bash
# Masuk ke MySQL/PostgreSQL
mysql -u username -p database_name < storage/backups/vendor_backup_YYYY_MM_DD_HH_MM_SS.sql

# Atau untuk PostgreSQL
psql -U username -d database_name -f storage/backups/vendor_backup_YYYY_MM_DD_HH_MM_SS.sql
```

## Keamanan

- ✅ Menggunakan database transaction (rollback jika error)
- ✅ Validasi input data
- ✅ Backup otomatis sebelum update
- ✅ Log semua aktivitas
- ✅ Konfirmasi sebelum eksekusi (Artisan command)

## Tips Penggunaan

1. **Selalu backup** sebelum update
2. **Test dengan data kecil** terlebih dahulu
3. **Periksa mapping kolom** sebelum konfirmasi
4. **Simpan file backup** di tempat yang aman
5. **Jalankan di environment staging** terlebih dahulu

## Troubleshooting

### Error: "Kolom kontainer tidak ditemukan"
- Periksa header CSV, pastikan ada kolom `Kontainer` atau `nomor_kontainer`
- Periksa delimiter CSV (harus `;`)

### Error: "File tidak ditemukan"
- Pastikan path file CSV benar
- Pastikan file accessible dari aplikasi

### Banyak "kontainer tidak ditemukan"
- Periksa format nomor kontainer di CSV vs database
- Pastikan tidak ada spasi extra atau karakter khusus

### Update berhasil tapi data tidak berubah
- Periksa apakah transaksi di-commit
- Periksa log aplikasi Laravel
- Pastikan koneksi database benar

## Contact

Jika ada pertanyaan atau error, hubungi tim development.