<?php
/**
 * Permission Seeder for Tanda Terima Module
 *
 * This script adds permissions for the Tanda Terima (Receipt) management module.
 * Tanda Terima records are automatically created from approved Surat Jalan.
 *
 * Permissions:
 * - tanda-terima-view: View tanda terima list and details
 * - tanda-terima-update: Edit additional fields in tanda terima
 * - tanda-terima-delete: Soft delete tanda terima records
 *
 * Note: No create permission because tanda terima is auto-created from approval
 */

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Permission;

// Define permissions
$permissions = [
    [
        'name' => 'tanda-terima-view',
        'display_name' => 'Lihat Tanda Terima',
        'description' => 'Melihat daftar dan detail tanda terima'
    ],
    [
        'name' => 'tanda-terima-update',
        'display_name' => 'Edit Tanda Terima',
        'description' => 'Mengedit data tambahan tanda terima (estimasi kapal, tanggal, jumlah, berat, dimensi)'
    ],
    [
        'name' => 'tanda-terima-delete',
        'display_name' => 'Hapus Tanda Terima',
        'description' => 'Menghapus tanda terima (soft delete)'
    ]
];

echo "Adding Tanda Terima permissions...\n";
echo str_repeat("=", 80) . "\n";

$insertedCount = 0;
$skippedCount = 0;

foreach ($permissions as $permData) {
    $existing = Permission::where('name', $permData['name'])->first();

    if ($existing) {
        echo "⚠️  SKIPPED: {$permData['name']} (already exists)\n";
        $skippedCount++;
        continue;
    }

    // Create permission
    Permission::create([
        'name' => $permData['name'],
        'display_name' => $permData['display_name'],
        'description' => $permData['description']
    ]);

    echo "✅ ADDED: {$permData['name']} - {$permData['display_name']}\n";
    $insertedCount++;
}

echo str_repeat("=", 80) . "\n";
echo "\nSummary:\n";
echo "- Permissions added: $insertedCount\n";
echo "- Permissions skipped: $skippedCount\n";
echo "- Total permissions: " . count($permissions) . "\n\n";

// Show next steps
echo "Next Steps:\n";
echo str_repeat("-", 80) . "\n";
echo "1. Assign permissions to Admin user:\n";
echo "   php add_tanda_terima_permissions_to_admin.php\n";
echo "2. Access menu at: /tanda-terima\n";
echo "3. Test workflow: Approve surat jalan → Check tanda terima auto-created\n\n";

