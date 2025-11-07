<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Expanding all problematic columns...\n";

try {
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN awalan_kontainer VARCHAR(10)");
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN nomor_seri_kontainer VARCHAR(20)");
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN akhiran_kontainer VARCHAR(10)");
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN nomor_seri_gabungan VARCHAR(20)");
    
    echo "✅ All columns expanded successfully!\n";
    echo "Now run: php fix_import_final.php\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}