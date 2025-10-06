# Update: Filter DP yang Sudah Digunakan

## Problem

Setelah DP dipilih dan digunakan dalam transaksi pembayaran pranota kontainer, DP tersebut masih muncul di daftar pilihan saat membuat transaksi baru. Hal ini dapat menyebabkan:

-   DP yang sama digunakan berkali-kali
-   Konflik data dan perhitungan yang salah
-   User bingung melihat DP yang seharusnya sudah terpakai

## Solution ✅

Mengupdate API endpoint `getAvailableDP()` untuk memfilter DP yang sudah digunakan dalam transaksi sebelumnya.

## Implementasi

### 1. ✅ Query Filter di Controller

```php
// Get DP IDs that are already used in pembayaran pranota kontainer
$usedDPIds = \App\Models\PembayaranPranotaKontainer::whereNotNull('dp_payment_id')
    ->pluck('dp_payment_id')
    ->toArray();

// Filter out used DPs
$dpPayments = \App\Models\PembayaranAktivitasLainnya::where('is_dp', true)
    ->whereNotIn('id', $usedDPIds)  // Exclude already used DPs
    ->with(['bank', 'creator'])
    ->orderBy('created_at', 'desc')
    ->get()
```

### 2. ✅ Logic Flow

1. **Query DP yang sudah digunakan**: Ambil semua `dp_payment_id` dari tabel `pembayaran_pranota_kontainer`
2. **Filter daftar DP**: Gunakan `whereNotIn()` untuk mengecualikan DP yang sudah digunakan
3. **Return clean list**: Hanya DP yang belum pernah digunakan yang dikembalikan

### 3. ✅ Database Relationship

-   **Table**: `pembayaran_pranota_kontainer`
-   **Column**: `dp_payment_id` (foreign key ke `pembayaran_aktivitas_lainnya`)
-   **Logic**: Jika `dp_payment_id` tidak null, berarti DP sudah digunakan

## Benefits

### ✅ Data Integrity

-   **Prevent duplicate usage**: DP tidak bisa digunakan berkali-kali
-   **Accurate reporting**: Laporan pembayaran lebih akurat
-   **Clean audit trail**: Jelas DP mana yang sudah digunakan

### ✅ User Experience

-   **Clear options**: User hanya melihat DP yang benar-benar available
-   **No confusion**: Tidak ada DP yang sudah terpakai di daftar
-   **Better workflow**: Proses pemilihan DP lebih smooth

### ✅ Business Logic

-   **One-time use**: DP hanya bisa digunakan sekali sesuai konsep bisnis
-   **Proper accounting**: Setiap DP memiliki alokasi yang jelas
-   **Audit compliance**: Memudahkan audit dan tracking

## Testing Scenarios

### ✅ Scenario 1: First Time

1. Buka form pembayaran baru
2. Klik "Pilih DP"
3. **Result**: Semua DP available muncul

### ✅ Scenario 2: After DP Used

1. Buat transaksi dengan DP tertentu
2. Submit dan save
3. Buka form pembayaran baru lagi
4. Klik "Pilih DP"
5. **Result**: DP yang sudah digunakan **TIDAK MUNCUL** lagi

### ✅ Scenario 3: Multiple DPs

1. Ada 3 DP: A, B, C
2. Gunakan DP A di transaksi pertama
3. Buat transaksi kedua → Hanya DP B dan C yang muncul
4. Gunakan DP B di transaksi kedua
5. Buat transaksi ketiga → Hanya DP C yang muncul

### ✅ Scenario 4: No Available DP

1. Semua DP sudah digunakan
2. Klik "Pilih DP"
3. **Result**: Muncul pesan "Tidak ada pembayaran DP yang tersedia"

## Database Impact

### Query Performance

-   **Additional query**: Satu query extra untuk mengambil used DP IDs
-   **Index recommendation**: Buat index pada `dp_payment_id` untuk performa optimal
-   **Impact**: Minimal, karena query sederhana dan data tidak terlalu besar

### Data Consistency

-   **No data change**: Tidak mengubah struktur data existing
-   **Backward compatible**: Tidak mempengaruhi transaksi yang sudah ada
-   **Safe implementation**: Hanya filtering, tidak ada data manipulation

## Files Modified

### ✅ Controller Update

-   **File**: `app/Http/Controllers/PembayaranPranotaKontainerController.php`
-   **Method**: `getAvailableDP()`
-   **Change**: Added filtering logic untuk exclude used DPs

## Status: COMPLETE ✅

**DP yang sudah digunakan sekarang tidak akan muncul lagi saat membuat transaksi baru!**

### Before:

❌ DP yang sudah digunakan masih muncul di daftar pilihan

### After:

✅ **Hanya DP yang belum digunakan yang muncul di daftar**

### Key Benefits:

-   🛡️ **Data Integrity**: Prevent duplicate DP usage
-   🎯 **Clear UX**: User hanya melihat DP yang available
-   📊 **Accurate Accounting**: Setiap DP hanya digunakan sekali
-   ✨ **Business Logic**: Sesuai konsep DP sebagai one-time payment

**Fitur sudah siap digunakan dan akan otomatis memfilter DP yang sudah terpakai!** 🚀
