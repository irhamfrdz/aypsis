<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p=App\Models\PranotaOngkosTruk::where('no_pranota','POT1126000008')->firstOrFail();
$a=app(App\Services\PranotaOngkosTrukAnalysisService::class)->analyze($p);
if($a['rows']->count()!==75||count($a['summaries'])!==10)throw new Exception('Unexpected analysis totals');
$r=$a['rows']->firstWhere('no',1);
if(!str_contains($r['keterangan'],'180.000')||$r['uj']!==775000.0)throw new Exception('Cibitung comparison failed');
$response=app(App\Http\Controllers\PranotaOngkosTrukController::class)->analysis($p->id,app(App\Services\PranotaOngkosTrukAnalysisService::class));
file_put_contents(__DIR__.'/application-analysis.pdf',$response->getContent());
echo 'PASS: 75 rows, 10 differing groups, Cibitung delta, PDF download response; '.$response->headers->get('content-type').PHP_EOL;
echo route('pranota-ongkos-truk.analysis',$p->id);
