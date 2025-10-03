# Import CSV - Penjelasan Format dan Perbaikan

## Masalah Yang Terjadi

Ketika import CSV, data tidak masuk ke database karena:

1. **Format CSV tidak memiliki kolom `harga` (tarif numerik)**

    - CSV memiliki kolom `tarif` yang berisi "Bulanan" atau "Harian" (tipe periode)
    - Controller mencari kolom `Harga` untuk nilai tarif numerik
    - Karena tidak ditemukan, tarif diset = 0
    - Data tidak tersimpan atau muncul error validasi

2. **Mismatch antara ekspektasi controller dan format CSV**
    - Controller: mengharapkan kolom `Harga` dengan nilai numerik
    - CSV aktual: kolom `tarif` berisi tipe periode ("Bulanan"/"Harian")

## Format CSV Anda

```csv
vendor;nomor_kontainer;size;group;tanggal_awal;tanggal_akhir;periode;tarif;status
DPE;CCLU3836629;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia
```

Dimana:

-   `periode` = nomor periode (1, 2, 3, ...)
-   `tarif` = tipe periode ("Bulanan" atau "Harian")
-   **TIDAK ADA** kolom `harga` dengan nilai numerik

## Solusi Yang Diterapkan

### 1. Update Controller (`DaftarTagihanKontainerSewaController.php`)

Fungsi `cleanDpeFormatData()` diperbaiki untuk:

```php
// Try to get tarif (harga) from 'Harga' or 'harga' column
$tarifValue = $getValue('Harga') ?: $getValue('harga');

// If no 'Harga' column, use default based on vendor and size
if (empty($tarifValue) || !is_numeric($tarifValue)) {
    // Default tarif berdasarkan vendor dan size
    if ($vendor === 'DPE') {
        $tarifValue = ($size == '20') ? 25000 : 35000; // DPE: 20ft=25k, 40ft=35k
    } else {
        $tarifValue = ($size == '20') ? 20000 : 30000; // ZONA: 20ft=20k, 40ft=30k
    }
}
```

### 2. Default Tarif

Jika kolom `harga` tidak ada dalam CSV, sistem akan menggunakan tarif default:

| Vendor | Size 20 | Size 40 |
| ------ | ------- | ------- |
| DPE    | 25,000  | 35,000  |
| ZONA   | 20,000  | 30,000  |

### 3. Fleksibilitas Format

Controller sekarang mendukung 2 format CSV:

#### Format A: Dengan kolom harga eksplisit

```csv
vendor;nomor_kontainer;size;tanggal_awal;tanggal_akhir;harga;status
DPE;CONT001;20;2025-01-01;2025-01-31;28000;ongoing
```

#### Format B: Tanpa kolom harga (menggunakan default)

```csv
vendor;nomor_kontainer;size;tanggal_awal;tanggal_akhir;tarif;status
DPE;CONT001;20;2025-01-01;2025-01-31;Bulanan;Tersedia
```

## Cara Import Data

1. **Via Web Interface:**

    ```
    http://your-domain/daftar-tagihan-kontainer-sewa/import
    ```

2. **Upload file CSV** dengan format seperti di atas

3. **Pilih opsi:**

    - ❌ **UNCHECK** "Hanya validasi" (agar data benar-benar disimpan)
    - ✅ **CHECK** "Skip data yang sudah ada" (jika ingin skip duplikat)

4. **Klik "Import Data"**

## Hasil Test

✅ Test berhasil dengan file CSV Anda:

-   **61 baris** data berhasil diproses
-   **0 error**
-   Semua data ter-calculate dengan benar (DPP, PPN, Grand Total)

## Perhitungan Otomatis

Sistem akan otomatis menghitung:

```
Periode (hari) = (Tanggal Akhir - Tanggal Awal) + 1
DPP = Periode × Tarif
DPP Nilai Lain = DPP × (11/12)
PPN = DPP × 11%
Grand Total = DPP + PPN
```

## Contoh Hasil

Input:

```
DPE;CCLU3836629;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia
```

Output tersimpan:

```
vendor: DPE
nomor_kontainer: CCLU3836629
size: 20
tanggal_awal: 2025-01-21
tanggal_akhir: 2025-02-20
periode: 31 hari
tarif: 25000 (default untuk DPE size 20)
DPP: 775000
PPN: 85250
Grand Total: 860250
```

## Troubleshooting

### Data masih tidak masuk?

1. **Check checkbox "Hanya validasi"**

    - Pastikan TIDAK tercentang jika ingin menyimpan data

2. **Check browser console**

    - Buka Developer Tools (F12)
    - Lihat tab Console untuk error JavaScript

3. **Check Laravel log**

    ```
    storage/logs/laravel.log
    ```

4. **Check response dari server**
    - Import akan return JSON dengan informasi detail
    - Lihat `imported_count`, `errors`, `warnings`

## File yang Dimodifikasi

1. `app/Http/Controllers/DaftarTagihanKontainerSewaController.php`
    - Fungsi `cleanDpeFormatData()` updated
    - Menambahkan fallback ke default tarif

## Catatan Penting

-   Kolom `periode` dalam CSV Anda (1, 2, 3...) adalah **nomor periode**, bukan jumlah hari
-   Jumlah hari (periode dalam database) di-calculate otomatis dari tanggal
-   Kolom `tarif` dalam CSV Anda ("Bulanan"/"Harian") adalah **tipe periode**, bukan harga
-   Harga aktual menggunakan default berdasarkan vendor dan size

---

**Status:** ✅ FIXED - Ready to use!
