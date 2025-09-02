# Update Tanggal Akhir Kontainer Sewa - Fitur Tarik Kontainer

## Deskripsi

Fitur ini mengimplementasikan update otomatis tanggal akhir kontainer di `daftar_tagihan_kontainer_sewa` ketika melakukan approval dengan kegiatan "tarik kontainer sewa".

## Kapan Fitur Aktif

Fitur ini akan aktif ketika:

1. **Jenis Kegiatan**: Mengandung kata "tarik" DAN "sewa", ATAU "pengambilan"

    - ✅ "tarik kontainer sewa"
    - ✅ "pengambilan kontainer"
    - ✅ "TARIK KONTAINER SEWA"
    - ✅ "PENGAMBILAN"
    - ❌ "pengiriman kontainer"
    - ❌ "bongkar muat"

2. **Vendor Terlibat**: ZONA, DPE, atau SOC
3. **Approval Status**: Selesai

## Proses yang Terjadi

### 1. Update Kontainer

```php
$kontainer->tanggal_selesai_sewa = $doneDate; // tanggal checkpoint supir
$kontainer->status = 'dikembalikan';
```

### 2. Update Daftar Tagihan Kontainer Sewa

-   **Mencari records existing**: berdasarkan `nomor_kontainer` dan `vendor` yang `tanggal_akhir` masih NULL
-   **Set tanggal_akhir**: menggunakan tanggal checkpoint supir
-   **Recalculate masa**: format "dd mmmm yyyy - dd mmmm yyyy"

### 3. Konversi Tarif Harian (Jika Perlu)

Jika masa sewa < 30 hari:

```php
$masaDays = $startObj->diffInDays($endObj) + 1;
if ($masaDays < 30) {
    $tagihan->tarif = 'Harian';
    $dailyRate = round((float)$originalDpp / 30, 2);
    $newDpp = round($dailyRate * $masaDays, 2);
    // Recalculate dpp_nilai_lain, ppn, pph, grand_total
}
```

## Method yang Dimodifikasi

### 1. PenyelesaianController::massProcess()

-   Tambah detection logic "tarik kontainer sewa"
-   Tambah call `updateTagihanTanggalAkhir()` untuk setiap kontainer

### 2. PenyelesaianController::store()

-   Tambah detection logic "tarik kontainer sewa"
-   Tambah call `updateTagihanTanggalAkhir()` untuk setiap kontainer

### 3. PenyelesaianController::updateTagihanTanggalAkhir() [NEW]

-   Method baru untuk update existing tagihan
-   Handle conversion ke tarif harian jika masa < 30 hari
-   Include comprehensive logging

## Input Data

-   **Tanggal Akhir**: Diambil dari tanggal checkpoint supir (tanggal_checkpoint)
-   **Nomor Kontainer**: Dari data kontainer yang terkait dengan permohonan
-   **Vendor**: Dari vendor_perusahaan di permohonan

## Logging

Setiap update akan menghasilkan log:

```
updateTagihanTanggalAkhir: Successfully updated records
- nomor_kontainer: CCLU1234567
- vendor: ZONA
- updated_count: 2
- tanggal_akhir: 2025-09-01
```

## Testing

1. ✅ Detection logic untuk berbagai nama kegiatan
2. ✅ Vendor validation (ZONA, DPE, SOC)
3. ✅ Integration dengan approval flow
4. ✅ Error handling dan logging

## Dampak pada System

-   ✅ Tagihan kontainer sewa otomatis ter-finalize dengan tanggal akhir
-   ✅ Tarif otomatis disesuaikan (harian vs bulanan) based on duration
-   ✅ History audit melalui comprehensive logging
-   ✅ No breaking changes pada existing functionality
