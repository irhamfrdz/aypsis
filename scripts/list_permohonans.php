<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permohonan;

$rows = Permohonan::withCount(['kontainers','checkpoints'])->latest()->limit(20)->get();
if ($rows->isEmpty()) { echo "No permohonans found\n"; exit(0); }
foreach ($rows as $r) {
    echo "id={$r->id}, nomor_memo={$r->nomor_memo}, vendor={$r->vendor_perusahaan}, status={$r->status}, kontainers={$r->kontainers_count}, checkpoints={$r->checkpoints_count}\n";
}
