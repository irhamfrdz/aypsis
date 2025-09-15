<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🎯 Final Test: Menu Master Karyawan untuk Test4\n";
echo "===============================================\n\n";

$userTest4 = User::where('username', 'test4')->first();
if (!$userTest4) {
    echo "❌ User 'test4' tidak ditemukan\n";
    exit;
}

echo "👤 User: test4 (ID: {$userTest4->id})\n";
echo "   Status: {$userTest4->status}\n\n";

// Cek semua permissions
$permissions = $userTest4->permissions->pluck('name')->sort();
echo "📋 Permissions Test4:\n";
foreach ($permissions as $perm) {
    echo "   - {$perm}\n";
}
echo "\n";

// Test permission yang diperlukan untuk sidebar
$sidebarTests = [
    'master-karyawan' => 'Menu utama (wajib untuk sidebar)',
    'master-karyawan.view' => 'View karyawan',
    'master-karyawan.create' => 'Create karyawan',
    'master-karyawan.update' => 'Update karyawan',
    'master-karyawan.delete' => 'Delete karyawan',
    'dashboard' => 'Dashboard access',
];

echo "🧪 Test Permission untuk Sidebar:\n";
$sidebarReady = true;
foreach ($sidebarTests as $perm => $desc) {
    $hasAccess = $userTest4->hasPermissionTo($perm);
    $status = $hasAccess ? '✅' : '❌';
    echo "   {$desc} ({$perm}): {$status}\n";

    if ($perm === 'master-karyawan' && !$hasAccess) {
        $sidebarReady = false;
    }
}

echo "\n🎯 Hasil Test:\n";
if ($sidebarReady) {
    echo "   ✅ SEMUA SYARAT TERPENUHI\n";
    echo "   🎉 Menu Master Karyawan HARUS muncul di sidebar\n\n";

    echo "📋 Yang perlu dilakukan user:\n";
    echo "   1. 🔄 Logout dari aplikasi\n";
    echo "   2. 🔑 Login kembali dengan user test4\n";
    echo "   3. 📱 Buka sidebar (klik menu di kiri atas)\n";
    echo "   4. 👀 Menu 'Master Data' → 'Karyawan' harus terlihat\n\n";

    echo "🔧 Jika masih tidak muncul:\n";
    echo "   1. Clear browser cache (Ctrl+F5)\n";
    echo "   2. Coba browser lain\n";
    echo "   3. Pastikan tidak ada cache aplikasi\n";

} else {
    echo "   ❌ SYARAT BELUM TERPENUHI\n";
    echo "   ⚠️  Permission 'master-karyawan' utama belum ada\n";
}

echo "\n🎯 Test selesai!\n";
