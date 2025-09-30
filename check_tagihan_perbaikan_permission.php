<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking tagihan-perbaikan-kontainer-view permission...\n";

$perm = DB::table('permissions')->where('name', 'tagihan-perbaikan-kontainer-view')->first();

if ($perm) {
    echo "✅ Permission exists: YES (ID: {$perm->id}, Description: {$perm->description})\n";
} else {
    echo "❌ Permission exists: NO\n";
}

// Check all tagihan-perbaikan-kontainer permissions
echo "\nAll tagihan-perbaikan-kontainer permissions:\n";
$perms = DB::table('permissions')->where('name', 'like', 'tagihan-perbaikan-kontainer-%')->get();

if ($perms->isEmpty()) {
    echo "No tagihan-perbaikan-kontainer permissions found!\n";
} else {
    foreach ($perms as $p) {
        echo "- {$p->name} (ID: {$p->id})\n";
    }
}
