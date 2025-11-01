<?php
// Manual test to call PranotaUangRitController@store
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\PranotaUangRitController;
use Illuminate\Support\Facades\Log;

try {
    echo "Starting manual store test...\n";

    // Prepare fake input data similar to the form
    $input = [
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Manual test',
        'surat_jalan_data' => [
            // use arbitrary id 99999, keys are kept as in form
            99999 => [
                'selected' => 1,
                'no_surat_jalan' => 'TEST-001',
                'supir_nama' => 'TEST SUPIR',
                'kenek_nama' => null,
                'no_plat' => 'B 0000 XYZ',
                'uang_rit_supir' => 85000,
            ],
        ],
        // no supir_details (test zero)
    ];

    $request = Request::create('/pranota-uang-rit', 'POST', $input);

    // Ensure the request has session & user data (Auth::id used in controller)
    // For test, we set created_by via simulating a user id in Auth facade by binding a dummy user
    \Auth::shouldReceive('id')->andReturn(1);

    $controller = new PranotaUangRitController();

    // Call store
    $response = $controller->store($request);
    echo "Store method returned.\n";
    var_dump($response);

} catch (\Exception $e) {
    echo "Exception caught:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "Done.\n";
