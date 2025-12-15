<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📋 Checking LCL Tables Status\n";
echo "================================\n\n";

$tables = [
    'tanda_terimas_lcl' => 'Main LCL table',
    'tanda_terima_lcl_items' => 'Items pivot table',
    'tanda_terima_lcl_penerima' => 'Penerima pivot table',
    'tanda_terima_lcl_pengirim' => 'Pengirim pivot table',
    'kontainer_tanda_terima_lcl' => 'Kontainer pivot table',
];

$existingTables = [];
$missingTables = [];

foreach ($tables as $table => $description) {
    if (DB::getSchemaBuilder()->hasTable($table)) {
        $count = DB::table($table)->count();
        $existingTables[] = $table;
        echo "✅ EXISTS: {$table}\n";
        echo "   └─ {$description}\n";
        echo "   └─ Records: {$count}\n\n";
    } else {
        $missingTables[] = $table;
        echo "❌ MISSING: {$table}\n";
        echo "   └─ {$description}\n\n";
    }
}

echo "\n================================\n";
echo "Summary:\n";
echo "- Existing tables: " . count($existingTables) . "\n";
echo "- Missing tables: " . count($missingTables) . "\n\n";

if (count($existingTables) > 0 && count($missingTables) > 0) {
    echo "⚠️  PARTIAL MIGRATION DETECTED!\n";
    echo "Some tables exist but not all. This is likely from incomplete migration.\n\n";
    echo "Recommendation:\n";
    echo "1. Run: php drop_existing_lcl_tables.php\n";
    echo "2. Then run: php artisan migrate\n";
} elseif (count($existingTables) === count($tables)) {
    echo "✅ ALL TABLES EXIST\n";
    echo "Migration already completed successfully.\n";
} else {
    echo "ℹ️  NO LCL TABLES FOUND\n";
    echo "You can run: php artisan migrate\n";
}

echo "\n================================\n";
echo "Migration status:\n\n";

$migrations = DB::table('migrations')
    ->where('migration', 'like', '%tanda_terima_lcl%')
    ->orderBy('id')
    ->get();

if ($migrations->count() > 0) {
    foreach ($migrations as $mig) {
        echo "- {$mig->migration}\n";
        echo "  Batch: {$mig->batch}\n\n";
    }
} else {
    echo "No LCL migrations found in migrations table.\n";
}
