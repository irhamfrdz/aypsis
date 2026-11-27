<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p=App\Models\PranotaOngkosTruk::where('no_pranota','POT1126000008')->firstOrFail();
$c=app(App\Http\Controllers\PranotaOngkosTrukController::class);$s=app(App\Services\PranotaOngkosTrukAnalysisService::class);
$html=$c->analysisPreview($p->id,$s)->render();
if(!str_contains($html,'<table')||!str_contains($html,'Pilih tujuan di atas')||str_contains($html,'JB10036598'))throw new Exception('Initial view must only offer destination selection');
$a=$s->analyze($p,'CIAWI');
if($a['rows']->pluck('tujuan')->unique()->all()!==['CIAWI']||$a['rows']->pluck('plat')->unique()->count()<2)throw new Exception('Cross plate destination filtering failed');
$app->instance('request',Illuminate\Http\Request::create('/','GET',['tujuan'=>'CIAWI']));
$html=$c->analysisPreview($p->id,$s)->render();
if(!str_contains($html,'JB10036598')||str_contains($html,'JB10036342'))throw new Exception('Wrong rows in preview');
$response=$c->analysis($p->id,$s);file_put_contents(__DIR__.'/destination.pdf',$response->getContent());
echo 'PASS: initial view has empty table; CIAWI includes '.$a['rows']->count().' rows across '.$a['rows']->pluck('plat')->unique()->count().' plates; filtered preview and PDF render.'.PHP_EOL;
