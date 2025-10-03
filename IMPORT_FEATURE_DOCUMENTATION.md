# Fitur Import Data Tagihan Kontainer Sewa

## Overview

Fitur import memungkinkan pengguna untuk mengupload data tagihan kontainer sewa dalam format CSV secara bulk, sehingga tidak perlu memasukkan data satu per satu.

## Files yang Dibuat/Dimodifikasi

### 1. Controller Methods

**File**: `app/Http/Controllers/DaftarTagihanKontainerSewaController.php`

**Methods yang ditambahkan**:

-   `importPage()` - Menampilkan halaman import
-   `exportTemplate()` - Download template CSV
-   `processImport()` - Memproses file import
-   `processCsvImport()` - Memproses CSV secara detail
-   `cleanImportData()` - Membersihkan dan validasi data
-   Helper methods untuk pembersihan data (cleanVendor, cleanSize, dll.)

### 2. View Import

**File**: `resources/views/daftar-tagihan-kontainer-sewa/import.blade.php`

Fitur halaman import:

-   Drag & drop file upload
-   Validasi file (ukuran, format)
-   Preview data sebelum import
-   Progress bar
-   Notifikasi hasil import
-   Opsi import (validate only, skip duplicates, update existing)

### 3. Routes

**File**: `routes/web.php`

Routes yang ditambahkan:

```php
Route::get('daftar-tagihan-kontainer-sewa/import', [DaftarTagihanKontainerSewaController::class, 'importPage'])
    ->name('daftar-tagihan-kontainer-sewa.import');

Route::post('daftar-tagihan-kontainer-sewa/import/process', [DaftarTagihanKontainerSewaController::class, 'processImport'])
    ->name('daftar-tagihan-kontainer-sewa.import.process');

Route::get('daftar-tagihan-kontainer-sewa/export-template', [DaftarTagihanKontainerSewaController::class, 'exportTemplate'])
    ->name('daftar-tagihan-kontainer-sewa.export-template');
```

### 4. Tombol di Index

**File**: `resources/views/daftar-tagihan-kontainer-sewa/index.blade.php`

Ditambahkan tombol:

-   "Import Data" - Link ke halaman import
-   "Download Template" - Download template CSV

## Cara Penggunaan

### 1. Download Template

1. Klik tombol "Download Template" di halaman daftar tagihan
2. File CSV template akan terdownload dengan format yang benar
3. Template berisi header dan contoh data

### 2. Persiapan Data

Format CSV yang diperlukan:

```csv
vendor,nomor_kontainer,size,tanggal_awal,tanggal_akhir,tarif,group,status
ZONA,ZONA001234,20,2024-01-01,2024-01-31,25000,GROUP001,ongoing
DPE,DPE567890,40,2024-01-01,2024-01-31,35000,GROUP002,selesai
```

**Kolom wajib**:

-   `vendor` - ZONA atau DPE
-   `nomor_kontainer` - Nomor kontainer unique
-   `size` - 20 atau 40
-   `tanggal_awal` - Format YYYY-MM-DD
-   `tanggal_akhir` - Format YYYY-MM-DD
-   `tarif` - Tarif per hari (angka)

**Kolom opsional**:

-   `group` - Nama group (kosong jika individual)
-   `status` - ongoing atau selesai (default: ongoing)

### 3. Proses Import

1. Klik tombol "Import Data" di halaman daftar tagihan
2. Upload file CSV yang sudah disiapkan
3. Pilih opsi import:
    - **Validate Only**: Hanya validasi tanpa menyimpan
    - **Skip Duplicates**: Lewati data yang sudah ada
    - **Update Existing**: Update data yang sudah ada
4. Klik "Import Data"
5. Tunggu proses selesai dan lihat hasilnya

## Validasi Data

### 1. Format File

-   File harus berformat CSV (.csv) atau text (.txt)
-   Maksimal ukuran 10MB
-   Encoding UTF-8
-   Delimiter koma (,)

### 2. Validasi Data

-   **Vendor**: Harus ZONA atau DPE
-   **Size**: Harus 20 atau 40
-   **Tanggal**: Format valid (YYYY-MM-DD, DD/MM/YYYY, dll.)
-   **Tarif**: Angka positif
-   **Nomor Kontainer**: Minimal 4 karakter
-   **Periode**: Maksimal 365 hari

### 3. Business Rules

-   Tanggal akhir tidak boleh lebih awal dari tanggal awal
-   Kombinasi nomor_kontainer + periode harus unique untuk deteksi duplikat
-   Jika tarif tidak diisi, akan diambil dari master pricelist

## Perhitungan Otomatis

Sistem akan menghitung otomatis:

-   **Periode**: Selisih hari antara tanggal awal dan akhir + 1
-   **Masa**: "{periode} Hari"
-   **DPP**: tarif × periode
-   **PPN**: DPP × 11%
-   **PPH**: DPP × 2%
-   **Grand Total**: DPP + PPN - PPH

## Fitur Error Handling

### 1. Validasi File Upload

-   Cek format file
-   Cek ukuran file
-   Cek struktur CSV (header)

### 2. Validasi Data Per Baris

-   Error message yang spesifik per baris
-   Lanjutkan proses meskipun ada error di baris tertentu
-   Summary hasil import (berapa berhasil, gagal, diskip)

### 3. Duplicate Handling

Tiga mode handling duplikat:

1. **Skip**: Lewati data duplikat (default)
2. **Update**: Update data yang sudah ada
3. **Error**: Stop jika ada duplikat

## Logging & Audit

Semua aktivitas import dicatat dalam log dengan informasi:

-   User yang melakukan import
-   Nama file
-   Hasil import (jumlah berhasil, gagal, diskip)
-   Error yang terjadi

## Security

-   Validasi file type untuk mencegah upload file berbahaya
-   Validasi permissions (perlu permission tagihan-kontainer-create)
-   Sanitasi input data
-   Rate limiting untuk prevent abuse

## Sample Data

File sample tersedia di `public/sample_import.csv` untuk referensi format yang benar.

## Troubleshooting

### Error: "Format file tidak didukung"

-   Pastikan file berformat CSV atau TXT
-   Pastikan tidak ada karakter khusus di nama file

### Error: "Header tidak lengkap"

-   Pastikan baris pertama CSV berisi header yang benar
-   Header wajib: vendor, nomor_kontainer, size, tanggal_awal, tanggal_akhir

### Error: "Format tanggal tidak valid"

-   Gunakan format YYYY-MM-DD untuk konsistensi
-   Format lain yang didukung: DD/MM/YYYY, DD-MM-YYYY

### Error: "Vendor tidak didukung"

-   Hanya ZONA dan DPE yang didukung
-   Huruf besar/kecil tidak masalah (akan dinormalisasi)

### Error: "Ukuran file terlalu besar"

-   Maksimal 10MB per file
-   Bagi file besar menjadi beberapa file kecil

## Future Enhancements

1. **Excel Support**: Tambah support untuk file .xlsx/.xls
2. **Batch Processing**: Untuk file yang sangat besar
3. **Schedule Import**: Import otomatis dari lokasi tertentu
4. **Data Mapping**: Mapping kolom yang flexible
5. **Import History**: Riwayat import dengan rollback capability
