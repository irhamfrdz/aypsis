<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Verifikasi Permission System Setelah Cleanup\n";
echo "==============================================\n\n";

// Cek permission user test4
$userTest4 = User::where('username', 'test4')->first();
if ($userTest4) {
    echo "👤 User: test4\n";
    echo "   Permissions: " . $userTest4->permissions->pluck('name')->join(', ') . "\n";

    $permissionsToCheck = [
        'master-karyawan.view',
        'master-karyawan.create',
        'master-karyawan.update',
        'master-karyawan.delete',
        'master-user.view'
    ];

    foreach ($permissionsToCheck as $perm) {
        $hasPermission = $userTest4->hasPermissionTo($perm);
        echo "   {$perm}: " . ($hasPermission ? '✅' : '❌') . "\n";
    }
    echo "\n";
}

// Cek permission user_terbatas
$userTerbatas = User::where('username', 'user_terbatas')->first();
if ($userTerbatas) {
    echo "👤 User: user_terbatas\n";
    echo "   Permissions: " . $userTerbatas->permissions->pluck('name')->join(', ') . "\n";

    $permissionsToCheck = [
        'master-user.view'
    ];

    foreach ($permissionsToCheck as $perm) {
        $hasPermission = $userTerbatas->hasPermissionTo($perm);
        echo "   {$perm}: " . ($hasPermission ? '✅' : '❌') . "\n";
    }
    echo "\n";
}

// Cek permission admin
$userAdmin = User::where('username', 'admin')->first();
if ($userAdmin) {
    echo "👤 User: admin\n";
    echo "   Permissions: " . $userAdmin->permissions->pluck('name')->join(', ') . "\n";

    $permissionsToCheck = [
        'master-karyawan',
        'master-user',
        'master-kontainer',
        'master-tujuan',
        'master-kegiatan',
        'master-permission',
        'master-mobil',
        'master-pricelist-sewa-kontainer'
    ];

    foreach ($permissionsToCheck as $perm) {
        $hasPermission = $userAdmin->hasPermissionTo($perm);
        echo "   {$perm}: " . ($hasPermission ? '✅' : '❌') . "\n";
    }
    echo "\n";
}

// Cek total permission yang tersisa
$totalPermissions = DB::table('permissions')->count();
echo "📊 Total permissions tersisa: {$totalPermissions}\n";

// Cek permission yang masih menggunakan format lama
$oldFormatPermissions = DB::table('permissions')
    ->where('name', 'like', 'master.%')
    ->where('name', 'not like', 'master-%')
    ->get();

if ($oldFormatPermissions->count() > 0) {
    echo "⚠️  Permission dengan format lama yang tersisa:\n";
    foreach ($oldFormatPermissions as $perm) {
        echo "   - {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "✅ Semua permission sudah menggunakan format baru (dash)\n";
}

echo "\n🎉 Verifikasi selesai!\n";
