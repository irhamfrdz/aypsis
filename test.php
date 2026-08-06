<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $content = file_get_contents('C:\kerjaan\aypsis\aypsis\aypsis\resources\views\layouts\app.blade.php');
    app('blade.compiler')->compileString($content);
    echo "OK\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}
