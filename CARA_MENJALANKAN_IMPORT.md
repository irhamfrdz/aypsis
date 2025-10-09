# 🚀 PANDUAN MENJALANKAN CSV TO PRANOTA IMPORT

## 📋 OVERVIEW

Script ini mengimport data CSV dari file `Zona.csv` ke dalam sistem Pranota dengan logic grouping berdasarkan nomor invoice vendor.

## ⚙️ CARA MENJALANKAN

### 1. 📊 Preview Data (Recommended - Aman untuk dicoba)

```bash
php preview_csv_to_pranota.php
```

**Fungsi:** Menampilkan preview data yang akan diimport tanpa membuat pranota sesungguhnya.

### 2. 🎯 Demo Grouping Logic

```bash
php demo_invoice_grouping.php
```

**Fungsi:** Mendemonstrasikan bagaimana invoice yang sama akan dikelompokkan menjadi 1 pranota.

### 3. 🚀 Import Sesungguhnya (PRODUCTION)

```bash
php import_csv_to_pranota.php
```

**Fungsi:** Melakukan import data CSV ke pranota sesungguhnya di database.

## 📊 BUSINESS LOGIC

### 🔍 Grouping Rules:

-   ✅ **Invoice Same Number = 1 Pranota**: Jika nomor invoice vendor sama, akan digabung ke 1 pranota
-   ✅ **Bank Filter**: Hanya memproses tagihan yang ada nomor bank (tidak kosong atau "-")
-   ✅ **Date Mapping**: Tanggal pranota menggunakan tanggal bank
-   ✅ **Financial Preserve**: Semua kalkulasi keuangan (DPP, PPN, PPH, Grand Total) dipertahankan

### 📈 Expected Results:

-   **121 Pranota** akan dibuat dari **209 Tagihan**
-   **503 Rows** diskip karena tidak ada bank info
-   Rata-rata **1.7 tagihan per pranota**

## 📁 FILE REQUIREMENTS

### ✅ File yang Diperlukan:

```
📄 Zona.csv                    # File CSV source data
🚀 import_csv_to_pranota.php   # Main import script
👁️ preview_csv_to_pranota.php  # Preview mode script
🎯 demo_invoice_grouping.php   # Grouping demo script
📋 test_pranota_import.php     # Testing utilities
```

## 🎛️ COMMAND EXAMPLES

### Basic Usage:

```bash
# 1. Cek preview terlebih dahulu
php preview_csv_to_pranota.php

# 2. Lihat demo grouping
php demo_invoice_grouping.php

# 3. Jalankan import setelah yakin
php import_csv_to_pranota.php
```

### Advanced Testing:

```bash
# Test model operations
php test_pranota_import.php

# Validate CSV structure
php -r "
$handle = fopen('Zona.csv', 'r');
$headers = fgetcsv($handle, 1000, ';');
echo 'CSV Headers: ' . implode(' | ', $headers) . \"\n\";
fclose($handle);
"
```

## 🔧 TROUBLESHOOTING

### ❌ Error: File not found

```bash
# Pastikan file Zona.csv ada di directory yang sama
ls -la Zona.csv
# atau di Windows:
dir Zona.csv
```

### ❌ Error: Database connection

```bash
# Test database connection
php artisan tinker --execute="
try {
    \DB::connection()->getPdo();
    echo 'Database connected successfully!\n';
} catch(Exception \$e) {
    echo 'Database error: ' . \$e->getMessage() . \"\n\";
}
"
```

### ❌ Error: Permission denied

```bash
# Pastikan model exists dan accessible
php artisan tinker --execute="
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;
echo 'Models loaded successfully!\n';
"
```

## 📊 VERIFICATION SETELAH IMPORT

### 🔍 Cek Pranota yang Dibuat:

```bash
php artisan tinker --execute="
use App\Models\PranotaTagihanKontainerSewa;
\$count = PranotaTagihanKontainerSewa::where('no_pranota', 'LIKE', 'PRN-ZONA-%')->count();
echo 'Total Pranota ZONA: ' . \$count . \"\n\";
\$latest = PranotaTagihanKontainerSewa::where('no_pranota', 'LIKE', 'PRN-ZONA-%')->latest()->first();
if(\$latest) {
    echo 'Latest Pranota: ' . \$latest->no_pranota . ' - Total: Rp ' . number_format(\$latest->grand_total) . \"\n\";
}
"
```

### 📋 Cek Tagihan yang Di-Link:

```bash
php artisan tinker --execute="
use App\Models\DaftarTagihanKontainerSewa;
\$linkedCount = DaftarTagihanKontainerSewa::whereNotNull('pranota_tagihan_kontainer_sewa_id')->count();
echo 'Tagihan linked to Pranota: ' . \$linkedCount . \"\n\";
"
```

## 🚨 IMPORTANT NOTES

### ⚠️ Before Import:

1. **BACKUP DATABASE** - Import akan membuat data baru
2. **Check CSV Format** - Pastikan delimiter semicolon (;)
3. **Verify Bank Data** - Hanya tagihan dengan bank info yang diproses
4. **Test Preview First** - Selalu jalankan preview dulu

### ✅ After Import:

1. **Verify Count** - Pastikan 121 pranota ter-create
2. **Check Financial** - Validasi total DPP, PPN, PPH
3. **Validate Grouping** - Cek apakah grouping sesuai ekspektasi
4. **Git Commit** - Commit hasil import jika berhasil

## 📞 SUPPORT

Jika ada error atau pertanyaan:

1. 📊 Jalankan preview mode dulu untuk debugging
2. 🧪 Gunakan test scripts untuk validate model
3. 📝 Check documentation di `PRANOTA_IMPORT_DOCUMENTATION.md`
4. 🔍 Review error logs di Laravel log files

---

**Created:** October 8, 2025  
**Last Updated:** After successful import of 121 pranota from 209 tagihan  
**Status:** ✅ Production Ready
