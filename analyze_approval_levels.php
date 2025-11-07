<?php

require_once 'bootstrap/app.php';
use App\Models\SuratJalanApproval;

echo "=== ANALISIS APPROVAL LEVELS ===\n";
echo "Unique approval levels in database:\n";
$levels = SuratJalanApproval::select('approval_level')->distinct()->get();
foreach($levels as $level) {
    $count = SuratJalanApproval::where('approval_level', $level->approval_level)->count();
    echo "- {$level->approval_level}: {$count} records\n";
}

echo "\n=== SAMPLE DATA ===\n";
$samples = SuratJalanApproval::with('suratJalan')
    ->select('surat_jalan_id', 'approval_level', 'status')
    ->take(10)
    ->get();
    
foreach($samples as $sample) {
    echo "SJ ID: {$sample->surat_jalan_id}, Level: {$sample->approval_level}, Status: {$sample->status}\n";
}

echo "\n=== CONTOH SURAT JALAN DENGAN MULTI LEVEL ===\n";
$multiLevel = SuratJalanApproval::where('surat_jalan_id', 3)->get();
foreach($multiLevel as $approval) {
    echo "SJ ID: {$approval->surat_jalan_id}, Level: {$approval->approval_level}, Status: {$approval->status}\n";
}