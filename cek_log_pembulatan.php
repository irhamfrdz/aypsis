<?php
// cek_log_pembulatan.php
$logFile = 'scripts/bulatkan_grand_total_result_20251111_170322.json';
$log = json_decode(file_get_contents($logFile), true);

echo "=== CEK LOG PEMBULATAN ===\n\n";
echo "Total updated: " . $log['stats']['updated'] . "\n";
echo "Total skipped: " . $log['stats']['skipped'] . "\n\n";

// Cari ID 5058 (MSKU2218091 P4)
$found = array_filter($log['details'], function($d) {
    return $d['id'] == 5058;
});

if (count($found) > 0) {
    echo "ID 5058 (MSKU2218091 P4) DITEMUKAN di log:\n";
    print_r(array_values($found)[0]);
} else {
    echo "ID 5058 TIDAK DITEMUKAN di log\n";
    echo "Kemungkinan: di-skip karena nilai sudah bulat atau ada error\n\n";
    
    // Cek beberapa sample yang di-skip
    echo "Checking database value...\n";
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $item = DB::table('daftar_tagihan_kontainer_sewa')->where('id', 5058)->first();
    $oldValue = 515540.71; // Dari backup
    $newValue = round($oldValue);
    $diff = abs($oldValue - $newValue);
    
    echo "Old grand_total: $oldValue\n";
    echo "Expected rounded: $newValue\n";
    echo "Difference: $diff\n";
    echo "Skip condition (abs diff > 0.009): " . ($diff > 0.009 ? 'NO (should update)' : 'YES (skipped)') . "\n";
    echo "\nCurrent value in DB: " . $item->grand_total . "\n";
}
