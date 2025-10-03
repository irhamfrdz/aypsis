# Dokumentasi Konversi File CSV DPE ke Format Import

## 📋 File yang Dihasilkan

Saya telah membuat **2 file CSV** siap import berdasarkan data DPE yang Anda berikan:

### 1. `template_import_dpe_tagihan.csv` - Format Lengkap

-   **Kolom**: Semua kolom termasuk nilai finansial (DPP, PPN, PPH, Grand Total)
-   **Grouping**: Auto-generated group ID dengan format `TK1YYMMXXXXXXX`
-   **Penggunaan**: Import dengan validasi lengkap

### 2. `template_import_dpe_auto_group.csv` - Format Auto-Grouping

-   **Kolom**: Hanya kolom wajib dengan group kosong untuk auto-grouping
-   **Grouping**: Kolom group dikosongkan, sistem akan auto-generate berdasarkan vendor + tanggal awal
-   **Penggunaan**: Import dengan full auto-grouping oleh sistem DPE

## 🔄 Proses Konversi yang Dilakukan

### Data Mapping:

-   `Group` → `group` (dengan format TK1YYMMXXXXXXX)
-   `Kontainer` → `nomor_kontainer`
-   `Awal` → `tanggal_awal` (converted to YYYY-MM-DD)
-   `Akhir` → `tanggal_akhir` (converted to YYYY-MM-DD)
-   `Ukuran` → `size`
-   `Periode` → `periode`
-   `Status` → `tarif` (Bulanan/Harian based on Hari count)
-   `DPP` → `dpp`
-   `ppn` → `ppn`
-   `pph` → `pph`
-   `grand_total` → `grand_total`

### Perhitungan yang Dilakukan:

1. **Konversi Tanggal**: Dari format DD-MM-YYYY ke YYYY-MM-DD
2. **DPP Nilai Lain**: Dihitung sebagai DPP × 11/12
3. **Group ID**: Auto-generated berdasarkan tanggal awal
4. **Status**: Diubah ke "Tersedia" atau "Ongoing"
5. **Tarif**: Ditentukan berdasarkan jumlah hari (Bulanan/Harian)

## 🚀 Cara Menggunakan File Import

### Langkah 1: Pilih File

-   **File Lengkap**: Jika ingin import dengan nilai finansial yang sudah dihitung
-   **File Simple**: Jika ingin sistem menghitung otomatis berdasarkan pricelist

### Langkah 2: Import ke Sistem

#### Untuk File Lengkap (`template_import_dpe_tagihan.csv`):

1. Login ke sistem Aypsis
2. Go to: Daftar Tagihan Kontainer Sewa
3. Klik **"Upload CSV"** (hijau) - Import Standard
4. Pilih file `template_import_dpe_tagihan.csv`
5. Klik **"Import"**
6. Konfirmasi di modal
7. Tunggu proses selesai

#### Untuk File Auto-Group (`template_import_dpe_auto_group.csv`):

1. Login ke sistem Aypsis
2. Go to: Daftar Tagihan Kontainer Sewa
3. Klik **"Upload CSV dengan Grouping"** (orange) - Import dengan Auto-Grouping
4. Pilih file `template_import_dpe_auto_group.csv`
5. Klik **"Import & Group"**
6. Konfirmasi di modal
7. Tunggu proses selesai

## 📊 Data Summary

### Total Records: 58 entries

-   **Vendor**: DPE (semua data)
-   **Container Sizes**: 20ft (55 entries), 40ft (3 entries)
-   **Periods**: 1-8 periode per kontainer
-   **Date Range**: Januari 2025 - Oktober 2025
-   **Containers**: 12 kontainer unik

### Group Distribution (Auto-generated):

-   `TK125010000001` sampai `TK125010000012` untuk kontainer 20ft
-   `TK125030000013` untuk kontainer 40ft

## ⚠️ Catatan Penting

### 1. Validasi Data

-   ✅ Semua format tanggal sudah dikonversi ke YYYY-MM-DD
-   ✅ Nilai finansial sudah disesuaikan dengan format sistem
-   ✅ Group ID sudah digenerate sesuai standar
-   ✅ Status sudah disesuaikan (Tersedia/Ongoing)

### 2. Rekomendasi

-   **Gunakan File Lengkap** jika ingin mempertahankan nilai adjustment yang ada
-   **Gunakan File Simple** jika ingin sistem menghitung ulang berdasarkan pricelist terbaru
-   **Backup data** sebelum import untuk antisipasi

### 3. Post-Import Actions

-   Verifikasi jumlah data yang berhasil diimport (58 entries)
-   Periksa grouping otomatis
-   Validasi perhitungan finansial
-   Lakukan adjustment manual jika diperlukan

## 🎯 Hasil yang Diharapkan

Setelah import berhasil, Anda akan mendapatkan:

-   ✅ 58 tagihan kontainer sewa DPE
-   ✅ 13 group otomatis (12 untuk 20ft, 1 untuk 40ft)
-   ✅ Periode tracking yang akurat
-   ✅ Nilai finansial yang sudah dihitung
-   ✅ Status yang sesuai dengan kondisi aktual

**File sudah siap untuk diimport!** Pilih file yang sesuai dengan kebutuhan Anda dan ikuti panduan import di atas.
