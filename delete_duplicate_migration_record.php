<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Delete the duplicate migration record from database
    $deleted = DB::table('migrations')
        ->where('migration', '2026_01_02_133902_add_tanggal_checkpoint_to_surat_jalan_bongkarans_table')
        ->delete();
    
    if ($deleted > 0) {
        echo "✓ Successfully deleted migration record: 2026_01_02_133902_add_tanggal_checkpoint_to_surat_jalan_bongkarans_table\n";
        echo "  Records deleted: {$deleted}\n";
    } else {
        echo "✗ Migration record not found in database\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
