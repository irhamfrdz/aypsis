<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "⚠️  WARNING: This will drop existing LCL tables!\n";
echo "This should ONLY be run if you're sure the tables are from incomplete migration.\n\n";
echo "Tables to be dropped:\n";
echo "- kontainer_tanda_terima_lcl\n";
echo "- tanda_terima_lcl_items\n";
echo "- tanda_terima_lcl_penerima\n";
echo "- tanda_terima_lcl_pengirim\n";
echo "- tanda_terimas_lcl (main table)\n\n";

echo "Type 'YES' to proceed: ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) !== 'YES') {
    echo "❌ Cancelled. No changes made.\n";
    exit(0);
}

echo "\n🔧 Dropping tables...\n\n";

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    // Drop in reverse order to avoid foreign key issues
    $tables = [
        'kontainer_tanda_terima_lcl',
        'tanda_terima_lcl_items',
        'tanda_terima_lcl_penerima', 
        'tanda_terima_lcl_pengirim',
        'tanda_terimas_lcl', // Main table last
    ];
    
    foreach ($tables as $table) {
        if (DB::getSchemaBuilder()->hasTable($table)) {
            DB::statement("DROP TABLE `{$table}`");
            echo "✅ Dropped table: {$table}\n";
        } else {
            echo "⏭️  Table not found, skipping: {$table}\n";
        }
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "\n✅ All specified tables dropped successfully!\n";
    echo "\nNow you can run: php artisan migrate\n";
    
} catch (\Exception $e) {
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
