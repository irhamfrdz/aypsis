<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cek Permission Surat Jalan ===\n";

// Cari permission yang ada di database
$permissions = App\Models\Permission::where('name', 'like', '%surat-jalan%')->get(['name', 'id']);
echo "Permission yang ada di database:\n";
foreach ($permissions as $perm) {
    echo "ID: {$perm->id}, Name: {$perm->name}\n";
}

echo "\n=== Cek Permission Pembayaran ===\n";
$pembayaranPermissions = App\Models\Permission::where('name', 'like', '%pembayaran%')->where('name', 'like', '%surat-jalan%')->get(['name', 'id']);
echo "Permission pembayaran surat jalan yang ada:\n";
foreach ($pembayaranPermissions as $perm) {
    echo "ID: {$perm->id}, Name: {$perm->name}\n";
}

echo "\n=== Cek User Permission ===\n";
$user = App\Models\User::find(1); // Asumsi admin adalah user ID 1
if ($user) {
    echo "User: {$user->username}\n";
    $userPermissions = $user->permissions()->where('name', 'like', '%surat-jalan%')->get(['name']);
    echo "Permission user yang terkait surat jalan:\n";
    foreach ($userPermissions as $perm) {
        echo "- {$perm->name}\n";
    }
}