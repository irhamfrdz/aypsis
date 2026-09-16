<?php

// Render the actual planner Blade partial with synthetic data, without reading the application database.
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = new App\Models\User;
$user->setRawAttributes(['id' => 1, 'username' => 'browser-test']);
Illuminate\Support\Facades\Auth::setUser($user);
Illuminate\Support\Facades\Gate::define('master-gudang-edit', fn () => true);
$gudang = new App\Models\Gudang;
$gudang->setRawAttributes(['id' => 1, 'nama_gudang' => 'Gudang Uji Jakarta', 'lokasi' => 'Area kontainer', 'status' => 'aktif']);
$containers = [
    ['key' => 'stock:1', 'source' => 'stock', 'container_id' => 1, 'number' => 'AYPU1234567', 'size' => '20', 'span' => 1, 'type' => 'Dry'],
    ['key' => 'sewa:1', 'source' => 'sewa', 'container_id' => 1, 'number' => 'SEWU7654321', 'size' => '40', 'span' => 2, 'type' => 'Dry'],
];
$fixtures = [];
foreach (['layout', 'positions', 'readonly'] as $fixture) {
    $mode = $fixture === 'layout' ? 'layout' : 'positions';
    Illuminate\Support\Facades\Gate::define('master-gudang-edit', fn () => $fixture !== 'readonly');
    $state = [
        'version' => 1,
        'layout' => $fixture === 'layout' ? null : ['blocks' => [['code' => 'A', 'bays' => 6, 'rows' => 4, 'tiers' => 3, 'disabled' => [['bay' => 6, 'row' => 4]]]]],
        'containers' => $containers,
        'positions' => [],
    ];
    $factory = app('view');
    // Capture pushed scripts before render() flushes the stacks.
    $html = view('denah-gudang.planner', compact('gudang', 'state', 'mode'))->render(function ($view, $contents) use ($factory) {
        return $contents.$factory->yieldPushContent('scripts');
    });
    $html = preg_replace('~<script src="[^"]*/js/gudang-plan.js" defer></script>~', '', $html);
    $fixtures[$fixture] = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="test"><style>body{margin:0;background:#f8fafc;font-family:Arial,sans-serif}*{box-sizing:border-box}fieldset{border:0;margin:0;padding:0}a{text-decoration:none}h1,h2,p{margin:0}button,input,select{font:inherit}'.file_get_contents(public_path('css/gudang-plan.css')).'</style></head><body>'.$html.'<script>'.file_get_contents(public_path('js/gudang-plan.js')).'</script></body></html>';
}
echo json_encode($fixtures, JSON_THROW_ON_ERROR);
