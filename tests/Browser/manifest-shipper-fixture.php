<?php

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manifest = new App\Models\Manifest;
$manifest->setRawAttributes([
    'id' => 1, 'nomor_bl' => 'BL-001', 'nomor_kontainer' => 'AYPU1234567', 'shipper_id' => null,
    'pengirim' => 'Lama', 'alamat_pengirim' => 'Alamat lama', 'penerima' => 'Consignee lama',
    'notify_party' => 'Notify lama', 'alamat_notify_party' => 'Alamat notify lama',
]);
$html = view('manifests.partials.shipper-button', compact('manifest'))->render();
$styles = '';
$html .= view('manifests.partials.shipper-modal')->render(function ($view, $contents) use (&$styles) {
    $styles = app('view')->yieldPushContent('styles');

    return $contents;
});
echo '<!doctype html><html><head><meta name="csrf-token" content="test"><meta name="viewport" content="width=device-width,initial-scale=1">'.$styles.'</head><body>'.$html
    .'<script>sessionStorage.setItem("loads", Number(sessionStorage.getItem("loads") || 0) + 1);</script><script>'
    .file_get_contents(public_path('js/manifest-shipper.js')).'</script></body></html>';
