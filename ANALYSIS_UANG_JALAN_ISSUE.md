# Analisis Issue: Uang Jalan Menampilkan "Rp 675" Bukan "Rp 675.000"

## Kesimpulan

**Ini BUKAN masalah formatting/display - ini adalah masalah DATA yang disimpan di database**

## Investigasi Lengkap

### 1. Database Values (VERIFIED)

```
Table: surat_jalans
- SJ00001: uang_jalan = 675.00 (DECIMAL type)
- SJ0001:  uang_jalan = 675.00 (DECIMAL type)
- SJ0003:  uang_jalan = 675.00 (DECIMAL type)
```

### 2. Formatting Code (WORKING CORRECTLY)

File: `resources/views/pranota-surat-jalan/create.blade.php` Line 185

```php
number_format($suratJalan->uang_jalan, 0, ',', '.')
```

Behavior: `number_format(675, 0, ',', '.')` menghasilkan **"675"**

-   Ini BENAR untuk nilai 675
-   Jika nilainya 675000, output akan "675.000" ✓

### 3. Master Table Structure

Table: `tujuan_kegiatan_utamas`

```
Columns:
- dari (VARCHAR)
- ke (VARCHAR)
- uang_jalan_20ft (DECIMAL 15,2)
- uang_jalan_40ft (DECIMAL 15,2)

Example Data:
- ke: KAPUK → uang_jalan_20ft: 350000.00 ← PERHATIKAN INI!
- ke: TANJUNG PRIUK → uang_jalan_20ft: 350000.00 ← JAUH LEBIH BESAR!
- ke: BANDENGAN → uang_jalan_20ft: 335000.00
```

### 4. Kesenjangan Data

```
Master Table: uang_jalan_20ft = 350,000+
Surat Jalan: uang_jalan = 675 ← TIDAK SESUAI!
```

## Root Cause

Ada 2 kemungkinan:

1. **Nilai diinput manual** - User/staff memasukkan 675 secara manual
2. **Bug pada saat create** - Uang jalan tidak dipopulate dari master table

## Solusi yang Dibutuhkan

### Option A: Gunakan Master Table untuk Auto-Populate

Ubah surat jalan form untuk:

-   Saat user pilih tujuan pengiriman → cari di `tujuan_kegiatan_utamas`
-   Ambil `uang_jalan_20ft` atau `uang_jalan_40ft` berdasarkan `size`
-   Auto-fill field `uang_jalan`

### Option B: Update Data Existing (Jika sudah banyak surat jalan)

```sql
-- Ini perlu hati-hati, butuh logic yang tepat
UPDATE surat_jalans sj
JOIN tujuan_kegiatan_utamas tku
  ON (sj.tujuan_pengiriman = tku.ke OR sj.tujuan_pengambilan = tku.ke)
SET sj.uang_jalan =
  CASE
    WHEN sj.size = '20' THEN tku.uang_jalan_20ft
    WHEN sj.size = '40' THEN tku.uang_jalan_40ft
    ELSE tku.uang_jalan_20ft
  END
WHERE sj.uang_jalan = 675
```

## Status Saat Ini

-   ✅ Formatting di view sudah benar
-   ❌ Data di database salah (675 bukan 675000)
-   ❓ Perlu confirm dari user: apakah ini masalah manual input atau sistem?

## Rekomendasi Next Step

User harus memilih:

1. **Ingin auto-populate** dari master table → implementasi di surat jalan form
2. **Atau manual input** tapi perlu di-correct nilai yang sudah ada
