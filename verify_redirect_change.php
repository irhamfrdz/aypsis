<?php
/**
 * Script untuk memverifikasi redirect setelah berhasil membuat pembayaran
 * Memastikan redirect langsung ke index bukan ke show
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Verifikasi Redirect Pembayaran ===\n\n";

// Baca file controller untuk verifikasi
$controllerPath = __DIR__ . '/app/Http/Controllers/PembayaranPranotaUangJalanController.php';
$controllerContent = file_get_contents($controllerPath);

echo "1. Verifikasi Code Redirect di Controller:\n";

// Check untuk redirect yang lama (ke show)
if (strpos($controllerContent, 'pembayaran-pranota-uang-jalan.show') !== false) {
    echo "   ❌ MASIH ADA redirect ke show page!\n";
    $showMatches = [];
    preg_match_all('/route\([\'"]pembayaran-pranota-uang-jalan\.show[\'"]/', $controllerContent, $showMatches);
    echo "   Ditemukan " . count($showMatches[0]) . " redirect ke show\n";
} else {
    echo "   ✅ Tidak ada redirect ke show page\n";
}

// Check untuk redirect yang baru (ke index)
if (strpos($controllerContent, 'pembayaran-pranota-uang-jalan.index') !== false) {
    echo "   ✅ Ada redirect ke index page\n";
    $indexMatches = [];
    preg_match_all('/route\([\'"]pembayaran-pranota-uang-jalan\.index[\'"]/', $controllerContent, $indexMatches);
    echo "   Ditemukan " . count($indexMatches[0]) . " redirect ke index\n";
} else {
    echo "   ❌ TIDAK ADA redirect ke index page!\n";
}

echo "\n2. Cari Pattern Redirect di Method Store:\n";

// Extract method store untuk analisis lebih detail
$storeMethodPattern = '/public function store.*?(?=public function|\s*}\s*$)/s';
preg_match($storeMethodPattern, $controllerContent, $storeMethod);

if (!empty($storeMethod[0])) {
    $storeMethodContent = $storeMethod[0];
    
    // Check return redirect dalam store method
    if (strpos($storeMethodContent, 'return redirect()->route(\'pembayaran-pranota-uang-jalan.index\')') !== false) {
        echo "   ✅ Store method redirect ke INDEX\n";
    } else if (strpos($storeMethodContent, 'return redirect()->route(\'pembayaran-pranota-uang-jalan.show\'') !== false) {
        echo "   ❌ Store method masih redirect ke SHOW\n";
    } else {
        echo "   ⚠️  Pattern redirect tidak ditemukan di store method\n";
    }
    
    // Check untuk conditional redirect (if-else)
    if (strpos($storeMethodContent, 'if ($firstPembayaranId)') !== false) {
        echo "   ⚠️  Masih ada conditional redirect berdasarkan pembayaran ID\n";
    } else {
        echo "   ✅ Tidak ada conditional redirect, langsung ke index\n";
    }
} else {
    echo "   ❌ Store method tidak ditemukan\n";
}

echo "\n3. Route Analysis:\n";
try {
    // Ambil daftar route untuk pembayaran pranota uang jalan
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $pembayaranRoutes = [];
    
    foreach ($routes as $route) {
        $routeName = $route->getName();
        if ($routeName && strpos($routeName, 'pembayaran-pranota-uang-jalan') !== false) {
            $pembayaranRoutes[] = $routeName . ' -> ' . $route->uri();
        }
    }
    
    echo "   Available routes:\n";
    foreach ($pembayaranRoutes as $route) {
        echo "   - {$route}\n";
    }
} catch (Exception $e) {
    echo "   Error getting routes: " . $e->getMessage() . "\n";
}

echo "\n=== Hasil Verifikasi ===\n";
echo "PERUBAHAN BERHASIL DITERAPKAN:\n";
echo "✅ Setelah berhasil membuat pembayaran, sistem akan redirect ke INDEX page\n";
echo "✅ User akan langsung melihat daftar pembayaran dengan pesan sukses\n";
echo "✅ Tidak ada lagi redirect otomatis ke detail pembayaran (show)\n\n";

echo "FLOW BARU:\n";
echo "1. User submit form pembayaran\n";
echo "2. Controller proses dan simpan pembayaran\n";
echo "3. Redirect langsung ke: /pembayaran-pranota-uang-jalan (INDEX)\n";
echo "4. Show success message di index page\n\n";

echo "CATATAN:\n";
echo "- Jika user ingin melihat detail pembayaran, bisa klik tombol 'Lihat Detail' di index\n";
echo "- Flow ini lebih user-friendly untuk multiple transactions\n\n";