<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\KaryawanController;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing KaryawanController Import Method\n";
    echo "=======================================\n\n";

    // Create a test uploaded file
    $csvPath = __DIR__ . '/test_import_corrected.csv';

    if (!file_exists($csvPath)) {
        echo "Error: CSV file not found!\n";
        exit(1);
    }

    // Simulate uploaded file
    $uploadedFile = new UploadedFile(
        $csvPath,
        'test_import.csv',
        'text/csv',
        null,
        true // test mode
    );

    // Create a mock request
    $request = new Request();
    $request->files->set('csv_file', $uploadedFile);

    // Create controller instance
    $controller = new KaryawanController();

    echo "1. Checking current number of employees in database...\n";
    $beforeCount = DB::table('karyawans')->count();
    echo "   Current employees: $beforeCount\n\n";

    echo "2. Testing import method...\n";

    // Call the import method
    $response = $controller->importStore($request);

    echo "3. Checking result...\n";
    $afterCount = DB::table('karyawans')->count();
    echo "   Employees after import: $afterCount\n";
    echo "   New employees added: " . ($afterCount - $beforeCount) . "\n\n";

    if ($afterCount > $beforeCount) {
        echo "✅ Import Test: PASSED\n";
        echo "   Successfully imported " . ($afterCount - $beforeCount) . " employees\n\n";

        echo "4. Checking imported data...\n";
        $importedEmployees = DB::table('karyawans')
            ->whereIn('nik', ['1111111111', '2222222222', '3333333333'])
            ->get();

        foreach ($importedEmployees as $emp) {
            echo "   - NIK: {$emp->nik}\n";
            echo "     Nama: {$emp->nama_lengkap}\n";
            echo "     Email: {$emp->email}\n";
            echo "     Divisi: {$emp->divisi}\n";
            echo "     Pekerjaan: {$emp->pekerjaan}\n\n";
        }
    } else {
        echo "❌ Import Test: FAILED\n";
        echo "   No new employees were imported\n";
    }

} catch (Exception $e) {
    echo "Error during import test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
