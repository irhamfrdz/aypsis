<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Checking master-karyawan permission:\n";

$perm = Permission::where('name', 'master-karyawan')->first();
if($perm) {
    echo "✅ FOUND - ID: " . $perm->id . ", Name: " . $perm->name . "\n";
} else {
    echo "❌ NOT FOUND\n";
}

// Also check for master.karyawan
$perm2 = Permission::where('name', 'master.karyawan')->first();
if($perm2) {
    echo "✅ FOUND master.karyawan - ID: " . $perm2->id . ", Name: " . $perm2->name . "\n";
} else {
    echo "❌ master.karyawan NOT FOUND\n";
}
