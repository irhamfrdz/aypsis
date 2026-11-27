<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p=App\Models\PranotaOngkosTruk::where('no_pranota','POT1126000008')->firstOrFail();
$html=app(App\Http\Controllers\PranotaOngkosTrukController::class)->analysisPreview($p->id,app(App\Services\PranotaOngkosTrukAnalysisService::class))->render();
foreach(['75 baris data','10 kelompok','JB10036342','180.000','KETERANGAN'] as $expected){if(!str_contains($html,$expected))throw new Exception('Missing '.$expected);}
echo 'PASS: preview renders 75 rows, 10 groups and comparison details'.PHP_EOL;
echo route('pranota-ongkos-truk.analysis-preview',$p->id);
