<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s=App\Models\SuratJalan::where('no_surat_jalan','JB10036598')->firstOrFail();
$ids=App\Models\PranotaOngkosTrukItem::where('surat_jalan_id',$s->id)->pluck('pranota_ongkos_truk_id');
foreach(App\Models\PranotaOngkosTruk::with('items')->whereIn('id',$ids)->get() as $p){
echo 'PRANOTA '.$p->no_pranota.PHP_EOL;
foreach($p->items as $i){$sj=$i->type==='SuratJalan'?$i->suratJalan:$i->suratJalanBongkaran;
if($sj&&mb_strtoupper(trim($sj->tujuan_pengambilan))==='CIAWI'&&stripos($sj->pengirim??'','Tirta Investama')!==false){
echo json_encode(['sj'=>$i->no_surat_jalan,'tanggal'=>$sj->tanggal_surat_jalan?->format('d/m/Y'),'plat'=>$sj->no_plat,'pengirim'=>$sj->pengirim,'tujuan'=>$sj->tujuan_pengambilan,'pelancar'=>$sj->uangJalan?->jumlah_pelancar,'uj'=>$sj->uangJalan?->jumlah_total],JSON_UNESCAPED_UNICODE).PHP_EOL;
}}
}
