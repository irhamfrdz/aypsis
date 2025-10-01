# Pembayaran Pranota Kontainer - Sistem Nomor Otomatis

## Implementasi

Sistem pembayaran pranota kontainer telah diperbarui untuk menggunakan master nomor terakhir dengan modul `nomor_pembayaran`. Nomor akan bertambah 1 setiap kali pembayaran berhasil dibuat.

## Format Nomor Pembayaran

```
BPK-[cetakan]-[YY]-[MM]-[XXXXXX]
```

-   **BPK**: Prefix (Bayar Pranota Kontainer)
-   **[cetakan]**: Nomor cetakan (1 digit, input dari user)
-   **[YY]**: Tahun (2 digit, contoh: 25 untuk 2025)
-   **[MM]**: Bulan (2 digit, contoh: 09 untuk September)
-   **[XXXXXX]**: Nomor terakhir + 1 (6 digit dengan leading zeros)

**Contoh**: `BPK-1-25-09-000002`

## Fitur yang Diimplementasi

### 1. Model PembayaranPranotaKontainer

-   `generateNomorPembayaran($cetakan)`: Generate nomor untuk preview (tidak update database)
-   `generateAndUpdateNomorPembayaran($cetakan)`: Generate nomor dan update database (untuk pembayaran berhasil)

### 2. Controller PembayaranPranotaKontainerController

-   `generateNomorPembayaran()`: API endpoint untuk AJAX request
-   `store()`: Update nomor_terakhir setelah pembayaran berhasil dibuat

### 3. Route

-   `GET /pembayaran-pranota-kontainer/generate-nomor`: Endpoint untuk mendapatkan nomor pembayaran

### 4. View JavaScript

-   AJAX call untuk mendapatkan nomor real-time
-   Auto-update saat nomor cetakan berubah
-   Fallback ke client-side generation jika server error

## Workflow

1. **Form Load**: JavaScript memanggil API `/generate-nomor` untuk mendapatkan nomor pembayaran
2. **User Input**: Saat user mengubah nomor cetakan, nomor pembayaran akan diperbarui secara real-time
3. **Form Submit**: User submit form dengan nomor pembayaran yang sudah di-generate
4. **Payment Success**: Jika pembayaran berhasil:
    - Extract running number dari nomor_pembayaran (6 digit terakhir)
    - Update `nomor_terakhir` di database dengan running number tersebut
    - Nomor berikutnya akan menjadi `nomor_terakhir + 1`

## Database

-   **Table**: `nomor_terakhir`
-   **Module**: `nomor_pembayaran`
-   **Field**: `nomor_terakhir` - menyimpan nomor terakhir yang telah digunakan

## Konsistensi Data

-   Menggunakan database transaction untuk memastikan konsistensi
-   Lock database saat generate dan update nomor
-   Nomor increment hanya terjadi setelah pembayaran benar-benar berhasil dibuat
