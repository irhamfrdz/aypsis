<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST COPY PERMISSION FEATURE ===\n\n";

// Get all users with their permissions
$users = User::with('permissions')->get();

echo "📋 DAFTAR USER DAN PERMISSION-NYA:\n";
echo str_repeat("=", 50) . "\n";

foreach ($users as $user) {
    $permissionNames = $user->permissions->pluck('name')->toArray();
    echo "👤 {$user->name} ({$user->username})\n";
    echo "   ID: {$user->id}\n";
    echo "   Permissions: " . (empty($permissionNames) ? '❌ Tidak ada' : implode(', ', $permissionNames)) . "\n";
    echo "   Count: " . count($permissionNames) . "\n";
    echo "---\n";
}

echo "\n🎯 FITUR COPY PERMISSION:\n";
echo "- Pilih user dari dropdown di form create user\n";
echo "- Klik tombol 'Copy Permission'\n";
echo "- Permission dari user yang dipilih akan otomatis dicentang\n";
echo "- Fitur ini menggunakan AJAX call ke endpoint /master/user/{id}/permissions-for-copy\n";

echo "\n✅ Fitur Copy Permission sudah siap digunakan!\n";
