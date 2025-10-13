<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== Checking Master Tujuan Kegiatan Utama Permissions ===\n";

$perms = Permission::where('name', 'like', 'master-tujuan-kegiatan-utama%')->get();
echo "Found " . $perms->count() . " permissions:\n";

foreach($perms as $p) {
    echo "- {$p->name}\n";
}

echo "\n=== Checking Route Middleware Requirements ===\n";
echo "Route middleware expects: master-tujuan-kegiatan-utama-view\n";
echo "But permissions use: master-tujuan-kegiatan-utama.view\n";

echo "\n=== Checking if admin has ALL required permissions ===\n";
$user = \App\Models\User::find(1);
if ($user) {
    $requiredPerms = [
        'master-tujuan-kegiatan-utama-view',
        'master-tujuan-kegiatan-utama-create',
        'master-tujuan-kegiatan-utama-update',
        'master-tujuan-kegiatan-utama-delete'
    ];

    foreach ($requiredPerms as $perm) {
        $hasPerm = $user->can($perm);
        echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
    }
}
