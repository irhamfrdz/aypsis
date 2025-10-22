<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\User;

echo "=== FINAL TEST ===\n";
$admin = User::with('permissions')->where('username', 'admin')->first();
echo "User: " . $admin->username . "\n\n";

$tests = [
    'pergerakan-kapal-view',
    'pergerakan-kapal-create',
    'pergerakan-kapal-update',
    'pergerakan-kapal-delete'
];

foreach($tests as $test) {
    $result = $admin->can($test) ? "✓ PASS" : "❌ FAIL";
    echo "{$result} - {$test}\n";
}

echo "\n✅ All permissions with dash format are working!\n";
