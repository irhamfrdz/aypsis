<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate auth
$user = \App\Models\User::where('username', 'admin')->first();
\Illuminate\Support\Facades\Auth::login($user);

if ($user) {
    echo 'Testing permission checks with auth:' . PHP_EOL;
    echo 'can("permohonan-memo-update"): ' . (\Illuminate\Support\Facades\Auth::user()->can('permohonan-memo-update') ? 'YES' : 'NO') . PHP_EOL;
    echo 'hasPermissionTo("permohonan-memo-update"): ' . (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('permohonan-memo-update') ? 'YES' : 'NO') . PHP_EOL;

    // Test gate
    $gateResult = \Illuminate\Support\Facades\Gate::allows('permohonan-memo-update');
    echo 'Gate::allows("permohonan-memo-update"): ' . ($gateResult ? 'YES' : 'NO') . PHP_EOL;

    // Test with check
    $checkResult = \Illuminate\Support\Facades\Auth::user()->can('permohonan-memo-update');
    echo 'Auth::user()->can(): ' . ($checkResult ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User admin not found' . PHP_EOL;
}
