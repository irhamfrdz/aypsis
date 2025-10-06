<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Admin User Karyawan Check ===\n";

// Get admin user
$adminUser = DB::table('users')->where('username', 'admin')->first();
echo "Admin User ID: {$adminUser->id}\n";
echo "Admin karyawan_id: " . ($adminUser->karyawan_id ?? 'NULL') . "\n";

if ($adminUser->karyawan_id) {
    // Check karyawans table (plural)
    $karyawan = DB::table('karyawans')->where('id', $adminUser->karyawan_id)->first();
    if ($karyawan) {
        echo "✓ Karyawan found: {$karyawan->nama}\n";
    } else {
        echo "❌ Karyawan ID {$adminUser->karyawan_id} not found in karyawans table\n";
    }
} else {
    echo "❌ Admin user has no karyawan_id assigned\n";

    echo "\nChecking if there are any karyawans with admin username:\n";
    $karyawan = DB::table('karyawans')->where('nama', 'like', '%admin%')->first();
    if ($karyawan) {
        echo "Found potential match: {$karyawan->nama} (ID: {$karyawan->id})\n";
        echo "Consider updating admin user: UPDATE users SET karyawan_id = {$karyawan->id} WHERE username = 'admin'\n";
    } else {
        echo "No karyawan found with admin-like name\n";
        echo "You may need to create a karyawan record for admin or update middleware to skip for admin role\n";
    }
}

// Check middleware files existence
echo "\n=== Middleware Files ===\n";
$middlewarePath = app_path('Http/Middleware');
$middlewareFiles = [
    'EnsureKaryawanPresent.php',
    'EnsureUserApproved.php',
    'EnsureCrewChecklistComplete.php'
];

foreach($middlewareFiles as $file) {
    $fullPath = $middlewarePath . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "❌ {$file} missing\n";
    }
}

?>
