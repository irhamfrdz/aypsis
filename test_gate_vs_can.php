<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Gate;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('username', 'marlina')->first();

if ($user) {
    echo "Testing Gate vs User::can for user marlina...\n";

    // Simulate login
    Auth::login($user);

    echo "Gate::allows('tagihan-perbaikan-kontainer-view'): " . (Gate::allows('tagihan-perbaikan-kontainer-view') ? 'YES' : 'NO') . "\n";
    echo "User::can('tagihan-perbaikan-kontainer-view'): " . ($user->can('tagihan-perbaikan-kontainer-view') ? 'YES' : 'NO') . "\n";

    // Check if permissions are loaded
    echo "Permissions loaded in collection: " . ($user->permissions->isNotEmpty() ? 'YES' : 'NO') . "\n";
    echo "Permissions count: " . $user->permissions->count() . "\n";

} else {
    echo "User marlina not found\n";
}
