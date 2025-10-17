<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST IMPORT MASTER KAPAL                                                     ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// 1. Cek route
echo "1️⃣  CEK ROUTE:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$routes = [
    'master-kapal.import-form' => 'GET master-kapal/import',
    'master-kapal.import' => 'POST master-kapal/import',
    'master-kapal.download-template' => 'GET master-kapal/download-template',
];

foreach ($routes as $name => $uri) {
    try {
        $url = route($name);
        echo "✅ Route '{$name}' exists: {$url}\n";
    } catch (\Exception $e) {
        echo "❌ Route '{$name}' NOT FOUND\n";
    }
}
echo "\n";

// 2. Cek permission
echo "2️⃣  CEK PERMISSION:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$permission = DB::table('permissions')
    ->where('name', 'master-kapal.create')
    ->first();

if ($permission) {
    echo "✅ Permission 'master-kapal.create' exists (ID: {$permission->id})\n";

    // Cek admin punya permission
    $adminHasPermission = DB::table('user_permissions')
        ->where('user_id', 1)
        ->where('permission_id', $permission->id)
        ->exists();

    if ($adminHasPermission) {
        echo "✅ Admin user has this permission\n";
    } else {
        echo "❌ Admin user does NOT have this permission\n";
    }
} else {
    echo "❌ Permission 'master-kapal.create' NOT FOUND\n";
}
echo "\n";

// 3. Cek template file
echo "3️⃣  CEK TEMPLATE FILE:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$templatePath = public_path('templates/master_kapal_template.csv');
if (file_exists($templatePath)) {
    echo "✅ Template file exists: {$templatePath}\n";
    echo "   File size: " . filesize($templatePath) . " bytes\n";

    // Baca header
    $content = file_get_contents($templatePath);
    $lines = explode("\n", $content);
    echo "   Header: " . trim($lines[0]) . "\n";
} else {
    echo "❌ Template file NOT FOUND: {$templatePath}\n";
}
echo "\n";

// 4. Simulasi test import
echo "4️⃣  TEST IMPORT SIMULATION:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$testData = "kode;kode_kapal;nama_kapal;nickname;pelayaran;catatan;status
TEST001;TK001;TEST SHIP;TESTER;PT Test;Test data;aktif";

$tempFile = tempnam(sys_get_temp_dir(), 'test_import_');
file_put_contents($tempFile, $testData);

echo "Created temp CSV file: {$tempFile}\n";
echo "Content:\n";
echo $testData . "\n\n";

// Parse CSV
$csvData = array_map(function($line) {
    return str_getcsv($line, ';');
}, file($tempFile));

$header = array_shift($csvData);
echo "Parsed header: " . implode(', ', $header) . "\n";

$expectedHeader = ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'catatan', 'status'];
echo "Expected header: " . implode(', ', $expectedHeader) . "\n";

if ($header === $expectedHeader) {
    echo "✅ Header matches!\n";
} else {
    echo "❌ Header does NOT match!\n";
    echo "Differences:\n";
    foreach ($expectedHeader as $i => $expected) {
        $actual = $header[$i] ?? 'MISSING';
        if ($expected !== $actual) {
            echo "  - Index {$i}: Expected '{$expected}', Got '{$actual}'\n";
        }
    }
}

echo "\nData rows:\n";
foreach ($csvData as $i => $row) {
    echo "Row " . ($i + 2) . ": " . implode(' | ', $row) . "\n";
}

unlink($tempFile);

echo "\n";
echo "5️⃣  CEK CONTROLLER METHOD:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

if (method_exists(\App\Http\Controllers\MasterKapalController::class, 'import')) {
    echo "✅ Method 'import' exists in MasterKapalController\n";
} else {
    echo "❌ Method 'import' NOT FOUND in MasterKapalController\n";
}

if (method_exists(\App\Http\Controllers\MasterKapalController::class, 'importForm')) {
    echo "✅ Method 'importForm' exists in MasterKapalController\n";
} else {
    echo "❌ Method 'importForm' NOT FOUND in MasterKapalController\n";
}

echo "\n────────────────────────────────────────────────────────────────────────────────\n";
echo "Test selesai! Cek error spesifik saat import di browser.\n";
