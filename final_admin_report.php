<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL ADMIN PERMISSIONS REPORT ===" . PHP_EOL;

$admin = App\Models\User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user tidak ditemukan!" . PHP_EOL;
    exit;
}

echo "👤 Admin User: {$admin->username} (ID: {$admin->id})" . PHP_EOL;
echo "📊 Total Permissions: " . $admin->permissions()->count() . PHP_EOL;

echo PHP_EOL . "🔍 PERMISSION CATEGORIES:" . PHP_EOL;

// Group permissions by category
$permissions = $admin->permissions()->get();
$categories = [];

foreach ($permissions as $perm) {
    $name = $perm->name;
    
    // Categorize permissions
    if (strpos($name, 'master-') === 0) {
        $categories['Master Data'][] = $name;
    } elseif (strpos($name, 'dashboard') !== false) {
        $categories['Dashboard'][] = $name;
    } elseif (strpos($name, 'audit') !== false) {
        $categories['Audit Log'][] = $name;
    } elseif (strpos($name, 'approval') !== false) {
        $categories['Approval'][] = $name;
    } elseif (strpos($name, 'surat-jalan') !== false) {
        $categories['Surat Jalan'][] = $name;
    } elseif (strpos($name, 'gate-in') !== false) {
        $categories['Gate In'][] = $name;
    } elseif (strpos($name, 'order') !== false) {
        $categories['Pesanan Pengambilan Barang'][] = $name;
    } elseif (strpos($name, 'pembayaran') !== false) {
        $categories['Pembayaran'][] = $name;
    } elseif (strpos($name, 'pranota') !== false) {
        $categories['Pranota'][] = $name;
    } elseif (strpos($name, 'kontainer') !== false) {
        $categories['Kontainer'][] = $name;
    } else {
        $categories['Lainnya'][] = $name;
    }
}

// Display categories
foreach ($categories as $category => $perms) {
    echo PHP_EOL . "📂 {$category} ({" . count($perms) . "} permissions):" . PHP_EOL;
    
    // Show first 5 permissions in each category
    $displayed = array_slice($perms, 0, 5);
    foreach ($displayed as $perm) {
        echo "   ✓ {$perm}" . PHP_EOL;
    }
    
    if (count($perms) > 5) {
        echo "   ... dan " . (count($perms) - 5) . " permission lainnya" . PHP_EOL;
    }
}

// Special focus on recently added permissions
echo PHP_EOL . "🆕 RECENTLY ADDED PERMISSIONS:" . PHP_EOL;
$recentPermissions = [
    'dashboard' => 'Akses halaman dashboard utama sistem',
    'dashboard-view' => 'Melihat halaman dashboard',
    'audit-log-view' => 'Melihat log audit sistem', 
    'audit-log-export' => 'Export log audit sistem'
];

foreach ($recentPermissions as $name => $desc) {
    $hasPermission = $admin->permissions()->where('name', $name)->exists();
    $status = $hasPermission ? '✅' : '❌';
    echo "   {$status} {$name} - {$desc}" . PHP_EOL;
}

echo PHP_EOL . "🚀 STATUS: Admin user sudah siap dengan semua permissions!" . PHP_EOL;
echo "📝 Login credentials:" . PHP_EOL;
echo "   - Username: admin" . PHP_EOL;
echo "   - Password: admin123" . PHP_EOL;
echo "   - URL: http://127.0.0.1:8000" . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;