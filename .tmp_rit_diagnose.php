<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pranotas = App\Models\PranotaUangRit::query()->orderByDesc('id')->limit(8)->get();
$service = new App\Services\RekapUangRitService;
$rows = collect();
foreach ($pranotas as $pranota) {
    foreach ($service->references($pranota) as $reference) {
        $sj = $reference['bongkaran']
            ? App\Models\SuratJalanBongkaran::where('nomor_surat_jalan', $reference['number'])->first()
            : App\Models\SuratJalan::where('no_surat_jalan', $reference['number'])->first();
        if ($sj) $rows->push($sj);
    }
}
echo 'rows ', $rows->count(), PHP_EOL;
echo 'types ', json_encode($rows->countBy(fn ($sj) => strtoupper(trim((string) $sj->tipe_kontainer)))->all()), PHP_EOL;
echo 'no container ', $rows->filter(fn ($sj) => trim((string) $sj->no_kontainer) === '')->count(), PHP_EOL;
echo 'multiple container ', $rows->filter(fn ($sj) => preg_match('/[,;\r\n]/', (string) $sj->no_kontainer))->count(), PHP_EOL;
echo 'cargo examples ', json_encode($rows->filter(fn ($sj) => stripos((string) $sj->tipe_kontainer, 'cargo') !== false || stripos((string) $sj->no_kontainer, 'cargo') !== false)
    ->take(8)->map(fn ($sj) => ['sj' => $sj->no_surat_jalan ?? $sj->nomor_surat_jalan, 'type' => $sj->tipe_kontainer, 'container' => $sj->no_kontainer, 'goods' => $sj->jenis_barang])->values()->all()), PHP_EOL;
