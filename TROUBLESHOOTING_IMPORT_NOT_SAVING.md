# Troubleshooting: Data Import Tidak Tersimpan

## Masalah

Data CSV tidak tersimpan ke database setelah proses import.

## Penyebab yang Mungkin

### 1. ✅ Checkbox "Hanya validasi" Tercentang

**PENYEBAB UTAMA**: Jika checkbox "Hanya validasi (tidak menyimpan data)" tercentang, data TIDAK akan tersimpan ke database.

**Lokasi**: `resources/views/daftar-tagihan-kontainer-sewa/import.blade.php` line 306

**Kode**:

```blade
<input type="checkbox" name="validate_only" id="validateOnly" ...>
<span class="ml-2 text-sm text-gray-700">Hanya validasi (tidak menyimpan data)</span>
```

**Logic di Controller**:

```php
// If validation only, don't save
if (!$options['validate_only']) {
    DaftarTagihanKontainerSewa::create($cleanedData);
}
```

**SOLUSI**:

-   ❌ **JANGAN** centang checkbox "Hanya validasi (tidak menyimpan data)"
-   ✅ Pastikan checkbox ini **TIDAK TERCENTANG** saat import

---

### 2. Skip Duplicates Aktif

**Checkbox**: "Skip data yang sudah ada" (**DEFAULT: CHECKED**)

Jika data dengan nomor kontainer dan periode yang sama sudah ada di database, data akan di-skip (tidak diimport).

**SOLUSI**:

-   Jika ingin import ulang data yang sama, **uncheck** "Skip data yang sudah ada"
-   Atau check "Update data yang sudah ada"

---

### 3. Data Sudah Ada di Database

Jika data dengan kombinasi yang sama sudah ada:

-   `nomor_kontainer` + `periode` + `tanggal_awal`

Data akan di-skip secara otomatis jika "Skip duplicates" aktif.

**Cara Cek**:

```sql
SELECT nomor_kontainer, periode, tanggal_awal, COUNT(*) as jumlah
FROM daftar_tagihan_kontainer_sewa
GROUP BY nomor_kontainer, periode, tanggal_awal
HAVING COUNT(*) > 0;
```

---

### 4. Error Validasi

Jika ada error pada data CSV (format salah, required field kosong), data tidak akan tersimpan.

**Cek di hasil import**:

-   Lihat section "Errors" di hasil import
-   Periksa pesan error untuk setiap baris

---

## Cara Test Import yang Benar

### Step 1: Pastikan Checkbox Settings

```
✅ File dipilih: export_tagihan_kontainer_sewa_2025-10-02_153813.csv
❌ Hanya validasi (tidak menyimpan data) - UNCHECK INI!
✅ Skip data yang sudah ada - Optional (tergantung kebutuhan)
❌ Update data yang sudah ada - Optional (centang jika ingin update)
```

### Step 2: Klik Import Data

-   Button text berubah: "Mengimport..."
-   Progress bar muncul
-   Tunggu sampai selesai

### Step 3: Cek Hasil Import

Hasil import akan menampilkan:

```
✅ Import Berhasil
• Data berhasil diimport: XX baris
• Data berhasil diupdate: XX baris (jika ada)
• Data diskip (duplikat): XX baris (jika ada)
• Total data diproses: XX baris
```

### Step 4: Verifikasi Database

```sql
-- Cek jumlah data terbaru
SELECT COUNT(*) as total FROM daftar_tagihan_kontainer_sewa;

-- Cek data terakhir yang diimport
SELECT * FROM daftar_tagihan_kontainer_sewa
ORDER BY created_at DESC
LIMIT 10;

-- Cek data dari CSV (contoh: CBHU3952697)
SELECT * FROM daftar_tagihan_kontainer_sewa
WHERE nomor_kontainer = 'CBHU3952697';
```

---

## Quick Test Script

Buat file `test_import_settings.php` untuk debug:

```php
<?php
// Simulate import request
$validateOnly = false; // FALSE = data akan tersimpan
$skipDuplicates = true; // TRUE = skip data duplikat
$updateExisting = false; // FALSE = tidak update data lama

echo "=== Import Settings Test ===\n";
echo "validate_only: " . ($validateOnly ? 'true (TIDAK SIMPAN DATA)' : 'false (SIMPAN DATA)') . "\n";
echo "skip_duplicates: " . ($skipDuplicates ? 'true' : 'false') . "\n";
echo "update_existing: " . ($updateExisting ? 'true' : 'false') . "\n";
echo "\n";

if ($validateOnly) {
    echo "❌ WARNING: Data TIDAK akan tersimpan karena validate_only = true\n";
    echo "   Uncheck checkbox 'Hanya validasi (tidak menyimpan data)'\n";
} else {
    echo "✅ Data AKAN tersimpan ke database\n";
}

if ($skipDuplicates && !$updateExisting) {
    echo "⚠️  Data duplikat akan di-skip (tidak diimport)\n";
} elseif ($updateExisting) {
    echo "✅ Data duplikat akan di-update\n";
} else {
    echo "⚠️  Data duplikat akan menyebabkan error (tidak ada skip/update)\n";
}
?>
```

---

## Kesimpulan

**PENYEBAB PALING SERING**:

1. ❌ Checkbox "Hanya validasi" tercentang → Data tidak tersimpan
2. ⚠️ Data sudah ada + "Skip duplicates" aktif → Data di-skip
3. ❌ Error validasi → Data tidak tersimpan

**SOLUSI**:

1. ✅ **UNCHECK** "Hanya validasi (tidak menyimpan data)"
2. ✅ Pastikan tidak ada error validasi
3. ✅ Jika data sudah ada, pilih "Update data yang sudah ada"

---

## Screenshot Form yang Benar

```
📁 File Upload: export_tagihan_kontainer_sewa_2025-10-02_153813.csv

Import Options:
[ ] Hanya validasi (tidak menyimpan data)          ← HARUS KOSONG!
[✓] Skip data yang sudah ada                        ← Optional
[ ] Update data yang sudah ada                      ← Optional (centang jika ingin update)

[Batal]  [Import Data]
```

---

## Next Steps

1. Buka halaman import: `/daftar-tagihan-kontainer-sewa/import`
2. Upload file CSV Anda
3. **PASTIKAN checkbox "Hanya validasi" TIDAK TERCENTANG**
4. Klik "Import Data"
5. Tunggu sampai muncul hasil: "Import Berhasil. XX data berhasil diimport"
6. Cek di halaman daftar: `/daftar-tagihan-kontainer-sewa`

Jika masih tidak tersimpan setelah mengikuti langkah di atas, periksa:

-   Laravel log: `storage/logs/laravel.log`
-   Browser console: F12 → Console tab
-   Network tab: Lihat response dari request import
