<?php

// Script untuk menganalisis masalah akses ditolak secara detail

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== ANALISIS MENDALAM MASALAH AKSES DITOLAK ===\n\n";

// Ambil semua user dan tampilkan permission mereka
$users = User::with('permissions')->get();

echo "=== DAFTAR SEMUA USER DAN PERMISSION ===\n";
foreach ($users as $user) {
    echo "\n👤 User: {$user->username} (ID: {$user->id})\n";
    echo "   Total permissions: " . $user->permissions->count() . "\n";

    // Cek permission untuk menu bermasalah
    $menuPermissions = [
        'master-cabang-view',
        'master-coa-view',
        'master-kode-nomor-view',
        'master-nomor-terakhir-view',
        'master-tipe-akun-view'
    ];

    $hasViewPermissions = [];
    foreach ($menuPermissions as $perm) {
        $has = $user->can($perm);
        $hasViewPermissions[$perm] = $has;
        $icon = $has ? '✅' : '❌';
        echo "   $icon $perm\n";
    }

    // Hitung berapa menu yang bisa diakses
    $accessibleMenus = array_sum($hasViewPermissions);
    $totalMenus = count($hasViewPermissions);
    echo "   📊 Dapat mengakses: $accessibleMenus/$totalMenus menu\n";
}

echo "\n=== KEMUNGKINAN PENYEBAB AKSES DITOLAK ===\n";

$possibleCauses = [
    "🔐 Session Login" => [
        "User belum login atau session expired",
        "Solusi: Logout dan login kembali"
    ],
    "🕐 Cache Permission" => [
        "Permission di-cache dan belum refresh",
        "Solusi: Clear cache dengan php artisan cache:clear"
    ],
    "🔄 Permission Sync" => [
        "Permission baru ditambahkan tapi belum sync",
        "Solusi: Re-save user di admin panel"
    ],
    "🌐 Browser Cache" => [
        "Browser menyimpan response lama",
        "Solusi: Hard refresh (Ctrl+Shift+R) atau buka incognito"
    ],
    "🛡️ Middleware Issue" => [
        "Ada middleware yang mengblokir akses",
        "Solusi: Cek routes dan middleware configuration"
    ],
    "📝 Permission Name Mismatch" => [
        "Nama permission di database tidak sesuai dengan yang dicek",
        "Solusi: Periksa nama permission yang exact"
    ]
];

foreach ($possibleCauses as $cause => $details) {
    echo "\n$cause\n";
    echo "  Problem: {$details[0]}\n";
    echo "  Solusi: {$details[1]}\n";
}

echo "\n=== LANGKAH-LANGKAH TROUBLESHOOTING ===\n";
echo "1. 🔍 Cek user mana yang bermasalah\n";
echo "2. 🔐 Pastikan user sudah login dengan benar\n";
echo "3. 🔄 Coba logout dan login kembali\n";
echo "4. 🧹 Clear cache browser (Ctrl+Shift+R)\n";
echo "5. 🗂️ Clear cache Laravel: php artisan cache:clear\n";
echo "6. 📋 Periksa permission di admin panel\n";
echo "7. 🔬 Cek Laravel log untuk error detail\n\n";

echo "=== PERINTAH DEBUGGING LANJUTAN ===\n";
echo "• Cek permission user tertentu:\n";
echo "  php diagnose_access_denied.php\n\n";
echo "• Cek Laravel logs:\n";
echo "  tail -f storage/logs/laravel.log\n\n";
echo "• Test permission langsung:\n";
echo "  php artisan tinker\n";
echo "  > \$user = User::find(1);\n";
echo "  > \$user->can('master-cabang-view');\n\n";

// Buat file script untuk perbaikan cepat
$fixScript = '<?php
require_once __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

echo "=== PERBAIKAN CEPAT PERMISSION ===\n";

// Clear semua cache
Artisan::call("cache:clear");
Artisan::call("config:clear");
Artisan::call("route:clear");
echo "✅ Cache cleared\n";

// Reload permission untuk semua user
$users = User::with("permissions")->get();
foreach ($users as $user) {
    $user->touch(); // Update timestamp untuk force reload
}
echo "✅ User permissions reloaded\n";

echo "\n🎉 Perbaikan selesai! Coba akses menu lagi.\n";
';

file_put_contents('fix_permissions_quick.php', $fixScript);
echo "📁 Script perbaikan cepat dibuat: fix_permissions_quick.php\n";
echo "   Jalankan dengan: php fix_permissions_quick.php\n";
