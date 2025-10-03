<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CLEAR ALL USER SESSIONS ===\n\n";

echo "⚠️  WARNING: Ini akan logout SEMUA user dari aplikasi!\n";
echo "Lanjutkan? (y/n): ";

$handle = fopen("php://stdin", "r");
$confirm = trim(fgets($handle));
fclose($handle);

if (strtolower($confirm) !== 'y') {
    echo "Dibatalkan.\n";
    exit;
}

try {
    // Clear sessions table
    $deleted = DB::table('sessions')->delete();
    echo "✅ {$deleted} session(s) dihapus dari database\n";

    // Clear cache
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";

    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";

    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✅ View cache cleared\n";

    echo "\n=== SELESAI ===\n";
    echo "Semua user HARUS login ulang.\n";
    echo "Setelah login ulang, permission akan ter-update otomatis.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
