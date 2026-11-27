<?php
require __DIR__.'/../../vendor/autoload.php';
$app=require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach(App\Models\SuratJalan::whereIn('no_surat_jalan',['JB10036435','JB10036624'])->get() as $s){
echo json_encode(['sj'=>$s->only(['id','no_surat_jalan','tanggal_surat_jalan','tanggal_muat','no_plat','pengirim','tujuan_pengambilan','size','kegiatan','jenis_barang','no_kontainer','uang_jalan']), 'uang_jalans'=>App\Models\UangJalan::where('surat_jalan_id',$s->id)->get()->toArray(),'invoices'=>App\Models\InvoiceAktivitasLain::where('surat_jalan_id',$s->id)->get()->map(fn($i)=>$i->only(['id','nomor_invoice','tanggal_invoice','jenis_aktivitas','surat_jalan_source','jenis_penyesuaian','tipe_penyesuaian','detail_pembayaran','deskripsi','catatan','keterangan','grand_total','status']))],JSON_PRETTY_PRINT).PHP_EOL;
}
