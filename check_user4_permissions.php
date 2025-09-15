<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking user4 permissions for tagihan-kontainer\n";
echo "=================================================\n";

// Find user4 (actually test4)
$user4 = User::where('username', 'test4')->first();
if (!$user4) {
    echo "User4 not found!\n";
    exit;
}

echo "User4 found (ID: {$user4->id})\n\n";

// Get user4 permissions
$user4Permissions = $user4->permissions()->select('id', 'name')->get();

echo "User4 current permissions:\n";
foreach ($user4Permissions as $perm) {
    echo "  {$perm->id}: {$perm->name}\n";
}

echo "\n";

// Check specific permissions
$tagihanKontainer = Permission::where('name', 'tagihan-kontainer')->first();
$masterPranotaTagihan = Permission::where('name', 'master-pranota-tagihan-kontainer')->first();

echo "Checking specific permissions:\n";
if ($tagihanKontainer) {
    $hasTagihan = $user4->permissions()->where('id', $tagihanKontainer->id)->exists();
    echo "tagihan-kontainer (ID: {$tagihanKontainer->id}): " . ($hasTagihan ? 'YES' : 'NO') . "\n";
}

if ($masterPranotaTagihan) {
    $hasMaster = $user4->permissions()->where('id', $masterPranotaTagihan->id)->exists();
    echo "master-pranota-tagihan-kontainer (ID: {$masterPranotaTagihan->id}): " . ($hasMaster ? 'YES' : 'NO') . "\n";
}

// Check if user4 has any tagihan-related permissions
$tagihanRelated = $user4Permissions->filter(function($perm) {
    return strpos($perm->name, 'tagihan') !== false;
});

echo "\nTagihan-related permissions for user4:\n";
if ($tagihanRelated->count() > 0) {
    foreach ($tagihanRelated as $perm) {
        echo "  {$perm->id}: {$perm->name}\n";
    }
} else {
    echo "  No tagihan-related permissions found\n";
}
