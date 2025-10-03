<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== CEK PERMISSION USER YANG LOGIN ===\n\n";

// Minta input username
echo "Masukkan username yang sedang login: ";
$handle = fopen("php://stdin", "r");
$username = trim(fgets($handle));
fclose($handle);

$user = User::where('username', $username)->with('permissions')->first();

if (!$user) {
    echo "❌ User '$username' tidak ditemukan!\n";
    exit;
}

echo "User: {$user->username} (ID: {$user->id})\n";
echo "Jumlah total permissions: " . $user->permissions->count() . "\n\n";

// Filter pranota-kontainer-sewa permissions
$pranotaPerms = $user->permissions->filter(function($perm) {
    return strpos($perm->name, 'pranota-kontainer-sewa') !== false;
});

echo "=== PRANOTA KONTAINER SEWA PERMISSIONS ===\n";
if ($pranotaPerms->count() > 0) {
    echo "✓ User MEMILIKI " . $pranotaPerms->count() . " pranota-kontainer-sewa permissions:\n";
    foreach ($pranotaPerms as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "❌ User TIDAK MEMILIKI pranota-kontainer-sewa permissions!\n";
}

echo "\n=== CEK PERMISSION YANG DIBUTUHKAN ===\n";
$requiredPerm = DB::table('permissions')->where('name', 'pranota-kontainer-sewa-view')->first();
if ($requiredPerm) {
    echo "✓ Permission 'pranota-kontainer-sewa-view' ada di database (ID: {$requiredPerm->id})\n";

    // Cek apakah user punya permission ini
    $hasPerm = $user->permissions->contains('id', $requiredPerm->id);
    if ($hasPerm) {
        echo "✓ User MEMILIKI permission ini!\n";
    } else {
        echo "❌ User TIDAK MEMILIKI permission ini!\n";
    }
} else {
    echo "❌ Permission 'pranota-kontainer-sewa-view' TIDAK ADA di database!\n";
}

echo "\n=== SEMUA PERMISSIONS USER ===\n";
foreach ($user->permissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== SARAN ===\n";
if ($pranotaPerms->count() == 0) {
    echo "1. Buka menu Master User\n";
    echo "2. Edit user '$username'\n";
    echo "3. Cari section 'Pranota Kontainer Sewa'\n";
    echo "4. Centang minimal checkbox 'View'\n";
    echo "5. Klik Save/Simpan\n";
    echo "6. Logout dari aplikasi\n";
    echo "7. Clear browser cache (Ctrl+Shift+Delete)\n";
    echo "8. Login kembali\n";
} else {
    echo "✓ User sudah punya permissions!\n";
    echo "Jika masih Access Denied, lakukan:\n";
    echo "1. Logout dari aplikasi\n";
    echo "2. Clear browser cache (Ctrl+Shift+Delete)\n";
    echo "3. Close semua tab browser\n";
    echo "4. Login kembali\n";
}
