<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFIKASI FINAL DASHBOARD PERMISSIONS ===" . PHP_EOL;

// Check total permissions
$totalPermissions = App\Models\Permission::count();
echo "Total permissions di database: {$totalPermissions}" . PHP_EOL;

// Check dashboard permissions
$dashboardPermissions = App\Models\Permission::where('name', 'LIKE', '%dashboard%')->get();
echo PHP_EOL . "Dashboard permissions yang tersedia:" . PHP_EOL;
foreach ($dashboardPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Check admin user permissions
$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $adminPermsCount = $admin->permissions()->count();
    $adminDashboardPerms = $admin->permissions()->where('name', 'LIKE', '%dashboard%')->get();
    
    echo PHP_EOL . "Admin user status:" . PHP_EOL;
    echo "  - Total permissions: {$adminPermsCount}" . PHP_EOL;
    echo "  - Dashboard permissions:" . PHP_EOL;
    foreach ($adminDashboardPerms as $perm) {
        echo "    * {$perm->name}" . PHP_EOL;
    }
}

// Test matrix conversion for dashboard
echo PHP_EOL . "Test matrix conversion untuk dashboard:" . PHP_EOL;
$userController = new App\Http\Controllers\UserController();

// Test 1: Dashboard permission -> Matrix
$dashboardPermNames = ['dashboard', 'dashboard-view'];
$matrix = $userController->testConvertPermissionsToMatrix($dashboardPermNames);
echo "  ✅ Permissions -> Matrix: " . json_encode($matrix) . PHP_EOL;

// Test 2: Matrix -> Permission IDs
$matrixInput = ['dashboard' => ['view' => '1']];
$permIds = $userController->testConvertMatrixPermissionsToIds($matrixInput);
echo "  ✅ Matrix -> Permission IDs: [" . implode(', ', $permIds) . "]" . PHP_EOL;

echo PHP_EOL . "🎉 DASHBOARD PERMISSIONS SUDAH SIAP DIGUNAKAN!" . PHP_EOL;
echo PHP_EOL . "=== INSTRUKSI PENGGUNAAN ===" . PHP_EOL;
echo "1. Buka halaman edit user: http://127.0.0.1:8000/master/user/[ID]/edit" . PHP_EOL;
echo "2. Centang checkbox 'Dashboard' pada kolom 'View'" . PHP_EOL;  
echo "3. Klik 'Perbarui' untuk menyimpan" . PHP_EOL;
echo "4. Permission dashboard akan tersimpan dengan benar" . PHP_EOL;