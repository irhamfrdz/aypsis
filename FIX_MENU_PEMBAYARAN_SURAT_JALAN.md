# Fix Menu Pembayaran Surat Jalan Tidak Muncul di Sidebar

## Masalah
User sudah mencentang permission "Pembayaran Surat Jalan" di form edit user, tapi menu tidak muncul di sidebar.

## Root Cause
Ada ketidakcocokan nama permission antara form edit user dan permission yang dicek di sidebar:

1. **Form edit user** menggunakan: `pembayaran-surat-jalan-*`
2. **Sidebar** mengecek: `pembayaran-pranota-surat-jalan-*`

## Solusi yang Diterapkan

### 1. Update Form Edit User
Mengubah form edit user untuk menggunakan permission `pembayaran-pranota-surat-jalan-*` agar sesuai dengan yang ada di sidebar.

**File:** `resources/views/master-user/edit.blade.php`
```php
// Sebelum
<input type="checkbox" name="permissions[pembayaran-surat-jalan][view]" ...>

// Sesudah  
<input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][view]" ...>
```

### 2. Update UserController
Mengupdate mapping permission di UserController untuk menangani `pembayaran-pranota-surat-jalan`.

**File:** `app/Http/Controllers/UserController.php`

**Method: `convertPermissionsToMatrix()`**
```php
// Update mapping untuk pembayaran-pranota-surat-jalan
if ($module === 'pembayaran' && strpos($action, 'pranota-surat-jalan-') === 0) {
    $action = str_replace('pranota-surat-jalan-', '', $action);
    $module = 'pembayaran-pranota-surat-jalan';
}
```

**Method: `convertMatrixPermissionsToIds()`** 
```php
// Update mapping untuk pembayaran-pranota-surat-jalan
if ($module === 'pembayaran-pranota-surat-jalan') {
    $actionMap = [
        'view' => 'view',
        'create' => 'create', 
        'update' => 'edit',      // Perhatikan: 'update' -> 'edit'
        'delete' => 'delete',
        'approve' => 'approve',
        'print' => 'print',
        'export' => 'export'
    ];
    
    $permissionName = 'pembayaran-pranota-surat-jalan-' . $actionMap[$action];
}
```

### 3. Tambah Permission yang Hilang
Menambahkan permission yang tidak ada di database:

```sql
-- Permission yang sudah ada:
pembayaran-pranota-surat-jalan-view (ID: 219)
pembayaran-pranota-surat-jalan-create (ID: 220) 
pembayaran-pranota-surat-jalan-edit (ID: 221)
pembayaran-pranota-surat-jalan-delete (ID: 222)

-- Permission yang ditambahkan:
pembayaran-pranota-surat-jalan-approve (ID: 371)
pembayaran-pranota-surat-jalan-print (ID: 372)
pembayaran-pranota-surat-jalan-export (ID: 373)
```

### 4. Assign Permission ke Admin
Memberikan semua permission `pembayaran-pranota-surat-jalan-*` ke user admin.

## Verifikasi

### Test Permission Matrix Conversion
✅ Permission matrix dapat dikonversi ke permission IDs dengan benar
✅ Permission IDs dapat dikonversi kembali ke matrix dengan benar

### Test Database
✅ Semua 7 permission tersedia di database
✅ Admin user sudah memiliki semua permission

### Test Sidebar
✅ Menu "Pembayaran" muncul di sidebar
✅ Sub-menu "Aktivitas Supir" muncul
✅ Menu "Bayar Pranota Surat Jalan" muncul dalam sub-menu

## Files yang Dimodifikasi

1. **resources/views/master-user/edit.blade.php**
   - Update checkbox names dari `pembayaran-surat-jalan` ke `pembayaran-pranota-surat-jalan`
   - Update label dari "Pembayaran Surat Jalan" ke "Pembayaran Pranota Surat Jalan"

2. **app/Http/Controllers/UserController.php**
   - Update mapping di `convertPermissionsToMatrix()`
   - Update mapping di `convertMatrixPermissionsToIds()`
   - Fix action mapping: 'update' -> 'edit'

3. **Database permissions**
   - Tambah 3 permission baru (approve, print, export)
   - Assign semua permission ke admin user

## Command untuk Verifikasi
```bash
# Test permission conversion
php test_pembayaran_pranota_surat_jalan.php

# Assign permission ke admin
php assign_all_pembayaran_pranota_permissions.php

# Verifikasi permission di database
php check_surat_jalan_permissions.php | Select-String "pembayaran-pranota-surat-jalan"
```

## Status: ✅ SELESAI
Menu "Pembayaran Pranota Surat Jalan" sekarang muncul di sidebar dengan benar ketika user memiliki permission `pembayaran-pranota-surat-jalan-view`.