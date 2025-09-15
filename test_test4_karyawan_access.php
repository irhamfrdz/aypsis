<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧪 Test Akses Master-Karyawan untuk User test4\n";
echo "=============================================\n\n";

// Cek permission user test4 saat ini
$userTest4 = User::where('username', 'test4')->first();
if ($userTest4) {
    echo "👤 User: test4\n";
    echo "   Current permissions: " . $userTest4->permissions->pluck('name')->join(', ') . "\n\n";

    // Test akses ke berbagai route master-karyawan
    $routesToTest = [
        'master-karyawan.view' => 'View Karyawan',
        'master-karyawan.create' => 'Create Karyawan',
        'master-karyawan.update' => 'Update Karyawan',
        'master-karyawan.delete' => 'Delete Karyawan',
        'master-karyawan.print' => 'Print Karyawan',
        'master-karyawan.export' => 'Export Karyawan',
    ];

    echo "🔍 Testing permission access:\n";
    foreach ($routesToTest as $permission => $description) {
        $hasAccess = $userTest4->hasPermissionTo($permission);
        echo "   {$description} ({$permission}): " . ($hasAccess ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    }

    echo "\n📝 Summary:\n";
    $karyawanPermissions = $userTest4->permissions->filter(function($perm) {
        return str_starts_with($perm->name, 'master-karyawan');
    });

    if ($karyawanPermissions->count() > 0) {
        echo "   ✅ User test4 has " . $karyawanPermissions->count() . " karyawan permissions\n";
        echo "   📋 Permissions: " . $karyawanPermissions->pluck('name')->join(', ') . "\n";
    } else {
        echo "   ❌ User test4 has NO karyawan permissions\n";
    }

    echo "\n🎯 Expected behavior: User should be able to access karyawan menu since they have permissions\n";

} else {
    echo "❌ User test4 not found\n";
}

echo "\n🧪 Test selesai!\n";
