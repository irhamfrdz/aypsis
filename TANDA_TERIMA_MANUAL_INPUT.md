# Fitur Input Tanda Terima Manual

## Overview

Fitur untuk membuat tanda terima kontainer secara manual tanpa harus melalui surat jalan yang di-approve. Berguna untuk:

-   Input tanda terima untuk kontainer khusus
-   Data historis yang tidak ada surat jalannya
-   Tanda terima dari sistem lain

## Tanggal Implementasi

16 Oktober 2025

## Perubahan Database

### Migration: `2025_10_16_134116_make_surat_jalan_id_nullable_in_tanda_terimas_table.php`

```sql
ALTER TABLE `tanda_terimas`
MODIFY COLUMN `surat_jalan_id` BIGINT UNSIGNED NULL;
```

**Alasan**: Memungkinkan tanda terima dibuat tanpa relasi ke surat jalan

## File yang Dibuat/Diubah

### 1. Controller: `TandaTerimaController.php`

**Method Baru:**

#### `create()`

```php
public function create()
```

-   Menampilkan form input tanda terima manual
-   Load master data: Master Kapal dan Master Kegiatan untuk dropdown
-   Return view: `tanda-terima.create`

#### `store(Request $request)`

```php
public function store(Request $request)
```

-   Validasi input form
-   Field wajib: `no_surat_jalan`, `tanggal_surat_jalan`
-   Field opsional: semua field lainnya
-   Upload gambar checkpoint (max 2MB)
-   Set `surat_jalan_id = null` untuk manual entry
-   Default status: `draft`
-   Transaction-safe dengan rollback

**Validasi:**

-   `no_surat_jalan`: required, unique di tabel tanda_terimas
-   `tanggal_surat_jalan`: required, format date
-   `gambar_checkpoint`: image, max 2MB (jpeg, jpg, png, gif)
-   Semua field lain: nullable

### 2. View: `resources/views/tanda-terima/create.blade.php`

Form input lengkap dengan section:

#### Section 1: Informasi Surat Jalan

-   No. Surat Jalan (required)
-   Tanggal Surat Jalan (required)
-   Supir
-   Kegiatan (dropdown dari master_kegiatans)

#### Section 2: Informasi Kontainer

-   No. Kontainer (textarea, pisah dengan koma)
-   No. Seal
-   Size (dropdown: 20, 40, 45 feet)
-   Jumlah Kontainer (auto-calculate dari no_kontainer)

#### Section 3: Informasi Pengiriman

-   Tujuan Pengiriman
-   Pengirim

#### Section 4: Informasi Kapal & Jadwal

-   Estimasi Nama Kapal (dropdown dari master_kapals)
-   Tanggal Ambil Kontainer
-   Tanggal Terima Pelabuhan
-   Tanggal Garasi

#### Section 5: Informasi Muatan

-   Jumlah
-   Satuan
-   Berat Kotor
-   Dimensi

#### Section 6: Gambar Checkpoint

-   Upload gambar (optional)

**Features:**

-   Auto-calculate jumlah kontainer dari input no_kontainer
-   Error handling dengan display error messages
-   Form styling dengan Tailwind CSS
-   Help text dan placeholder untuk guidance

### 3. View Update: `resources/views/tanda-terima/index.blade.php`

**Perubahan:**

-   Tambah tombol "Tambah Manual" di header (dengan permission check)
-   Tambah badge "Manual" untuk tanda terima tanpa surat_jalan_id

```blade
@can('tanda-terima-create')
<a href="{{ route('tanda-terima.create') }}" class="...">
    <i class="fas fa-plus-circle mr-2"></i>
    Tambah Manual
</a>
@endcan
```

### 4. View Update: `resources/views/tanda-terima/show.blade.php`

**Perubahan:**

-   Tambah badge "Input Manual" di header jika `surat_jalan_id` null

```blade
@if(!$tandaTerima->surat_jalan_id)
    <span class="...">
        <i class="fas fa-hand-paper"></i> Input Manual
    </span>
@endif
```

## Routes

Menggunakan resource route yang sudah ada:

```php
Route::resource('tanda-terima', TandaTerimaController::class);
```

Akses:

-   `GET /tanda-terima/create` - Form input manual
-   `POST /tanda-terima` - Simpan data

