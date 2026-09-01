<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting fix for empty penerima in BiayaKapal...\n";

// Fix for BiayaKapalBuruhBatam
$batamDetails = App\Models\BiayaKapalBuruhBatam::whereNotNull('penerima')->get();
$fixedCount = 0;

foreach ($batamDetails as $detail) {
    $bk = App\Models\BiayaKapal::find($detail->biaya_kapal_id);
    if ($bk && empty($bk->penerima)) {
        $bk->update([
            'penerima' => $detail->penerima,
            'nama_vendor' => $detail->nama_vendor,
            'nomor_rekening' => $detail->nomor_rekening,
            'bank_id' => $detail->bank_id
        ]);
        echo "Fixed BKP ID: " . $bk->id . " (" . $bk->nomor_invoice . ") from Buruh Batam\n";
        $fixedCount++;
    }
}

// Fix for BiayaKapalBuruhBongkar (if any)
$bongkarDetails = App\Models\BiayaKapalBuruhBongkar::whereNotNull('penerima')->get();
foreach ($bongkarDetails as $detail) {
    $bk = App\Models\BiayaKapal::find($detail->biaya_kapal_id);
    if ($bk && empty($bk->penerima)) {
        $bk->update([
            'penerima' => $detail->penerima,
            'nama_vendor' => $detail->nama_vendor,
            'nomor_rekening' => $detail->nomor_rekening,
            'bank_id' => $detail->bank_id
        ]);
        echo "Fixed BKP ID: " . $bk->id . " (" . $bk->nomor_invoice . ") from Buruh Bongkar\n";
        $fixedCount++;
    }
}

echo "Done. Total fixed: " . $fixedCount . "\n";
