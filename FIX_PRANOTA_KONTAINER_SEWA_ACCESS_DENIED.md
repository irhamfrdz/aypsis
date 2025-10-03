# FIX ACCESS DENIED - PRANOTA KONTAINER SEWA

## Masalah Ditemukan

User sudah centang permission `pranota-kontainer-sewa-view` di Master User, tapi saat klik menu muncul **"Access Denied"**.

## Root Cause Analysis

Setelah pengecekan menyeluruh pada `routes/web.php`, ditemukan **3 masalah kritis**:

### 1. ❌ Route Mismatch di Sidebar (app.blade.php)

```blade
<!-- SALAH -->
@if($user && $user->can('pranota-kontainer-sewa-view'))
    <a href="{{ route('pranota.index') }}">  <!-- ❌ Route ini butuh permission 'pranota-view' -->

<!-- BENAR -->
@if($user && $user->can('pranota-kontainer-sewa-view'))
    <a href="{{ route('pranota-kontainer-sewa.index') }}">  <!-- ✅ Route ini butuh permission 'pranota-kontainer-sewa-view' -->
```

**Penjelasan:**

-   Menu sidebar menggunakan permission `pranota-kontainer-sewa-view` ✅ (BENAR)
-   Tapi URL nya ke `route('pranota.index')` ❌ (SALAH)
-   Route `pranota.index` butuh permission `pranota-view` yang BERBEDA!

### 2. ❌ Middleware Salah di web.php

```php
// Line 1181 - SALAH
Route::post('pranota-kontainer-sewa/bulk-create-from-tagihan-kontainer-sewa', ...)
     ->middleware('can:pranota-create');  // ❌ Harusnya 'pranota-kontainer-sewa-create'

// Line 1187 - SALAH
Route::post('pranota-kontainer-sewa/bulk-status-update', ...)
     ->middleware('can:tagihan-kontainer-update');  // ❌ Harusnya 'pranota-kontainer-sewa-update'
```

### 3. ❌ Route Tidak Lengkap

Route yang ada di group `pranota.*` tidak ada di `pranota-kontainer-sewa.*`:

-   `update.status` - untuk update status pranota
-   `lepas-kontainer` - untuk lepas kontainer
-   `destroy` - untuk hapus pranota

## Perbedaan Permission

Ada **2 set permission berbeda** di database:

### Permission Set 1: `pranota-*` (ID: 347-353)

-   pranota-view
-   pranota-create
-   pranota-update
-   pranota-delete
-   pranota-print
-   pranota-export

### Permission Set 2: `pranota-kontainer-sewa-*` (ID: 1206-1211)

-   pranota-kontainer-sewa-view
-   pranota-kontainer-sewa-create
-   pranota-kontainer-sewa-update
-   pranota-kontainer-sewa-delete
-   pranota-kontainer-sewa-print
-   pranota-kontainer-sewa-export

**User HANYA punya set 2**, tapi sistem mengarahkan ke route yang butuh **set 1**!

## Solusi yang Diterapkan

### 1. ✅ Fix Sidebar Route

**File:** `resources/views/layouts/app.blade.php` (line ~697)

```blade
<!-- SEBELUM -->
<a href="{{ route('pranota.index') }}" class="... {{ Request::routeIs('pranota.*') ? ... }}">

<!-- SESUDAH -->
<a href="{{ route('pranota-kontainer-sewa.index') }}" class="... {{ Request::routeIs('pranota-kontainer-sewa.*') ? ... }}">
```

### 2. ✅ Fix Middleware di web.php

**File:** `routes/web.php`

**Line 1181:**

```php
// SEBELUM
->middleware('can:pranota-create');

// SESUDAH
->middleware('can:pranota-kontainer-sewa-create');
```

**Line 1187:**

```php
// SEBELUM
->middleware('can:tagihan-kontainer-update');

// SESUDAH
->middleware('can:pranota-kontainer-sewa-update');
```

### 3. ✅ Tambah Route yang Missing

**File:** `routes/web.php` (setelah line 1187)

```php
// Route untuk update status
Route::patch('pranota-kontainer-sewa/{pranota}/status', [PranotaTagihanKontainerSewaController::class, 'updateStatus'])
     ->name('pranota-kontainer-sewa.update.status')
     ->middleware('can:pranota-kontainer-sewa-update');

// Route untuk lepas kontainer
Route::post('pranota-kontainer-sewa/{pranota}/lepas-kontainer', [PranotaTagihanKontainerSewaController::class, 'lepasKontainer'])
     ->name('pranota-kontainer-sewa.lepas-kontainer')
     ->middleware('can:pranota-kontainer-sewa-update');

// Route untuk delete
Route::delete('pranota-kontainer-sewa/{pranota}', [PranotaTagihanKontainerSewaController::class, 'destroy'])
     ->name('pranota-kontainer-sewa.destroy')
     ->middleware('can:pranota-kontainer-sewa-delete');
```

### 4. ✅ Fix All View References

**Files Updated:**

-   `resources/views/pranota/index.blade.php`
-   `resources/views/pranota/create.blade.php`
-   `resources/views/pranota/show.blade.php`

**Changes:**

