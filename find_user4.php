<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Cari User dengan Username Mirip 'user4'\n";
echo "=========================================\n\n";

// Cari user dengan username yang mengandung '4' atau 'user'
$possibleUsers = User::where('username', 'like', '%4%')
    ->orWhere('username', 'like', '%user%')
    ->get();

if ($possibleUsers->count() > 0) {
    echo "👥 User yang ditemukan:\n";
    foreach ($possibleUsers as $user) {
        echo "   - {$user->username} (ID: {$user->id}, Email: {$user->email})\n";
    }
    echo "\n";
} else {
    echo "❌ Tidak ada user dengan username mengandung '4' atau 'user'\n\n";
}

// Tampilkan semua user untuk referensi
echo "📋 Semua User di Sistem:\n";
$allUsers = User::orderBy('username')->get();
foreach ($allUsers as $user) {
    echo "   - {$user->username} (ID: {$user->id})\n";
}
echo "\n";

echo "💡 Kemungkinan:\n";
echo "   1. Username mungkin 'user4' tapi tidak ada di database\n";
echo "   2. Username berbeda, coba cek lagi\n";
echo "   3. User belum dibuat\n\n";

echo "🔍 Pencarian selesai!\n";
