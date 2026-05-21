<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\BiayaKapalController;
use App\Models\Bank;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalTenagaKerja;
use App\Models\Buruh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "Running BiayaKapal bank_id integration test...\n";

    // 1. Fetch required dependencies from the database
    $buruh = Buruh::where('status', 'aktif')->first();
    if (! $buruh) {
        throw new \Exception('No active Buruh found in DB. Cannot proceed with test.');
    }
    echo "Using Buruh: {$buruh->nama} (ID: {$buruh->id})\n";

    $bank = Bank::first();
    if (! $bank) {
        throw new \Exception('No Bank found in DB. Cannot proceed with test.');
    }
    echo "Using Bank: {$bank->name} (ID: {$bank->id})\n";

    $jenisBiaya = DB::table('klasifikasi_biayas')->first();
    if (! $jenisBiaya) {
        throw new \Exception('No Klasifikasi Biaya found in DB. Cannot proceed with test.');
    }
    echo "Using Klasifikasi Biaya: {$jenisBiaya->kode}\n";

    $pricelistBuruh = DB::table('pricelist_buruh')->first();
    if (! $pricelistBuruh) {
        throw new \Exception('No Pricelist Buruh found in DB. Cannot proceed with test.');
    }
    echo "Using Pricelist Buruh: (ID: {$pricelistBuruh->id})\n";

    // Mock authenticated user
    Auth::shouldReceive('id')->andReturn(1);
    Auth::shouldReceive('user')->andReturn((object) ['id' => 1, 'username' => 'Test User', 'name' => 'Test User']);

    // 2. Prepare request data for store
    $input = [
        'tanggal' => date('Y-m-d'),
        'nomor_referensi' => 'REF-TEST-BANK-001',
        'jenis_biaya' => $jenisBiaya->kode,
        'nominal' => '100.000',
        'penerima' => 'Test Receiver',
        'bank_id' => $bank->id,
        'keterangan' => 'Integration test for bank_id selection',
        'kapal_sections' => [
            [
                'kapal' => 'KM TEST EXPRESS',
                'voyage' => 'V.123',
                'barang' => [
                    [
                        'barang_id' => $pricelistBuruh->id,
                        'jumlah' => '5',
                    ],
                ],
                'tenaga_kerja' => [
                    [
                        'buruh_id' => $buruh->id,
                        'nominal' => '50.000',
                    ],
                ],
                'total_nominal' => '50.000',
                'dp' => '0',
                'sisa_pembayaran' => '50.000',
            ],
        ],
    ];

    // Bootstrap session
    $request = Request::create('/biaya-kapal', 'POST', $input);
    $session = $app['session']->driver('array');
    $request->setLaravelSession($session);

    // Bind session/validation stuff if needed, or call the controller directly
    $controller = new BiayaKapalController;

    echo "Calling BiayaKapalController@store...\n";
    $response = $controller->store($request);

    echo 'Response class: '.get_class($response)."\n";
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo 'Redirect URL: '.$response->getTargetUrl()."\n";
        // Check session errors
        echo "Session contents:\n";
        print_r($session->all());
    } else {
        echo 'Response content excerpt: '.substr($response->getContent(), 0, 500)."\n";
    }

    // 3. Verify that BiayaKapal was saved and check the bank_id
    $biayaKapal = BiayaKapal::where('nomor_referensi', 'REF-TEST-BANK-001')->first();
    if (! $biayaKapal) {
        throw new \Exception('Failed to save BiayaKapal! Controller returned response.');
    }
    echo "Successfully saved BiayaKapal (ID: {$biayaKapal->id})\n";

    $tk = BiayaKapalTenagaKerja::where('biaya_kapal_id', $biayaKapal->id)->first();
    if (! $tk) {
        throw new \Exception('Failed to save BiayaKapalTenagaKerja record!');
    }

    echo "Saved BiayaKapalTenagaKerja details:\n";
    echo "  - Buruh ID: {$tk->buruh_id}\n";
    echo "  - Nominal: {$tk->nominal}\n";

    echo "Saved BiayaKapal details:\n";
    echo '  - Bank ID: '.($biayaKapal->bank_id ?? 'NULL')."\n";

    if ($biayaKapal->bank_id != $bank->id) {
        throw new \Exception("Mismatch! Expected bank_id {$bank->id}, got: {$biayaKapal->bank_id}");
    }
    echo "SUCCESS: bank_id saved correctly on store!\n";

    // 4. Test updating the record
    echo "Testing update. Changing bank_id to null/empty...\n";

    $updateInput = $input;
    $updateInput['nomor_invoice'] = $biayaKapal->nomor_invoice;
    // Set bank_id to empty/null
    $updateInput['bank_id'] = null;

    // In edit/update we also pass nominal back
    $updateInput['nominal'] = '100.000';

    $updateRequest = Request::create("/biaya-kapal/{$biayaKapal->id}", 'PUT', $updateInput);
    $updateRequest->setLaravelSession($session);

    echo "Calling BiayaKapalController@update...\n";
    $updateResponse = $controller->update($updateRequest, $biayaKapal);

    if ($updateResponse instanceof \Illuminate\Http\RedirectResponse) {
        $updateErrors = $session->get('errors');
        if ($updateErrors) {
            echo "Update Session Validation Errors:\n";
            print_r($updateErrors->getBag('default')->getMessages());
        }
    }

    $biayaKapal->refresh();
    echo "Updated BiayaKapal details:\n";
    echo '  - Bank ID: '.($biayaKapal->bank_id ?? 'NULL')."\n";

    if ($biayaKapal->bank_id !== null) {
        throw new \Exception("Mismatch! Expected bank_id to be NULL, got: {$biayaKapal->bank_id}");
    }
    echo "SUCCESS: bank_id updated correctly to NULL on update!\n";

} catch (\Illuminate\Validation\ValidationException $ve) {
    echo "ERROR: Validation failed!\n";
    print_r($ve->errors());
} catch (\Exception $e) {
    echo "ERROR: Test failed!\n";
    echo 'Message: '.$e->getMessage()."\n";
    echo "Trace:\n".$e->getTraceAsString()."\n";
} finally {
    DB::rollBack();
    echo "Database transaction rolled back. DB is clean.\n";
}
