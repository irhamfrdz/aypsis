# Tanda Terima Module - Documentation

## Overview

**Tanda Terima** adalah modul untuk mengelola tanda terima kontainer yang **otomatis dibuat** setelah Surat Jalan di-approve. Modul ini menambahkan data tambahan seperti informasi kapal, tanggal pengambilan, berat, dan dimensi barang.

## Features

### ✅ Implemented

1. **Auto-Creation from Approval**

    - Tanda terima otomatis dibuat ketika surat jalan di-approve
    - Semua data dari surat jalan di-copy ke tanda terima
    - Status awal: `draft`

2. **Data Fields**

    - **From Surat Jalan**: No. surat jalan, tanggal, supir, kegiatan, size, jumlah kontainer, no. kontainer, no. seal, tujuan, pengirim, gambar checkpoint
    - **Additional Fields**: Estimasi nama kapal, tanggal ambil kontainer, tanggal terima pelabuhan, tanggal garasi, jumlah, satuan, berat kotor (kg), dimensi

3. **CRUD Operations**

    - **View**: List dengan filter (search, status, date range) + detail view
    - **Edit**: Form untuk mengisi data tambahan
    - **Delete**: Soft delete untuk preserve data
    - ❌ **No Create**: Dibuat otomatis dari approval (tidak ada form create manual)

4. **Permissions**

    - `tanda-terima-view`: Melihat daftar dan detail
    - `tanda-terima-update`: Mengedit data tambahan
    - `tanda-terima-delete`: Menghapus tanda terima

5. **UI/UX**
    - Modern Tailwind CSS design
    - Responsive grid layout
    - Status badges (draft/submitted/completed)
    - Quick actions sidebar
    - Breadcrumb navigation
    - Auto-hide alerts (5 seconds)

## Database Schema

### Table: `tanda_terimas`

```sql
CREATE TABLE tanda_terimas (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,

    -- Foreign Key
    surat_jalan_id BIGINT UNSIGNED,
    FOREIGN KEY (surat_jalan_id) REFERENCES surat_jalans(id) ON DELETE CASCADE,

    -- Data from Surat Jalan
    no_surat_jalan VARCHAR(255),
    tanggal_surat_jalan DATE,
    supir VARCHAR(255),
    kegiatan VARCHAR(255),
    size VARCHAR(255),
    jumlah_kontainer INT,
    no_kontainer TEXT,
    no_seal TEXT,
    tujuan VARCHAR(255),
    pengirim VARCHAR(255),
    gambar_checkpoint TEXT,

    -- Additional Fields
    estimasi_nama_kapal VARCHAR(255),
    tanggal_ambil_kontainer DATE,
    tanggal_terima_pelabuhan DATE,
    tanggal_garasi DATE,
    jumlah INT,
    satuan VARCHAR(100),
    berat_kotor DECIMAL(10,2),
    dimensi VARCHAR(255),

    -- Metadata
    catatan TEXT,
    status ENUM('draft', 'submitted', 'completed') DEFAULT 'draft',
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    -- Indexes
    INDEX idx_no_surat_jalan (no_surat_jalan),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_surat_jalan)
);
```

## Workflow

### 1. Approval Surat Jalan → Auto-Create Tanda Terima

```php
// File: app/Http/Controllers/SuratJalanApprovalController.php
public function approve($id) {
    DB::beginTransaction();
    try {
        $suratJalan = SuratJalan::findOrFail($id);

        // Update surat jalan status
        $suratJalan->update(['status' => 'completed']);

        // Update kontainer status
        $this->updateKontainerStatus($suratJalan);

        // Auto-create tanda terima
        $this->createTandaTerima($suratJalan);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
    }
}
```

### 2. Edit Tanda Terima (Fill Additional Data)

-   User dapat mengakses `/tanda-terima` untuk melihat daftar
-   Klik "Edit" untuk mengisi data tambahan:
    -   Pilih estimasi nama kapal (dropdown dari master_kapals)
    -   Isi tanggal ambil kontainer, terima pelabuhan, garasi
    -   Isi jumlah, satuan, berat kotor, dimensi
    -   Tambahkan catatan jika perlu

### 3. View Details

-   Tampilkan semua data dari surat jalan
-   Tampilkan data tambahan yang sudah diisi
-   Preview gambar checkpoint (image/PDF)
-   Link ke surat jalan terkait

## Files Created/Modified

### Created Files

1. **Migration**

    - `database/migrations/2025_10_16_130616_create_tanda_terimas_table.php`

2. **Model**

    - `app/Models/TandaTerima.php`

3. **Controller**

    - `app/Http/Controllers/TandaTerimaController.php`

4. **Views**

    - `resources/views/tanda-terima/index.blade.php`
    - `resources/views/tanda-terima/edit.blade.php`
    - `resources/views/tanda-terima/show.blade.php`

5. **Permission Seeders**
    - `add_tanda_terima_permissions.php`
    - `add_tanda_terima_permissions_to_admin.php`

### Modified Files

