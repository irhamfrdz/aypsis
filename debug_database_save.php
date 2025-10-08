<?php
/**
 * Debug proses import step by step dengan database simulation
 */

echo "=== DEBUG IMPORT DENGAN DATABASE TEST ===\n\n";

// Include Laravel framework untuk akses model
require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

$csvFile = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan: $csvFile\n";
    exit(1);
}

// Helper functions dari controller
function getValue($row, $headers, $columnName) {
    $index = array_search($columnName, $headers);
    return $index !== false && isset($row[$index]) ? $row[$index] : null;
}

function cleanDpeNumber($value) {
    if (empty($value) || trim($value) === '' || trim($value) === '-') {
        return 0;
    }

    // Handle negative values with comma format
    $value = trim($value);
    $isNegative = false;

    if (strpos($value, '-') !== false) {
        $isNegative = true;
        $value = str_replace('-', '', $value);
    }

    // Remove currency symbols, spaces, and formatting
    $cleaned = preg_replace('/[^\d.,]/', '', $value);
    $cleaned = str_replace(',', '', $cleaned); // Remove thousands separator

    $result = (float) $cleaned;
    return $isNegative ? -$result : $result;
}

echo "Testing fillable fields di model:\n";
$model = new DaftarTagihanKontainerSewa();
$fillable = $model->getFillable();
echo "Fillable fields: " . implode(', ', $fillable) . "\n\n";

if (in_array('adjustment', $fillable)) {
    echo "✓ Field 'adjustment' ada di fillable\n\n";
} else {
    echo "✗ Field 'adjustment' TIDAK ada di fillable\n\n";
}

// Baca CSV file
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 1000, ',');

echo "Headers CSV: " . implode(' | ', $headers) . "\n\n";

// Test satu record yang ada adjustment-nya
$recordFound = false;
$rowCount = 0;

while (($row = fgetcsv($handle, 1000, ',')) !== false && !$recordFound) {
    $rowCount++;
    
    $adjustmentValue = getValue($row, $headers, 'Adjustment');
    
    if (!empty($adjustmentValue) && $adjustmentValue != '0' && $adjustmentValue != '0.00') {
        $recordFound = true;
        
        echo "=== TESTING RECORD $rowCount DENGAN ADJUSTMENT ===\n";
        echo "Raw adjustment value: '$adjustmentValue'\n";
        
        $cleanedAdjustment = cleanDpeNumber($adjustmentValue);
        echo "Cleaned adjustment value: $cleanedAdjustment\n";
        
        // Prepare test data
        $testData = [
            'vendor' => getValue($row, $headers, 'Vendor'),
            'nomor_kontainer' => getValue($row, $headers, 'Nomor Kontainer'),
            'size' => getValue($row, $headers, 'Size'),
            'tanggal_awal' => date('Y-m-d', strtotime(getValue($row, $headers, 'Tanggal Awal'))),
            'tanggal_akhir' => date('Y-m-d', strtotime(getValue($row, $headers, 'Tanggal Akhir'))),
            'tarif' => getValue($row, $headers, 'Tarif'),
            'adjustment' => $cleanedAdjustment,
            'periode' => getValue($row, $headers, 'Periode') ?: 1,
            'group' => getValue($row, $headers, 'Group'),
            'status' => getValue($row, $headers, 'Status') ?: 'ongoing',
            'masa' => '1 Jan 2024 - 31 Jan 2024', // dummy
            'dpp' => 0,
            'ppn' => 0,
            'pph' => 0,
            'grand_total' => 0,
        ];
        
        echo "\nData yang akan disave:\n";
        foreach ($testData as $key => $value) {
            echo "  $key: " . ($value ?? 'NULL') . "\n";
        }
        
        echo "\n=== TESTING DATABASE SAVE ===\n";
        try {
            // Try to save to database
            $model = new DaftarTagihanKontainerSewa();
            $model->fill($testData);
            
            echo "Model filled successfully\n";
            echo "Model adjustment attribute: " . $model->adjustment . "\n";
            echo "Model attributes: " . json_encode($model->getAttributes()) . "\n\n";
            
            // Try to save (comment this out if you don't want to actually save)
            // $model->save();
            // echo "✓ Model saved successfully with ID: " . $model->id . "\n";
            
        } catch (Exception $e) {
            echo "✗ Error saving model: " . $e->getMessage() . "\n";
            echo "Error trace: " . $e->getTraceAsString() . "\n";
        }
    }
}

fclose($handle);

if (!$recordFound) {
    echo "Tidak ada record dengan adjustment ditemukan dalam 100 record pertama\n";
}

echo "\n=== CHECKING DATABASE CONSTRAINTS ===\n";
try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    $stmt = $pdo->prepare("DESCRIBE daftar_tagihan_kontainer_sewa");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'adjustment') {
            echo "Adjustment column info:\n";
            echo "  Type: " . $column['Type'] . "\n";
            echo "  Null: " . $column['Null'] . "\n";
            echo "  Default: " . $column['Default'] . "\n";
            echo "  Extra: " . $column['Extra'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error checking database: " . $e->getMessage() . "\n";
}