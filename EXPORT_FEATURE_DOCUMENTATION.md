# Export Feature - Daftar Tagihan Kontainer Sewa

## Fitur Export Data

Tombol **Export Data** telah ditambahkan ke halaman Daftar Tagihan Kontainer Sewa.

### Lokasi Tombol

Tombol Export berada di bagian atas halaman, bersama dengan tombol:

-   Tambah Tagihan
-   Buat Group
-   Import Data
-   Download Template

### Cara Menggunakan

1. **Export Semua Data**

    - Klik tombol "Export Data" (biru)
    - Semua data akan diexport ke file CSV

2. **Export dengan Filter**

    - Gunakan filter yang tersedia (Vendor, Size, Periode, Status, dll)
    - Klik tombol "Export Data"
    - Hanya data yang sesuai filter yang akan diexport

3. **Export dengan Search**
    - Gunakan search box untuk mencari data spesifik
    - Klik tombol "Export Data"
    - Hanya data hasil search yang akan diexport

### Format Export (CSV)

File CSV yang diexport menggunakan delimiter **semicolon (;)** dan berisi kolom:

| Kolom           | Deskripsi                     |
| --------------- | ----------------------------- |
| Group           | Nama group kontainer          |
| Vendor          | Nama vendor (DPE/ZONA)        |
| Nomor Kontainer | Nomor kontainer               |
| Size            | Ukuran kontainer (20/40)      |
| Tanggal Awal    | Tanggal mulai sewa            |
| Tanggal Akhir   | Tanggal akhir sewa            |
| Periode         | Nomor periode                 |
| Masa            | Masa sewa                     |
| Tarif           | Jenis tarif (Bulanan/Harian)  |
| Status          | Status kontainer              |
| DPP             | Dasar Pengenaan Pajak         |
| Adjustment      | Nilai adjustment              |
| DPP Nilai Lain  | DPP nilai lain                |
| PPN             | Pajak Pertambahan Nilai (11%) |
| PPH             | Pajak Penghasilan (2%)        |
| Grand Total     | Total keseluruhan             |
| Status Pranota  | Status pranota                |
| Pranota ID      | ID pranota terkait            |

### Format Tanggal

Tanggal di export menggunakan format: **dd-mm-yyyy** (contoh: 21-01-2025)

### Nama File

File export akan memiliki nama format:

```
export_tagihan_kontainer_sewa_YYYY-MM-DD_HHmmss.csv
```

Contoh: `export_tagihan_kontainer_sewa_2025-10-02_151230.csv`

### Fitur Export

✅ **Export dengan filter aktif** - Filter yang diterapkan di halaman akan ikut di export
✅ **Export hasil search** - Data yang dicari akan di export
✅ **UTF-8 with BOM** - Mendukung karakter Unicode
✅ **Delimiter semicolon** - Kompatibel dengan Excel Indonesia
✅ **Loading indicator** - Tombol menampilkan status saat export
✅ **Notification** - Notifikasi sukses setelah export

### Permission Required

User harus memiliki permission: **`tagihan-kontainer-sewa-view`**

### Technical Details

#### Route

```php
Route::get('daftar-tagihan-kontainer-sewa/export', [DaftarTagihanKontainerSewaController::class, 'export'])
    ->name('daftar-tagihan-kontainer-sewa.export')
    ->middleware('can:tagihan-kontainer-sewa-view');
```

#### Controller Method

```php
public function export(Request $request)
```

Method ini:

-   Membaca filter dari request
-   Query data sesuai filter
-   Generate CSV dengan delimiter semicolon
-   Stream download ke browser

### Troubleshooting

**Problem**: File CSV tidak ter-download

-   **Solution**: Cek permission user, pastikan memiliki `tagihan-kontainer-sewa-view`

**Problem**: Data tidak sesuai filter

-   **Solution**: Pastikan filter sudah diterapkan sebelum klik Export

**Problem**: Karakter tidak terbaca dengan benar di Excel

-   **Solution**: File sudah menggunakan UTF-8 with BOM, buka dengan Excel 2016+

**Problem**: Data kosong di export

-   **Solution**: Cek apakah ada data yang sesuai dengan filter yang diterapkan

## Update Log

**Date**: October 2, 2025
**Author**: GitHub Copilot
**Changes**:

-   ✅ Added export method to DaftarTagihanKontainerSewaController
-   ✅ Added export route to web.php
-   ✅ Added Export Data button to index.blade.php
-   ✅ Added JavaScript handler for export button with loading state
-   ✅ Export respects current filters (vendor, size, periode, status, status_pranota)
-   ✅ Export includes search results
-   ✅ CSV format with semicolon delimiter
-   ✅ UTF-8 with BOM encoding
-   ✅ Date format: dd-mm-yyyy
-   ✅ Success notification after export
