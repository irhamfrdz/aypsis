<?php

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::find(18);
if ($user) {
    echo "User: {$user->name}\n";
    echo "Has tagihan-perbaikan-kontainer-view: " . ($user->can('tagihan-perbaikan-kontainer-view') ? 'YES' : 'NO') . "\n";

    echo "\nAll permissions:\n";
    foreach ($user->permissions as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "User not found\n";
}
