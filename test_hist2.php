<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$out = DB::select("SELECT hk.id, hk.nomor_kontainer, hk.jenis_kegiatan, hk.asal_gudang_id, hk.gudang_id, k.gudangs_id as k_gudang, sk.gudangs_id as sk_gudang 
FROM history_kontainers hk 
LEFT JOIN kontainers k ON hk.nomor_kontainer = k.nomor_seri_gabungan 
LEFT JOIN stock_kontainers sk ON hk.nomor_kontainer = sk.nomor_seri_gabungan 
WHERE k.gudangs_id = 6 OR sk.gudangs_id = 6
ORDER BY hk.id DESC LIMIT 15");
print_r($out);
