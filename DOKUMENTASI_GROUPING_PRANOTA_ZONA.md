# Fitur Grouping Pranota Kontainer Sewa Berdasarkan Invoice Vendor dan Nomor Bank

## Overview

Fitur ini memungkinkan sistem untuk secara otomatis mengelompokkan kontainer yang memiliki **nomor invoice vendor** dan **nomor bank** yang sama ke dalam satu pranota, sehingga mengurangi jumlah pranota yang harus dibuat dan memudahkan pengelolaan.

## Analisis Data Zona

Berdasarkan analisis file `Zona.csv`:

### Statistik Data:

-   **Total kontainer**: 712
-   **Kontainer dengan data lengkap** (invoice vendor + nomor bank): 209
-   **Kontainer tanpa invoice vendor**: 111
-   **Kontainer tanpa nomor bank**: 392
-   **Jumlah group yang terbentuk**: 121
-   **Penghematan**: 88 pranota (dari 209 menjadi 121)

### Group Efisien (Multiple Kontainer):

-   **55 group** dengan **143 kontainer** (rata-rata 2.6 kontainer per pranota)
-   **Penghematan signifikan**: 88 pranota

### Contoh Group Efisien:

1. **Invoice ZONA25.05.28123 + Bank EBK250600289**: 5 kontainer
2. **Invoice ZONA24.01.22359 + Bank EBK240500055**: 4 kontainer
3. **Invoice ZONA24.02.22623 + Bank EBK240500055**: 4 kontainer

## Implementasi

### 1. Controller Methods

#### `createPranotaByVendorInvoiceGroup(Request $request)`

-   **Purpose**: Membuat pranota berdasarkan grouping invoice vendor + nomor bank
-   **Input**: Array ID tagihan kontainer sewa
-   **Process**:
    1. Filter kontainer yang memiliki invoice vendor dan nomor bank
    2. Group berdasarkan kombinasi `no_invoice_vendor|no_bank`
    3. Buat pranota untuk setiap group
    4. Update status tagihan menjadi 'included'
-   **Output**: Redirect dengan pesan sukses/error

#### `previewVendorInvoiceGrouping(Request $request)`

-   **Purpose**: Preview hasil grouping sebelum membuat pranota
-   **Input**: Array ID tagihan kontainer sewa
-   **Output**: JSON response dengan detail grouping

#### `groupKontainerByVendorInvoiceAndBank($tagihanItems)` (Private)

-   **Purpose**: Helper method untuk logic grouping
-   **Input**: Collection tagihan items
-   **Output**: Array grouped data dengan key `invoice_vendor|no_bank`

### 2. Routes

```php
// Create pranota by grouping
Route::post('pranota-kontainer-sewa/create-by-vendor-invoice-group',
    [PranotaTagihanKontainerSewaController::class, 'createPranotaByVendorInvoiceGroup'])
    ->name('pranota-kontainer-sewa.create-by-vendor-invoice-group')
    ->middleware('can:pranota-kontainer-sewa-create');

// Preview grouping
Route::post('pranota-kontainer-sewa/preview-vendor-invoice-grouping',
    [PranotaTagihanKontainerSewaController::class, 'previewVendorInvoiceGrouping'])
    ->name('pranota-kontainer-sewa.preview-vendor-invoice-grouping')
    ->middleware('can:pranota-kontainer-sewa-view');
```

### 3. Database Requirements

Pastikan tabel `daftar_tagihan_kontainer_sewa` memiliki kolom:

-   `no_invoice_vendor` (string, nullable)
-   `no_bank` (string, nullable)
-   `tgl_invoice_vendor` (date, nullable)
-   `tgl_bank` (date, nullable)
-   `status_pranota` (enum: 'pending', 'included')
-   `pranota_id` (foreign key, nullable)

## Usage Flow

### 1. Frontend Implementation

```javascript
// Preview grouping sebelum create
function previewGrouping(selectedIds) {
    fetch("/pranota-kontainer-sewa/preview-vendor-invoice-grouping", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
            tagihan_kontainer_sewa_ids: selectedIds,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                displayGroupingPreview(data.preview_data, data.summary);
            }
        });
}

// Create pranota by grouping
function createPranotaByGroup(selectedIds) {
    fetch("/pranota-kontainer-sewa/create-by-vendor-invoice-group", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
            tagihan_kontainer_sewa_ids: selectedIds,
        }),
    }).then((response) => {
        if (response.ok) {
            location.reload(); // Refresh halaman
        }
    });
}
```

### 2. User Workflow

1. User memilih multiple tagihan kontainer sewa
2. User klik **"Preview Grouping"** (opsional)
    - Sistem menampilkan preview group yang akan terbentuk
    - Menunjukkan kontainer mana yang akan masuk group mana
