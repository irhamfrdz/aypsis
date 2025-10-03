====================================
TROUBLESHOOTING GUIDE - Import CSV
====================================

Jika Anda tidak bisa import file CSV, ikuti langkah troubleshooting ini:

## 1. CEK PERMISSION USER

Pastikan user yang login memiliki permission:

-   tagihan-kontainer-sewa-create
-   tagihan-kontainer-sewa-import (jika ada)

## 2. CEK DI BROWSER

1. Buka Developer Tools (F12)
2. Pilih tab Network
3. Coba upload file CSV
4. Lihat apakah ada error HTTP (4xx, 5xx)

## 3. CEK CONSOLE ERROR

Di Developer Tools, pilih tab Console untuk melihat JavaScript error

## 4. TEST DENGAN FILE KECIL

Gunakan file test_import_sample.csv yang sudah dibuat:

```
vendor;nomor_kontainer;size;group;tanggal_awal;tanggal_akhir;periode;tarif;status
DPE;TGHU1234567;20;;2024-10-01;2024-10-31;30;150000;aktif
```

## 5. MANUAL TEST ROUTE

Coba akses route langsung:

-   GET: http://yourdomain/daftar-tagihan-kontainer-sewa
-   POST: http://yourdomain/daftar-tagihan-kontainer-sewa/import

## 6. CEK ERROR LOG

Setelah mencoba import, cek log di:

-   storage/logs/laravel.log
-   Browser Network Tab Response

## 7. PASTIKAN FILE FORMAT

File CSV harus:

-   Encoding UTF-8
-   Separator: semicolon (;)
-   Header sesuai template
-   Kolom group kosong untuk auto-grouping

## 8. KEMUNGKINAN PENYEBAB

-   User tidak memiliki permission
-   File terlalu besar (cek upload_max_filesize)
-   Format file tidak sesuai
-   Session expired
-   CSRF token tidak valid

====================================
