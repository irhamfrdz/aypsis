# 🚀 PANDUAN DEPLOYMENT AUDIT TRAIL KE SERVER

## 📋 LANGKAH-LANGKAH DEPLOYMENT

### 1. 📤 UPLOAD FILES KE SERVER

#### Upload semua file yang sudah dibuat:

```bash
# Files yang harus diupload:
📁 app/Models/AuditLog.php
📁 app/Traits/Auditable.php
📁 app/Http/Controllers/AuditLogController.php
📁 database/migrations/2025_10_17_220426_create_karyawan_audit_logs_table.php
📁 resources/views/audit-logs/
📁 resources/views/components/audit-log-button.blade.php
📁 resources/views/components/audit-log-modal.blade.php
📁 Semua view files yang sudah dimodifikasi (51 files)
```

#### Upload script automation:

```bash
📁 add_auditable_to_all_models.php
📁 implement_audit_log_batch.php
📁 test_audit_log_implementation.php
```

### 2. 🗄️ JALANKAN MIGRATION DI SERVER

```bash
# SSH ke server, masuk ke folder project
cd /path/to/your/project

# Jalankan migration
php artisan migrate

# Atau jika ada masalah:
php artisan migrate --force
```

### 3. 🔑 SETUP PERMISSIONS DI SERVER

Buat script khusus untuk server:

**File: `setup_audit_permissions_server.php`**

```php
<?php
// Script untuk setup permissions audit log di server
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔑 SETUP AUDIT LOG PERMISSIONS DI SERVER\n";
echo "=======================================\n\n";

try {
    // 1. Buat permissions jika belum ada
    $permissions = [
        'audit-log-view' => 'Melihat audit log',
        'audit-log-export' => 'Export audit log'
    ];

    foreach ($permissions as $name => $description) {
        $existing = DB::table('permissions')->where('name', $name)->first();

        if (!$existing) {
            $id = DB::table('permissions')->insertGetId([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Permission '$name' created (ID: $id)\n";
        } else {
            echo "ℹ️  Permission '$name' already exists (ID: {$existing->id})\n";
        }
    }

    // 2. Assign ke user admin
    $admin = DB::table('users')->where('username', 'admin')->first();
    if (!$admin) {
        echo "❌ User 'admin' tidak ditemukan!\n";
        exit;
    }

    $auditPermissions = DB::table('permissions')
        ->whereIn('name', ['audit-log-view', 'audit-log-export'])
        ->get();

    foreach ($auditPermissions as $perm) {
        $existing = DB::table('user_permissions')
            ->where('user_id', $admin->id)
            ->where('permission_id', $perm->id)
            ->first();

        if (!$existing) {
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $perm->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Permission '{$perm->name}' assigned to admin\n";
        } else {
            echo "ℹ️  Permission '{$perm->name}' sudah ada untuk admin\n";
        }
    }

    // 3. Setup routes jika belum ada
    echo "\n📍 SETUP ROUTES:\n";
    echo "Pastikan file routes/web.php memiliki:\n\n";
    echo "Route::middleware(['auth'])->group(function () {\n";
    echo "    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');\n";
    echo "    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');\n";
    echo "    Route::get('/audit-logs/model/data', [AuditLogController::class, 'getModelAuditLogs'])->name('audit-logs.model');\n";
    echo "    Route::get('/audit-logs/export/csv', [AuditLogController::class, 'export'])->name('audit-logs.export');\n";
    echo "});\n\n";

    echo "🎉 SETUP PERMISSIONS SELESAI!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
```

### 4. 🔧 JALANKAN SETUP DI SERVER

```bash
# Upload script setup ke server, kemudian jalankan:
php setup_audit_permissions_server.php

# Jalankan script untuk menambahkan Auditable trait ke models:
php add_auditable_to_all_models.php

# Test implementasi:
php test_audit_log_implementation.php
```

### 5. 📁 UPDATE ROUTES DI SERVER

Pastikan file `routes/web.php` memiliki routes audit log:

```php
use App\Http\Controllers\AuditLogController;

Route::middleware(['auth'])->group(function () {
    // ... routes existing ...

    // Audit Log Routes
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])
        ->name('audit-logs.show')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/model/data', [AuditLogController::class, 'getModelAuditLogs'])
        ->name('audit-logs.model')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/export/csv', [AuditLogController::class, 'export'])
        ->name('audit-logs.export')
        ->middleware('can:audit-log-export');
});
```

### 6. 🎨 UPDATE SIDEBAR MENU

Update sidebar untuk menampilkan menu Audit Log:

```blade
@can('audit-log-view')
<li class="nav-item">
    <a href="{{ route('audit-logs.index') }}" class="nav-link">
        <i class="fas fa-history nav-icon"></i>
        <p>Audit Log</p>
    </a>
</li>
@endcan
```

## 🧪 TESTING DI SERVER

### 1. Cek Database

```sql
-- Cek apakah table audit_logs sudah dibuat
SHOW TABLES LIKE 'audit_logs';

-- Cek struktur table
DESCRIBE audit_logs;

-- Cek permissions
SELECT * FROM permissions WHERE name LIKE 'audit-log%';
```

### 2. Test Functionality

```bash
# Test script di server
php test_audit_log_implementation.php

# Cek log Laravel jika ada error
tail -f storage/logs/laravel.log
```

### 3. Test UI

1. Login sebagai admin
2. Buka menu master data (misal: master karyawan)
3. Cek apakah tombol "Riwayat" muncul
4. Klik tombol dan lihat apakah modal audit log muncul
5. Test create/update/delete data untuk memastikan audit log tercatat

## 🔧 TROUBLESHOOTING

### Jika Migration Error:

```bash
# Cek status migration
php artisan migrate:status

# Rollback jika perlu
php artisan migrate:rollback --step=1

# Migrate ulang
php artisan migrate
```

### Jika Permission Error:

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Jalankan ulang setup permissions
php setup_audit_permissions_server.php
```

### Jika Routes Error:

```bash
# Cek routes
php artisan route:list | grep audit

# Clear route cache
php artisan route:clear
```

## 📋 CHECKLIST DEPLOYMENT

-   [ ] Upload semua files ke server
-   [ ] Jalankan migration (`php artisan migrate`)
-   [ ] Setup permissions (`php setup_audit_permissions_server.php`)
-   [ ] Update routes di `routes/web.php`
-   [ ] Update sidebar menu
-   [ ] Jalankan `php add_auditable_to_all_models.php`
-   [ ] Test functionality (`php test_audit_log_implementation.php`)
-   [ ] Test UI di browser
-   [ ] Verify audit logs tercatat saat CRUD operations

## ⚡ QUICK DEPLOYMENT SCRIPT

Buat script all-in-one untuk deployment:

**File: `deploy_audit_trail.php`**

```bash
#!/bin/bash

echo "🚀 DEPLOYING AUDIT TRAIL SYSTEM"
echo "==============================="

# 1. Run migration
echo "📊 Running migration..."
php artisan migrate --force

# 2. Setup permissions
echo "🔑 Setting up permissions..."
php setup_audit_permissions_server.php

# 3. Add Auditable trait to models
echo "🏷️  Adding Auditable trait to models..."
php add_auditable_to_all_models.php

# 4. Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 5. Test implementation
echo "🧪 Testing implementation..."
php test_audit_log_implementation.php

echo "✅ DEPLOYMENT COMPLETE!"
echo "Login sebagai admin dan cek menu 'Audit Log'"
```

Jalankan dengan: `chmod +x deploy_audit_trail.php && ./deploy_audit_trail.php`
