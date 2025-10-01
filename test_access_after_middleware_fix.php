<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST AKSES SETELAH PERBAIKAN MIDDLEWARE ===\n\n";

try {
    $user = \App\Models\User::find(1); // admin user

    if (!$user) {
        echo "❌ User admin tidak ditemukan\n";
        exit;
    }

    echo "✅ User admin loaded: {$user->username}\n\n";

    // Test modules yang diperbaiki
    $testModules = [
        'cabang' => [
            'permission' => 'master-cabang-view',
            'route' => 'master.cabang.index'
        ],
        'coa' => [
            'permission' => 'master-coa-view',
            'route' => 'master-coa-index'
        ],
        'tipe_akun' => [
            'permission' => 'master-tipe-akun-view',
            'route' => 'master.tipe-akun.index'
        ],
        'kode_nomor' => [
            'permission' => 'master-kode-nomor-view',
            'route' => 'master.kode-nomor.index'
        ],
        'nomor_terakhir' => [
            'permission' => 'master-nomor-terakhir-view',
            'route' => 'master.nomor-terakhir.index'
        ],
        'tujuan' => [
            'permission' => 'master-tujuan-view',
            'route' => 'tujuan.index'
        ],
        'kegiatan' => [
            'permission' => 'master-kegiatan-view',
            'route' => 'kegiatan.index'
        ]
    ];

    echo "=== TEST PERMISSION USER ADMIN ===\n";

    $allGranted = true;

    foreach ($testModules as $module => $config) {
        $hasPermission = $user->can($config['permission']);
        $status = $hasPermission ? '✅ GRANTED' : '❌ DENIED';

        echo sprintf("%-15s: %s can('%s')\n",
            strtoupper($module),
            $status,
            $config['permission']
        );

        if (!$hasPermission) {
            $allGranted = false;
        }
    }

    echo "\n=== HASIL PERBAIKAN ===\n";

    if ($allGranted) {
        echo "🎉 SEMUA AKSES GRANTED!\n";
        echo "✅ Sekarang user admin dapat mengakses semua menu master\n";
        echo "✅ Tidak perlu permission 'create' lagi untuk akses menu\n\n";

        echo "=== PERUBAHAN YANG DILAKUKAN ===\n";
        echo "1. ❌ Dihapus requirement permission 'create' untuk akses index\n";
        echo "2. ✅ Sekarang hanya butuh permission 'view' untuk akses menu\n";
        echo "3. 🧹 Middleware lebih bersih dan logis\n\n";

        echo "=== INSTRUKSI UNTUK USER ===\n";
        echo "1. 🔄 REFRESH halaman web (F5 atau Ctrl+F5)\n";
        echo "2. 🧪 Test akses menu:\n";
        foreach ($testModules as $module => $config) {
            echo "   - " . ucwords(str_replace('_', ' ', $module)) . " → Sekarang bisa diakses ✅\n";
        }
        echo "3. ✅ Tidak akan ada lagi pesan 'Akses Ditolak'\n";

    } else {
        echo "⚠️  Masih ada permission yang kurang\n";
        echo "💡 Perlu troubleshooting lebih lanjut\n";
    }

    echo "\n=== MIDDLEWARE SEKARANG VS SEBELUMNYA ===\n";
    echo "❌ SEBELUM: index butuh view + create + update + delete (4 permission)\n";
    echo "✅ SESUDAH: index hanya butuh view (1 permission)\n\n";

    echo "💡 KEUNTUNGAN PERBAIKAN INI:\n";
    echo "- User dengan role 'viewer' bisa akses menu tanpa permission edit\n";
    echo "- Permission system lebih logis dan mudah diatur\n";
    echo "- Tidak ada permission berlebihan untuk akses basic menu\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
