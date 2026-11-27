<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p=App\Models\PranotaOngkosTruk::where('no_pranota','POT1126000008')->firstOrFail();
$html=app(App\Http\Controllers\PranotaOngkosTrukController::class)->analysisPreview($p->id,app(App\Services\PranotaOngkosTrukAnalysisService::class))->render();
file_put_contents(__DIR__.'/detail-preview.html',$html);
echo 'Rendered preview';
