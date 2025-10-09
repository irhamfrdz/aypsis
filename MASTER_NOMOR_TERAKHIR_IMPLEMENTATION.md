# Implementasi Master Nomor Terakhir untuk DP OB dan Pembayaran OB

## Overview

Sistem pembayaran DP OB dan pembayaran OB telah diperbarui untuk menggunakan master nomor terakhir dengan modul `nomor_pembayaran`, mengikuti standar yang sama dengan pembayaran lainnya dalam sistem.

## Perubahan Implementasi

### 1. Model Updates

#### PembayaranDpOb Model

-   **Removed**: Old `generateNomorPembayaran()` instance method
-   **Added**: New static `generateNomorPembayaran($coaId)` method
-   **Integration**: Uses master nomor_terakhir dengan modul `nomor_pembayaran`

#### PembayaranOb Model

-   **Removed**: Old `generateNomorPembayaran()` instance method
-   **Added**: New static `generateNomorPembayaran($coaId)` method
-   **Integration**: Uses master nomor_terakhir dengan modul `nomor_pembayaran`

### 2. Controller Updates

#### PembayaranDpObController

-   **Added**: `generateNomor()` method untuk API preview
-   **Updated**: `store()` method menggunakan master nomor terakhir
-   **Removed**: Old tracking system dengan jenis dan prefix

#### PembayaranObController

-   **Added**: `generateNomor()` method untuk API preview
-   **Updated**: `store()` method menggunakan master nomor terakhir
-   **Removed**: Old tracking system dengan jenis dan prefix

### 3. Format Nomor Pembayaran

#### Format Standar

```
[KODE_BANK]-[MM]-[YY]-[XXXXXX]
```

#### Contoh Implementasi

-   **KBJ-10-25-000008**: Kas Besar, Oktober 2025, sequence 8
-   **MPJ-10-25-000009**: Kas Kecil, Oktober 2025, sequence 9
-   **BCA-10-25-000010**: Bank BCA, Oktober 2025, sequence 10

#### Komponen Format

-   **KODE_BANK**: Dari field `kode_nomor` di tabel `akun_coa`
-   **MM**: Bulan (2 digit), contoh: 10 untuk Oktober
-   **YY**: Tahun (2 digit), contoh: 25 untuk 2025
-   **XXXXXX**: Sequence 6 digit dari master nomor terakhir

### 4. Master Nomor Terakhir Integration

#### Module Configuration

-   **Modul**: `nomor_pembayaran`
-   **Shared Usage**: DP OB, Pembayaran OB, Pembayaran Aktivitas Lainnya, dll
-   **Increment Logic**: Auto increment +1 setiap pembayaran berhasil
-   **Thread Safety**: Menggunakan `lockForUpdate()` untuk concurrency

#### Database Tracking

```sql
-- Master tracking table
SELECT * FROM nomor_terakhir WHERE modul = 'nomor_pembayaran';

-- Current example:
-- modul: nomor_pembayaran
-- nomor_terakhir: 8
-- keterangan: Nomor pembayaran untuk seluruh modul pembayaran
```

### 5. API Endpoints

#### Generate Nomor Preview

-   **DP OB**: `GET /pembayaran-dp-ob/generate-nomor?kas_bank_id={id}`
-   **OB**: `GET /pembayaran-ob/generate-nomor?kas_bank_id={id}`

#### Response Format

```json
{
    "success": true,
    "nomor_pembayaran": "KBJ-10-25-000009",
    "preview": true
}
```

### 6. JavaScript Updates

#### Auto-Generation Logic

-   **Trigger**: Saat pilih bank/kas dari dropdown
-   **Preview**: Tidak increment nomor (preview saja)
-   **Fallback**: Generate manual jika API error
-   **Validation**: Require bank selection

#### Implementation

```javascript
async function generateNomor() {
    const kasBankId = document.getElementById("kas_bank").value;
    if (!kasBankId) return;

    const response = await fetch(`/generate-nomor?kas_bank_id=${kasBankId}`);
    const data = await response.json();

    if (data.success) {
        document.getElementById("nomor_pembayaran").value =
            data.nomor_pembayaran;
    }
}
```

## Keuntungan Sistem Baru

### 1. Konsistensi

-   **Unified System**: Semua modul pembayaran menggunakan sistem yang sama
-   **Standard Format**: Format nomor konsisten di seluruh aplikasi
-   **Centralized Tracking**: Satu master table untuk semua nomor

### 2. Data Integrity

-   **No Gaps**: Sequence number berurutan tanpa gap
-   **Concurrency Safe**: Thread-safe dengan database locking
-   **Audit Trail**: Complete tracking di master nomor terakhir

### 3. Maintainability

-   **Single Source of Truth**: Master nomor terakhir sebagai sumber tunggal
-   **Easier Debugging**: Clear tracking dan logging
-   **Scalable**: Easy untuk menambah modul pembayaran baru

### 4. User Experience

-   **Auto Generation**: Nomor otomatis ter-generate saat pilih bank
-   **Preview Mode**: User bisa lihat nomor sebelum save
-   **Intuitive Format**: Format yang mudah dibaca dan dipahami

## Migration Path

### From Old System

```php
// OLD: Per-module tracking
NomorTerakhir::where('jenis', 'pembayaran_dp_ob')
             ->where('prefix', $prefix)
             ->first();

// NEW: Centralized tracking
NomorTerakhir::where('modul', 'nomor_pembayaran')
             ->lockForUpdate()
             ->first();
```

### Database Cleanup

-   **Remove**: Old entries dengan jenis pembayaran_dp_ob dan pembayaran_ob
-   **Keep**: Master entry dengan modul nomor_pembayaran
-   **Validate**: Ensure sequence integrity

## Testing Validation

### Current Status

```php
// Master module exists
NomorTerakhir::where('modul', 'nomor_pembayaran')->exists(); // true

// Current sequence
$current = NomorTerakhir::where('modul', 'nomor_pembayaran')->value('nomor_terakhir'); // 7

// Next preview
$next = "KBJ-10-25-" . str_pad($current + 1, 6, '0', STR_PAD_LEFT); // KBJ-10-25-000008
```

### API Testing

-   ✅ Generate nomor preview works
-   ✅ Auto-increment on save works
-   ✅ Format consistency validated
-   ✅ JavaScript integration functional

## Production Deployment

### Pre-Deployment

1. **Backup**: nomor_terakhir table
2. **Validate**: Master nomor_pembayaran module exists
3. **Test**: API endpoints accessible
4. **Verify**: No conflicts dengan existing data

### Post-Deployment

1. **Monitor**: Sequence increment properly
2. **Validate**: Format consistency
3. **Check**: No gaps in numbering
4. **Test**: User workflow end-to-end

## Implementation Summary

| Feature                | DP OB | Pembayaran OB | Status      |
| ---------------------- | ----- | ------------- | ----------- |
| Master nomor terakhir  | ✅    | ✅            | Implemented |
| API generate nomor     | ✅    | ✅            | Implemented |
| Format standardization | ✅    | ✅            | Implemented |
| JavaScript integration | ✅    | ✅            | Implemented |
| Database cleanup       | ✅    | ✅            | Ready       |
| Thread safety          | ✅    | ✅            | Implemented |

**Result**: Unified, consistent, dan maintainable nomor pembayaran system untuk seluruh modul pembayaran.
