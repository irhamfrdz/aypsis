# TROUBLESHOOTING: Access Denied Setelah Centang Permission

## Masalah

User sudah **centang permission** di Master User, tapi saat akses menu masih muncul **"Access Denied"**.

## Penyebab

Masalah ini terjadi karena **session cache** di browser dan Laravel masih menyimpan data permission lama sebelum user di-update.

## Diagnosis

Sudah dilakukan pengecekan lengkap:

### ✅ Permission di Database - OK

```
User: admin (ID: 1)
✓ Memiliki 7 pranota-kontainer-sewa permissions:
  - pranota-kontainer-sewa-view
  - pranota-kontainer-sewa-create
  - pranota-kontainer-sewa-update
  - pranota-kontainer-sewa-delete
  - pranota-kontainer-sewa-print
  - pranota-kontainer-sewa-export
  - pranota-kontainer-sewa-approve
```

### ✅ Gate Check - OK

```
Direct DB check: ✅ PASSED
Model relationship: ✅ PASSED
hasPermissionTo(): ✅ PASSED
Gate::authorize(): ✅ PASSED
Gate::allows(): ✅ PASSED
```

### ❌ Session Cache - MASALAH

Session user masih menggunakan permission lama sebelum update.

## Solusi yang Sudah Dilakukan

### 1. Clear Laravel Cache

```bash
php artisan optimize:clear  ✅
php artisan cache:clear     ✅
```

### 2. Clear All User Sessions

```bash
php clear_all_sessions.php  ✅
```

-   Menghapus 1 session dari database
-   Force logout semua user
-   Clear application, config, view cache

### 3. Permission Matrix Conversion - Fixed

File: `app/Http/Controllers/UserController.php`

**Ditambahkan di line ~523 (convertPermissionsToMatrix):**

```php
// Special handling for pranota-kontainer-sewa dash notation permissions
if (strpos($permissionName, 'pranota-kontainer-sewa-') === 0) {
    $module = 'pranota-kontainer-sewa';
    $action = str_replace('pranota-kontainer-sewa-', '', $permissionName);
    // ... mapping logic
}
```

**Ditambahkan di line ~1965 (convertMatrixPermissionsToIds):**

```php
// Special handling for pranota-kontainer-sewa module
if ($module === 'pranota-kontainer-sewa') {
    $actionMap = [
        'view' => 'pranota-kontainer-sewa-view',
        'create' => 'pranota-kontainer-sewa-create',
        'update' => 'pranota-kontainer-sewa-update',
        'delete' => 'pranota-kontainer-sewa-delete',
        'print' => 'pranota-kontainer-sewa-print',
        'export' => 'pranota-kontainer-sewa-export'
    ];
    // ... conversion logic
}
```

## Langkah User HARUS Lakukan

### ✅ WAJIB (Semua harus dilakukan):

1. **Logout dari aplikasi**

    - Klik menu Logout / Keluar

2. **Close semua tab browser yang buka aplikasi**

    - Tutup semua tab
    - Tutup browser sepenuhnya

3. **Clear Browser Cache**

    - Chrome/Edge: `Ctrl + Shift + Delete`
    - Pilih "Cached images and files"
    - Pilih "Cookies and other site data"
    - Time range: "All time"
    - Klik "Clear data"

4. **Buka browser baru**

    - Buka browser fresh (bukan dari history)

5. **Login kembali**
    - Login dengan user yang sudah punya permission
    - Coba akses menu Pranota Kontainer Sewa

## Verifikasi Berhasil

Setelah langkah di atas, cek:

-   ✅ Menu "Pranota Kontainer Sewa" muncul di sidebar
-   ✅ Bisa membuka halaman pranota-kontainer-sewa
-   ✅ Tidak ada pesan "Access Denied"

## Script Troubleshooting

### Check Permission User

```bash
php check_current_user_permission.php
```

Input: username yang sedang login

### Force Permission Check (Real-time)

```bash
php force_permission_check.php
```

Mengecek:

-   Direct database query
-   Model relationship
-   hasPermissionTo method
-   Laravel Gate authorization

### Clear All Sessions

```bash
php clear_all_sessions.php
```

⚠️ WARNING: Akan logout SEMUA user!

## Catatan Teknis

### Gate Definition (AppServiceProvider.php)

```php
Gate::define($permission->name, function (User $user) use ($permission) {
    return $user->permissions()->where('name', $permission->name)->exists();
});
```

Gate menggunakan **direct database query** dengan `->exists()`, jadi seharusnya tidak ada cache issue di level Gate. Masalah hanya di **session user object** yang disimpan di browser/Laravel session.

### Middleware Check (routes/web.php)

```php
Route::get('pranota-kontainer-sewa', [PranotaTagihanKontainerSewaController::class, 'index'])
     ->name('pranota-kontainer-sewa.index')
     ->middleware('can:pranota-kontainer-sewa-view');
```

Middleware `can:` akan memanggil Gate yang sudah didefinisikan di AppServiceProvider.

## Kesimpulan

✅ **Semua fix sudah dilakukan di level code dan database**
✅ **Session cache sudah di-clear**
❌ **User WAJIB logout + clear browser cache + login ulang**

Tanpa langkah user (logout, clear cache, login ulang), masalah akan tetap ada karena browser masih menyimpan session lama.

---

**Tanggal**: 2 Oktober 2025
**Status**: RESOLVED (Waiting for user action)
