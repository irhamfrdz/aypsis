<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Permohonan;
use Illuminate\Support\Facades\App;

// Boot Laravel application
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$memos = ['MS125086193895','MS125085705681','MS125084197249'];
$out = [];
foreach ($memos as $memo) {
    $p = Permohonan::where('nomor_memo', $memo)->with('kontainers')->first();
    if (!$p) continue;
    $out[] = [
        'memo' => $p->nomor_memo,
        'kontainers' => $p->kontainers->map(function($k){
            return [
                'id' => $k->id,
                'nomor_seri_gabungan' => $k->nomor_seri_gabungan,
                'nomor_kontainer_attr' => $k->nomor_kontainer,
                'status' => $k->status,
            ];
        })->toArray()
    ];
}

echo json_encode($out, JSON_PRETTY_PRINT);
