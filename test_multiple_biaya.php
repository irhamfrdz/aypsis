<?php

/**
 * Test Script untuk Multiple Biaya System
 * Script ini untuk testing manual sistem multiple biaya yang telah dibuat
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GateIn;
use App\Models\GateInAktivitas;
use App\Models\GateInPetikemas;
use App\Models\PricelistGateIn;
use App\Models\MasterKapal;
use Illuminate\Support\Facades\DB;

echo "=== TEST MULTIPLE BIAYA SYSTEM ===\n\n";

try {
    // 1. Test Database Tables
    echo "1. Testing Database Tables...\n";

    // Check if tables exist
    $tables = ['gate_ins', 'gate_in_aktivitas_details', 'gate_in_petikemas_details', 'pricelist_gate_ins'];
    foreach ($tables as $table) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "   - Table '$table': " . ($exists ? "✓ EXISTS" : "✗ MISSING") . "\n";
    }

    // 2. Test Pricelist Data
    echo "\n2. Testing Pricelist Data...\n";
    $pricelistData = PricelistGateIn::whereIn('biaya', ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STEVEDORING', 'STUFFING'])
                                   ->get();

    echo "   Found " . $pricelistData->count() . " pricelist entries:\n";
    foreach ($pricelistData as $pricelist) {
        echo "   - {$pricelist->biaya}: {$pricelist->tarif}\n";
    }

    // 3. Create Test Data
    echo "\n3. Creating Test Data...\n";

    // Create test Kapal if not exists
    $kapal = MasterKapal::firstOrCreate([
        'nama_kapal' => 'TEST VESSEL 001'
    ], [
        'kode' => 'TS001',
        'call_sign' => 'TS001',
        'bendera' => 'Indonesia',
        'grt' => 5000,
        'dwt' => 7000,
        'loa' => 150,
        'beam' => 20,
        'draft' => 8
    ]);
    echo "   - Test Kapal created/found: {$kapal->nama_kapal}\n";

    // Create test Gate In with RECEIVING kegiatan
    $gateIn = GateIn::create([
        'nomor_gate_in' => 'TEST-' . date('YmdHis'),
        'kapal_id' => $kapal->id,
        'kegiatan' => 'RECEIVING',
        'pelabuhan' => 'JAKARTA',
        'tanggal_gate_in' => now(),
        'status' => 'aktif',
        'user_id' => 1 // Assuming admin user exists
    ]);
    echo "   - Test Gate In created: {$gateIn->nomor_gate_in}\n";

    // Set container count for testing
    $kontainerCount = 3;

    // 4. Test Multiple Biaya Logic
    echo "\n4. Testing Multiple Biaya Logic...\n";

    // Get aktivitas for RECEIVING
    $aktivitasMapping = [
        'RECEIVING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO'],
        'DISCHARGE' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STEVEDORING'],
        'LOADING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STUFFING']
    ];

    $aktivitasList = $aktivitasMapping['RECEIVING'];
    $totalBiaya = 0;

    foreach ($aktivitasList as $aktivitas) {
        $pricelist = PricelistGateIn::where('biaya', $aktivitas)->first();

        if ($pricelist) {
        // ADMINISTRASI charged once (box=1), others per container
        $box = ($aktivitas === 'ADMINISTRASI') ? 1 : $kontainerCount;
        $itm = 1;
        $tarif = $pricelist->tarif;
        $total = $box * $itm * $tarif;
        $totalBiaya += $total;            // Create aktivitas detail
            GateInAktivitas::create([
                'gate_in_id' => $gateIn->id,
                'aktivitas' => $aktivitas,
                's_t_s' => '20/20/20',
                'box' => $box,
                'itm' => $itm,
                'tarif' => $tarif,
                'total' => $total
            ]);

            echo "   - Aktivitas {$aktivitas}: Box={$box}, Tarif={$tarif}, Total={$total}\n";
        } else {
            echo "   - ✗ Pricelist not found for {$aktivitas}\n";
        }
    }

    // Create petikemas details
    for ($i = 1; $i <= $kontainerCount; $i++) {
        $estimasiBiaya = $totalBiaya / $kontainerCount;

        GateInPetikemas::create([
            'gate_in_id' => $gateIn->id,
            'no_petikemas' => 'TEST' . str_pad($i, 6, '0', STR_PAD_LEFT),
            's_t_s' => '20/20/20',
            'estimasi' => now()->addDays(7),
            'estimasi_biaya' => $estimasiBiaya
        ]);

        echo "   - Petikemas TEST" . str_pad($i, 6, '0', STR_PAD_LEFT) . ": {$estimasiBiaya}\n";
    }

    // Update total biaya
    $gateIn->update(['total_biaya' => $totalBiaya]);
    echo "   - Total Biaya Updated: {$totalBiaya}\n";

    // Test Relationships
    echo "\n5. Testing Model Relationships...\n";

    $gateInWithRelations = GateIn::with(['aktivitas', 'petikemas', 'kapal'])
                                 ->find($gateIn->id);

    echo "   - Gate In ID: {$gateInWithRelations->id}\n";
    echo "   - Aktivitas count: {$gateInWithRelations->aktivitas->count()}\n";
    echo "   - Petikemas count: {$gateInWithRelations->petikemas->count()}\n";
    echo "   - Kapal: " . ($gateInWithRelations->kapal ? $gateInWithRelations->kapal->nama_kapal : 'Not found') . "\n";
    echo "   - Total from aktivitas: {$gateInWithRelations->aktivitas->sum('total')}\n";

    // 6. Test View Data Structure
    echo "\n6. Testing View Data Structure...\n";

    $viewData = [
        'gateIn' => $gateInWithRelations,
        'aktivitas' => $gateInWithRelations->aktivitas,
        'petikemas' => $gateInWithRelations->petikemas,
        'grandTotal' => $gateInWithRelations->aktivitas->sum('total')
    ];

    echo "   - View data structure ready for Blade template\n";
    echo "   - Aktivitas details:\n";
    foreach ($viewData['aktivitas'] as $aktivitas) {
        echo "     * {$aktivitas->aktivitas}: {$aktivitas->s_t_s} | {$aktivitas->box}x{$aktivitas->itm} | {$aktivitas->tarif} = {$aktivitas->total}\n";
    }

    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    echo "Test Gate In ID: {$gateIn->id}\n";
    echo "Test Gate In Number: {$gateIn->nomor_gate_in}\n";
    echo "You can now test the web interface using this Gate In data.\n";

} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
