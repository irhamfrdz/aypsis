<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== DEBUG USER ANGGI STATUS ===\n\n";

$user = User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    exit;
}

echo "✅ User ditemukan: {$user->username} (ID: {$user->id})\n";
echo "📧 Email: {$user->email}\n";
echo "📅 Email verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";
echo "📅 Email verified at: " . ($user->email_verified_at ?? 'NULL') . "\n";
echo "👤 Name: {$user->name}\n";
echo "🔐 Status: " . ($user->status ?? 'NULL') . "\n";

// Check karyawan relationship
echo "\n--- Karyawan Check ---\n";
if ($user->karyawan_id) {
    $karyawan = $user->karyawan;
    if ($karyawan) {
        echo "✅ Karyawan linked: {$karyawan->nama_lengkap}\n";
        echo "📋 Crew checklist complete: " . ($karyawan->crew_checklist_complete ? 'YES' : 'NO') . "\n";
    } else {
        echo "❌ Karyawan ID {$user->karyawan_id} tidak ditemukan!\n";
    }
} else {
    echo "❌ User tidak terhubung dengan karyawan\n";
}

// Check permissions for specific routes
echo "\n--- Route Permission Check ---\n";
$routePermissions = [
    'order-view' => 'Order Index',
    'order-create' => 'Order Create', 
    'order-update' => 'Order Update',
    'surat-jalan-view' => 'Surat Jalan Index',
    'surat-jalan-create' => 'Surat Jalan Create',
    'surat-jalan-update' => 'Surat Jalan Update'
];

foreach ($routePermissions as $permission => $description) {
    $hasPermission = $user->hasPermissionTo($permission);
    $status = $hasPermission ? '✅' : '❌';
    echo "{$status} {$description}: {$permission} = " . ($hasPermission ? 'YES' : 'NO') . "\n";
}

echo "\n=== END DEBUG ===\n";

?>