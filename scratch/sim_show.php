<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

class SimController extends \App\Http\Controllers\RekapBiayaKapalController
{
    public function sim($kapal, $voyage)
    {
        $selectedShipLower = strtolower(trim($kapal));
        $selectedVoyageLower = strtolower(trim($voyage));

        $relations = [
            'barangDetails',
            'airDetails',
            'tkbmDetails',
            'truckingDetails',
            'stuffingDetails',
            'perlengkapanDetails',
            'labuhTambatDetails',
            'oppOptDetails',
            'thcDetails',
            'loloDetails',
            'storageDetails',
            'freightDetails',
            'perijinanDetails',
            'meratusDetails',
            'temasDetails',
            'tantoDetails',
            'demurrageDetails',
            'tenagaKerjaDetails',
            'operasionalDetails',
        ];

        $reflection = new \ReflectionClass(\App\Http\Controllers\RekapBiayaKapalController::class);

        $recordHasShipAndVoyage = $reflection->getMethod('recordHasShipAndVoyage');
        $recordHasShipAndVoyage->setAccessible(true);

        $getApportionedCostForRecord = $reflection->getMethod('getApportionedCostForRecord');
        $getApportionedCostForRecord->setAccessible(true);

        $allRelations = array_merge(['klasifikasiBiaya', 'vendor'], $relations);
        $biayaKapals = BiayaKapal::with($allRelations)
            ->get()
            ->filter(function ($record) use ($kapal, $voyage, $recordHasShipAndVoyage) {
                return $recordHasShipAndVoyage->invoke($this, $record, $kapal, $voyage);
            });

        echo 'Filtered count: '.$biayaKapals->count()."\n";

        foreach ($biayaKapals as $record) {
            if ($record->nomor_invoice == 'BKP-05-26-000059') {
                echo "Found invoice BKP-05-26-000059!\n";
                $apportioned = $getApportionedCostForRecord->invoke($this, $record, $kapal, $voyage);
                echo "Apportioned:\n";
                print_r($apportioned);
            }
        }
    }
}

$sim = new SimController;
$sim->sim('KM PASIFIC STAR', 'PS01JP26');
