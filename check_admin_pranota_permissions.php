<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('username', 'admin')->first();
if ($user) {
    Auth::login($user);
    echo 'User admin permissions:' . PHP_EOL;
    foreach($user->permissions as $perm) {
        if (strpos($perm->name, 'pranota-cat') !== false || strpos($perm->name, 'tagihan') !== false) {
            echo '- ' . $perm->name . PHP_EOL;
        }
    }
    echo PHP_EOL;
    echo 'Has pranota-cat-view: ' . ($user->can('pranota-cat-view') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has tagihan-pranota-cat: ' . ($user->can('tagihan-pranota-cat') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'Admin user not found' . PHP_EOL;
}
