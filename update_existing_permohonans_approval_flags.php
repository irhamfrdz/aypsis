<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permohonan;

// Update existing approved permohonans to have both approval flags set to true
// This is for backward compatibility - permohonans that were already approved
// before the dual approval system was implemented
$approvedPermohonans = Permohonan::whereIn('status', ['Selesai', 'Bermasalah'])
    ->where(function($query) {
        $query->where('approved_by_system_1', false)
              ->orWhere('approved_by_system_2', false);
    })
    ->get();

echo "Found " . $approvedPermohonans->count() . " permohonans that need approval flags updated\n";

foreach ($approvedPermohonans as $permohonan) {
    $permohonan->update([
        'approved_by_system_1' => true,
        'approved_by_system_2' => true,
    ]);
    echo "Updated permohonan ID {$permohonan->id} ({$permohonan->nomor_memo})\n";
}

echo "\nAll existing approved permohonans have been updated with dual approval flags.\n";
echo "New permohonans will require approval from both Approval Tugas 1 and Approval Tugas 2.\n";
