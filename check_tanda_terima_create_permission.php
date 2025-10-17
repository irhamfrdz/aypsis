<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ CEK PERMISSION TANDA-TERIMA-CREATE                                           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Cek permission tanda-terima-create
$permission = DB::table('permissions')
    ->where('name', 'tanda-terima-create')
    ->first();

if ($permission) {
    echo "✅ Permission 'tanda-terima-create' ada (ID: {$permission->id})\n\n";

    // Cek user mana saja yang punya permission ini
    $usersWithPermission = DB::table('user_permissions')
        ->join('users', 'users.id', '=', 'user_permissions.user_id')
        ->where('user_permissions.permission_id', $permission->id)
        ->select('users.id', 'users.name', 'users.username')
        ->get();

    echo "👥 User yang punya permission ini:\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n";

    if ($usersWithPermission->count() > 0) {
        foreach ($usersWithPermission as $user) {
            echo "  - ID: {$user->id} | Name: {$user->name} | Username: {$user->username}\n";
        }
    } else {
        echo "  ⚠️  Tidak ada user yang punya permission ini!\n";
        echo "\n💡 Solusi: Jalankan script assign permission ke admin:\n";
        echo "  php add_tanda_terima_permissions_to_admin.php\n";
    }
} else {
    echo "❌ Permission 'tanda-terima-create' TIDAK DITEMUKAN!\n";
    echo "\n💡 Solusi: Jalankan seeder atau script untuk menambahkan permission:\n";
    echo "  php add_tanda_terima_permissions.php\n";
}

echo "────────────────────────────────────────────────────────────────────────────────\n";
