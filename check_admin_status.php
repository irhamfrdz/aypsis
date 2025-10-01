<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = \App\Models\User::where('username', 'admin')->first();
if ($admin) {
    echo 'Admin User Details:' . PHP_EOL;
    echo '- ID: ' . $admin->id . PHP_EOL;
    echo '- Username: ' . $admin->username . PHP_EOL;
    echo '- Status: ' . ($admin->status ?? 'NULL') . PHP_EOL;
    echo '- Karyawan ID: ' . ($admin->karyawan_id ?? 'NULL') . PHP_EOL;
    echo '- Has karyawan_id: ' . (empty($admin->karyawan_id) ? 'NO ❌' : 'YES ✅') . PHP_EOL;
    echo '- Is approved: ' . (($admin->status === 'approved') ? 'YES ✅' : 'NO ❌') . PHP_EOL;

    echo PHP_EOL . 'MIDDLEWARE WILL BLOCK IF:' . PHP_EOL;
    echo '- EnsureKaryawanPresent: ' . (empty($admin->karyawan_id) ? 'BLOCKED ❌' : 'ALLOWED ✅') . PHP_EOL;
    echo '- EnsureUserApproved: ' . (($admin->status !== 'approved') ? 'BLOCKED ❌' : 'ALLOWED ✅') . PHP_EOL;
} else {
    echo 'Admin not found' . PHP_EOL;
}

?>
