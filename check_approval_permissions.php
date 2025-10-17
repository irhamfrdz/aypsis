<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Ambil user admin untuk testing
$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo 'User admin tidak ditemukan' . PHP_EOL;
    exit(1);
}

echo 'User: ' . $user->username . ' (ID: ' . $user->id . ')' . PHP_EOL;
echo 'Permissions untuk Approval Surat Jalan:' . PHP_EOL;

// Cek permission yang dibutuhkan
$permissions = [
    'surat-jalan-approval-dashboard',
    'surat-jalan-approval-level-1-view',
    'surat-jalan-approval-level-2-view',
    'surat-jalan-approval-level-1-approve',
    'surat-jalan-approval-level-2-approve'
];

foreach ($permissions as $permission) {
    $hasPermission = $user->can($permission);
    echo '- ' . $permission . ': ' . ($hasPermission ? 'YA' : 'TIDAK') . PHP_EOL;
}

// Cek apakah permission ada di database
echo PHP_EOL . 'Cek permission di database:' . PHP_EOL;
foreach ($permissions as $permission) {
    $permissionExists = \Spatie\Permission\Models\Permission::where('name', $permission)->exists();
    echo '- ' . $permission . ': ' . ($permissionExists ? 'ADA' : 'TIDAK ADA') . PHP_EOL;
}
