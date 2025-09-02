<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenyelesaianController;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

echo "Test: createOrUpdateTagihan helper (transactional)\n";

$p = Permohonan::with(['kontainers','checkpoints'])->where('status','Selesai')->first();
if (!$p) { echo "No Selesai permohonan found\n"; exit(0); }

DB::beginTransaction();
try {
    $ctrl = new PenyelesaianController();
    $date = $p->checkpoints && $p->checkpoints->count() ? $p->checkpoints->min('tanggal_checkpoint') : now()->toDateString();
    $ref = new ReflectionClass($ctrl);
    $m = $ref->getMethod('createOrUpdateTagihan');
    $m->setAccessible(true);
    $id = $m->invoke($ctrl, $p, $date);
    echo "createOrUpdateTagihan returned id={$id}\n";
    echo "Rolling back...\n";
    DB::rollBack();
    echo "Rollback done.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
