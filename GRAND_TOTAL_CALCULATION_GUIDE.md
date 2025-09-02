# Penjelasan Perhitungan Grand Total (DIPERBAIKI)

## 📊 Formula Grand Total yang BENAR

Grand Total dalam sistem tagihan kontainer sewa dihitung berdasarkan formula:

```
Grand Total = Adjusted DPP + PPN - PPH
```

**CATATAN PENTING**: DPP Nilai Lain TIDAK dimasukkan dalam perhitungan Grand Total!

### 1. **Adjusted DPP** (DPP yang Disesuaikan)

-   **Formula**: `DPP Asli + Adjustment`
-   **Contoh**: Rp 22,523 + Rp 10,000 = **Rp 32,523**
-   **Catatan**: Ini adalah DPP setelah penyesuaian/adjustment

### 2. **PPN** (Pajak Pertambahan Nilai - 11%)

-   **Formula**: `Adjusted DPP × 11%`
-   **Perhitungan**: Rp 32,523 × 11% = **Rp 3,578**
-   **Penting**: Dihitung dari DPP yang sudah disesuaikan, bukan DPP asli

### 3. **PPH** (Pajak Penghasilan - 2%)

-   **Formula**: `Adjusted DPP × 2%`
-   **Perhitungan**: Rp 32,523 × 2% = **Rp 650**
-   **Penting**: Dihitung dari DPP yang sudah disesuaikan, bukan DPP asli

### 4. **DPP Nilai Lain** (TIDAK masuk Grand Total)

-   **Sumber**: Langsung dari database (`dpp_nilai_lain`)
-   **Contoh**: **Rp 20,646**
-   **Catatan**: Kolom informasi terpisah, TIDAK digunakan dalam perhitungan Grand Total

## 🔄 Langkah-Langkah Perhitungan

### Step 1: Hitung Adjusted DPP

```php
$originalDpp = 22523;        // Dari database
$adjustment = 10000;         // Input adjustment
$adjustedDpp = $originalDpp + $adjustment;  // 32,523
```

### Step 2: Hitung PPN

```php
$ppnRate = 0.11;             // 11%
$calculatedPpn = $adjustedDpp * $ppnRate;   // 3,578
```

### Step 3: Hitung PPH

```php
$pphRate = 0.02;             // 2%
$calculatedPph = $adjustedDpp * $pphRate;   // 650
```

### Step 4: Hitung Grand Total

```php
// FORMULA YANG BENAR (tanpa DPP Nilai Lain)
$newGrandTotal = $adjustedDpp + $calculatedPpn - $calculatedPph;
// = 32,523 + 3,578 - 650 = 35,450
```

## 💰 Dampak Adjustment

### Tanpa Adjustment:

-   **DPP**: Rp 22,523
-   **PPN**: Rp 2,478 (dari DPP asli)
-   **PPH**: Rp 450 (dari DPP asli)
-   **Grand Total**: Rp 24,550

### Dengan Adjustment (+Rp 10,000):

-   **DPP**: Rp 32,523 (disesuaikan)
-   **PPN**: Rp 3,578 (dari DPP yang disesuaikan)
-   **PPH**: Rp 650 (dari DPP yang disesuaikan)
-   **Grand Total**: Rp 35,450

### Selisih:

**+Rp 10,900** (bukan hanya +Rp 10,000 karena PPN dan PPH juga berubah)

## 🎯 Mengapa Selisih Grand Total Bukan Sama dengan Adjustment?

Karena adjustment mempengaruhi:

1. **DPP** naik sebesar adjustment (+Rp 10,000)
2. **PPN** naik karena dihitung dari DPP baru (+Rp 1,100)
3. **PPH** naik karena dihitung dari DPP baru (-Rp 200)

**Total dampak**: +Rp 10,000 + Rp 1,100 - Rp 200 = **+Rp 10,900**

## 📝 Kode Implementasi

### Dalam View (Blade Template):

```php
@php
    // Hitung DPP yang disesuaikan
    $originalDpp = (float)(optional($tagihan)->dpp ?? 0);
    $adjustment = (float)(optional($tagihan)->adjustment ?? 0);
    $adjustedDpp = $originalDpp + $adjustment;

    // Hitung PPN dan PPH dari DPP yang disesuaikan
    $ppnRate = 0.11;
    $pphRate = 0.02;
    $calculatedPpn = $adjustedDpp * $ppnRate;
    $calculatedPph = $adjustedDpp * $pphRate;

    // Hitung Grand Total (FORMULA YANG BENAR - tanpa DPP Nilai Lain)
    $newGrandTotal = $adjustedDpp + $calculatedPpn - $calculatedPph;
@endphp
```

### Dalam Controller (Update Adjustment):

```php
public function updateAdjustment(Request $request, $id)
{
    $tagihan = DaftarTagihanKontainerSewa::findOrFail($id);
    $adjustment = $request->input('adjustment', 0);

    // Update adjustment
    $tagihan->adjustment = $adjustment;

    // Recalculate values
    $adjustedDpp = $tagihan->dpp + $adjustment;
    $tagihan->ppn = $adjustedDpp * 0.11;
    $tagihan->pph = $adjustedDpp * 0.02;

    // FORMULA YANG BENAR: tanpa DPP Nilai Lain
    $tagihan->grand_total = $adjustedDpp + $tagihan->ppn - $tagihan->pph;

    $tagihan->save();
}
```

## ✅ Validasi

Sistem telah diperbaiki dan memastikan:

-   ✅ PPN dihitung dari DPP yang disesuaikan
-   ✅ PPH dihitung dari DPP yang disesuaikan
-   ✅ Grand Total = DPP + PPN - PPH (TANPA DPP Nilai Lain)
-   ✅ Tampilan visual menunjukkan dampak adjustment
-   ✅ Database tersimpan dengan nilai yang benar

## ❌ Kesalahan yang Diperbaiki

### Formula Lama (SALAH):

```
Grand Total = Adjusted DPP + DPP Nilai Lain + PPN - PPH
```

### Formula Baru (BENAR):

```
Grand Total = Adjusted DPP + PPN - PPH
```

**Perbedaan**: DPP Nilai Lain (Rp 20,646) tidak lagi dimasukkan dalam perhitungan Grand Total.

## 🔍 Cara Verifikasi

1. **Manual**: Gunakan kalkulator untuk memverifikasi setiap langkah
2. **Script**: Jalankan `php explain_grand_total_calculation.php`
3. **Test**: Jalankan `php test_adjustment_impact.php`
4. **Browser**: Lihat langsung di interface sistem

---

**Tanggal**: 1 September 2025  
**Status**: ✅ Implementasi Lengkap
