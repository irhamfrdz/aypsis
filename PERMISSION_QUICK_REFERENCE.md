# 📋 AYPSIS - Quick Permission Reference

## 🎯 Permission Naming Convention

Sistem ini menggunakan format: `{module}-{resource}-{action}`

**Contoh:**

-   `master-user-view` = Modul Master → Resource User → Action View
-   `pranota-supir-create` = Modul Pranota → Resource Supir → Action Create

## 🔍 Quick Search

### Cari Permission Berdasarkan Modul

```bash
# Master Data
master-user-*
master-karyawan-*
master-kontainer-*
master-divisi-*
master-bank-*
master-pajak-*
master-pekerjaan-*
master-cabang-*
master-coa-*
master-vendor-bengkel-*
master-kegiatan-*
master-permission-*
master-mobil-*
master-pricelist-sewa-kontainer-*
master-pricelist-cat-*
master-stock-kontainer-*
master-kode-nomor-*
master-nomor-terakhir-*
master-tipe-akun-*
master-tujuan-*

# Business Process
permohonan-memo-*
pranota-supir-*
pranota-cat-*
pranota-kontainer-sewa-*
pranota-perbaikan-kontainer-*
tagihan-cat-*
tagihan-kontainer-sewa-*
tagihan-perbaikan-kontainer-*
perbaikan-kontainer-*

# Pembayaran
pembayaran-pranota-supir-*
pembayaran-pranota-cat-*
pembayaran-pranota-kontainer-*
pembayaran-pranota-perbaikan-kontainer-*
pembayaran-aktivitas-lainnya-*

# Lain-lain
aktivitas-lainnya-*
approval-*
profile-*
```

## 📊 Permission by Action Type

### 👁️ VIEW Permissions (Lihat Data)

Semua permission yang berakhiran `-view` atau `-index`

### ✏️ CREATE Permissions (Tambah Data)

Semua permission yang berakhiran `-create` atau `-store`

### 🔄 UPDATE Permissions (Edit Data)

Semua permission yang berakhiran `-update` atau `-edit`

### 🗑️ DELETE Permissions (Hapus Data)

Semua permission yang berakhiran `-delete` atau `-destroy`

### 🖨️ PRINT Permissions (Cetak/Download)

Semua permission yang berakhiran `-print`

### 📤 EXPORT Permissions (Export Data)

Semua permission yang berakhiran `-export`

### ✅ APPROVE Permissions (Persetujuan)

Semua permission yang berakhiran `-approve`

## 🎭 Permission Sets (Role-Based)

### 👑 Super Admin

**Semua 207+ permissions**

### 👨‍💼 Admin

Recommended permissions untuk admin:

-   Semua `master-*` permissions
-   Semua `dashboard` permission
-   Semua approval permissions
-   Profile permissions

### 👥 Staff

Recommended permissions untuk staff:

-   `dashboard`
-   View permissions untuk master data yang relevan
-   CRUD permissions untuk modul operasional
-   `profile-view`, `profile-update`

### 🚚 Supir

Recommended permissions untuk supir:

-   `dashboard`
-   `pranota-supir-view`
-   `pembayaran-pranota-supir-view`
-   `profile-view`, `profile-update`

## 🔐 Critical Permissions

### ⚠️ High Risk Permissions

Permissions yang sebaiknya hanya diberikan ke admin/trusted users:

```
master-user-delete
master-permission-delete
approval-dashboard
master-user-bulk-manage
```

### 🛡️ Audit Permissions

Permissions yang memerlukan audit trail:

```
master-user-create
master-user-update
master-user-delete
master-permission-create
master-permission-update
master-permission-delete
pembayaran-*-approve
aktivitas-lainnya-approve
```

## 📝 Common Permission Combinations

### Full CRUD Access (Standard Module)

```
{module}-view
{module}-create
{module}-update
{module}-delete
```

### Read-Only Access

```
{module}-view
```

### Data Entry Only

```
{module}-view
{module}-create
```

### Editor Access

```
{module}-view
{module}-update
```

### Approver Access

```
{module}-view
{module}-approve
```

## 🔍 Query Permissions

### Via Tinker

```php
# Lihat semua permissions
\App\Models\Permission::all();

# Cari permission by name
\App\Models\Permission::where('name', 'like', 'master-user%')->get();

# Lihat permissions user tertentu
\App\Models\User::find(1)->permissions;

# Count permissions by module
\App\Models\Permission::where('name', 'like', 'master-%')->count();
```

### Via SQL

```sql
-- Lihat semua permissions
SELECT * FROM permissions ORDER BY name;

-- Permissions by module
SELECT * FROM permissions WHERE name LIKE 'master-%';

-- User permissions
SELECT u.username, p.name
FROM users u
JOIN permission_user pu ON u.id = pu.user_id
JOIN permissions p ON pu.permission_id = p.id
WHERE u.id = 1;

-- Count permissions per user
SELECT u.username, COUNT(p.id) as total_permissions
FROM users u
LEFT JOIN permission_user pu ON u.id = pu.user_id
LEFT JOIN permissions p ON pu.permission_id = p.id
GROUP BY u.id;
```

## 🎯 Best Practices

### 1. Principle of Least Privilege

Berikan hanya permissions yang benar-benar dibutuhkan

### 2. Group by Role

Buat role-based permission sets untuk kemudahan management

### 3. Regular Audit

Review permissions secara berkala, terutama untuk user dengan akses tinggi

### 4. Document Changes

Catat setiap perubahan permission untuk audit trail

### 5. Test Before Production

Selalu test permission changes di environment development dulu

## 📞 Support

Jika ada pertanyaan atau permission baru yang perlu ditambahkan:

1. Tambahkan di `PermissionSeederComprehensive.php`
2. Update documentation ini
3. Run seeder untuk update database
4. Assign permissions ke user yang memerlukan

---

**Last Updated**: 2024-10-03  
**Total Permissions**: 207+  
**Version**: 1.0
