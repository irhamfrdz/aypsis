<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('username', 'admin')->first();
if (!$user) {
    die('Admin user not found!' . PHP_EOL);
}

echo 'Testing COA permissions for admin user:' . PHP_EOL;
echo '- master-coa-view: ' . ($user->can('master-coa-view') ? 'YES' : 'NO') . PHP_EOL;
echo '- master-coa-create: ' . ($user->can('master-coa-create') ? 'YES' : 'NO') . PHP_EOL;
echo '- master-coa-update: ' . ($user->can('master-coa-update') ? 'YES' : 'NO') . PHP_EOL;
echo '- master-coa-delete: ' . ($user->can('master-coa-delete') ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL . 'Route test:' . PHP_EOL;
try {
    $url = route('master-coa-index');
    echo '- master-coa-index route: EXISTS (' . $url . ')' . PHP_EOL;
} catch (Exception $e) {
    echo '- master-coa-index route: ERROR - ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . 'If all permissions show YES and route exists, the COA menu should now appear in the sidebar!' . PHP_EOL;
?>
