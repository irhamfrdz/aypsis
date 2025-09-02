<?php
// scripts/wipe_tagihan_containers.php
// Safe one-off script to remove all TagihanKontainerSewa rows and pivot links.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TagihanKontainerSewa;

echo "Wipe Tagihan Kontainer Sewa - START\n";

$driver = DB::getDriverName();
echo "DB driver: {$driver}\n";

// Disable foreign key checks depending on driver
if ($driver === 'mysql') {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
} elseif ($driver === 'sqlite') {
    DB::statement('PRAGMA foreign_keys = OFF;');
} elseif ($driver === 'pgsql') {
    // For Postgres, use a transactional approach per-table; we'll set constraints deferred
    DB::statement('SET CONSTRAINTS ALL DEFERRED;');
}

$pivotTable = 'tagihan_kontainer_sewa_kontainers';
$mainTable = (new TagihanKontainerSewa())->getTable();

$beforeTag = TagihanKontainerSewa::count();
$beforePiv = DB::table($pivotTable)->count();
echo "Before: tagihan={$beforeTag}, pivots={$beforePiv}\n";

// Delete pivot rows then delete main rows
DB::table($pivotTable)->delete();

// Use truncate where possible (resets autoincrement); fallback to delete
try {
    DB::table($mainTable)->truncate();
} catch (\Exception $e) {
    // Some drivers (sqlite) may not support truncate; fall back to delete
    DB::table($mainTable)->delete();
}

echo "Deleted tagihan and pivot rows.\n";

$afterTag = TagihanKontainerSewa::count();
$afterPiv = DB::table($pivotTable)->count();
echo "After: tagihan={$afterTag}, pivots={$afterPiv}\n";

// Re-enable FK checks
if ($driver === 'mysql') {
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
} elseif ($driver === 'sqlite') {
    DB::statement('PRAGMA foreign_keys = ON;');
} elseif ($driver === 'pgsql') {
    DB::statement('SET CONSTRAINTS ALL IMMEDIATE;');
}

echo "Wipe Tagihan Kontainer Sewa - DONE\n";

return 0;
