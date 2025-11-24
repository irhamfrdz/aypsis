<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Capsule\Manager as DB;

// Configurasi database
$capsule = new DB;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== CEK PERMISSION USER KIKY UNTUK INVOICE TAGIHAN SEWA ===\n\n";

// Cari user Kiky (hanya berdasarkan username)
$kiky = User::where('username', 'kiky')->first();

if (!$kiky) {
    echo "❌ User Kiky tidak ditemukan!\n";
    echo "Coba cari dengan nama lain...\n\n";
    
    // Cari user dengan nama yang mengandung 'kiky'
    $users = User::where('username', 'like', '%kiky%')->get();
    
    echo "User yang ditemukan dengan kata 'kiky':\n";
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Username: {$user->username}\n";
    }
    
    exit;
}

echo "✅ User ditemukan:\n";
echo "- ID: {$kiky->id}\n";
echo "- Username: {$kiky->username}\n\n";

// Cek semua permission yang dimiliki Kiky terkait tagihan-kontainer-sewa
echo "=== PERMISSION TAGIHAN-KONTAINER-SEWA YANG DIMILIKI KIKY ===\n";
$kikyTagihanPermissions = $kiky->permissions()
    ->where('name', 'like', '%tagihan-kontainer-sewa%')
    ->get();

if ($kikyTagihanPermissions->count() > 0) {
    foreach ($kikyTagihanPermissions as $perm) {
        echo "✅ {$perm->name} - {$perm->description}\n";
    }
} else {
    echo "❌ Kiky TIDAK memiliki permission tagihan-kontainer-sewa apapun!\n";
}

echo "\n=== PERMISSION YANG DIPERLUKAN UNTUK INVOICE TAGIHAN SEWA ===\n";
$requiredPermissions = [
    'tagihan-kontainer-sewa-index' => 'Akses menu invoice',
    'tagihan-kontainer-sewa-view' => 'Lihat detail invoice (tombol show)',
    'tagihan-kontainer-sewa-update' => 'Edit invoice',
    'tagihan-kontainer-sewa-delete' => 'Hapus invoice',
    'tagihan-kontainer-sewa-create' => 'Create pranota dari invoice',
];

foreach ($requiredPermissions as $permName => $description) {
    $hasPermission = $kiky->hasPermissionTo($permName);
    $status = $hasPermission ? "✅" : "❌";
    echo "{$status} {$permName} - {$description}\n";
    
    if (!$hasPermission) {
        // Cek apakah permission ada di database
        $permExists = Permission::where('name', $permName)->exists();
        if (!$permExists) {
            echo "   ⚠️  Permission '{$permName}' tidak ada di database!\n";
        } else {
            echo "   ℹ️  Permission ada di database tapi tidak assigned ke Kiky\n";
        }
    }
}

echo "\n=== KESIMPULAN ===\n";
$canAccessInvoiceMenu = $kiky->hasPermissionTo('tagihan-kontainer-sewa-index');
$canViewInvoiceDetails = $kiky->hasPermissionTo('tagihan-kontainer-sewa-view');

echo "Dapat akses menu Invoice Tagihan Sewa: " . ($canAccessInvoiceMenu ? "✅ YA" : "❌ TIDAK") . "\n";
echo "Dapat lihat detail/show Invoice: " . ($canViewInvoiceDetails ? "✅ YA" : "❌ TIDAK") . "\n";

if (!$canViewInvoiceDetails) {
    echo "\n🔧 SOLUSI:\n";
    echo "Berikan permission 'tagihan-kontainer-sewa-view' kepada user Kiky dengan cara:\n";
    echo "1. Masuk ke admin panel\n";
    echo "2. Kelola permission user Kiky\n";
    echo "3. Atau jalankan script PHP untuk assign permission\n\n";
    
    echo "Script untuk memberikan permission:\n";
    echo "```php\n";
    echo "\$kiky = User::find({$kiky->id});\n";
    echo "\$kiky->givePermissionTo('tagihan-kontainer-sewa-view');\n";
    echo "```\n";
}

echo "\n=== SEMUA PERMISSION KIKY ===\n";
$allPermissions = $kiky->permissions()->get();
echo "Total permission: " . $allPermissions->count() . "\n";
foreach ($allPermissions as $perm) {
    echo "- {$perm->name}\n";
}
