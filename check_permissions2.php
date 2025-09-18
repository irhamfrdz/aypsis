<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$permissions = DB::table('permissions')->where('name', 'like', '%pranota-perbaikan-kontainer%')->get();
echo 'Pranota permissions found:' . PHP_EOL;
foreach($permissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL . 'Checking users table...' . PHP_EOL;
$users = DB::table('users')->count();
echo 'Total users: ' . $users . PHP_EOL;

if($users > 0) {
    $firstUser = DB::table('users')->first();
    echo 'First user: ' . ($firstUser->name ?? $firstUser->email) . ' (ID: ' . $firstUser->id . ')' . PHP_EOL;
}
?>