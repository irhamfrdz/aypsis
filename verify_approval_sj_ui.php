<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFIKASI PERMISSION APPROVAL SURAT JALAN ===\n\n";

// Cek apakah permission sudah ada di database
$permissions = [
    'approval-surat-jalan-view',
    'approval-surat-jalan-approve', 
    'approval-surat-jalan-reject',
    'approval-surat-jalan-print',
    'approval-surat-jalan-export'
];

echo "1. Checking database permissions:\n";
foreach ($permissions as $permName) {
    $permission = App\Models\Permission::where('name', $permName)->first();
    if ($permission) {
        echo "   ✅ $permName (ID: {$permission->id})\n";
    } else {
        echo "   ❌ $permName NOT FOUND\n";
    }
}

echo "\n2. Testing user interface integration:\n";
$userController = new App\Http\Controllers\UserController();

// Test matrix conversion
$testPermissions = ['approval-surat-jalan-view', 'approval-surat-jalan-approve'];
$matrixResult = $userController->testConvertPermissionsToMatrix($testPermissions);

if (isset($matrixResult['approval-surat-jalan'])) {
    echo "   ✅ UI dapat mengenali approval-surat-jalan permissions\n";
    echo "   📋 Matrix result: " . json_encode($matrixResult['approval-surat-jalan']) . "\n";
} else {
    echo "   ❌ UI tidak dapat mengenali approval-surat-jalan permissions\n";
}

echo "\n3. Next steps:\n";
echo "   1. Buka /master/user/[ID]/edit\n";
echo "   2. Cari section 'Operational Management'\n";
echo "   3. Klik pada baris tersebut untuk expand\n";
echo "   4. Cari sub-modul 'Approval Surat Jalan'\n";
echo "   5. Centang checkbox sesuai kebutuhan\n";

echo "\n=== VERIFIKASI SELESAI ===\n";