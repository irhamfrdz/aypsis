<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\SuratJalan;
use App\Models\SuratJalanApproval;

echo "=== DETAIL SURAT JALAN YANG PERLU APPROVAL ===\n\n";

// Get pending approvals with surat jalan data
$pendingApprovals = SuratJalanApproval::with('suratJalan')
    ->where('status', 'pending')
    ->get();

$grouped = $pendingApprovals->groupBy('approval_level');

foreach ($grouped as $level => $approvals) {
    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║ LEVEL: " . strtoupper($level) . str_repeat(' ', 70 - strlen($level)) . "║\n";
    echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";
    echo "║ Total Pending: " . $approvals->count() . str_repeat(' ', 63) . "║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

    foreach ($approvals as $approval) {
        $sj = $approval->suratJalan;

        echo "┌─ Surat Jalan ID: {$sj->id} ─────────────────────────────────────────────\n";
        echo "│ No. Surat Jalan : {$sj->no_surat_jalan}\n";
        echo "│ Tanggal         : " . ($sj->tanggal_surat_jalan ? date('d/m/Y', strtotime($sj->tanggal_surat_jalan)) : '-') . "\n";
        echo "│ Supir           : {$sj->supir}\n";
        echo "│ Kegiatan        : {$sj->kegiatan}\n";
        echo "│ No. Kontainer   : " . ($sj->no_kontainer ?: 'Belum diisi') . "\n";
        echo "│ No. Seal        : " . ($sj->no_seal ?: 'Belum diisi') . "\n";
        echo "│ Tujuan          : " . ($sj->tujuan ?: '-') . "\n";
        echo "│ Status SJ       : {$sj->status}\n";
        echo "│ Submitted       : " . $approval->created_at->diffForHumans() . "\n";
        echo "└───────────────────────────────────────────────────────────────────────\n\n";
    }
}

// Pick one approval to test
echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ SIAP UNTUK TESTING                                                           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

$testApproval = $pendingApprovals->first();
if ($testApproval) {
    echo "Saya akan test approval untuk:\n";
    echo "- Surat Jalan ID  : {$testApproval->surat_jalan_id}\n";
    echo "- No. Surat Jalan : {$testApproval->suratJalan->no_surat_jalan}\n";
    echo "- Approval Level  : {$testApproval->approval_level}\n";
    echo "- Approval ID     : {$testApproval->id}\n\n";

    echo "Route untuk approval:\n";
    echo "GET  : /approval/surat-jalan/{$testApproval->approval_level}\n";
    echo "GET  : /approval/surat-jalan/{$testApproval->suratJalan->id}\n";
    echo "POST : /approval/surat-jalan/{$testApproval->suratJalan->id}/approve\n";
}
