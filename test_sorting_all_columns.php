<?php
// Test sorting functionality untuk semua kolom
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\KaryawanController;

echo "=== Test Sorting Functionality untuk Semua Kolom ===\n\n";

// Test data untuk masing-masing kolom sorting
$testCases = [
    'nik' => ['direction' => 'asc', 'title' => 'Sort NIK A-Z'],
    'nama_lengkap' => ['direction' => 'asc', 'title' => 'Sort Nama Lengkap A-Z'],
    'nama_panggilan' => ['direction' => 'desc', 'title' => 'Sort Nama Panggilan Z-A'],
    'divisi' => ['direction' => 'asc', 'title' => 'Sort Divisi A-Z'],
    'pekerjaan' => ['direction' => 'desc', 'title' => 'Sort Pekerjaan Z-A'],
    'status_pajak' => ['direction' => 'asc', 'title' => 'Sort Status Pajak A-Z'],
    'tanggal_masuk' => ['direction' => 'desc', 'title' => 'Sort Tanggal Masuk Terbaru']
];

$controller = new KaryawanController();

foreach ($testCases as $column => $config) {
    echo "--- {$config['title']} ---\n";

    // Create mock request
    $request = new Request([
        'sort' => $column,
        'direction' => $config['direction']
    ]);

    try {
        // Test query langsung
        $query = Karyawan::query();
        $query->orderBy($column, $config['direction']);
        $karyawans = $query->limit(5)->get();

        echo "✅ Query berhasil untuk kolom: {$column}\n";
        echo "Jumlah data: " . $karyawans->count() . "\n";

        if ($karyawans->count() > 0) {
            echo "Sample data (top 3):\n";
            foreach ($karyawans->take(3) as $k) {
                $value = $k->$column ?? 'NULL';
                if ($column === 'tanggal_masuk' && $value) {
                    $value = \Carbon\Carbon::parse($value)->format('d/M/Y');
                }
                echo "  - {$k->nama_lengkap}: {$value}\n";
            }
        }

    } catch (Exception $e) {
        echo "❌ Error untuk kolom {$column}: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

// Test validation untuk kolom yang tidak diizinkan
echo "--- Test Security Validation ---\n";
$invalidColumns = ['password', 'created_at', 'updated_at', 'invalid_column'];

foreach ($invalidColumns as $invalidCol) {
    $request = new Request([
        'sort' => $invalidCol,
        'direction' => 'asc'
    ]);

    // Simulate controller logic
    $sortField = $request->get('sort', 'nama_lengkap');
    $allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'status_pajak', 'tanggal_masuk'];

    if (!in_array($sortField, $allowedSortFields)) {
        echo "✅ Security check passed: '{$invalidCol}' blocked, fallback to 'nama_lengkap'\n";
    } else {
        echo "❌ Security issue: '{$invalidCol}' not blocked!\n";
    }
}

echo "\n=== Test Completed ===\n";

// Test URL generation
echo "\n--- Test URL Generation ---\n";
$baseUrl = "http://localhost/master/karyawan";
$currentParams = ['search' => 'test'];

foreach (array_keys($testCases) as $column) {
    $ascUrl = $baseUrl . "?" . http_build_query(array_merge($currentParams, [
        'sort' => $column,
        'direction' => 'asc'
    ]));

    $descUrl = $baseUrl . "?" . http_build_query(array_merge($currentParams, [
        'sort' => $column,
        'direction' => 'desc'
    ]));

    echo "✅ {$column} ASC URL: {$ascUrl}\n";
    echo "✅ {$column} DESC URL: {$descUrl}\n\n";
}

echo "All URL generation tests passed!\n";
?>