1. **Controller**

    - `app/Http/Controllers/SuratJalanApprovalController.php`
        - Added `use TandaTerima` model
        - Modified `approve()` to call `createTandaTerima()`
        - Added `createTandaTerima()` private method

2. **Routes**

    - `routes/web.php`
        - Added resource routes for tanda-terima (except create/store)

3. **Layout**
    - `resources/views/layouts/app.blade.php`
        - Added Tanda Terima menu item in sidebar

## Installation Steps

### 1. Run Migration

```bash
php artisan migrate
```

### 2. Add Permissions

```bash
php add_tanda_terima_permissions.php
```

### 3. Assign Permissions to Admin

```bash
php add_tanda_terima_permissions_to_admin.php
```

### 4. Test Workflow

1. Login sebagai admin
2. Buka menu "Approval Surat Jalan"
3. Approve salah satu surat jalan
4. Buka menu "Tanda Terima"
5. Cek tanda terima baru dengan status "Draft"
6. Klik "Edit" untuk mengisi data tambahan
7. Simpan dan lihat detail

## Routes

```php
// Index - List all tanda terima
GET /tanda-terima
Route: tanda-terima.index
Permission: tanda-terima-view

// Show - View details
GET /tanda-terima/{id}
Route: tanda-terima.show
Permission: tanda-terima-view

// Edit - Form for additional data
GET /tanda-terima/{id}/edit
Route: tanda-terima.edit
Permission: tanda-terima-update

// Update - Save additional data
PUT /tanda-terima/{id}
Route: tanda-terima.update
Permission: tanda-terima-update

// Delete - Soft delete
DELETE /tanda-terima/{id}
Route: tanda-terima.destroy
Permission: tanda-terima-delete
```

## Controller Methods

### TandaTerimaController

```php
// List with filters
public function index(Request $request)
- Search: no_surat_jalan, no_kontainer, estimasi_nama_kapal, pengirim
- Filter by: status
- Date range: start_date, end_date
- Pagination: 20 per page

// Edit form
public function edit($id)
- Load tanda terima
- Load master kapals for dropdown
- Check permission

// Update
public function update(Request $request, $id)
- Validate: estimasi_nama_kapal (required), dates, jumlah (integer), satuan, berat_kotor (numeric), dimensi
- Update with user ID
- Logging
- Redirect with success message

// Show details
public function show($id)
- Load with relationships (suratJalan, creator, updater)
- Display all data

// Soft delete
public function destroy($id)
- Soft delete
- Logging
- Redirect with success message
```

## Model Relationships

### TandaTerima Model

```php
// Belongs to Surat Jalan
public function suratJalan()

// Created by User
public function creator()

// Updated by User
public function updater()

// Scopes
scopeStatus($query, $status)
scopeDateRange($query, $start, $end)
```

## Status Workflow

1. **draft** (Default)

    - Dibuat otomatis dari approval
    - Belum diisi data tambahan
    - Dapat diedit

2. **submitted** (Manual)

    - Data tambahan sudah lengkap
    - Menunggu verifikasi

3. **completed** (Manual)
    - Sudah diverifikasi
    - Proses selesai

## Permissions Matrix

| Action      | Permission            | Description                 |
| ----------- | --------------------- | --------------------------- |
| View List   | `tanda-terima-view`   | Melihat daftar tanda terima |
| View Detail | `tanda-terima-view`   | Melihat detail tanda terima |
| Edit        | `tanda-terima-update` | Mengedit data tambahan      |
| Delete      | `tanda-terima-delete` | Soft delete tanda terima    |
| Create      | -                     | Tidak ada (auto-created)    |

## Best Practices

1. **Always approve surat jalan** - Tanda terima hanya dibuat dari approval
2. **Fill additional data ASAP** - Data tambahan penting untuk tracking
3. **Use master kapal dropdown** - Konsistensi nama kapal
4. **Add notes if needed** - Dokumentasi untuk informasi tambahan
5. **Don't delete unnecessarily** - Data penting untuk audit trail

## Troubleshooting

### Tanda Terima tidak otomatis dibuat setelah approval?

**Check:**

1. Apakah `SuratJalanApprovalController::approve()` memanggil `createTandaTerima()`?
2. Apakah ada error di log?
3. Cek database: `SELECT * FROM tanda_terimas ORDER BY id DESC LIMIT 1`

### Menu tidak muncul di sidebar?

**Check:**

1. Apakah user memiliki permission `tanda-terima-view`?
2. Clear cache: `php artisan cache:clear`
3. Cek file `resources/views/layouts/app.blade.php`

### Error saat edit?

**Check:**

1. Apakah tabel `master_kapals` ada data?
2. Apakah user memiliki permission `tanda-terima-update`?
3. Cek validation error di form

## Future Enhancements

-   [ ] Export to Excel/PDF
-   [ ] Bulk update status
-   [ ] Email notification saat tanda terima dibuat
-   [ ] Dashboard widget untuk statistik
-   [ ] QR Code untuk tracking
-   [ ] Integration dengan sistem pelabuhan

---

**Created:** 2025-01-16  
**Last Updated:** 2025-01-16  
**Version:** 1.0.0
