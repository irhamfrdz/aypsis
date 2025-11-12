<?php
// test_update_5058.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Before update:\n";
$before = DB::table('daftar_tagihan_kontainer_sewa')->where('id', 5058)->first();
echo "grand_total: " . $before->grand_total . "\n\n";

// Try different update methods
echo "Attempting update dengan nilai integer 515541...\n";
DB::table('daftar_tagihan_kontainer_sewa')
    ->where('id', 5058)
    ->update(['grand_total' => 515541]);

echo "After update:\n";
$after = DB::table('daftar_tagihan_kontainer_sewa')->where('id', 5058)->first();
echo "grand_total: " . $after->grand_total . "\n";
echo "grand_total (float): " . (float)$after->grand_total . "\n";
echo "grand_total (formatted): Rp " . number_format($after->grand_total, 2, ',', '.') . "\n";

if ($after->grand_total == 515541) {
    echo "\n✓ SUCCESS! Nilai sudah 515541\n";
} else {
    echo "\n✗ FAILED! Nilai masih " . $after->grand_total . "\n";
}
