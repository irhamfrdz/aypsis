<?php
// export_container.php
// Usage: php export_container.php EMCU6063235

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\Storage;

$container = $argv[1] ?? 'EMCU6063235';

$records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)
    ->orderBy('periode', 'asc')
    ->get()
    ->map(function($r){
        // convert dates to strings and remove internal attributes
        return [
            'nomor_kontainer' => $r->nomor_kontainer,
            'vendor' => $r->vendor,
            'size' => $r->size,
            'tanggal_awal' => optional($r->tanggal_awal)->toDateString(),
            'tanggal_akhir' => optional($r->tanggal_akhir)->toDateString(),
            'group' => $r->group,
            'masa' => $r->masa,
            'tarif' => $r->tarif,
            'tarif_nominal' => $r->tarif_nominal,
            'dpp' => $r->dpp,
            'adjustment' => $r->adjustment,
            'adjustment_note' => $r->adjustment_note,
            'invoice_vendor' => $r->invoice_vendor,
            'tanggal_vendor' => optional($r->tanggal_vendor)->toDateString(),
            'dpp_nilai_lain' => $r->dpp_nilai_lain,
            'ppn' => $r->ppn,
            'pph' => $r->pph,
            'grand_total' => $r->grand_total,
            'status' => $r->status,
            'status_pranota' => $r->status_pranota,
            'pranota_id' => $r->pranota_id,
            'periode' => $r->periode,
            'created_at' => optional($r->created_at)->toDateTimeString(),
            'updated_at' => optional($r->updated_at)->toDateTimeString(),
        ];
    })->toArray();

if (empty($records)) {
    echo "No records found for container: {$container}\n";
    exit(1);
}

$outDir = __DIR__ . '/exported_containers';
if (!is_dir($outDir)) mkdir($outDir, 0755, true);

$file = $outDir . "/container_{$container}.json";
file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Exported " . count($records) . " records to {$file}\n";
echo "Next: transfer this JSON file to your production server and run import_container.php there.\n";

