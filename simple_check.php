<?php

require_once 'vendor/autoload.php';

use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('username', 'test4')->first();
if ($user) {
    echo 'User: ' . $user->username . ' (ID: ' . $user->id . ')' . PHP_EOL;
    echo 'Permissions:' . PHP_EOL;
    foreach($user->permissions as $perm) {
        echo '  - ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
    }
} else {
    echo 'User test4 not found' . PHP_EOL;
}