3. User klik **"Buat Pranota Berdasarkan Invoice & Bank"**
4. Sistem membuat pranota sesuai grouping
5. User mendapat feedback: "X pranota berhasil dibuat untuk Y kontainer"

## Expected Results

### Input Example:

```
Selected Tagihan IDs: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

Data:
- FORU8416289: Invoice ZONA25.05.28123, Bank EBK250600289
- FORU8480890: Invoice ZONA25.05.28123, Bank EBK250600289
- IFLU2990380: Invoice ZONA25.05.28123, Bank EBK250600289
- MSKU2218091: Invoice ZONA25.05.28123, Bank EBK250600289
- TTNU3216943: Invoice ZONA25.05.28123, Bank EBK250600289
- EGHU9005182: Invoice ZONA24.01.22359, Bank EBK240500055
- NYKU5622053: Invoice ZONA24.01.22359, Bank EBK240500055
- MSCU9903797: Invoice ZONA24.01.22359, Bank EBK240500055
- VGCU2886097: Invoice ZONA24.01.22359, Bank EBK240500055
- TDRU6124340: Invoice ZONA24.02.22500, Bank EBK240500055
```

### Output:

```
3 pranota berhasil dibuat untuk 10 kontainer berdasarkan grouping invoice vendor dan nomor bank:

- PMS1124250001: Invoice Vendor ZONA25.05.28123, Bank EBK250600289 (5 kontainer, Rp 2.823.200,00)
- PMS1124250002: Invoice Vendor ZONA24.01.22359, Bank EBK240500055 (4 kontainer, Rp 5.499.100,00)
- PMS1124250003: Invoice Vendor ZONA24.02.22500, Bank EBK240500055 (1 kontainer, Rp 1.374.775,00)

Penghematan: 7 pranota (dari 10 menjadi 3)
```

## Benefits

### 1. Efisiensi Operasional

-   **Mengurangi jumlah pranota**: Dari 209 menjadi 121 (penghematan 88 pranota)
-   **Pengelolaan lebih mudah**: Satu pranota per kombinasi invoice-bank
-   **Konsistensi data**: Kontainer dengan invoice dan bank yang sama dikelompokkan

### 2. Akurasi Pembayaran

-   **Sesuai dengan invoice vendor**: Pranota mengikuti pengelompokan invoice
-   **Sesuai dengan nomor bank**: Memudahkan rekonsiliasi bank
-   **Mengurangi error**: Otomatis grouping mengurangi human error

### 3. Reporting & Tracking

-   **Lebih mudah dilacak**: Satu pranota = satu kombinasi invoice-bank
-   **Audit trail yang jelas**: History pembayaran per invoice vendor
-   **Rekonsiliasi bank lebih mudah**: Sesuai dengan nomor bank

## Error Handling

### 1. Data Tidak Lengkap

```php
// Kontainer tanpa invoice vendor atau nomor bank akan di-skip
if (empty($item->no_invoice_vendor) || empty($item->no_bank)) {
    continue; // Skip item ini
}
```

### 2. Tidak Ada Group Yang Terbentuk

```php
if (empty($createdPranota)) {
    return redirect()->back()->with('warning',
        'Tidak ada pranota yang dibuat. Pastikan kontainer memiliki nomor invoice vendor dan nomor bank yang lengkap.');
}
```

### 3. Database Transaction

```php
try {
    DB::beginTransaction();
    // Process grouping and create pranota
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    return redirect()->back()->with('error', 'Gagal membuat pranota: ' . $e->getMessage());
}
```

## Testing

Test telah dilakukan dengan hasil:

-   ✅ **Grouping logic berfungsi dengan benar**
-   ✅ **Filter data tidak lengkap berfungsi**
-   ✅ **Penghematan pranota sesuai ekspektasi**
-   ✅ **Total amount calculation akurat**

## Deployment Checklist

1. ✅ **Controller methods** sudah dibuat
2. ✅ **Routes** sudah ditambahkan
3. ✅ **Testing** sudah dilakukan
4. ⏳ **Frontend UI** perlu ditambahkan
5. ⏳ **Database migration** jika diperlukan kolom baru
6. ⏳ **Permission setup** untuk fitur baru
7. ⏳ **Documentation** untuk end user

## Next Steps

1. **Frontend Development**:

    - Tambahkan tombol "Buat Pranota Berdasarkan Invoice & Bank" di halaman tagihan kontainer sewa
    - Implement preview modal untuk grouping
    - Add loading states dan error handling

2. **Enhancement**:

    - Add batch processing untuk dataset besar
    - Implement background job jika diperlukan
    - Add export functionality untuk hasil grouping

3. **Documentation**:
    - User manual untuk fitur baru
    - API documentation untuk developer
