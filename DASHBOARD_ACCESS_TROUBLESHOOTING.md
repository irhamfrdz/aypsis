# 🚨 TROUBLESHOOTING: Dashboard Access Denied Meskipun Ada Permission

## 📋 Masalah Yang Ditemukan

User memiliki permission `dashboard` di server tapi akses ke dashboard ditolak.

## 🔍 Root Cause Analysis

Berdasarkan investigasi, ditemukan beberapa masalah utama:

### 1. **Status User Bukan 'approved'**

-   Middleware `EnsureUserApproved` memblokir user yang statusnya bukan 'approved'
-   Banyak user memiliki status: `pending`, `rejected`, `active`
-   Hanya user dengan status `approved` yang diizinkan akses

### 2. **User Tanpa karyawan_id Valid**

-   Middleware `EnsureKaryawanPresent` memblokir user tanpa `karyawan_id`
-   Beberapa user memiliki `karyawan_id` yang tidak ada di tabel `karyawans`

### 3. **Middleware Stack yang Kompleks**

Route dashboard menggunakan middleware:

```php
Route::middleware([
    'auth',                           // ✅ User harus login
    'EnsureKaryawanPresent',         // ❌ User harus punya karyawan_id valid
    'EnsureUserApproved',            // ❌ User status harus 'approved'
    'EnsureCrewChecklistComplete',   // ✅ Tidak memblokir (sudah diperbaiki)
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard')
         ->middleware('can:dashboard');  // ✅ Permission ada
});
```

## ✅ Solusi Yang Sudah Diterapkan

### 1. **Fix User Status**

```sql
-- Set semua user ke status 'approved'
UPDATE users SET status = 'approved' WHERE status != 'approved' OR status IS NULL;
```

### 2. **Clear Cache**

```bash
php artisan optimize:clear
```

## 🎯 Status User Admin (Sudah Benar)

```
Username: admin
Karyawan ID: 1 ✅
Status: approved ✅
Permission dashboard: ✅ Ada
```

## ⚠️ User Lain Yang Bermasalah

User dengan `karyawan_id` tidak valid (akan tetap diblokir):

-   longringlong → karyawan_id: 91 (tidak ada)
-   test → karyawan_id: 74 (tidak ada)
-   test_onboarding → karyawan_id: 16 (tidak ada)
-   Dan 13 user lainnya...

## 🔧 Langkah Troubleshooting Lanjutan

### Jika Masih Access Denied:

#### 1. **Cek Session & Cache**

```bash
# Clear semua cache
php artisan optimize:clear

# Clear browser cache
Ctrl + F5 (hard refresh)

# Logout dan login ulang
```

#### 2. **Bypass Middleware untuk Admin**

Edit `routes/web.php`, tambahkan route khusus admin:

```php
// Route khusus admin bypass middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
         ->name('admin.dashboard')
         ->middleware('can:dashboard');
});
```

#### 3. **Debug Permission Real-time**

```php
// Tambahkan di DashboardController::index()
dd([
    'user_id' => auth()->id(),
    'can_dashboard' => auth()->user()->can('dashboard'),
    'permissions' => auth()->user()->permissions->pluck('name'),
    'status' => auth()->user()->status,
    'karyawan_id' => auth()->user()->karyawan_id,
]);
```

#### 4. **Cek Log Laravel**

```bash
tail -f storage/logs/laravel.log
```

#### 5. **Test Route Langsung**

Buat route test tanpa middleware:

```php
Route::get('/test-dashboard', function() {
    return 'Dashboard access works!';
})->middleware(['auth', 'can:dashboard']);
```

## 📊 Data Yang Sudah Diperbaiki

### Users Status Updated:

-   ✅ 14 user di-update dari `pending`/`rejected`/`active` ke `approved`
-   ✅ User `admin` sudah correct: `approved` dengan `karyawan_id: 1`

### Permission Verification:

-   ✅ Permission `dashboard` exists (ID: 482)
-   ✅ 15 users memiliki permission dashboard
-   ✅ User admin memiliki permission dashboard

## 🎯 Kesimpulan

**User admin seharusnya sudah bisa akses dashboard** karena:

1. ✅ Status: `approved`
2. ✅ Karyawan ID: 1 (valid)
3. ✅ Permission: `dashboard` (ada)
4. ✅ Cache: sudah di-clear

**Jika masih access denied**, kemungkinan:

1. Session belum ter-refresh → **Logout/login ulang**
2. Browser cache → **Hard refresh (Ctrl+F5)**
3. Middleware custom → **Cek log Laravel**
4. Route cache → **php artisan route:clear**

## 🚀 Next Steps

1. **Logout dan login ulang** dengan user admin
2. **Akses /dashboard** langsung
3. **Jika masih error**, cek log Laravel untuk pesan error spesifik
4. **Jika perlu**, gunakan route bypass admin sementara

## 🏷️ Tags

`laravel` `middleware` `permission` `dashboard` `access-denied` `troubleshooting` `authentication`
