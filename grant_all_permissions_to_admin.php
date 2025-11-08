<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== SCRIPT MEMBERIKAN SEMUA PERMISSION KE USER ADMIN ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Cari user admin
echo "1. Mencari user admin...\n";

$adminUsers = User::where('username', 'admin')
    ->orWhere('username', 'like', '%admin%')
    ->get();

if ($adminUsers->count() == 0) {
    echo "❌ Tidak ada user admin yang ditemukan.\n";
    echo "User yang tersedia:\n";
    
    $allUsers = User::select('id', 'username')->take(10)->get();
    foreach ($allUsers as $user) {
        echo "- ID: {$user->id}, Username: {$user->username}\n";
    }
    exit;
}

echo "Ditemukan " . $adminUsers->count() . " user admin:\n";
foreach ($adminUsers as $user) {
    echo "- ID: {$user->id}, Username: {$user->username}\n";
}

// 2. Pilih user admin yang akan diberi permission
if ($adminUsers->count() == 1) {
    $selectedAdmin = $adminUsers->first();
    echo "\nMenggunakan user: {$selectedAdmin->username} (ID: {$selectedAdmin->id})\n";
} else {
    echo "\nPilih user admin (masukkan ID): ";
    $handle = fopen("php://stdin", "r");
    $selectedId = trim(fgets($handle));
    fclose($handle);
    
    $selectedAdmin = $adminUsers->find($selectedId);
    if (!$selectedAdmin) {
        echo "❌ User dengan ID {$selectedId} tidak ditemukan.\n";
        exit;
    }
    echo "Terpilih: {$selectedAdmin->username} (ID: {$selectedAdmin->id})\n";
}

// 3. Ambil semua permission yang tersedia
echo "\n2. Mengambil semua permission yang tersedia...\n";

$allPermissions = Permission::all();
echo "Ditemukan " . $allPermissions->count() . " permission.\n";

// Tampilkan beberapa contoh permission
echo "\nContoh permission yang akan diberikan:\n";
$allPermissions->take(10)->each(function($permission) {
    echo "- {$permission->name}: {$permission->description}\n";
});

if ($allPermissions->count() > 10) {
    echo "... dan " . ($allPermissions->count() - 10) . " permission lainnya\n";
}

// 4. Cek permission yang sudah dimiliki
$currentPermissions = $selectedAdmin->permissions()->get();
echo "\nUser {$selectedAdmin->username} saat ini memiliki " . $currentPermissions->count() . " permission.\n";

// 5. Konfirmasi
echo "\n3. Konfirmasi pemberian semua permission ke {$selectedAdmin->username}? (y/n): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) != 'y' && strtolower($confirmation) != 'yes') {
    echo "Operasi dibatalkan.\n";
    exit;
}

// 6. Berikan semua permission
echo "\n4. Memberikan semua permission ke user {$selectedAdmin->username}...\n";

try {
    // Ambil semua ID permission
    $allPermissionIds = $allPermissions->pluck('id')->toArray();
    
    // Sync semua permission ke user (ini akan mengganti semua permission existing)
    $selectedAdmin->permissions()->sync($allPermissionIds);
    
    echo "✅ Berhasil memberikan " . count($allPermissionIds) . " permission ke user {$selectedAdmin->username}!\n";
    
    // 7. Verifikasi
    echo "\n5. Verifikasi hasil...\n";
    $newPermissionCount = $selectedAdmin->fresh()->permissions()->count();
    $totalPermissions = Permission::count();
    
    if ($newPermissionCount == $totalPermissions) {
        echo "✅ Verifikasi berhasil! User {$selectedAdmin->username} sekarang memiliki SEMUA {$newPermissionCount} permission.\n";
    } else {
        echo "⚠️ Ada masalah! User memiliki {$newPermissionCount} permission dari total {$totalPermissions} permission.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error saat memberikan permission: " . $e->getMessage() . "\n";
}

// 8. Tampilkan beberapa permission baru
echo "\n6. Beberapa permission yang sekarang dimiliki:\n";
$selectedAdmin->fresh()->permissions()->take(15)->each(function($permission) {
    echo "✓ {$permission->name}\n";
});

$totalUserPermissions = $selectedAdmin->fresh()->permissions()->count();
if ($totalUserPermissions > 15) {
    echo "... dan " . ($totalUserPermissions - 15) . " permission lainnya\n";
}

echo "\n=== SCRIPT SELESAI ===\n";
echo "User {$selectedAdmin->username} sekarang memiliki akses SUPER ADMIN dengan semua permission!\n";
echo "Waktu selesai: " . date('Y-m-d H:i:s') . "\n";