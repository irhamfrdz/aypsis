# Fitur Pilih DP untuk Pembayaran Pranota Kontainer

## Overview

Fitur ini memungkinkan user untuk memilih pembayaran DP (Down Payment) dari modul **Pembayaran Aktivitas Lain-lain** yang sudah diinput sebelumnya, untuk digunakan dalam pembayaran pranota kontainer.

## Implementasi Lengkap ✅

### 1. Database Schema Changes

-   **Migration**: `add_dp_reference_to_pembayaran_pranota_kontainer`
-   **New Columns**:
    -   `dp_payment_id` (foreign key ke `pembayaran_aktivitas_lainnya`)
    -   `dp_amount` (decimal untuk menyimpan jumlah DP)

### 2. Model Updates

-   **PembayaranPranotaKontainer.php**:
    -   Added `dp_payment_id` dan `dp_amount` ke `$fillable`
    -   Added `dp_amount` cast sebagai `decimal:2`
    -   Added relationship `dpPayment()` ke PembayaranAktivitasLainnya

### 3. Controller Enhancements

-   **PembayaranPranotaKontainerController.php**:
    -   **New Method**: `getAvailableDP()` - API endpoint untuk mengambil daftar DP
    -   **Updated**: `store()` method dengan validasi dan penyimpanan DP
    -   **Route**: `/pembayaran-pranota-kontainer/get-available-dp`

### 4. Frontend Implementation

-   **create.blade.php**:
    -   ✅ **Tombol "Pilih DP"** dengan icon money-bill-wave
    -   ✅ **Modal untuk memilih DP** dengan fitur:
        -   Loading state saat fetch data
        -   Search/filter berdasarkan nomor pembayaran, aktivitas, bank
        -   Table dengan data lengkap DP (nomor, tanggal, jumlah, bank, aktivitas, creator)
        -   Radio button selection
        -   Responsive design dengan Tailwind CSS
    -   ✅ **Hidden inputs** untuk menyimpan DP yang dipilih
    -   ✅ **Info panel** menampilkan DP yang sudah dipilih
    -   ✅ **JavaScript integration** untuk handling modal dan data

### 5. API Endpoint

**URL**: `/pembayaran-pranota-kontainer/get-available-dp`
**Method**: GET
**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nomor_pembayaran": "PAL-10-25-000001",
            "tanggal_pembayaran": "04/10/2025",
            "total_pembayaran": 5000000,
            "total_formatted": "Rp 5.000.000",
            "bank_name": "BCA - Operasional",
            "aktivitas_pembayaran": "DP Pembayaran Kontainer",
            "creator_name": "admin"
        }
    ]
}
```

### 6. User Experience Flow

1. **User buka form** pembayaran pranota kontainer
2. **Klik "Pilih DP"** → Modal terbuka dengan loading
3. **Data DP dimuat** dari server (hanya yang `is_dp = true`)
4. **User dapat search/filter** berdasarkan nomor, aktivitas, atau bank
5. **Klik row atau radio** untuk memilih DP
6. **Klik "Pilih DP"** untuk konfirmasi
7. **Info DP muncul** di form dengan detail yang dipilih
8. **Tombol berubah** jadi "Ubah DP" untuk edit pilihan
9. **Submit form** akan menyimpan referensi DP ke database

### 7. Data Validation

```php
'selected_dp_id' => 'nullable|exists:pembayaran_aktivitas_lainnya,id',
'selected_dp_amount' => 'nullable|numeric'
```

### 8. Security Features

-   ✅ Permission middleware: `pembayaran-pranota-kontainer-create`
-   ✅ CSRF protection pada form
-   ✅ Foreign key constraint untuk data integrity
-   ✅ Validation pada server side

### 9. Error Handling

-   ✅ Loading state saat fetch data
-   ✅ Error message jika gagal load data
-   ✅ Fallback jika tidak ada DP tersedia
-   ✅ Validation errors ditampilkan di form

### 10. Features

-   ✅ **Responsive Design** - Modal dan table responsive
-   ✅ **Real-time Search** - Filter data DP secara live
-   ✅ **Visual Feedback** - Loading, selected state, button changes
-   ✅ **Data Formatting** - Currency format, date format
-   ✅ **Keyboard Navigation** - Modal dapat ditutup dengan ESC
-   ✅ **Accessibility** - Proper ARIA labels dan focus management

## Testing Scenarios

### ✅ Scenario 1: Ada DP tersedia

1. Klik "Pilih DP" → Modal terbuka
2. Data DP ditampilkan dalam table
3. Search berfungsi untuk filter data
4. Select DP → Info muncul di form
5. Submit form → Data tersimpan dengan referensi DP

### ✅ Scenario 2: Tidak ada DP

1. Klik "Pilih DP" → Modal terbuka
2. Tampil pesan "Tidak ada pembayaran DP yang tersedia"
3. Tombol "Pilih DP" disabled

### ✅ Scenario 3: Error loading

1. Server error → Tampil alert error
2. Modal tertutup otomatis
3. User bisa coba lagi

### ✅ Scenario 4: Ubah pilihan DP

1. User sudah pilih DP → Tombol jadi "Ubah DP"
2. Klik "Ubah DP" → Modal terbuka lagi
3. Bisa pilih DP lain atau batal

## Files Modified/Created

### Database

-   ✅ `database/migrations/2025_10_04_080627_add_dp_reference_to_pembayaran_pranota_kontainer.php`

### Models

-   ✅ `app/Models/PembayaranPranotaKontainer.php`

### Controllers

-   ✅ `app/Http/Controllers/PembayaranPranotaKontainerController.php`

### Routes

-   ✅ `routes/web.php`

### Views

-   ✅ `resources/views/pembayaran-pranota-kontainer/create.blade.php`

## Status: COMPLETE ✅

Fitur telah diimplementasikan dengan lengkap dan siap untuk digunakan. User sekarang dapat memilih pembayaran DP dari modul Pembayaran Aktivitas Lain-lain untuk digunakan dalam pembayaran pranota kontainer.

**Key Benefits:**

-   💰 Integrasi DP antar modul
-   🔍 Search dan filter data DP
-   📱 Responsive design
-   🛡️ Security dan validation lengkap
-   ✨ User experience yang smooth
