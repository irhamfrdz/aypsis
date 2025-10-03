# Panduan Import CSV - Daftar Tagihan Kontainer Sewa

## Fitur Import yang Tersedia

Sistem menyediakan **2 jenis import CSV** untuk Daftar Tagihan Kontainer Sewa:

### 1. Import CSV Standard

-   Import data sesuai template CSV
-   Tidak ada auto-grouping
-   Manual group assignment jika diperlukan
-   Cocok untuk data yang sudah memiliki group atau tidak memerlukan grouping

### 2. Import CSV dengan Auto-Grouping

-   Import data dengan auto-grouping otomatis
-   Group berdasarkan vendor dan tanggal awal yang sama
-   Generate group ID otomatis dengan format: `TK1YYMMXXXXXXX`
-   Cocok untuk data baru yang perlu dikelompokkan

## Format CSV yang Didukung

### Kolom Wajib:

-   `vendor` - Nama vendor (ZONA, DPE, dll)
-   `nomor_kontainer` - Nomor kontainer unik
-   `size` - Ukuran kontainer (20, 40, dll)

### Kolom Opsional:

-   `group` - Kode group (jika kosong, akan auto-generate)
-   `tanggal_awal` - Tanggal mulai sewa (format: YYYY-MM-DD)
-   `tanggal_akhir` - Tanggal selesai sewa (format: YYYY-MM-DD)
-   `periode` - Periode tagihan (angka, default: 1)
-   `masa` - Masa sewa dalam hari
-   `tarif` - Jenis tarif (Bulanan/Harian)
-   `dpp` - Dasar Pengenaan Pajak
-   `dpp_nilai_lain` - DPP Nilai Lain
-   `ppn` - Pajak Pertambahan Nilai
-   `pph` - Pajak Penghasilan
-   `grand_total` - Total keseluruhan
-   `status` - Status tagihan

## Cara Menggunakan

### Langkah 1: Download Template

1. Klik tombol **"Download Template CSV"**
2. Buka file template di Excel/aplikasi spreadsheet
3. Isi data sesuai kolom yang tersedia

### Langkah 2: Persiapan File CSV

-   Simpan file dalam format CSV dengan separator semicolon (;)
-   Pastikan encoding UTF-8 untuk karakter khusus Indonesia
-   Periksa format tanggal: YYYY-MM-DD (contoh: 2024-01-15)

### Langkah 3: Import Data

#### Untuk Import Standard:

1. Klik **"Upload CSV"** (hijau)
2. Pilih file CSV yang sudah disiapkan
3. Klik **"Import"**
4. Konfirmasi di modal yang muncul
5. Tunggu proses selesai

#### Untuk Import dengan Grouping:

1. Klik **"Upload CSV dengan Grouping"** (orange)
2. Pilih file CSV yang sudah disiapkan
3. Klik **"Import & Group"**
4. Konfirmasi di modal yang muncul
5. Tunggu proses selesai

## Contoh Data CSV

```csv
vendor;nomor_kontainer;size;tanggal_awal;tanggal_akhir;periode;tarif;status
ZONA;ZONA-12345;40;2024-01-01;2024-12-31;1;Bulanan;Tersedia
DPE;DPE-67890;20;2024-01-15;;1;Bulanan;Tersedia
```

## Auto-Grouping Logic

Sistem akan mengelompokkan data berdasarkan:

1. **Vendor yang sama** (ZONA, DPE, dll)
2. **Tanggal awal yang sama**

Format Group ID: `TK1YYMMXXXXXXX`

-   `TK1` = Prefix tetap
-   `YY` = 2 digit tahun
-   `MM` = 2 digit bulan
-   `XXXXXXX` = 7 digit running number

Contoh: `TK124010000001` untuk Januari 2024

## Tips dan Best Practices

### 1. Validasi Data Sebelum Import

-   Periksa nama vendor konsisten (ZONA, bukan zona atau Zona)
-   Pastikan nomor kontainer unik
-   Validasi format tanggal

### 2. Penanganan Error

-   Jika import gagal, periksa format CSV dan data
-   Pastikan file tidak kosong dan memiliki header
-   Periksa permission user untuk import

### 3. Setelah Import

-   Verifikasi data yang berhasil diimport
-   Periksa grouping otomatis jika menggunakan import dengan grouping
-   Lakukan adjustment jika diperlukan

## Troubleshooting

### File tidak bisa diupload

-   Periksa ukuran file (maksimal 10MB)
-   Pastikan format file CSV atau TXT
-   Periksa permission folder storage

### Data tidak sesuai harapan

-   Periksa mapping kolom di template
-   Validasi format data (tanggal, angka)
-   Periksa separator CSV (harus semicolon)

### Group tidak terbentuk

-   Pastikan menggunakan "Import dengan Grouping"
-   Periksa data vendor dan tanggal awal
-   Validasi format tanggal

## Permissions yang Dibutuhkan

User harus memiliki permission:

-   `tagihan-kontainer-sewa-create` untuk melakukan import
-   `tagihan-kontainer-sewa-index` untuk download template

## Support

Jika mengalami masalah, hubungi administrator sistem atau periksa log error di sistem.
