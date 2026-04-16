<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\HistoryKontainer;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mismatchesJson = file_get_contents('scratch/mismatches.json');
$mismatches = json_decode($mismatchesJson, true);

echo "Total mismatches to fix: " . count($mismatches) . "\n";

$fixed = 0;
$skipped = 0;

foreach ($mismatches as $m) {
    try {
        DB::beginTransaction();
        
        // Cek lagi apakah masih mismatch (prevent double run)
        $lastHistory = HistoryKontainer::where('nomor_kontainer', $m['nomor'])->orderBy('id', 'desc')->first();
        if ($lastHistory && $lastHistory->gudang_id == $m['current_gudang_id']) {
            DB::rollBack();
            $skipped++;
            continue;
        }

        // Buat History baru
        HistoryKontainer::create([
            'nomor_kontainer' => $m['nomor'],
            'tipe_kontainer' => $m['type'],
            'jenis_kegiatan' => 'Masuk (Penyesuaian)',
            'tanggal_kegiatan' => now(), // Kita gunakan waktu sekarang karena kita tidak tahu tepatnya kapan pindah
            'asal_gudang_id' => $m['last_history_gudang_id'], // Gunakan lokasi terakhir yang tercatat sebagai asal
            'gudang_id' => $m['current_gudang_id'],
            'keterangan' => 'Penyesuaian data otomatis (Backfill riwayat pergerakan)',
            'created_by' => 1, // Sistem / Admin
        ]);

        DB::commit();
        $fixed++;
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Error fixing {$m['nomor']}: " . $e->getMessage() . "\n";
    }
}

echo "Fix completed: {$fixed} entries created, {$skipped} skipped.\n";
