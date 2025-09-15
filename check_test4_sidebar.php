<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔍 Mengecek permission user test4\n\n";

$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ User test4 tidak ditemukan\n";
    exit;
}

echo "✅ User test4 ditemukan (ID: {$user->id})\n\n";

$permissions = $user->permissions;
echo "📋 Permissions user test4:\n";

if ($permissions->isEmpty()) {
    echo "❌ Tidak ada permission\n";
} else {
    foreach ($permissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
}

echo "\n🔍 Mengecek permission master-karyawan:\n";
$hasMainPermission = $user->hasPermissionTo('master-karyawan');
$hasViewPermission = $user->hasPermissionTo('master-karyawan.view');

echo "  - master-karyawan: " . ($hasMainPermission ? '✅ ADA' : '❌ TIDAK ADA') . "\n";
echo "  - master-karyawan.view: " . ($hasViewPermission ? '✅ ADA' : '❌ TIDAK ADA') . "\n";

echo "\n🎯 Kesimpulan:\n";
if ($hasViewPermission) {
    echo "✅ User test4 memiliki permission master-karyawan.view yang diperlukan untuk sidebar\n";
    echo "💡 Menu Master Data SEHARUSNYA muncul di sidebar\n";
} else {
    echo "❌ User test4 TIDAK memiliki permission master-karyawan.view\n";
    echo "💡 Menu Master Data TIDAK akan muncul di sidebar\n";
    echo "🔧 Jalankan: php add_master_permissions_fixed.php untuk menambahkan permission\n";
}
