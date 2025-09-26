<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\User;
use App\Models\Permission;

// Ganti dengan ID user yang bermasalah
$userId = 1; // Sesuaikan dengan user yang bermasalah

$user = User::find($userId);
if (!$user) {
    echo "User dengan ID $userId tidak ditemukan\n";
    exit;
}

echo "Checking permissions for user: {$user->username} (ID: {$user->id})\n\n";

$catPermissions = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();

if ($catPermissions->isEmpty()) {
    echo "❌ User tidak memiliki permission pembayaran-pranota-cat\n";
} else {
    echo "✅ User memiliki permission pembayaran-pranota-cat:\n";
    foreach ($catPermissions as $p) {
        echo "  - {$p->name}\n";
    }
}

echo "\nSemua permission user:\n";
$userPermissions = $user->permissions()->orderBy('name')->get();
foreach ($userPermissions as $p) {
    if (strpos($p->name, 'pembayaran-pranota-cat') !== false) {
        echo "  - {$p->name} (ID: {$p->id})\n";
    }
}
