# Pembayaran Surat Jalan - Permission System Implementation

## 📋 Overview

Implementasi sistem permission untuk fitur **Pembayaran Surat Jalan** di aplikasi AYPSIS. Sistem ini memungkinkan pengaturan akses user terhadap berbagai fungsi pembayaran surat jalan.

## 🔑 Permissions yang Dibuat

Sistem ini menciptakan 7 jenis permission untuk modul Pembayaran Surat Jalan:

| Permission Name                  | Description                    | Fungsi                               |
| -------------------------------- | ------------------------------ | ------------------------------------ |
| `pembayaran-surat-jalan-view`    | View pembayaran surat jalan    | Melihat data pembayaran surat jalan  |
| `pembayaran-surat-jalan-create`  | Create pembayaran surat jalan  | Membuat pembayaran surat jalan baru  |
| `pembayaran-surat-jalan-update`  | Update pembayaran surat jalan  | Mengubah data pembayaran surat jalan |
| `pembayaran-surat-jalan-delete`  | Delete pembayaran surat jalan  | Menghapus pembayaran surat jalan     |
| `pembayaran-surat-jalan-approve` | Approve pembayaran surat jalan | Menyetujui pembayaran surat jalan    |
| `pembayaran-surat-jalan-print`   | Print pembayaran surat jalan   | Mencetak dokumen pembayaran          |
| `pembayaran-surat-jalan-export`  | Export pembayaran surat jalan  | Export data ke berbagai format       |

## 🔧 Files yang Dimodifikasi

### 1. `app/Http/Controllers/UserController.php`

#### Modifikasi di Method `convertPermissionsToMatrix()`

**Lokasi:** Lines ~950-960

```php
// Special handling for pembayaran-surat-jalan-* permissions
if ($module === 'pembayaran' && strpos($action, 'surat-jalan-') === 0) {
    $action = str_replace('surat-jalan-', '', $action);
    $module = 'pembayaran-surat-jalan';
}
```

**Lokasi:** Lines ~985-995 (Dash notation pattern)

```php
// Special handling for pembayaran-surat-jalan-* permissions
if (strpos($permissionName, 'pembayaran-surat-jalan-') === 0) {
    $module = 'pembayaran-surat-jalan';
    $action = str_replace('pembayaran-surat-jalan-', '', $permissionName);
}
```

#### Modifikasi di Method `convertMatrixPermissionsToIds()`

**Lokasi:** Lines ~2150-2170

```php
// Special handling for pembayaran-surat-jalan
if ($module === 'pembayaran-surat-jalan') {
    // Map matrix actions directly to permission names
    $actionMap = [
        'view' => 'view',
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
        'approve' => 'approve',
        'print' => 'print',
        'export' => 'export'
    ];

    if (isset($actionMap[$action])) {
        $permissionName = 'pembayaran-surat-jalan-' . $actionMap[$action];
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $permissionIds[] = $permission->id;
            $found = true;
        }
    }
}
```

### 2. `resources/views/master-user/edit.blade.php`

Interface permission matrix sudah tersedia di lokasi:

-   **Lines 1313-1328**: Checkbox interface untuk semua 7 permissions
-   Terintegrasi sebagai submodule di bawah "Pembayaran" section

## 🚀 Scripts yang Dibuat

### 1. `add_pembayaran_surat_jalan_permissions.php`

Script untuk menambahkan permissions ke database.

**Usage:**

```bash
php add_pembayaran_surat_jalan_permissions.php
```

### 2. `add_pembayaran_surat_jalan_to_admin.php`

Script untuk menambahkan permissions ke user admin.

**Usage:**

```bash
php add_pembayaran_surat_jalan_to_admin.php
```

### 3. `test_pembayaran_surat_jalan_permissions.php`

Script untuk testing dan verifikasi sistem permission.

**Usage:**

```bash
php test_pembayaran_surat_jalan_permissions.php
```

## ✅ Hasil Testing

Semua test berhasil dijalankan:

1. ✅ **Database Permissions**: 7 permissions berhasil dibuat
2. ✅ **Admin Access**: Admin user memiliki akses ke semua permissions
3. ✅ **Matrix Conversion**: Konversi permission ke format matrix berhasil
4. ✅ **Reverse Conversion**: Konversi matrix kembali ke permission IDs berhasil

## 📱 Cara Penggunaan

### Untuk Administrator:

1. **Akses User Management:**

    - Login sebagai admin
    - Buka menu Master Data → User Management

2. **Edit User Permissions:**

    - Pilih user yang akan diedit
    - Scroll ke section "Pembayaran"
    - Expand untuk melihat "Pembayaran Surat Jalan"
    - Centang permission yang diperlukan:
        - ☐ View (Lihat)
        - ☐ Create (Buat)
        - ☐ Update (Edit)
        - ☐ Delete (Hapus)
        - ☐ Approve (Setuju)
        - ☐ Print (Cetak)
        - ☐ Export (Export)

3. **Simpan Perubahan:**
    - Klik tombol "Update" untuk menyimpan

### Untuk Developer:

**Middleware Check dalam Controller:**

```php
// Check view permission
if (!auth()->user()->hasPermission('pembayaran-surat-jalan-view')) {
    abort(403, 'Tidak memiliki akses untuk melihat pembayaran surat jalan');
}

// Check create permission
if (!auth()->user()->hasPermission('pembayaran-surat-jalan-create')) {
    abort(403, 'Tidak memiliki akses untuk membuat pembayaran surat jalan');
}

// Check approve permission
if (!auth()->user()->hasPermission('pembayaran-surat-jalan-approve')) {
    abort(403, 'Tidak memiliki akses untuk menyetujui pembayaran surat jalan');
}
```

**Blade Template Check:**

```blade
@if(auth()->user()->hasPermission('pembayaran-surat-jalan-view'))
    <a href="{{ route('pembayaran-surat-jalan.index') }}">Lihat Pembayaran</a>
@endif

@if(auth()->user()->hasPermission('pembayaran-surat-jalan-create'))
    <a href="{{ route('pembayaran-surat-jalan.create') }}">Buat Baru</a>
@endif

@if(auth()->user()->hasPermission('pembayaran-surat-jalan-print'))
    <button onclick="printDocument()">Cetak</button>
@endif
```

## 🔗 Integration Points

Permission system ini siap diintegrasikan dengan:

1. **Route Middleware**: Proteksi route berdasarkan permission
2. **Controller Authorization**: Validasi permission di level controller
3. **View Elements**: Conditional rendering berdasarkan permission
4. **API Endpoints**: Authorization untuk API access
5. **Menu System**: Dynamic menu berdasarkan user permissions

## 📝 Notes

-   Permission system menggunakan format matrix untuk UI yang user-friendly
-   Mendukung both dash notation (`pembayaran-surat-jalan-view`) dan hierarchical pattern
-   Terintegrasi dengan existing permission architecture
-   Fully tested dan ready untuk production use
-   Backwards compatible dengan sistem permission yang ada

## 🔍 Troubleshooting

**Jika permission tidak muncul di UI:**

1. Cek database apakah permissions sudah ada
2. Jalankan script test untuk verifikasi
3. Clear cache Laravel jika diperlukan

**Jika user tidak bisa akses fitur:**

1. Cek user permissions di user management
2. Pastikan permission sudah di-assign ke user
3. Verify dengan hasPermission() method

---

_Generated on: November 2, 2025_  
_System: AYPSIS Permission Management_
