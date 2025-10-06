<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Karyawans Table Structure ===\n";
$columns = DB::select('DESCRIBE karyawans');
foreach($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\n=== Admin User Karyawan Data ===\n";
$adminUser = DB::table('users')->where('username', 'admin')->first();
echo "Admin karyawan_id: " . ($adminUser->karyawan_id ?? 'NULL') . "\n";

if ($adminUser->karyawan_id) {
    $karyawan = DB::table('karyawans')->where('id', $adminUser->karyawan_id)->first();
    if ($karyawan) {
        echo "✓ Karyawan found (ID: {$karyawan->id})\n";
        // Print all fields
        foreach($karyawan as $field => $value) {
            echo "  {$field}: {$value}\n";
        }
    } else {
        echo "❌ Karyawan ID {$adminUser->karyawan_id} not found\n";
    }
}

?>
