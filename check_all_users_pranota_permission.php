<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Daftar semua user dan permission pranota-kontainer-sewa mereka:\n";
echo "==============================================================\n\n";

$users = User::with('permissions')->get();

foreach ($users as $user) {
    echo "User: {$user->username} (ID: {$user->id})\n";

    $pranotaKontainerSewaPerms = $user->permissions()
        ->where('name', 'LIKE', 'pranota-kontainer-sewa-%')
        ->get();

    if ($pranotaKontainerSewaPerms->isNotEmpty()) {
        echo "  Pranota Kontainer Sewa permissions:\n";
        foreach ($pranotaKontainerSewaPerms as $perm) {
            echo "    ✓ {$perm->name}\n";
        }
    } else {
        echo "  ✗ TIDAK memiliki permission pranota-kontainer-sewa\n";
    }

    echo "\n";
}

echo "\n";
echo "Solusi jika masih 'akses ditolak':\n";
echo "1. Logout dari aplikasi\n";
echo "2. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "3. Jalankan: php artisan cache:clear\n";
echo "4. Jalankan: php artisan config:clear\n";
echo "5. Login kembali\n";
echo "\n";
echo "Atau jalankan script ini untuk clear cache:\n";
echo "  php artisan optimize:clear\n";
