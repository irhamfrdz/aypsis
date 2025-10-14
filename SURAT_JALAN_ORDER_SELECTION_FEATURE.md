# Fitur Pemilihan Order untuk Surat Jalan - Dokumentasi

## Overview

Fitur ini memungkinkan user untuk memilih order terlebih dahulu sebelum membuat surat jalan. Data dari order yang dipilih akan mengisi otomatis form surat jalan untuk mempermudah dan mempercepat proses input.

## Flow Aplikasi

### 1. Pemilihan Order

-   User mengklik "Tambah Surat Jalan" dari halaman index
-   Sistem mengarahkan ke halaman pemilihan order (`/surat-jalan/select-order`)
-   User dapat melihat list order dengan status: active, confirmed, processing
-   User dapat melakukan pencarian dan filter order
-   User memilih order dengan mengklik tombol "Pilih Order"

### 2. Form Surat Jalan

-   Setelah memilih order, user diarahkan ke form create surat jalan
-   Form akan menampilkan informasi order yang dipilih di bagian atas
-   Field-field berikut terisi otomatis dari data order:
    -   Pengirim (dari relasi pengirim)
    -   Jenis Barang (dari relasi jenisBarang)
    -   Tujuan Pengambilan (dari field tujuan_ambil)
    -   Tujuan Pengiriman (dari field tujuan_kirim)
    -   Tipe Kontainer (dari field tipe_kontainer)
    -   Size Kontainer (dari field size_kontainer)

## File-File yang Dimodifikasi

### 1. Routes (`routes/web.php`)

```php
// Tambahan route untuk pemilihan order
Route::get('/surat-jalan/select-order', [\App\Http\Controllers\SuratJalanController::class, 'selectOrder'])
     ->name('surat-jalan.select-order')
     ->middleware('can:surat-jalan-create');
```

### 2. Controller (`app/Http/Controllers/SuratJalanController.php`)

**Method Baru:**

-   `selectOrder(Request $request)` - Menampilkan halaman pemilihan order dengan pencarian dan filter
-   Modifikasi `create(Request $request)` - Menangani parameter order_id dan me-load data order
-   Modifikasi `store(Request $request)` - Validasi dan simpan order_id ke surat jalan

### 3. Model (`app/Models/SuratJalan.php`)

**Penambahan:**

-   Field `order_id` ke $fillable array
-   Relationship `order()` ke model Order

### 4. Migration (`database/migrations/2025_10_14_110713_add_order_id_to_surat_jalans_table.php`)

**Schema Update:**

```php
$table->unsignedBigInteger('order_id')->nullable()->after('id');
$table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
$table->index('order_id');
```

### 5. Views

#### `resources/views/surat-jalan/select-order.blade.php` (Baru)

-   Halaman pemilihan order dengan tabel responsif
-   Filter by status (active, confirmed, processing)
-   Pencarian by nomor order, pengirim, jenis barang
-   Tombol "Pilih Order" hanya untuk status yang valid

#### `resources/views/surat-jalan/create.blade.php` (Dimodifikasi)

-   Info box order yang dipilih di bagian atas
-   Auto-fill form fields dari data order
-   Hidden input untuk order_id

#### `resources/views/surat-jalan/index.blade.php` (Dimodifikasi)

-   Tambah kolom "No. Order" di tabel
-   Update button "Tambah Surat Jalan" ke route select-order

#### `resources/views/surat-jalan/show.blade.php` (Dimodifikasi)

-   Tampilkan nomor order di detail surat jalan

## Fitur-Fitur

### 1. Validasi Order

-   Hanya order dengan status `active`, `confirmed`, atau `processing` yang bisa dipilih
-   Validasi di controller untuk memastikan order valid

### 2. Auto-Fill Data

Field yang terisi otomatis dari order:

```php
// Pengirim
value="{{ old('pengirim', $selectedOrder ? $selectedOrder->pengirim->nama ?? '' : '') }}"

// Jenis Barang
value="{{ old('jenis_barang', $selectedOrder ? $selectedOrder->jenisBarang->nama ?? '' : '') }}"

// Tujuan Pengambilan
value="{{ old('tujuan_pengambilan', $selectedOrder ? $selectedOrder->tujuan_ambil ?? '' : '') }}"

// Tujuan Pengiriman
value="{{ old('tujuan_pengiriman', $selectedOrder ? $selectedOrder->tujuan_kirim ?? '' : '') }}"

// Tipe Kontainer & Size
@php $selectedTipeKontainer = old('tipe_kontainer', $selectedOrder ? $selectedOrder->tipe_kontainer ?? '' : ''); @endphp
@php $selectedSize = old('size', $selectedOrder ? $selectedOrder->size_kontainer ?? '' : ''); @endphp
```

### 3. Pencarian & Filter

-   Pencarian: nomor order, pengirim, jenis barang, tujuan kirim/ambil
-   Filter by status order
-   Pagination dengan query string preserved

### 4. Relationship Tracking

-   Surat jalan akan menyimpan referensi ke order (order_id)
-   Menampilkan nomor order di list dan detail surat jalan
-   Foreign key constraint dengan ON DELETE SET NULL

## Database Schema

### Tabel: surat_jalans

```sql
-- Kolom baru
order_id BIGINT UNSIGNED NULL
INDEX idx_order_id (order_id)
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
```

## Manfaat Fitur

1. **Efisiensi Input**: Data order otomatis mengisi form surat jalan
2. **Konsistensi Data**: Mengurangi kesalahan input manual
3. **Tracking**: Mudah melacak hubungan antara order dan surat jalan
4. **User Experience**: Flow yang lebih intuitif untuk pembuatan surat jalan
5. **Validasi**: Hanya order yang valid yang bisa digunakan

## Permission yang Diperlukan

-   `surat-jalan-create`: Untuk akses halaman select order dan create surat jalan
-   `surat-jalan-view`: Untuk melihat list dan detail surat jalan
-   `surat-jalan-update`: Untuk edit surat jalan
-   `surat-jalan-delete`: Untuk hapus surat jalan

## Catatan Teknis

1. **Backward Compatibility**: Surat jalan tanpa order tetap bisa dibuat (order_id nullable)
2. **Performance**: Menggunakan eager loading `with('order')` untuk menghindari N+1 query
3. **Security**: Semua route protected dengan permission middleware
4. **Validation**: Full validation untuk order_id dengan `exists:orders,id`

## Testing

1. Akses `/surat-jalan/select-order` - harus tampil list order
2. Pilih order - harus redirect ke form create dengan data terisi
3. Submit form - data harus tersimpan dengan order_id
4. Lihat detail surat jalan - nomor order harus tampil
5. List surat jalan - kolom nomor order harus tampil

Fitur ini meningkatkan workflow pembuatan surat jalan dengan memberikan konteks order yang jelas dan mengurangi input manual.
