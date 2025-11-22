#!/usr/bin/env php
<?php
/**
 * Script to automatically add resizable columns to all table views
 * Usage: php apply_resizable_tables.php
 */

$viewsPath = __DIR__ . '/resources/views';

// Files to process with their table IDs
$files = [
    'uang-jalan/index.blade.php' => 'uangJalanTable',
    'pranota-uang-jalan/index.blade.php' => 'pranotaUangJalanTable',
    'tanda-terima/index.blade.php' => 'tandaTerimaTable',
    'vendor-kontainer-sewa/index.blade.php' => 'vendorKontainerSewaTable',
    'daftar-tagihan-kontainer-sewa/index.blade.php' => 'tagihanKontainerSewaTable',
    'tanda-terima-tanpa-surat-jalan/index.blade.php' => 'tandaTerimaTanpaSJTable',
    'surat-jalan-bongkaran/index.blade.php' => 'suratJalanBongkaranTable',
    'tagihan-ob/index.blade.php' => 'tagihanObTable',
    'tagihan-cat/index.blade.php' => 'tagihanCatTable',
    'prospek/index.blade.php' => 'prospekTable',
    'pranota-uang-rit/index.blade.php' => 'pranotaUangRitTable',
    'pranota-uang-kenek/index.blade.php' => 'pranotaUangKenekTable',
    'permohonan/index.blade.php' => 'permohonanTable',
    'pranota-supir/index.blade.php' => 'pranotaSupirTable',
    'pranota-perbaikan-kontainer/index.blade.php' => 'pranotaPerbaikanKontainerTable',
    'pranota-ob/index.blade.php' => 'pranotaObTable',
    'pranota/index.blade.php' => 'pranotaTable',
    'pranota-cat/index.blade.php' => 'pranotaCatTable',
    'pergerakan-kapal/index.blade.php' => 'pergerakanKapalTable',
    'perbaikan-kontainer/index.blade.php' => 'perbaikanKontainerTable',
    'pembayaran-uang-muka/index.blade.php' => 'pembayaranUangMukaTable',
    'pembayaran-pranota-uang-jalan/index.blade.php' => 'pembayaranPranotaUangJalanTable',
    'pembayaran-pranota-surat-jalan/index.blade.php' => 'pembayaranPranotaSuratJalanTable',
    'pembayaran-pranota-supir/index.blade.php' => 'pembayaranPranotaSupirTable',
    'pembayaran-pranota-kontainer/index.blade.php' => 'pembayaranPranotaKontainerTable',
    'pembayaran-pranota-cat/index.blade.php' => 'pembayaranPranotaCatTable',
    'pembayaran-ob/index.blade.php' => 'pembayaranObTable',
    'pembayaran-aktivitas-lainnya/index.blade.php' => 'pembayaranAktivitasLainnyaTable',
    'outstanding/index.blade.php' => 'outstandingTable',
    'naik-kapal/index.blade.php' => 'naikKapalTable',
    'orders/approval/index.blade.php' => 'ordersApprovalTable',
    'master-user/index.blade.php' => 'masterUserTable',
    'master-tujuan/index.blade.php' => 'masterTujuanTable',
    'master-tujuan-kegiatan-utama/index.blade.php' => 'masterTujuanKegiatanUtamaTable',
    'master-tipe-akun/index.blade.php' => 'masterTipeAkunTable',
    'master-term/index.blade.php' => 'masterTermTable',
    'master-stock-kontainer/index.blade.php' => 'masterStockKontainerTable',
    'master-pricelist-uang-jalan/index.blade.php' => 'masterPricelistUangJalanTable',
    'master-pricelist-sewa-kontainer/index.blade.php' => 'masterPricelistSewaKontainerTable',
    'master-pricelist-cat/index.blade.php' => 'masterPricelistCatTable',
    'master-pengirim/index.blade.php' => 'masterPengirimTable',
    'master-permission/index.blade.php' => 'masterPermissionTable',
    'master-pelabuhan/index.blade.php' => 'masterPelabuhanTable',
];

echo "Starting to process " . count($files) . " files...\n\n";

foreach ($files as $file => $tableId) {
    $filePath = $viewsPath . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "❌ File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Check if already has resizable-table class
    if (strpos($content, 'resizable-table') !== false) {
        echo "⏭️  Already processed: $file\n";
        continue;
    }
    
    // Pattern 1: Add resizable-table class and id to table tag
    $content = preg_replace(
        '/<table class="min-w-full([^"]*)"/',
        '<table class="min-w-full$1 resizable-table" id="' . $tableId . '"',
        $content,
        1
    );
    
    // Pattern 2: Add resizable-th class and resize handle to th tags (except last one - Aksi column)
    $content = preg_replace_callback(
        '/(<thead[^>]*>.*?<tr[^>]*>)(.*?)(<\/tr>.*?<\/thead>)/s',
        function($matches) {
            $theadStart = $matches[1];
            $thContent = $matches[2];
            $theadEnd = $matches[3];
            
            // Split th tags
            preg_match_all('/<th\s+class="([^"]*)"([^>]*)>(.*?)<\/th>/s', $thContent, $thMatches, PREG_SET_ORDER);
            
            $newThContent = '';
            $totalTh = count($thMatches);
            
            foreach ($thMatches as $index => $thMatch) {
                $classes = $thMatch[1];
                $attributes = $thMatch[2];
                $innerContent = $thMatch[3];
                
                // Skip last column (usually "Aksi")
                if ($index < $totalTh - 1) {
                    // Add resizable-th class if not exists
                    if (strpos($classes, 'resizable-th') === false) {
                        $classes = 'resizable-th ' . $classes;
                    }
                    
                    // Add position relative style if not exists
                    if (strpos($attributes, 'position: relative') === false) {
                        $attributes .= ' style="position: relative;"';
                    }
                    
                    // Add resize handle if not exists
                    if (strpos($innerContent, 'resize-handle') === false) {
                        $innerContent .= '<div class="resize-handle"></div>';
                    }
                }
                
                $newThContent .= '<th class="' . trim($classes) . '"' . $attributes . '>' . $innerContent . '</th>';
            }
            
            return $theadStart . $newThContent . $theadEnd;
        },
        $content
    );
    
    // Pattern 3: Add component include and initialization script at the end (before @endsection)
    if (strpos($content, "@include('components.resizable-table')") === false) {
        $content = preg_replace(
            '/@endsection\s*$/',
            "@endsection\n\n@include('components.resizable-table')\n\n@push('scripts')\n<script>\n\$(document).ready(function() {\n    initResizableTable('$tableId');\n});\n</script>\n@endpush",
            $content
        );
    }
    
    // Write back to file
    file_put_contents($filePath, $content);
    
    echo "✅ Processed: $file (Table ID: $tableId)\n";
}

echo "\n✨ Done! Processed all files.\n";
echo "📝 Note: Please review the changes and test each table.\n";
