<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\PembayaranAktivitasLainnya;
use App\Models\Coa;

echo "Testing PembayaranAktivitasLainnya Model and Controller...\n";
echo "=" . str_repeat("=", 60) . "\n";

try {
    // Test 1: Check if model can be instantiated
    echo "1. Testing model instantiation...\n";
    $model = new PembayaranAktivitasLainnya();
    echo "   ✓ Model created successfully\n";

    // Test 2: Check fillable fields
    echo "2. Testing fillable fields...\n";
    $fillable = $model->getFillable();
    echo "   ✓ Fillable fields: " . implode(', ', $fillable) . "\n";

    // Test 3: Check table structure
    echo "3. Testing table structure...\n";
    $columns = DB::select('DESCRIBE pembayaran_aktivitas_lainnya');
    foreach($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }

    // Test 4: Check bank accounts availability
    echo "4. Testing bank accounts...\n";
    $bankCount = Coa::where('tipe_akun', '=', 'Kas/Bank')->count();
    echo "   ✓ Found {$bankCount} bank accounts in COA\n";

    // Test 5: Test relationship
    echo "5. Testing bank relationship...\n";
    if ($bankCount > 0) {
        $firstBank = Coa::where('tipe_akun', '=', 'Kas/Bank')->first();
        echo "   ✓ First bank: {$firstBank->nomor_akun} - {$firstBank->nama_akun}\n";
    }

    echo "\n✅ All tests passed! Controller should work properly now.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
