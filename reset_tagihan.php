<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Reset tagihan 842 and 863
\App\Models\DaftarTagihanKontainerSewa::whereIn('id', [842, 863])->update([
    'status_pranota' => 'included',
    'pranota_id' => 1,
    'group' => 'TK125040000039'
]);

echo "Reset done\n";
?>
