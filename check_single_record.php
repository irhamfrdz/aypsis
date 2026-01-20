<?php
/**
 * Script untuk melihat kontainer yang BENAR-BENAR hanya punya 1 record
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "===========================================\n";
echo "Analisis Kontainer dengan Record Tunggal\n";
echo "===========================================\n\n";

// Get containers that truly only have 1 record total
$singleRecordContainers = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer')
    ->havingRaw('COUNT(*) = 1')
    ->pluck('nomor_kontainer');

echo "📦 Kontainer dengan hanya 1 record total: " . $singleRecordContainers->count() . "\n\n";

if ($singleRecordContainers->count() > 0) {
    echo "Detail 20 kontainer pertama:\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-18s %-10s %-6s %-12s %-12s %-8s %-10s\n", 
        "Nomor", "Vendor", "Size", "Tgl Awal", "Tgl Akhir", "Periode", "Durasi");
    echo str_repeat("-", 100) . "\n";
    
    foreach ($singleRecordContainers->take(20) as $nomorKontainer) {
        $record = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->first();
        
        $durasi = '-';
        if ($record->tanggal_awal && $record->tanggal_akhir) {
            $start = Carbon::parse($record->tanggal_awal);
            $end = Carbon::parse($record->tanggal_akhir);
            $durasi = $start->diffInDays($end) + 1 . ' hari';
        }
        
        printf("%-18s %-10s %-6s %-12s %-12s %-8d %-10s\n",
            $record->nomor_kontainer,
            $record->vendor ?? '-',
            $record->size ?? '-',
            $record->tanggal_awal ? Carbon::parse($record->tanggal_awal)->format('d/m/Y') : '-',
            $record->tanggal_akhir ? Carbon::parse($record->tanggal_akhir)->format('d/m/Y') : 'ongoing',
            $record->periode ?? 1,
            $durasi
        );
    }
    echo str_repeat("-", 100) . "\n";
}

// Juga cek kontainer yang punya multiple records tapi semua periode = 1
echo "\n\n📊 Kontainer dengan multiple records tapi semua periode = 1:\n";
$allPeriode1 = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer')
    ->havingRaw('COUNT(*) > 1 AND MAX(periode) = 1')
    ->pluck('nomor_kontainer');

echo "   Ditemukan: " . $allPeriode1->count() . " kontainer\n\n";

if ($allPeriode1->count() > 0) {
    echo "Detail:\n";
    foreach ($allPeriode1->take(10) as $nk) {
        $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nk)->get();
        echo "   - {$nk}: " . $records->count() . " records\n";
        foreach ($records->take(3) as $r) {
            echo "      * periode={$r->periode}, tanggal_awal=" . Carbon::parse($r->tanggal_awal)->format('d/m/Y') . "\n";
        }
    }
}

// Overall stats
echo "\n\n===========================================\n";
echo "Rangkuman:\n";
echo "===========================================\n";

$uniqueContainers = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->distinct('nomor_kontainer')->count('nomor_kontainer');
$totalRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')->count();

echo "- Total kontainer unik: {$uniqueContainers}\n";
echo "- Total records: {$totalRecords}\n";
echo "- Rata-rata record per kontainer: " . round($totalRecords / max(1, $uniqueContainers), 2) . "\n";

echo "\n";
