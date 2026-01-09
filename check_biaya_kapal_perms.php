<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$permissions = DB::table('permissions')
    ->where('name', 'like', '%biaya-kapal%')
    ->get(['id', 'name', 'description']);

echo "Biaya Kapal Permissions Found: " . $permissions->count() . "\n\n";

foreach ($permissions as $perm) {
    echo "ID: {$perm->id} | Name: {$perm->name} | Description: {$perm->description}\n";
}

if ($permissions->count() == 0) {
    echo "\nNo biaya-kapal permissions found in database!\n";
    echo "You may need to run: php artisan db:seed --class=BiayaKapalPermissionSeeder\n";
}
