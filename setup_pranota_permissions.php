<?php

// Simple permission script for pranota surat jalan
echo "Adding Pranota Surat Jalan Permissions...\n";

// Change to Laravel directory 
chdir(__DIR__);

// Run individual commands
$commands = [
    'php artisan tinker --execute="use Spatie\Permission\Models\Permission; Permission::firstOrCreate([\'name\' => \'pranota-surat-jalan-view\']); echo \'Created: pranota-surat-jalan-view\' . PHP_EOL;"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Permission; Permission::firstOrCreate([\'name\' => \'pranota-surat-jalan-create\']); echo \'Created: pranota-surat-jalan-create\' . PHP_EOL;"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Permission; Permission::firstOrCreate([\'name\' => \'pranota-surat-jalan-update\']); echo \'Created: pranota-surat-jalan-update\' . PHP_EOL;"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Permission; Permission::firstOrCreate([\'name\' => \'pranota-surat-jalan-delete\']); echo \'Created: pranota-surat-jalan-delete\' . PHP_EOL;"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Role; \$adminRole = Role::where(\'name\', \'admin\')->first(); if (\$adminRole) { \$adminRole->givePermissionTo(\'pranota-surat-jalan-view\'); echo \'Assigned to admin: pranota-surat-jalan-view\' . PHP_EOL; }"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Role; \$adminRole = Role::where(\'name\', \'admin\')->first(); if (\$adminRole) { \$adminRole->givePermissionTo(\'pranota-surat-jalan-create\'); echo \'Assigned to admin: pranota-surat-jalan-create\' . PHP_EOL; }"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Role; \$adminRole = Role::where(\'name\', \'admin\')->first(); if (\$adminRole) { \$adminRole->givePermissionTo(\'pranota-surat-jalan-update\'); echo \'Assigned to admin: pranota-surat-jalan-update\' . PHP_EOL; }"',
    'php artisan tinker --execute="use Spatie\Permission\Models\Role; \$adminRole = Role::where(\'name\', \'admin\')->first(); if (\$adminRole) { \$adminRole->givePermissionTo(\'pranota-surat-jalan-delete\'); echo \'Assigned to admin: pranota-surat-jalan-delete\' . PHP_EOL; }"',
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    system($command);
    echo "\n";
}

echo "Permissions setup completed!\n";