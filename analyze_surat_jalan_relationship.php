<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;
use App\Models\SuratJalan;

echo "=== ANALYZING SURAT JALAN RELATIONSHIP ===\n\n";

// Check prospek table structure for surat_jalan related fields
echo "1. PROSPEK TABLE FIELDS RELATED TO SURAT JALAN:\n";
$prospek = Prospek::first();
if ($prospek) {
    $attributes = $prospek->getAttributes();
    $suratJalanFields = array_filter(array_keys($attributes), function($key) {
        return strpos(strtolower($key), 'surat') !== false || strpos(strtolower($key), 'jalan') !== false;
    });
    echo "Surat Jalan related fields: " . implode(', ', $suratJalanFields) . "\n";
    
    // Show sample data
    echo "\nSample prospek data:\n";
    $sample = Prospek::select('id', 'no_surat_jalan', 'tanda_terima_id', 'created_at')->take(5)->get();
    foreach ($sample as $item) {
        echo "ID: {$item->id}, no_surat_jalan: {$item->no_surat_jalan}, tanda_terima_id: {$item->tanda_terima_id}\n";
    }
}

echo "\n";

// Check tanda_terima table structure for surat_jalan related fields
echo "2. TANDA TERIMA TABLE FIELDS RELATED TO SURAT JALAN:\n";
$tandaTerima = TandaTerima::first();
if ($tandaTerima) {
    $attributes = $tandaTerima->getAttributes();
    $suratJalanFields = array_filter(array_keys($attributes), function($key) {
        return strpos(strtolower($key), 'surat') !== false || strpos(strtolower($key), 'jalan') !== false;
    });
    echo "Surat Jalan related fields: " . implode(', ', $suratJalanFields) . "\n";
    
    // Show sample data
    echo "\nSample tanda terima data:\n";
    $sample = TandaTerima::select('id', 'no_surat_jalan', 'term', 'created_at')->take(5)->get();
    foreach ($sample as $item) {
        echo "ID: {$item->id}, no_surat_jalan: {$item->no_surat_jalan}, term: {$item->term}\n";
    }
}

echo "\n";

// Check if SuratJalan model exists and has the fields
echo "3. SURAT JALAN TABLE (if exists):\n";
try {
    $suratJalan = SuratJalan::first();
    if ($suratJalan) {
        $attributes = $suratJalan->getAttributes();
        echo "Available fields: " . implode(', ', array_keys($attributes)) . "\n";
        
        // Show sample data
        echo "\nSample surat jalan data:\n";
        $sample = SuratJalan::select('id', 'no_surat_jalan', 'term')->take(5)->get();
        foreach ($sample as $item) {
            echo "ID: {$item->id}, no_surat_jalan: {$item->no_surat_jalan}, term: " . ($item->term ?? 'NULL') . "\n";
        }
    }
} catch (Exception $e) {
    echo "SuratJalan model/table might not exist or accessible: " . $e->getMessage() . "\n";
}

echo "\n";

// Find matching patterns
echo "4. MATCHING PATTERNS ANALYSIS:\n";
echo "Looking for prospek and tanda_terima with same no_surat_jalan...\n";

$prospekWithSuratJalan = Prospek::whereNotNull('no_surat_jalan')
    ->where('tanda_terima_id', null)
    ->select('id', 'no_surat_jalan')
    ->get();

$tandaTerimaWithSuratJalan = TandaTerima::whereNotNull('no_surat_jalan')
    ->whereNotNull('term')
    ->select('id', 'no_surat_jalan', 'term')
    ->get();

echo "\nProspek without tanda_terima_id but with no_surat_jalan:\n";
foreach ($prospekWithSuratJalan->take(10) as $prospek) {
    echo "Prospek ID: {$prospek->id}, no_surat_jalan: {$prospek->no_surat_jalan}\n";
}

echo "\nTandaTerima with term and no_surat_jalan:\n";
foreach ($tandaTerimaWithSuratJalan as $tt) {
    echo "TandaTerima ID: {$tt->id}, no_surat_jalan: {$tt->no_surat_jalan}, term: {$tt->term}\n";
}

echo "\n5. POTENTIAL MATCHES:\n";
foreach ($tandaTerimaWithSuratJalan as $tt) {
    $matchingProspek = $prospekWithSuratJalan->where('no_surat_jalan', $tt->no_surat_jalan);
    if ($matchingProspek->count() > 0) {
        echo "TandaTerima ID {$tt->id} (no_surat_jalan: {$tt->no_surat_jalan}, term: {$tt->term}) can be linked to:\n";
        foreach ($matchingProspek as $prospek) {
            echo "  - Prospek ID: {$prospek->id}\n";
        }
        echo "\n";
    }
}

echo "=== ANALYSIS COMPLETE ===\n";