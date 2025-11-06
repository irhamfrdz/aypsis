# Implementasi Filter BTM untuk Master Mobil

## Overview

Implementasi filter branch untuk Master Mobil yang **HANYA** berlaku untuk user dengan cabang BTM. User dengan cabang lain atau tanpa karyawan dapat melihat semua mobil.

## Kondisi Filter

-   **User cabang BTM**: Hanya dapat melihat mobil yang ditugaskan kepada karyawan dengan cabang BTM
-   **User cabang lain**: Dapat melihat semua mobil (tidak ada filter)
-   **User tanpa karyawan**: Dapat melihat semua mobil (tidak ada filter)

## Implementasi Detail

### 1. MobilController.php

#### Method `index()` - Listing Mobil

```php
// Filter berdasarkan cabang user yang login - HANYA untuk user cabang BTM
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    // Filter mobil berdasarkan cabang karyawan yang terkait dengan mobil tersebut
    $query->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
}
```

#### Method `create()` - Form Tambah Mobil

```php
// Filter karyawan berdasarkan cabang user yang login - HANYA untuk user cabang BTM
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    $karyawansQuery->where('cabang', 'BTM');
}
```

#### Method `show()` - Detail Mobil

```php
// Verifikasi akses berdasarkan cabang - HANYA untuk user cabang BTM
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    // Cek apakah mobil ini memiliki karyawan dengan cabang BTM
    if (!$mobil->karyawan || $mobil->karyawan->cabang !== 'BTM') {
        abort(404, 'Data mobil tidak ditemukan.');
    }
}
```

#### Method `edit()` - Form Edit Mobil

```php
// Verifikasi akses berdasarkan cabang - HANYA untuk user cabang BTM
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    // Cek apakah mobil ini memiliki karyawan dengan cabang BTM
    if (!$mobil->karyawan || $mobil->karyawan->cabang !== 'BTM') {
        abort(404, 'Data mobil tidak ditemukan.');
    }
}

// Filter karyawan dropdown - HANYA untuk user cabang BTM
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    $karyawansQuery->where('cabang', 'BTM');
}
```

### 2. MasterMobilImportController.php

#### Method `export()` - Export Data

```php
// Filter berdasarkan cabang user yang login - HANYA untuk user cabang BTM
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    // Filter mobil berdasarkan cabang karyawan yang terkait dengan mobil tersebut
    $query->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
}
```

## Keamanan & Access Control

### User Cabang BTM

-   ✅ Hanya melihat mobil dengan karyawan cabang BTM
-   ✅ Hanya dapat mengakses detail/edit mobil cabang BTM
-   ✅ Dropdown karyawan hanya menampilkan karyawan cabang BTM
-   ✅ Export hanya mengekspor data mobil cabang BTM

### User Cabang Lain (Jakarta, Surabaya, dll)

-   ✅ Melihat semua mobil tanpa filter
-   ✅ Dapat mengakses semua detail/edit mobil
-   ✅ Dropdown karyawan menampilkan semua karyawan supir
-   ✅ Export mengekspor semua data mobil

### User Tanpa Karyawan

-   ✅ Melihat semua mobil tanpa filter
-   ✅ Dapat mengakses semua detail/edit mobil
-   ✅ Dropdown karyawan menampilkan semua karyawan supir
-   ✅ Export mengekspor semua data mobil

## Skenario Testing

1. **User BTM Login**: Hanya melihat mobil yang ditugaskan ke karyawan BTM
2. **User Jakarta Login**: Melihat semua mobil dari semua cabang
3. **User Surabaya Login**: Melihat semua mobil dari semua cabang
4. **Admin tanpa karyawan**: Melihat semua mobil dari semua cabang

## Perubahan File

-   `app/Http/Controllers/MobilController.php` - Filter untuk CRUD operations
-   `app/Http/Controllers/MasterMobilImportController.php` - Filter untuk export
-   Test files untuk validasi logic

## Commit

```
02ca76d - Implement BTM branch filter for Master Mobil
```

Implementasi berhasil: **HANYA user dengan cabang BTM yang mendapat pembatasan akses.**
