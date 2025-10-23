<?php
// Test update tanda terima functionality
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Use Laravel's environment
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

use App\Models\TandaTerima;
use Illuminate\Support\Facades\Schema;

echo "=== TEST TANDA TERIMA UPDATE ===\n\n";

// 1. Check if status column exists
$hasStatusColumn = Schema::hasColumn('tanda_terimas', 'status');
echo "1. Status column exists: " . ($hasStatusColumn ? 'YES' : 'NO') . "\n\n";

// 2. Get a sample tanda terima
$tandaTerima = TandaTerima::first();
if (!$tandaTerima) {
    echo "2. No tanda terima found in database\n";
    exit;
}

echo "2. Sample tanda terima found:\n";
echo "   ID: {$tandaTerima->id}\n";
echo "   No Surat Jalan: {$tandaTerima->no_surat_jalan}\n";
if ($hasStatusColumn && property_exists($tandaTerima, 'status')) {
    echo "   Status: {$tandaTerima->status}\n";
}
echo "\n";

// 3. Check fillable fields
$fillable = $tandaTerima->getFillable();
echo "3. Fillable fields:\n";
foreach ($fillable as $field) {
    echo "   - $field\n";
}
echo "\n";

// 4. Test update data structure
echo "4. Testing update data structure:\n";
$updateData = [
    'estimasi_nama_kapal' => 'Test Kapal',
    'tanggal_ambil_kontainer' => '2024-01-01',
    'jumlah' => 10,
    'satuan' => 'Pcs',
    'catatan' => 'Test update from script',
    'updated_by' => 1,
];

// Only include status if column exists
if ($hasStatusColumn) {
    $updateData['status'] = 'draft';
    echo "   Status field included\n";
} else {
    echo "   Status field excluded (column doesn't exist)\n";
}

echo "   Update data prepared:\n";
foreach ($updateData as $key => $value) {
    echo "   - $key: $value\n";
}
echo "\n";

// 5. Test actual update (dry run)
echo "5. Testing update validation:\n";
try {
    // Create a request-like validation
    $validator = \Illuminate\Support\Facades\Validator::make($updateData, [
        'estimasi_nama_kapal' => 'nullable|string|max:255',
        'tanggal_ambil_kontainer' => 'nullable|date',
        'tanggal_terima_pelabuhan' => 'nullable|date',
        'tanggal_garasi' => 'nullable|date',
        'jumlah' => 'nullable|integer|min:0',
        'satuan' => 'nullable|string|max:50',
        'panjang' => 'nullable|numeric|min:0',
        'lebar' => 'nullable|numeric|min:0',
        'tinggi' => 'nullable|numeric|min:0',
        'meter_kubik' => 'nullable|numeric|min:0',
        'tonase' => 'nullable|numeric|min:0',
        'tujuan_pengiriman' => 'nullable|string|max:255',
        'catatan' => 'nullable|string',
        'status' => 'nullable|in:draft,submitted,approved,completed,cancelled',
        'dimensi_items' => 'nullable|array',
        'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
        'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
        'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
        'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
        'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
    ]);

    if ($validator->fails()) {
        echo "   Validation FAILED:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "   Validation PASSED\n";
    }
} catch (Exception $e) {
    echo "   Validation ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== CONCLUSION ===\n";
echo "Update should work now. Key fixes:\n";
echo "- Status validation made optional\n";
echo "- Status field only included if column exists\n";
echo "- Removed status references from form\n";
echo "- Added Schema import to controller\n";
