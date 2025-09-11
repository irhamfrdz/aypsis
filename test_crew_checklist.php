<?php

require_once 'vendor/autoload.php';

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "Testing Crew Checklist Update\n";
echo "=============================\n\n";

// Find ABK karyawan
$karyawan = Karyawan::where('divisi', 'like', '%ABK%')->first();

if (!$karyawan) {
    echo "No ABK karyawan found!\n";
    exit(1);
}

echo "Found karyawan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";
echo "Divisi: {$karyawan->divisi}\n\n";

// Check current Kartu Keluarga status
$kkChecklist = $karyawan->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();

if ($kkChecklist) {
    echo "Current Kartu Keluarga:\n";
    echo "- Status: {$kkChecklist->status}\n";
    echo "- Nomor: " . ($kkChecklist->nomor_sertifikat ?? 'null') . "\n";
    echo "- ID: {$kkChecklist->id}\n\n";
} else {
    echo "Kartu Keluarga checklist not found, will create new one\n\n";
}

// Prepare test data
$testData = [
    'checklist' => [
        ($kkChecklist ? $kkChecklist->id : 'new_4') => [
            'item_name' => 'Kartu Keluarga',
            'nomor_sertifikat' => 'KK123456', // Valid nomor with 8 alphanumeric chars
            'issued_date' => '2023-01-01',
            'expired_date' => '2028-01-01',
            'catatan' => 'Test entry'
        ]
    ]
];

echo "Test data to send:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Create mock request
$request = new Request();
$request->merge($testData);

// Test the controller method
try {
    $controller = app(\App\Http\Controllers\KaryawanController::class);
    $response = $controller->updateCrewChecklist($request, $karyawan->id);

    echo "Controller executed successfully\n";

    // Check updated data
    $updatedKK = $karyawan->fresh()->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();

    if ($updatedKK) {
        echo "\nUpdated Kartu Keluarga:\n";
        echo "- Status: {$updatedKK->status}\n";
        echo "- Nomor: " . ($updatedKK->nomor_sertifikat ?? 'null') . "\n";
        echo "- Issued Date: " . ($updatedKK->issued_date ? $updatedKK->issued_date->format('Y-m-d') : 'null') . "\n";
        echo "- Expired Date: " . ($updatedKK->expired_date ? $updatedKK->expired_date->format('Y-m-d') : 'null') . "\n";

        if ($updatedKK->status === 'ada' && $updatedKK->nomor_sertifikat === 'KK123456') {
            echo "\n✅ TEST PASSED: Data saved correctly!\n";
        } else {
            echo "\n❌ TEST FAILED: Data not saved as expected\n";
        }
    } else {
        echo "\n❌ TEST FAILED: Kartu Keluarga checklist not found after update\n";
    }

} catch (\Exception $e) {
    echo "❌ TEST FAILED: Exception occurred: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
}