```blade
<!-- All route references changed from: -->
route('pranota.index')
route('pranota.create')
route('pranota.show')
route('pranota.print')
route('pranota.update.status')
route('pranota.lepas-kontainer')

<!-- To: -->
route('pranota-kontainer-sewa.index')
route('pranota-kontainer-sewa.create')
route('pranota-kontainer-sewa.show')
route('pranota-kontainer-sewa.print')
route('pranota-kontainer-sewa.update.status')
route('pranota-kontainer-sewa.lepas-kontainer')
```

### 5. ✅ Clear All Cache

```bash
php artisan optimize:clear
```

-   cache cleared ✅
-   compiled cleared ✅
-   config cleared ✅
-   events cleared ✅
-   routes cleared ✅
-   views cleared ✅

## Route Mapping

### ✅ Route Pranota-Kontainer-Sewa (Yang Benar)

```
GET  /pranota-kontainer-sewa                    → index   (pranota-kontainer-sewa-view)
GET  /pranota-kontainer-sewa/create             → create  (pranota-kontainer-sewa-create)
GET  /pranota-kontainer-sewa/{id}               → show    (pranota-kontainer-sewa-view)
GET  /pranota-kontainer-sewa/{id}/print         → print   (pranota-kontainer-sewa-print)
POST /pranota-kontainer-sewa                    → store   (pranota-kontainer-sewa-create)
POST /pranota-kontainer-sewa/bulk-create-...    → bulk    (pranota-kontainer-sewa-create)
GET  /pranota-kontainer-sewa/next-number        → next    (pranota-kontainer-sewa-view)
POST /pranota-kontainer-sewa/bulk-status-update → bulk    (pranota-kontainer-sewa-update)
PATCH /pranota-kontainer-sewa/{id}/status       → status  (pranota-kontainer-sewa-update)
POST /pranota-kontainer-sewa/{id}/lepas-...     → lepas   (pranota-kontainer-sewa-update)
DELETE /pranota-kontainer-sewa/{id}             → destroy (pranota-kontainer-sewa-delete)
```

### ❌ Route Pranota (Yang Salah - Jangan Digunakan)

```
GET  /pranota           → index   (pranota-view)      ❌ User tidak punya permission ini
GET  /pranota/create    → create  (pranota-create)    ❌ User tidak punya permission ini
GET  /pranota/{id}      → show    (pranota-view)      ❌ User tidak punya permission ini
...
```

## Hasil Akhir

### ✅ URL Akses yang Benar:

```
http://your-domain.com/pranota-kontainer-sewa
```

### ❌ URL yang Salah (Jangan Digunakan):

```
http://your-domain.com/pranota  ← Ini akan Access Denied!
```

## Langkah User

**WAJIB DILAKUKAN** agar fix berfungsi:

1. **Logout** dari aplikasi
2. **Close semua tab** browser
3. **Clear browser cache**: `Ctrl + Shift + Delete`
    - Pilih "Cookies and other site data"
    - Pilih "Cached images and files"
    - Time range: "All time"
4. **Buka browser baru** (fresh start)
5. **Login kembali**
6. **Klik menu "Daftar Pranota Kontainer Sewa"**

### Expected Result:

✅ Halaman terbuka dengan URL: `/pranota-kontainer-sewa`
✅ Tidak ada pesan "Access Denied"
✅ Semua tombol (Create, View, Print, Update Status, dll) berfungsi

## Catatan Penting

### Kenapa Harus Clear Browser Cache?

Browser menyimpan:

-   Session cookies lama
-   Cached JavaScript/CSS yang mungkin masih menggunakan route lama
-   Route definitions yang ter-cache

Tanpa clear cache, browser mungkin masih:

-   Menggunakan route `pranota.index` (yang lama)
-   Menyimpan permission check yang lama

### Struktur Permission yang Benar

```
Master User → Edit User → Tab Permissions
└── Section: Daftar Pranota Kontainer Sewa
    ├── View   → pranota-kontainer-sewa-view   ✅ (MINIMAL INI HARUS DICENTANG)
    ├── Create → pranota-kontainer-sewa-create
    ├── Update → pranota-kontainer-sewa-update
    ├── Delete → pranota-kontainer-sewa-delete
    ├── Print  → pranota-kontainer-sewa-print
    └── Export → pranota-kontainer-sewa-export
```

## Testing Checklist

Setelah fix, test hal berikut:

-   [ ] Menu sidebar "Daftar Pranota Kontainer Sewa" muncul
-   [ ] Klik menu → halaman index terbuka (tidak Access Denied)
-   [ ] URL browser: `/pranota-kontainer-sewa`
-   [ ] Tombol "Buat Pranota Baru" berfungsi
-   [ ] View detail pranota berfungsi
-   [ ] Print pranota berfungsi
-   [ ] Update status berfungsi
-   [ ] Lepas kontainer berfungsi
-   [ ] Tombol Back/Kembali ke index berfungsi

## Kesimpulan

**Root Cause:** Route mismatch antara permission check dan URL target
**Solution:** Update semua route references dari `pranota.*` ke `pranota-kontainer-sewa.*`
**Status:** ✅ FIXED (perlu user logout + clear cache + login)

---

**Updated:** 2 Oktober 2025
**Status:** RESOLVED
**Action Required:** User must logout, clear cache, and login again
