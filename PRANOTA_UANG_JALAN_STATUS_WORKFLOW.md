# PRANOTA UANG JALAN - STATUS WORKFLOW

## Ringkasan Fitur

Sistem pranota uang jalan telah dilengkapi dengan workflow status yang otomatis mengubah status uang jalan dari "belum masuk pranota" menjadi "sudah masuk pranota" setelah berhasil membuat pranota.

## Status Workflow

### Status Uang Jalan

1. **belum_dibayar** - Status awal uang jalan yang baru dibuat
2. **belum_masuk_pranota** - Uang jalan belum dimasukkan ke pranota
3. **sudah_masuk_pranota** - Uang jalan sudah dimasukkan ke pranota
4. **lunas** - Uang jalan sudah dibayar
5. **dibatalkan** - Uang jalan dibatalkan

### Workflow Otomatis

```
Uang Jalan Baru
    ↓ (status: belum_masuk_pranota)
Tersedia untuk Pranota
    ↓ (pilih untuk pranota)
Berhasil Membuat Pranota
    ↓ (otomatis update status)
Status: sudah_masuk_pranota
```

## Perubahan yang Dilakukan

### 1. Controller - PranotaSuratJalanController.php

#### Method `create()`

-   **Sebelum**: Hanya menampilkan uang jalan dengan status `'belum_dibayar'`
-   **Sesudah**: Menampilkan uang jalan dengan status `['belum_dibayar', 'belum_masuk_pranota']`

#### Method `store()`

-   **Validasi Tambahan**: Memastikan uang jalan yang dipilih tersedia untuk pranota
-   **Update Status Otomatis**: Mengubah status uang jalan menjadi `'sudah_masuk_pranota'`
-   **Pesan Sukses**: Menampilkan informasi jumlah uang jalan yang statusnya diubah

### 2. View - create.blade.php

#### Informasi Status

-   **Info Banner**: Menjelaskan perubahan status yang akan terjadi
-   **Status Badge**: Menampilkan status saat ini pada setiap uang jalan
-   **Kolom Status**: Ditambahkan kolom status pada tabel

#### Status Display

```php
'belum_dibayar' => ['bg-yellow-100', 'text-yellow-800', 'Belum Dibayar'],
'belum_masuk_pranota' => ['bg-orange-100', 'text-orange-800', 'Belum Pranota'],
'sudah_masuk_pranota' => ['bg-blue-100', 'text-blue-800', 'Sudah Pranota'],
```

### 3. Validasi dan Security

#### Validasi Tambahan

```php
// Memastikan uang jalan yang dipilih masih tersedia
$selectedUangJalans = UangJalan::whereIn('id', $request->uang_jalan_ids)
    ->whereDoesntHave('pranotaUangJalan')
    ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
    ->get();

if ($selectedUangJalans->count() !== count($request->uang_jalan_ids)) {
    return redirect()->back()
        ->withErrors(['uang_jalan_ids' => 'Beberapa uang jalan yang dipilih tidak tersedia atau sudah masuk pranota.'])
        ->withInput();
}
```

## Testing

### Data Test

-   **ID 1**: UJ1125000001, Status: belum_dibayar, Total: Rp 805,000
-   **ID 2**: UJ1125000002, Status: belum_masuk_pranota, Total: Rp 895,000

### Scenario Test

1. ✅ Menampilkan uang jalan dengan status yang tepat
2. ✅ Validasi uang jalan yang tersedia
3. ✅ Update status otomatis setelah membuat pranota
4. ✅ Pesan sukses dengan informasi update

## Manfaat Fitur

### 1. Tracking Status

-   Admin dapat melihat status uang jalan dengan jelas
-   Mencegah double entry ke pranota
-   Tracking workflow pembayaran

### 2. User Experience

-   Informasi yang jelas tentang perubahan status
-   Visual feedback dengan status badge
-   Pesan konfirmasi yang informatif

### 3. Data Integrity

-   Validasi ketat sebelum membuat pranota
-   Transaction rollback jika ada error
-   Logging aktivitas untuk audit

## File yang Dimodifikasi

1. `app/Http/Controllers/PranotaSuratJalanController.php`

    - Update method create() dan store()
    - Tambah validasi dan status update

2. `resources/views/pranota-uang-jalan/create.blade.php`

    - Tambah info banner status workflow
    - Tambah kolom status pada tabel
    - Update status badge display

3. Database Migration (sudah ada)
    - `2025_11_07_095911_add_belum_masuk_pranota_to_uang_jalans_status_enum.php`

## Workflow Integration

### Integrasi dengan Sistem Lain

-   **Uang Jalan**: Status otomatis diupdate
-   **Pranota**: Relasi many-to-many dengan uang jalan
-   **Pembayaran**: Status siap untuk proses pembayaran
-   **Audit Log**: Tracking perubahan status

### Next Steps

1. Implementasi reverse workflow (hapus dari pranota)
2. Bulk status update untuk multiple pranota
3. Dashboard monitoring status uang jalan
4. Notifikasi untuk status changes
