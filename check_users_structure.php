<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Cek Struktur Tabel Users\n";
echo "===========================\n\n";

$columns = Schema::getColumnListing('users');
echo "Kolom yang tersedia di tabel users:\n";
foreach ($columns as $column) {
    echo "   - {$column}\n";
}
echo "\n";

// Cek data user test4
$userTest4 = User::where('username', 'test4')->first();
if ($userTest4) {
    echo "👤 Data user test4:\n";
    foreach ($columns as $column) {
        $value = $userTest4->$column;
        echo "   {$column}: " . (is_null($value) ? 'NULL' : $value) . "\n";
    }
} else {
    echo "❌ User test4 tidak ditemukan\n";
}

echo "\n🔍 Pengecekan selesai!\n";
