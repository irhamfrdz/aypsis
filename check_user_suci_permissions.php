<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== CEK PERMISSION USER SUCI ===\n\n";

// Cari user suci
$user = User::where('username', 'suci')->first();

if (!$user) {
    echo "❌ User 'suci' tidak ditemukan!\n";
    exit;
}

echo "✓ User ditemukan:\n";
echo "  - Username: {$user->username}\n";
echo "  - ID: {$user->id}\n";
echo "  - Karyawan: " . ($user->karyawan ? $user->karyawan->nama_lengkap : 'Tidak terhubung') . "\n\n";

echo "1. CEK PERMISSION 'master-mobil-view':\n";
$hasMobilView = $user->hasPermissionTo('master-mobil-view');
echo "   " . ($hasMobilView ? "✓ PUNYA" : "✗ TIDAK PUNYA") . " permission 'master-mobil-view'\n\n";

echo "2. SEMUA PERMISSION MOBIL YANG DIMILIKI USER SUCI:\n";
$mobilPerms = $user->permissions()->where('name', 'like', '%mobil%')->get();
if ($mobilPerms->count() > 0) {
    foreach ($mobilPerms as $perm) {
        echo "   ✓ {$perm->name}\n";
    }
} else {
    echo "   ✗ TIDAK ADA PERMISSION MOBIL!\n";
}

echo "\n3. TOTAL SEMUA PERMISSION USER SUCI:\n";
echo "   Total: {$user->permissions->count()} permissions\n\n";

echo "4. CEK PERMISSION YANG ADA DI DATABASE:\n";
$dbPerm = Permission::where('name', 'master-mobil-view')->first();
if ($dbPerm) {
    echo "   ✓ Permission 'master-mobil-view' ada di database (ID: {$dbPerm->id})\n";
} else {
    echo "   ✗ Permission 'master-mobil-view' TIDAK ADA di database!\n";
}

echo "\n5. CEK DIRECT PERMISSION (bukan dari role):\n";
$directPerms = DB::table('model_has_permissions')
    ->where('model_type', 'App\Models\User')
    ->where('model_id', $user->id)
    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
    ->where('permissions.name', 'like', '%mobil%')
    ->select('permissions.name', 'permissions.id')
    ->get();

if ($directPerms->count() > 0) {
    foreach ($directPerms as $perm) {
        echo "   ✓ {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "   ✗ Tidak ada direct permission mobil\n";
}

echo "\n=== SELESAI ===\n";
