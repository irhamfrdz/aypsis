# DP Status Tracking Implementation

## Overview

Implementasi sistem tracking status DP (Down Payment) pada pembayaran DP OB dengan status "dp_belum_terpakai" dan "dp_terpakai".

## Database Changes

### 1. Migration untuk Status DP

```php
// Modified: 2025_10_09_145740_create_pembayaran_dp_obs_table.php
$table->enum('status', ['dp_belum_terpakai', 'dp_terpakai'])->default('dp_belum_terpakai');
```

### 2. Migration untuk Relasi

```php
// New: 2025_10_09_152620_add_pembayaran_dp_ob_id_to_pembayaran_obs_table.php
$table->foreignId('pembayaran_dp_ob_id')->nullable()->after('keterangan')->constrained('pembayaran_dp_obs')->onDelete('set null');
```

## Model Updates

### 1. PembayaranDpOb Model

-   **Added status methods**:

    -   `markAsTerpakai()` - Ubah status ke dp_terpakai
    -   `markAsBelumTerpakai()` - Ubah status ke dp_belum_terpakai
    -   `isDpTerpakai()` - Check apakah DP sudah terpakai
    -   `isDpBelumTerpakai()` - Check apakah DP belum terpakai

-   **Added query scopes**:

    -   `scopeBelumTerpakai()` - Filter DP yang belum terpakai
    -   `scopeTerpakai()` - Filter DP yang sudah terpakai

-   **Added relationships**:
    -   `pembayaranObs()` - Relasi hasMany ke PembayaranOb

### 2. PembayaranOb Model

-   **Added fillable field**: `pembayaran_dp_ob_id`
-   **Added relationship**: `pembayaranDpOb()` - Relasi belongsTo

## Controller Updates

### 1. PembayaranDpObController

-   **Updated index method**: Menambah filter berdasarkan status DP
-   **Filter parameters**: status (dp_belum_terpakai, dp_terpakai)

### 2. PembayaranObController

-   **Updated create method**: Menambah list DP yang belum terpakai
-   **Updated store validation**: Menambah field `pembayaran_dp_ob_id`
-   **Updated store logic**: Auto update status DP ketika digunakan

## View Updates

### 1. Pembayaran DP OB Index (resources/views/pembayaran-dp-ob/index.blade.php)

-   **Added status column** dengan badge visual:
    -   🕒 DP Belum Terpakai (blue badge)
    -   ✅ DP Terpakai (green badge)
-   **Added status filter** di form pencarian
-   **Updated grid** dari 4 kolom ke 5 kolom untuk filter
-   **Updated colspan** untuk empty state

### 2. Pembayaran OB Create (resources/views/pembayaran-ob/create.blade.php)

-   **Added DP selection dropdown** setelah field keterangan
-   **Dropdown features**:
    -   Show nomor pembayaran, tanggal, jumlah supir, total
    -   Show keterangan jika ada
    -   Show warning jika tidak ada DP tersedia
    -   Optional field (nullable)

## Business Logic

### 1. DP Tracking Flow

1. **DP Creation**: Status otomatis `dp_belum_terpakai`
2. **DP Usage**: Ketika dipilih di pembayaran OB, status berubah ke `dp_terpakai`
3. **DP Display**: Index menampilkan status dengan badge warna dan informasi penggunaan

### 2. Status Update Logic

```php
// Dalam PembayaranObController@store
if ($validated['pembayaran_dp_ob_id']) {
    $dpOb = \App\Models\PembayaranDpOb::find($validated['pembayaran_dp_ob_id']);
    if ($dpOb) {
        $dpOb->markAsTerpakai();
    }
}
```

### 3. Filter Implementation

-   **DP OB Index**: Filter berdasarkan status DP
-   **OB Create**: Hanya tampilkan DP dengan status `dp_belum_terpakai`

## Visual Features

### 1. Status Badges

-   **DP Belum Terpakai**: Blue badge dengan clock icon
-   **DP Terpakai**: Green badge dengan check icon
-   **Usage Info**: Tampilkan berapa pembayaran OB yang menggunakan DP

### 2. DP Selection

-   **Informative dropdown**: Nomor, tanggal, supir, total, keterangan
-   **Smart filtering**: Hanya DP yang belum terpakai
-   **Warning message**: Jika tidak ada DP tersedia

## Database Schema

```sql
-- pembayaran_dp_obs table
status ENUM('dp_belum_terpakai', 'dp_terpakai') DEFAULT 'dp_belum_terpakai'

-- pembayaran_obs table
pembayaran_dp_ob_id BIGINT UNSIGNED NULL
FOREIGN KEY (pembayaran_dp_ob_id) REFERENCES pembayaran_dp_obs(id) ON DELETE SET NULL
```

## Usage Examples

### 1. Create DP

-   DP otomatis status `dp_belum_terpakai`
-   Muncul di dropdown pembayaran OB

### 2. Use DP in OB Payment

-   Pilih DP dari dropdown
-   Status DP berubah ke `dp_terpakai`
-   DP tidak muncul lagi di dropdown untuk pembayaran OB baru

### 3. Track DP Usage

-   View index DP OB menampilkan status dengan badge
-   Filter berdasarkan status
-   Info berapa kali DP digunakan

## Benefits

1. **Clear Status Tracking**: Visual indicator status DP
2. **Prevent Duplicate Usage**: DP yang sudah terpakai tidak bisa digunakan lagi
3. **Audit Trail**: Relasi antara DP dan pembayaran OB yang menggunakannya
4. **User Friendly**: Filter dan dropdown informatif
5. **Data Integrity**: Foreign key dengan soft delete protection

## Testing Checklist

-   [x] Migration berhasil dijalankan
-   [x] Model relationships berfungsi
-   [x] Controller logic update status DP
-   [x] View menampilkan status dengan benar
-   [x] Filter status berfungsi
-   [x] DP selection dropdown berfungsi
-   [x] Status update otomatis ketika DP digunakan

## Notes

-   Status DP tidak bisa diubah manual di UI (business logic controlled)
-   Relasi menggunakan `onDelete('set null')` untuk data integrity
-   Filter dan dropdown hanya menampilkan DP yang relevan
-   Visual feedback yang jelas untuk user experience
