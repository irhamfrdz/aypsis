<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('username', 'admin')->first();
if ($user) {
    Auth::login($user);
    echo 'User admin tagihan-cat permissions:' . PHP_EOL;
    foreach($user->permissions as $perm) {
        if (strpos($perm->name, 'tagihan-cat') !== false) {
            echo '- ' . $perm->name . PHP_EOL;
        }
    }
    echo PHP_EOL;
    echo 'Has tagihan-cat-view: ' . ($user->can('tagihan-cat-view') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has tagihan-cat-index: ' . ($user->can('tagihan-cat-index') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'Admin user not found' . PHP_EOL;
}
