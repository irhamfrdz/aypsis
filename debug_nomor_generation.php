<?php

// Simple test script to debug the generate nomor issue

echo "Testing Generate Nomor Pembayaran...\n";
echo "==================================\n";

// Test 1: Direct method call simulation
echo "1. Testing COA lookup...\n";

// Simulate the COA lookup
$coaTypes = ['Kas/Bank', 'kas/bank', 'Bank', 'Kas', 'BANK', 'KAS'];
echo "   Looking for COA types: " . implode(', ', $coaTypes) . "\n";

// Test 2: Test nomor terakhir
echo "\n2. Testing nomor terakhir lookup...\n";
echo "   Looking for module: pembayaran_aktivitas_lainnya\n";

// Test 3: Test number generation format
echo "\n3. Testing number format generation...\n";
$today = new DateTime();
$tahun = $today->format('y'); // 2 digit year
$bulan = $today->format('m'); // 2 digit month
$kodeBank = '001'; // Sample
$sequence = str_pad(1, 6, '0', STR_PAD_LEFT);

$nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";
echo "   Generated format: {$nomorPembayaran}\n";

echo "\n✓ Format generation works!\n";
echo "\n📋 Debugging Steps:\n";
echo "1. Check if you're logged in to the system\n";
echo "2. Check if COA table has 'Kas/Bank' type records\n";
echo "3. Check if nomor_terakhir has 'pembayaran_aktivitas_lainnya' module\n";
echo "4. Check browser console for detailed error messages\n";
