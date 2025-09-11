<?php

// Test script to verify updateCrewChecklist controller functionality
echo "Testing updateCrewChecklist controller..." . PHP_EOL;

// Simulate request data for Kartu Keluarga (itemId 4) - format sesuai dengan yang diharapkan controller
$testData = [
    'checklist' => [
        4 => [ // itemId 4 untuk Kartu Keluarga
            'item_name' => 'Kartu Keluarga',
            'nomor_sertifikat' => 'KK1234567890', // Valid nomor with 4+ alphanumeric chars
            'issued_date' => '2020-01-01',
            'expired_date' => '2030-01-01',
            'catatan' => 'Test entry'
        ]
    ]
];

echo "Test data:" . PHP_EOL;
echo "  karyawan_id: 6" . PHP_EOL;
echo "  item_name: " . $testData['checklist'][4]['item_name'] . PHP_EOL;
echo "  nomor_sertifikat: " . $testData['checklist'][4]['nomor_sertifikat'] . PHP_EOL;
echo "  Expected status: ada (should match /^[A-Za-z0-9]{4,}$/)" . PHP_EOL;

// Test the regex pattern that controller uses
$fourAlnumPattern = '/^[A-Za-z0-9]{4,}$/';
$nomor = $testData['checklist'][4]['nomor_sertifikat'];
$expectedStatus = ($nomor && preg_match($fourAlnumPattern, $nomor)) ? 'ada' : 'tidak';

echo "Regex test result: " . ($expectedStatus == 'ada' ? 'PASS' : 'FAIL') . PHP_EOL;
echo "Expected status: " . $expectedStatus . PHP_EOL;

// Now try to call the controller method
try {
    $controller = new App\Http\Controllers\KaryawanController();

    // Create a mock request
    $request = new Illuminate\Http\Request();
    $request->merge($testData);

    echo PHP_EOL . "Calling updateCrewChecklist..." . PHP_EOL;
    $response = $controller->updateCrewChecklist($request, 6); // karyawan ID 6

    echo "Controller response: " . $response . PHP_EOL;

    // Check if data was saved
    $karyawan = App\Models\Karyawan::find(6);
    if ($karyawan) {
        $kk = $karyawan->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();
        if ($kk) {
            echo PHP_EOL . "Database verification:" . PHP_EOL;
            echo "  Status: " . $kk->status . PHP_EOL;
            echo "  Nomor: " . ($kk->nomor_sertifikat ?? 'null') . PHP_EOL;
            echo "  Test result: " . ($kk->status == $expectedStatus ? 'PASS' : 'FAIL') . PHP_EOL;
        } else {
            echo "Kartu Keluarga record not found after update" . PHP_EOL;
        }
    }

} catch (Exception $e) {
    echo "Error calling controller: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Test completed." . PHP_EOL;
