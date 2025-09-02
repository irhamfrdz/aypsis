# Format Nomor Pranota Baru

## Deskripsi

Sistem pranota telah diupdate untuk menggunakan format nomor pranota yang baru sesuai dengan permintaan.

## Format Nomor Pranota

Format: **PTK + 1 digit nomor cetakan + 2 digit tahun + 2 digit bulan + 6 digit running number**

### Komponen Format:

1. **PTK** - Kode prefix (2 karakter)
2. **X** - Nomor cetakan (1 digit: 1-9)
3. **YY** - Tahun 2 digit (contoh: 25 untuk 2025)
4. **MM** - Bulan 2 digit (contoh: 09 untuk September)
5. **NNNNNN** - Running number 6 digit (contoh: 000001)

### Contoh:

-   `PTK12509000001` - Pranota cetakan 1, September 2025, nomor urut 1
-   `PTK22509000002` - Pranota cetakan 2, September 2025, nomor urut 2
-   `PTK11225000123` - Pranota cetakan 1, Desember 2025, nomor urut 123

## Implementasi

### 1. Controller (PranotaController.php)

-   Method `store()` dan `bulkStore()` telah diupdate
-   Generate nomor otomatis berdasarkan:
    -   Nomor cetakan dari input user (default: 1)
    -   Tahun dan bulan saat ini
    -   Running number berdasarkan jumlah pranota di bulan yang sama

### 2. Frontend (Modal Pranota)

-   Field "Nomor Pranota" menjadi read-only dan auto-generate
-   Ditambahkan field "Nomor Cetakan" yang bisa diubah user (1-9)
-   JavaScript untuk update preview nomor pranota real-time

### 3. Database

-   Tidak ada perubahan struktur database
-   Field `no_invoice` di tabel `pranotalist` menyimpan nomor dengan format baru

## Fitur

### Auto-generation

-   Nomor pranota dibuat otomatis oleh sistem
-   User hanya perlu memasukkan nomor cetakan (opsional, default 1)
-   Running number increment otomatis per bulan

### Real-time Preview

-   Preview nomor pranota di modal berubah saat user mengubah nomor cetakan
-   Format konsisten di seluruh aplikasi

### Backward Compatibility

-   Pranota lama dengan format berbeda tetap bisa ditampilkan
-   Sistem bisa menangani mixed format (lama dan baru)

## Testing

### Test Scripts

1. `test_nomor_pranota_format.php` - Test format generation
2. `test_pranota_creation.php` - Test pembuatan pranota dengan format baru

### Test Results

```
✓ Format PTK berhasil diimplement
✓ Auto-generation nomor berhasil
✓ Running number increment correctly
✓ Different nomor cetakan handled properly
✓ Month/year calculation accurate
```

## Usage

### Melalui Web Interface:

1. Buka halaman Daftar Tagihan Kontainer Sewa
2. Pilih tagihan yang ingin dimasukkan pranota
3. Klik "Buat Pranota"
4. Ubah "Nomor Cetakan" jika diperlukan (1-9)
5. Nomor pranota akan ter-generate otomatis
6. Submit form

### Programmatically:

```php
// Single pranota
$pranota = Pranota::create([
    'no_invoice' => $generatedNumber, // PTK format
    'total_amount' => $amount,
    'status' => 'draft',
    // ... other fields
]);

// With custom nomor cetakan
$request->input('nomor_cetakan', 1); // Default 1
```

## Benefits

1. **Konsistensi** - Format terstandardisasi di seluruh sistem
2. **Traceability** - Mudah track berdasarkan tahun/bulan/cetakan
3. **User-friendly** - Auto-generation mengurangi error input
4. **Scalability** - Running number 6 digit support up to 999,999 pranota per bulan
5. **Flexibility** - Multiple nomor cetakan untuk kebutuhan berbeda