## Permissions

Menggunakan permission yang sudah ada:

-   `tanda-terima-view` - Lihat daftar
-   `tanda-terima-create` - Akses form create dan store
-   `tanda-terima-edit` - Edit data
-   `tanda-terima-delete` - Hapus data

## Testing

### Test Script: `test_create_manual_tanda_terima.php`

Memverifikasi:

-   ✅ Tanda terima dapat dibuat dengan `surat_jalan_id = NULL`
-   ✅ Master data tersedia (kapal & kegiatan)
-   ✅ Data tersimpan dengan benar
-   ✅ Status default: draft
-   ✅ Created by: user yang login

### Test Script: `add_sample_master_kapal.php`

Menambahkan sample data Master Kapal:

-   MV SINAR JAYA (nickname: SINAR)
-   MV SAMUDRA RAYA (nickname: SAMUDRA)
-   MV NUSANTARA MAKMUR (nickname: NUSA)

## Hasil Test

```
✅ Tanda Terima berhasil dibuat!
   ID: 3
   No. Surat Jalan: SJ-MANUAL-20251016134204
   Surat Jalan ID: NULL (Manual)
   Status: draft
```

## Perbedaan Tanda Terima Auto vs Manual

| Aspek            | Auto (dari Approval)        | Manual (Input Langsung)   |
| ---------------- | --------------------------- | ------------------------- |
| `surat_jalan_id` | Terisi (FK ke surat_jalans) | NULL                      |
| Cara Buat        | Otomatis saat approve SJ    | Manual via form create    |
| Data Awal        | Auto-populate dari SJ       | Input manual semua field  |
| Badge            | -                           | "Manual" / "Input Manual" |
| Use Case         | Workflow normal             | Data khusus/historis      |

## UI/UX Features

### 1. Auto-calculate Jumlah Kontainer

JavaScript auto-hitung jumlah kontainer dari no_kontainer:

```javascript
document.getElementById("no_kontainer").addEventListener("input", function () {
    const value = this.value.trim();
    if (value) {
        const containers = value
            .split(",")
            .filter((item) => item.trim() !== "");
        document.getElementById("jumlah_kontainer").value = containers.length;
    }
});
```

### 2. Visual Indicators

-   Info alert: Menjelaskan tanda terima manual tidak terhubung dengan SJ
-   Badge "Manual" di list (warna purple)
-   Badge "Input Manual" di detail (dengan icon)

### 3. Form Validation

-   Client-side: HTML5 validation (required, type, max)
-   Server-side: Laravel validation rules
-   Error display: Per-field error messages

## Best Practices

### Naming Convention

Gunakan prefix yang jelas untuk no_surat_jalan manual:

```
SJ-MANUAL-YYYYMMDDHHMMSS
SJ-SPECIAL-001
SJ-IMPORT-2025-001
```

### Data Entry

-   Pastikan no_surat_jalan unique
-   Isi minimal: nomor dan tanggal surat jalan
-   Upload gambar jika ada
-   Pilih kapal dari dropdown untuk consistency

### Permission Check

Selalu check permission sebelum akses:

```blade
@can('tanda-terima-create')
    // Show create button
@endcan
```

## Troubleshooting

### Error: "surat_jalan_id cannot be null"

**Solusi**: Pastikan migration sudah dijalankan:

```bash
php artisan migrate
```

### Error: "no_surat_jalan already exists"

**Solusi**: Gunakan nomor yang unik, tidak boleh duplikat

### Dropdown Master Kapal kosong

**Solusi**: Tambahkan data master kapal:

```bash
php add_sample_master_kapal.php
```

## Future Enhancements

1. Import Excel untuk bulk input manual
2. Template nomor surat jalan (auto-generate)
3. Validation: cross-check no_kontainer dengan stock
4. Export PDF untuk tanda terima manual
5. Filter khusus untuk tanda terima manual di list

## Dokumentasi Terkait

-   `TANDA_TERIMA_MODULE.md` - Dokumentasi module utama
-   `MASTER_KAPAL_DOCUMENTATION.md` - Master kapal reference
-   Migration files di `database/migrations/`

---

**Created by**: AI Assistant  
**Date**: 16 Oktober 2025  
**Version**: 1.0
