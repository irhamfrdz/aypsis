<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('username', 'admin')->first();

if ($user) {
    echo "Admin user found\n";
    echo "Username: {$user->username}\n";
    echo "Name: {$user->name}\n";

    // Test common passwords
    $passwords = ['admin123', 'password', 'admin', '123456'];

    foreach ($passwords as $pass) {
        if (Hash::check($pass, $user->password)) {
            echo "✅ Password is: $pass\n";
            exit;
        }
    }

    echo "❌ None of the common passwords work\n";
    echo "Setting password to 'admin123'...\n";

    $user->password = bcrypt('admin123');
    $user->save();

    echo "✅ Password updated to 'admin123'\n";
} else {
    echo "Admin user not found\n";
}
