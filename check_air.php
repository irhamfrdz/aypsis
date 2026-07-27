<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fix BKP-02-26-000016 (ID 71)
$bk = \App\Models\BiayaKapal::where('nomor_invoice', 'BKP-02-26-000016')->first();
if (!$bk) { echo "Invoice not found!\n"; exit; }

echo "=== BEFORE FIX ===\n";
echo "Nominal: {$bk->nominal}\n\n";

$airs = \App\Models\BiayaKapalAir::where('biaya_kapal_id', $bk->id)->get();
foreach ($airs as $a) {
    echo "  kapal={$a->kapal}, voyage={$a->voyage}, qty={$a->kuantitas}, harga={$a->harga}, jasa_air={$a->jasa_air}, sub={$a->sub_total}, pph={$a->pph}, grand={$a->grand_total}\n";
}

// Fix each record: set jasa_air from 10,000,000 to 100,000 and recalculate
$correctJasaAir = 100000;
$totalGrand = 0;

foreach ($airs as $a) {
    $waterCost = (float)$a->kuantitas * (float)$a->harga;
    $jasaAir = ($a->jasa_air > 0) ? $correctJasaAir : 0; // only if originally had jasa_air
    $subTotal = $waterCost + $jasaAir;
    
    // Abqori PPH logic: "Air Tawar (Tanki)" is NOT taxable, only jasa_air is
    $isAbqori = str_contains(strtoupper($a->vendor ?? ''), 'ABQORI');
    $isTypeTaxable = true;
    if ($isAbqori) {
        $taxableTerms = ['AGENCY', 'JASA AIR'];
        $isTypeTaxable = false;
        foreach ($taxableTerms as $term) {
            if (str_contains(strtoupper($a->type_keterangan ?? ''), $term)) {
                $isTypeTaxable = true;
                break;
            }
        }
    }
    
    $pphBase = $isTypeTaxable ? $subTotal : $jasaAir;
    $pph = $a->pph_active ? round($pphBase * 0.02) : 0;
    $grandTotal = $subTotal - $pph;
    
    $a->update([
        'jasa_air' => $jasaAir,
        'sub_total' => $subTotal,
        'pph' => $pph,
        'grand_total' => $grandTotal,
    ]);
    
    $totalGrand += $grandTotal;
}

// Update nominal
$bk->update(['nominal' => $totalGrand]);

echo "\n=== AFTER FIX ===\n";
echo "Nominal: {$totalGrand}\n\n";

$airs = \App\Models\BiayaKapalAir::where('biaya_kapal_id', $bk->id)->get();
foreach ($airs as $a) {
    echo "  kapal={$a->kapal}, voyage={$a->voyage}, qty={$a->kuantitas}, harga={$a->harga}, jasa_air={$a->jasa_air}, sub={$a->sub_total}, pph={$a->pph}, grand={$a->grand_total}\n";
}
echo "\nTotal Grand: {$totalGrand}\n";

// Also check and fix any OTHER invoices that may have corrupted jasa_air
echo "\n\n=== CHECKING OTHER INVOICES WITH SUSPICIOUS JASA_AIR ===\n";
$table = (new \App\Models\BiayaKapalAir)->getTable();
$suspicious = \DB::select("
    SELECT biaya_kapal_id, COUNT(*) as cnt, MAX(jasa_air) as max_jasa
    FROM {$table}
    WHERE jasa_air > 1000000
    GROUP BY biaya_kapal_id
");

foreach ($suspicious as $s) {
    $inv = \App\Models\BiayaKapal::find($s->biaya_kapal_id);
    if (!$inv) continue;
    echo "  Invoice: {$inv->nomor_invoice} (ID: {$s->biaya_kapal_id}), count: {$s->cnt}, max jasa_air: {$s->max_jasa}\n";
}
