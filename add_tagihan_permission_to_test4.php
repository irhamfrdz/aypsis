<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Menambahkan permission tagihan-kontainer-view ke user test4\n";
echo "==========================================================\n\n";

// Cari user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 tidak ditemukan!\n";
    exit(1);
}

echo "User ditemukan: {$user->username} (ID: {$user->id})\n\n";

// Cari permission tagihan-kontainer-view
$permission = Permission::where('name', 'tagihan-kontainer-view')->first();
if (!$permission) {
    echo "❌ Permission tagihan-kontainer-view tidak ditemukan!\n";
    exit(1);
}

echo "Permission ditemukan: {$permission->name} (ID: {$permission->id})\n\n";

// Cek apakah user sudah memiliki permission ini
$existing = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->where('permission_id', $permission->id)
    ->first();

if ($existing) {
    echo "✅ User test4 sudah memiliki permission tagihan-kontainer-view\n";
    exit(0);
}

// Tambahkan permission ke user
try {
    DB::table('user_permissions')->insert([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "✅ Berhasil menambahkan permission tagihan-kontainer-view ke user test4\n";
} catch (Exception $e) {
    echo "❌ Gagal menambahkan permission: {$e->getMessage()}\n";
    exit(1);
}

// Verifikasi penambahan
$verify = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->where('permission_id', $permission->id)
    ->first();

if ($verify) {
    echo "✅ Verifikasi berhasil: Permission telah ditambahkan\n";
} else {
    echo "❌ Verifikasi gagal: Permission tidak ditemukan setelah penambahan\n";
}

echo "\nSelesai!\n";
