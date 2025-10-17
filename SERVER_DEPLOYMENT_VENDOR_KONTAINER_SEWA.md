# Server Deployment Guide - Vendor Kontainer Sewa Permissions

## 🚀 Quick Setup for Server

### Step 1: Create Permissions

```bash
# Jalankan script simple (recommended untuk server)
php simple_vendor_permissions.php
```

### Step 2: Assign to Admin (Manual)

```bash
# Access tinker
php artisan tinker
```

In tinker console:

```php
// Find admin user
$admin = User::where('username', 'admin')->first() ?? User::find(1);
echo "Admin: " . $admin->username;

// Create permissions array
$permissions = ['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete'];

// If Spatie Permission is available
if (method_exists($admin, 'givePermissionTo')) {
    $admin->givePermissionTo($permissions);
    echo "✅ Permissions assigned via Spatie";
} else {
    // Manual assignment via database
    foreach ($permissions as $perm) {
        $permId = DB::table('permissions')->where('name', $perm)->value('id');
        if ($permId) {
            DB::table('model_has_permissions')->insertOrIgnore([
                'permission_id' => $permId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $admin->id
            ]);
        }
    }
    echo "✅ Permissions assigned via DB";
}

// Verify
$admin->refresh();
foreach ($permissions as $perm) {
    $has = $admin->can($perm) ? '✓' : '✗';
    echo "{$has} {$perm}";
}
```

## Alternative Scripts (Choose One)

### 1. ⭐ Simple Script (Recommended)

```bash
php simple_vendor_permissions.php
```

-   ✅ Only creates permissions
-   ✅ Works without Spatie package
-   ✅ Manual assignment guide included

### 2. Quick Script (With auto-assignment)

```bash
php quick_vendor_permissions.php
```

-   ✅ Attempts auto-assignment
-   ✅ Fallback to manual method
-   ⚠ May fail if permission tables missing

### 3. Comprehensive Script (Full featured)

```bash
php deploy_vendor_kontainer_sewa_permissions.php
```

-   ✅ Detailed logging
-   ✅ Multiple admin detection methods
-   ✅ Complete verification
-   ⚠ Requires full permission system

## Complete Deployment Process

### 1. Pull Latest Code

```bash
cd /path/to/your/project
git pull origin main
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Run Migrations

```bash
php artisan migrate --force

# If master_kapals migration fails:
php fix_master_kapals_migration.php
```

### 4. Setup Permissions

```bash
# Use the simple script
php simple_vendor_permissions.php

# Then follow manual assignment steps above
```

### 5. Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Set File Permissions (Linux/Unix)

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Verification Commands

### Check Permissions Exist

```bash
php artisan tinker --execute="
\$count = DB::table('permissions')->whereIn('name', ['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete'])->count();
echo 'Permissions in DB: ' . \$count . '/4';
"
```

### Check Routes

```bash
php artisan route:list --name=vendor-kontainer-sewa
```

### Test Access

```bash
# Access the URL
curl -I http://your-domain/vendor-kontainer-sewa
# Should return 200 or 302 (redirect to login), not 404
```

## Expected Results

### After Running Scripts:

```
✅ Permissions setup completed!
📝 Manual assignment required:
🎯 Access URL: /vendor-kontainer-sewa
📋 Permissions created:
   - vendor-kontainer-sewa-view
   - vendor-kontainer-sewa-create
   - vendor-kontainer-sewa-edit
   - vendor-kontainer-sewa-delete
```

### Routes Available:

-   GET /vendor-kontainer-sewa (Index)
-   GET /vendor-kontainer-sewa/create (Create Form)
-   POST /vendor-kontainer-sewa (Store)
-   GET /vendor-kontainer-sewa/{id} (Show)
-   GET /vendor-kontainer-sewa/{id}/edit (Edit Form)
-   PUT /vendor-kontainer-sewa/{id} (Update)
-   DELETE /vendor-kontainer-sewa/{id} (Delete)

## Troubleshooting

### Permissions Table Not Found

```bash
# Install permission package
composer require spatie/laravel-permission

# Publish migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migrations
php artisan migrate
```

### Admin User Not Found

```bash
# List available users
php artisan tinker --execute="User::select('id', 'username', 'email')->get()"

# Use specific user ID
php artisan tinker --execute="
\$user = User::find(USER_ID);
echo 'Found: ' . \$user->username;
"
```

### 403 Access Denied

1. Check if permissions are assigned to user
2. Check middleware in routes
3. Verify permission names match exactly
4. Clear cache: `php artisan cache:clear`

### Route Not Found (404)

1. Check if routes are registered: `php artisan route:list`
2. Clear route cache: `php artisan route:clear`
3. Verify controller exists: `ls app/Http/Controllers/VendorKontainerSewaController.php`

## Quick Manual Setup (If Scripts Fail)

```bash
php artisan tinker
```

```php
// Create permissions manually
$permissions = [
    ['name' => 'vendor-kontainer-sewa-view', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'vendor-kontainer-sewa-create', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'vendor-kontainer-sewa-edit', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'vendor-kontainer-sewa-delete', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]
];

DB::table('permissions')->insertOrIgnore($permissions);

// Find admin and assign
$admin = User::where('username', 'admin')->first() ?? User::find(1);
foreach (['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete'] as $perm) {
    $permId = DB::table('permissions')->where('name', $perm)->value('id');
    DB::table('model_has_permissions')->insertOrIgnore([
        'permission_id' => $permId,
        'model_type' => 'App\\Models\\User',
        'model_id' => $admin->id
    ]);
}

echo "✅ Setup completed manually";
```

## Success Indicators

-   ✅ All 4 permissions exist in database
-   ✅ Admin user has permissions assigned
-   ✅ Routes return 200/302 (not 404)
-   ✅ No 403 errors when accessing as admin
-   ✅ CRUD operations work properly
