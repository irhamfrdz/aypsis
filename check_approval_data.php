<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\SuratJalan;
use App\Models\SuratJalanApproval;

echo "=== CHECKING SURAT JALAN DATA ===\n\n";

// Check total surat jalan
$totalSJ = SuratJalan::count();
echo "Total Surat Jalan: $totalSJ\n\n";

// Check surat jalan yang submitted (status = 'submitted')
$submitted = SuratJalan::where('status', 'submitted')->get();
echo "Surat Jalan dengan status 'submitted': " . $submitted->count() . "\n";

if ($submitted->count() > 0) {
    echo "\nDetail Surat Jalan yang submitted:\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($submitted as $sj) {
        echo "ID: {$sj->id}\n";
        echo "No. Surat Jalan: {$sj->no_surat_jalan}\n";
        echo "Tanggal: " . ($sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-') . "\n";
        echo "Supir: {$sj->supir}\n";
        echo "Kegiatan: {$sj->kegiatan}\n";
        echo "Status: {$sj->status}\n";

        // Check approval status
        $pendingApproval = SuratJalanApproval::where('surat_jalan_id', $sj->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingApproval) {
            echo "✅ Ada pending approval (Level: {$pendingApproval->approval_level})\n";
        } else {
            echo "❌ Tidak ada pending approval\n";
        }
        echo str_repeat('-', 80) . "\n";
    }
} else {
    echo "\n⚠️ Tidak ada surat jalan dengan status submitted\n";

    // Check draft surat jalan
    $drafts = SuratJalan::where('status', 'draft')->limit(3)->get();
    echo "\nSurat Jalan dengan status 'draft': " . $drafts->count() . "\n";

    if ($drafts->count() > 0) {
        echo "\nContoh 3 surat jalan draft:\n";
        echo str_repeat('-', 80) . "\n";
        foreach ($drafts as $sj) {
            echo "ID: {$sj->id} | No: {$sj->no_surat_jalan} | Supir: {$sj->supir} | Status: {$sj->status}\n";
        }
        echo str_repeat('-', 80) . "\n";
    }
}

// Check approval records
echo "\n=== CHECKING APPROVAL RECORDS ===\n\n";
$totalApprovals = SuratJalanApproval::count();
echo "Total Approval Records: $totalApprovals\n";

$pendingApprovals = SuratJalanApproval::where('status', 'pending')->get();
echo "Pending Approvals: " . $pendingApprovals->count() . "\n";

if ($pendingApprovals->count() > 0) {
    echo "\nDetail Pending Approvals:\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($pendingApprovals as $approval) {
        echo "ID: {$approval->id}\n";
        echo "Surat Jalan ID: {$approval->surat_jalan_id}\n";
        echo "Level: {$approval->approval_level}\n";
        echo "Status: {$approval->status}\n";
        echo "Created: " . $approval->created_at->format('d/m/Y H:i') . "\n";
        echo str_repeat('-', 80) . "\n";
    }
}
