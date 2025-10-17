<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding missing Tanda Terima permissions...\n";
echo "================================================================================\n";

$permissions = [
    ['name' => 'tanda-terima-create', 'description' => 'Create Tanda Terima'],
    ['name' => 'tanda-terima-edit', 'description' => 'Edit Tanda Terima'],
];

$added = 0;
$skipped = 0;

DB::beginTransaction();

try {
    foreach ($permissions as $permission) {
        $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ ADDED: {$permission['name']}\n";
            $added++;
        } else {
            echo "⚠️  SKIPPED: {$permission['name']} (already exists)\n";
            $skipped++;
        }
    }

    DB::commit();

    echo "================================================================================\n\n";
    echo "Summary:\n";
    echo "- Permissions added: $added\n";
    echo "- Permissions skipped: $skipped\n";
    echo "- Total permissions: " . ($added + $skipped) . "\n\n";

    if ($added > 0) {
        echo "✅ Success! Permissions have been added.\n\n";
        echo "Next: Assign to Admin user:\n";
        echo "  php add_tanda_terima_permissions_to_admin.php\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
