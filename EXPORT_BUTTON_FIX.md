# Solusi: Tombol Export Tidak Muncul

## Masalah

Tombol Export Data tidak muncul di halaman index untuk user admin.

## Penyebab

Permission yang digunakan di view tidak konsisten:

-   Awalnya menggunakan: `@can('tagihan-kontainer-sewa-view')`
-   Permission ini **TIDAK ADA** di database

## Solusi

Permission di tombol export sudah diubah menjadi:

```blade
@can('tagihan-kontainer-sewa-create')
```

## Verifikasi Admin Permission

User **admin** memiliki permissions berikut:

-   ✅ tagihan-kontainer-sewa-index
-   ✅ **tagihan-kontainer-sewa-create** ← Yang dibutuhkan untuk Export button
-   ✅ tagihan-kontainer-sewa-update
-   ✅ tagihan-kontainer-sewa-destroy
-   ✅ tagihan-kontainer-sewa-approve
-   ✅ tagihan-kontainer-sewa-print
-   ✅ tagihan-kontainer-sewa-export

## File yang Diubah

**File**: `resources/views/daftar-tagihan-kontainer-sewa/index.blade.php`

**Perubahan**:

```blade
<!-- SEBELUM -->
@can('tagihan-kontainer-sewa-view')
<button type="button" id="btnExport" ...>
    Export Data
</button>
@endcan

<!-- SESUDAH -->
@can('tagihan-kontainer-sewa-create')
<button type="button" id="btnExport" ...>
    Export Data
</button>
@endcan
```

## Cara Test

1. **Refresh browser** (Clear cache jika perlu: Ctrl+F5)
2. Login sebagai user **admin**
3. Buka halaman **Daftar Tagihan Kontainer Sewa**
4. Tombol **"Export Data"** (biru) seharusnya muncul di bagian atas

## Tombol-tombol yang Seharusnya Muncul

Untuk user **admin**, tombol berikut seharusnya terlihat:

-   ✅ Tambah Tagihan (ungu)
-   ✅ Buat Group (ungu)
-   ✅ Import Data (hijau)
-   ✅ **Export Data (biru)** ← Ini yang diperbaiki
-   ✅ Download Template (orange)

## Troubleshooting

### Jika tombol masih tidak muncul:

1. **Clear cache Laravel**:

    ```bash
    php artisan cache:clear
    php artisan view:clear
    php artisan config:clear
    ```

2. **Clear browser cache**: Ctrl+Shift+Delete atau Ctrl+F5

3. **Periksa console browser** (F12) untuk error JavaScript

4. **Verifikasi login**: Pastikan login sebagai user "admin"

5. **Check permission lagi**:
    ```bash
    php check_admin_export_permission.php
    ```

## Permission Konsistensi

Berikut permission yang digunakan di view ini:

-   Tambah Tagihan: `tagihan-kontainer-create`
-   Buat Group: `tagihan-kontainer-create`
-   Import Data: `tagihan-kontainer-sewa-create`
-   **Export Data: `tagihan-kontainer-sewa-create`** ← Diperbaiki
-   Download Template: `tagihan-kontainer-sewa-create`

## Status

✅ **FIXED** - Tombol export sekarang menggunakan permission yang benar dan user admin memiliki permission tersebut.
