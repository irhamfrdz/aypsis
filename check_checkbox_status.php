<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "Checking pranota status...\n";

$pranota = DB::table('pranotalist')->first();

if ($pranota) {
    echo "Pranota: {$pranota->no_invoice}\n";
    echo "Status: {$pranota->status}\n";

    if ($pranota->status === 'sent') {
        echo "✅ Checkbox enabled (status = sent)\n";
    } else {
        echo "❌ Checkbox disabled (status = {$pranota->status})\n";
        echo "Change status to 'sent' to enable checkbox\n";
    }
} else {
    echo "No pranota found\n";
}
