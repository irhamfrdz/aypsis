<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Permission;

echo "Checking pembayaran-pranota-cat permissions:\n";
$permissions = Permission::where('name', 'like', 'pembayaran-pranota-cat%')->get();

if ($permissions->isEmpty()) {
    echo "❌ No pembayaran-pranota-cat permissions found!\n";
} else {
    foreach ($permissions as $p) {
        echo "✅ {$p->name} (ID: {$p->id})\n";
    }
}

echo "\nTotal: " . $permissions->count() . "\n";
