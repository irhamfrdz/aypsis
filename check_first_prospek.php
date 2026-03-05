<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Prospek;

$p = Prospek::orderBy('id', 'asc')->first();
if ($p) {
    echo "First Prospek ID: " . $p->id . "\n";
} else {
    echo "No prospeks found\n";
}
