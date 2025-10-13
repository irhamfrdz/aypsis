<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Verifying Master Tujuan Kegiatan Utama Permissions Match Master Tujuan ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n\n";

echo "=== Master Tujuan Permissions ===\n";
$masterTujuanPerms = [
    'master-tujuan.view',
    'master-tujuan.create',
    'master-tujuan.update',
    'master-tujuan.delete',
    'master-tujuan.print',
    'master-tujuan.export'
];

foreach ($masterTujuanPerms as $perm) {
    $hasPerm = $user->can($perm);
    echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
}

echo "\n=== Master Tujuan Kegiatan Utama Permissions ===\n";
$masterTujuanKegiatanUtamaPerms = [
    'master-tujuan-kegiatan-utama.view',
    'master-tujuan-kegiatan-utama.create',
    'master-tujuan-kegiatan-utama.update',
    'master-tujuan-kegiatan-utama.delete',
    'master-tujuan-kegiatan-utama.print',
    'master-tujuan-kegiatan-utama.export'
];

foreach ($masterTujuanKegiatanUtamaPerms as $perm) {
    $hasPerm = $user->can($perm);
    echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
}

echo "\n=== Summary ===\n";
$allMatch = true;
$masterTujuanCount = count(array_filter($masterTujuanPerms, fn($p) => $user->can($p)));
$masterTujuanKegiatanUtamaCount = count(array_filter($masterTujuanKegiatanUtamaPerms, fn($p) => $user->can($p)));

echo "Master Tujuan permissions: {$masterTujuanCount}/" . count($masterTujuanPerms) . "\n";
echo "Master Tujuan Kegiatan Utama permissions: {$masterTujuanKegiatanUtamaCount}/" . count($masterTujuanKegiatanUtamaPerms) . "\n";

if ($masterTujuanCount === count($masterTujuanPerms) && $masterTujuanKegiatanUtamaCount === count($masterTujuanKegiatanUtamaPerms)) {
    echo "🎉 Both modules have complete permissions!\n";
    echo "✅ Master Tujuan Kegiatan Utama now has the same permission structure as Master Tujuan\n";
} else {
    echo "❌ Some permissions are missing\n";
}
