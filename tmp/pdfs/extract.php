<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p=App\Models\PranotaOngkosTruk::with('items')->where('no_pranota','POT1126000008')->firstOrFail();
$rows=[];
foreach($p->items as $idx=>$i){
$s=$i->type==='SuratJalan'?$i->suratJalan:$i->suratJalanBongkaran;
$u=$s?->uangJalan; $rel=$s?->tujuanPengambilanRelation;
$ongkos=$rel?(str_contains(strtolower($s->size??''),'40')?$rel->ongkos_truk_40ft:$rel->ongkos_truk_20ft):0;
if($s?->tujuan_pengambilan==='PULO GADUNG ( BESI SCRAP )')$ongkos=1050000;
$tt=null;
if($s){if($i->type==='SuratJalan')$tt=$s->tanggal_tanda_terima?:$s->tandaTerima?->tanggal;else $tt=$s->tandaTerima?->tanggal_tanda_terima;}
$b=collect();if($u)foreach($u->pranotaUangJalan as $puj)$b=$b->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
$rows[]=['no'=>$idx+1,'tgl_sj'=>($s?->tanggal_surat_jalan?:$i->tanggal)?->format('d/m/Y'),'tgl_tt'=>$tt?Illuminate\Support\Carbon::parse($tt)->format('d/m/Y'):'-','sj'=>$i->no_surat_jalan,'plat'=>$s?->no_plat??'-','size'=>$s?->size??'-','rit_s'=>$s&&($s->supir||$s->supir2||$s->supirKaryawan)?1:0,'rit_k'=>$s&&($s->kenek||$s->kenekKaryawan)?1:0,'bukti'=>$b->filter()->unique()->implode(', ')?:'-','tujuan'=>$rel?->ke??$s?->tujuan_pengambilan??'-','ongkos'=>(float)$ongkos,'uj'=>(float)($u?->jumlah_total??0),'nominal'=>(float)$i->nominal,'components'=>$u?->only(['nomor_uang_jalan','jumlah_uang_jalan','jumlah_mel','jumlah_pelancar','jumlah_kawalan','jumlah_parkir','subtotal','jumlah_penyesuaian','jumlah_total','alasan_penyesuaian','memo']),'pengirim'=>$s?->pengirim,'type'=>$i->type];
}
file_put_contents(__DIR__.'/data.json',json_encode(['pranota'=>$p->only(['no_pranota','tanggal_pranota','total_nominal','adjustment','adjustments']),'rows'=>$rows],JSON_PRETTY_PRINT));
echo count($rows).' rows';
