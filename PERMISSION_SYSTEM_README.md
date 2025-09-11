# Permission Management System - Documentation

## Overview

Sistem manajemen permission yang efisien untuk Laravel dengan fitur-fitur canggih untuk mengelola permission users secara massal dan terstruktur.

## Features

### 1. Permission Templates

Template permission yang telah didefinisikan untuk role umum:

-   **Admin**: Akses penuh ke semua fitur
-   **Staff**: Akses operasional dasar
-   **Supervisor**: Akses pengawasan dengan approval
-   **Viewer**: Akses read-only

### 2. Permission Groups

Pengelompokan permission berdasarkan fungsi:

-   Master Data
-   Pranota
-   Pembayaran
-   Approval
-   Laporan
-   Tagihan Kontainer
-   Permohonan

### 3. Bulk Permission Management

Halaman khusus untuk mengelola permission multiple users sekaligus:

-   Pilih multiple users
-   Pilih multiple permissions
-   Operasi: Add, Remove, Replace
-   Terapkan template ke multiple users

### 4. Enhanced UI Features

-   Search dan filter permission
-   Quick assign by group
-   Copy permission dari user lain
-   Template selection
-   Real-time counter

## Configuration Files

### config/permission_templates.php

```php
return [
    'admin' => [
        'label' => 'Administrator',
        'description' => 'Full access to all features',
        'permissions' => [
            'master-user', 'master-karyawan', // ... more permissions
        ],
    ],
    // ... more templates
];
```

### config/permission_groups.php

```php
return [
    'master' => [
        'label' => 'Master Data',
        'prefixes' => ['master-'],
    ],
    // ... more groups
];
```

## Usage

### 1. Create/Edit User with Templates

1. Buka halaman Create/Edit User
2. Pilih template dari dropdown
3. Klik "Terapkan Template"
4. Atau pilih permission secara manual

### 2. Bulk Permission Management

1. Akses menu "Kelola Izin Massal"
2. Pilih users yang ingin dikelola
3. Pilih permissions atau template
4. Pilih operasi (Tambah/Hapus/Ganti)
5. Klik execute

### 3. Quick Assign by Group

1. Pilih grup dari dropdown
2. Klik "Pilih Grup" untuk assign semua permission dalam grup tersebut

## API Endpoints

### Template Assignment

```
POST /master/user/{user}/assign-template
```

Body: `{ "template": "admin" }`

### Bulk Permission Operations

```
POST /master/user/bulk-assign-permissions
```

Body:

```json
{
    "user_ids": [1, 2, 3],
    "permission_ids": [10, 20, 30],
    "action": "add|remove|replace"
}
```

### Get User Permissions

```
GET /master/user/{user}/permissions
```

## Audit Tools

### Permission Audit Script

Jalankan untuk menganalisis status permission:

```bash
php scripts/audit_permissions.php
```

Fitur audit:

-   Analisis distribusi permission
-   Deteksi permission orphan
-   Compliance check dengan template
-   Rekomendasi perbaikan

## Security Considerations

1. Semua endpoint protected dengan middleware `can:master-user`
2. Validasi input ketat untuk mencegah unauthorized access
3. CSRF protection pada semua form
4. Permission validation sebelum assignment

## Performance Tips

1. Gunakan template untuk role standard
2. Lakukan bulk operations untuk multiple users
3. Regularly audit permission assignments
4. Monitor permission usage dengan audit script

## Troubleshooting

### Common Issues

1. **Template tidak muncul**: Pastikan config `permission_templates.php` ter-load
2. **Permission tidak tersimpan**: Check database connection dan table structure
3. **Bulk operation gagal**: Verify user dan permission IDs valid

### Debug Commands

```bash
# Check user permissions
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->permissions->pluck('name')

# Check available templates
>>> config('permission_templates')

# Check permission groups
>>> config('permission_groups')
```

## Future Enhancements

1. Role-based permission inheritance
2. Permission expiration dates
3. Audit logging untuk permission changes
4. Permission request workflow
5. Advanced reporting dan analytics
