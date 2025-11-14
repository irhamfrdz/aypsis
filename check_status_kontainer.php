<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Status yang ada di database kontainer:\n";
echo "========================================\n\n";

// Ambil semua status unik beserta jumlahnya
$statuses = DB::table('kontainers')
    ->select('status', DB::raw('COUNT(*) as total'))
    ->groupBy('status')
    ->orderBy('status')
    ->get();

if ($statuses->count() > 0) {
    foreach ($statuses as $row) {
        $statusDisplay = $row->status ?: '(null/kosong)';
        echo "Status: {$statusDisplay}\n";
        echo "Jumlah: {$row->total} kontainer\n";
        echo "----------------------------------------\n";
    }
    
    echo "\nTotal jenis status: " . $statuses->count() . "\n";
} else {
    echo "Tidak ada data kontainer\n";
}

echo "\nSelesai!\n";
