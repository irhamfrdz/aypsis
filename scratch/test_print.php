<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bank;
use App\Models\BiayaKapal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

try {
    DB::beginTransaction();

    echo "Running print page integration test...\n";

    // 1. Fetch or create a Bank
    $bank = Bank::first();
    if (! $bank) {
        $bank = Bank::create([
            'name' => 'BCA Test',
            'code' => 'BCA',
            'keterangan' => 'BCA Test Bank',
        ]);
    }
    echo "Using Bank: {$bank->name} (ID: {$bank->id})\n";

    // 2. Fetch or create a BiayaKapal record for testing (KB024 for Biaya Buruh)
    $biayaKapal = BiayaKapal::where('jenis_biaya', 'KB024')->first();
    if (! $biayaKapal) {
        $biayaKapal = BiayaKapal::create([
            'tanggal' => date('Y-m-d'),
            'nomor_invoice' => 'BKP-05-26-999999',
            'jenis_biaya' => 'KB024',
            'nominal' => 150000.00,
            'penerima' => 'SUTRISNO TEST',
            'nama_vendor' => 'SUNDA KELAPA TRISNO TEST',
            'nomor_rekening' => '2400303757',
            'bank_id' => $bank->id,
        ]);
    } else {
        // Temporarily set bank_id to our test bank
        $biayaKapal->bank_id = $bank->id;
        $biayaKapal->nomor_rekening = '2400303757';
        $biayaKapal->save();
    }

    echo "Using BiayaKapal ID: {$biayaKapal->id}, Invoice: {$biayaKapal->nomor_invoice}\n";

    // Load relation
    $biayaKapal->load(['klasifikasiBiaya', 'bank']);

    echo 'Eager loaded bank: '.($biayaKapal->bank ? $biayaKapal->bank->name : 'NONE')."\n";

    // 3. Test rendering the print view
    echo "Rendering view biaya-kapal.print...\n";
    $html = View::make('biaya-kapal.print', [
        'biayaKapal' => $biayaKapal,
        'paperSize' => 'Half-Folio',
        'currentPaper' => [
            'size' => '165.1mm 215.9mm',
            'width' => '165.1mm',
            'height' => '215.9mm',
            'containerWidth' => '165.1mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
        ],
    ])->render();

    echo "SUCCESS: view compiled without errors!\n";

    // Check if the Bank name is present in the rendered HTML
    if (strpos($html, $bank->name) !== false) {
        echo "SUCCESS: Bank name '{$bank->name}' is present in the rendered print view!\n";
    } else {
        throw new \Exception('Bank name not found in rendered HTML!');
    }

    // 4. Test print-tkbm.blade.php as well
    echo "Rendering view biaya-kapal.print-tkbm...\n";
    $htmlTkbm = View::make('biaya-kapal.print-tkbm', [
        'biayaKapal' => $biayaKapal,
        'paperSize' => 'Half-Folio',
        'currentPaper' => [
            'size' => '165.1mm 215.9mm',
            'width' => '165.1mm',
            'height' => '215.9mm',
            'containerWidth' => '165.1mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
        ],
    ])->render();

    echo "SUCCESS: print-tkbm view compiled without errors!\n";

    if (strpos($htmlTkbm, $bank->name) !== false) {
        echo "SUCCESS: Bank name '{$bank->name}' is present in the rendered print-tkbm view!\n";
    } else {
        throw new \Exception('Bank name not found in rendered print-tkbm HTML!');
    }

} catch (\Exception $e) {
    echo "ERROR: Test failed!\n";
    echo 'Message: '.$e->getMessage()."\n";
    echo "Trace:\n".$e->getTraceAsString()."\n";
} finally {
    DB::rollBack();
    echo "Database transaction rolled back. DB is clean.\n";
}
