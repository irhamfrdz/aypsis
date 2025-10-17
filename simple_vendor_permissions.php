<?php
// Script ultra-simple untuk server - hanya create permissions
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Vendor Kontainer Sewa Permissions Setup ===\n";

try {
    $permissions = [
        'vendor-kontainer-sewa-view',
        'vendor-kontainer-sewa-create',
        'vendor-kontainer-sewa-edit',
        'vendor-kontainer-sewa-delete'
    ];

    echo "Creating permissions in database...\n";

    foreach ($permissions as $perm) {
        $exists = \Illuminate\Support\Facades\DB::table('permissions')
            ->where('name', $perm)->exists();

        if (!$exists) {
            \Illuminate\Support\Facades\DB::table('permissions')->insert([
                'name' => $perm,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Created: {$perm}\n";
        } else {
            echo "→ Exists: {$perm}\n";
        }
    }

    echo "\n✅ Permissions setup completed!\n";
    echo "📝 Manual assignment required:\n";
    echo "1. Access Laravel Tinker: php artisan tinker\n";
    echo "2. Find your admin user: \$admin = User::where('username', 'admin')->first();\n";
    echo "3. Check user: \$admin->username\n";
    echo "4. Assign permissions manually through admin panel or code\n\n";

    echo "🎯 Access URL: /vendor-kontainer-sewa\n";
    echo "📋 Permissions created:\n";
    foreach ($permissions as $perm) {
        echo "   - {$perm}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n🔧 Troubleshooting:\n";
    echo "1. Check if 'permissions' table exists\n";
    echo "2. Run: php artisan migrate\n";
    echo "3. Install permission package: composer require spatie/laravel-permission\n";
}

echo "\n=== Setup Complete ===\n";
?>
