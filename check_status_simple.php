<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PranotaTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== PRANOTA STATUS CHECK ===\n";

$statuses = PranotaTagihanKontainerSewa::distinct()->pluck('status');
echo "Existing status values:\n";
foreach($statuses as $status) {
    echo "- " . ($status ?? 'NULL') . "\n";
}

$columns = DB::select("DESCRIBE pranota_tagihan_kontainer_sewa");
foreach($columns as $column) {
    if($column->Field === 'status') {
        echo "\nStatus column info:\n";
        echo "Type: " . $column->Type . "\n";
        echo "Null: " . $column->Null . "\n";
        echo "Default: " . ($column->Default ?? 'NULL') . "\n";
    }
}