<?php

$content = file_get_contents('routes/web.php');
$lines = explode("\n", $content);

echo "=== MENCARI PENUTUP GROUP MIDDLEWARE KARYAWAN ===\n\n";

$bracketDepth = 0;
$inKaryawanGroup = false;
$karyawanStartLine = 0;

foreach ($lines as $lineNum => $line) {
    $lineNum++; // 1-indexed
    
    if (strpos($line, 'EnsureKaryawanPresent') !== false && strpos($line, '->group(function') !== false) {
        $inKaryawanGroup = true;
        $karyawanStartLine = $lineNum;
        $bracketDepth = 1;
        echo "✅ Karyawan group starts at line $lineNum\n";
        echo "   Line content: " . trim($line) . "\n\n";
        continue;
    }
    
    if ($inKaryawanGroup) {
        // Count opening brackets
        $openBrackets = substr_count($line, '{');
        $closeBrackets = substr_count($line, '}');
        
        $bracketDepth += $openBrackets - $closeBrackets;
        
        if ($openBrackets > 0 || $closeBrackets > 0) {
            echo "Line $lineNum: depth=$bracketDepth, opens=$openBrackets, closes=$closeBrackets - " . trim($line) . "\n";
        }
        
        if ($bracketDepth === 0) {
            echo "\n🎯 Karyawan group ENDS at line $lineNum\n";
            echo "   Line content: " . trim($line) . "\n";
            echo "   Group spans from line $karyawanStartLine to $lineNum\n\n";
            break;
        }
    }
}

// Now let's check what routes are AFTER the karyawan group
echo "=== ROUTES SETELAH GROUP KARYAWAN ===\n";
$afterKaryawanGroup = false;
$lineCounter = 0;

foreach ($lines as $lineNum => $line) {
    $lineNum++; // 1-indexed
    
    if ($lineNum > 2247) { // After the karyawan group ends
        $lineCounter++;
        
        // Look for order routes
        if (strpos($line, 'orders') !== false || strpos($line, 'ORDER') !== false) {
            echo "Line $lineNum: " . trim($line) . "\n";
        }
        
        // Stop after checking 50 lines
        if ($lineCounter > 50) {
            break;
        }
    }
}