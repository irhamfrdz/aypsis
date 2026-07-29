<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("SHOW COLUMNS FROM pergerakan_kapal WHERE Field = 'status'");
echo "Current Type: " . $columns[0]->Type . "\n";

if (strpos($columns[0]->Type, 'docking') === false) {
    DB::statement("ALTER TABLE pergerakan_kapal MODIFY COLUMN status ENUM('scheduled', 'sailing', 'arrived', 'departed', 'delayed', 'cancelled', 'docking') DEFAULT 'scheduled'");
    echo "Added 'docking' to enum.\n";
} else {
    echo "Enum already has 'docking'.\n";
}
