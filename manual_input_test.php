<?php

// Test script to simulate manual input and check if JavaScript logic works
echo "=== MANUAL INPUT SIMULATION TEST ===\n\n";

// Reset Kartu Keluarga status to "tidak" first
$karyawan = App\Models\Karyawan::find(6);
if ($karyawan) {
    $kk = $karyawan->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();
    if ($kk) {
        $kk->update([
            'status' => 'tidak',
            'nomor_sertifikat' => null,
            'issued_date' => null,
            'expired_date' => null,
            'catatan' => null
        ]);
        echo "✅ Kartu Keluarga status reset to 'tidak'\n";
    }
}

// Check current status after reset
$karyawan = App\Models\Karyawan::find(6);
$kk = $karyawan->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();
echo "\n📊 Current Status After Reset:\n";
echo "  Status: " . $kk->status . "\n";
echo "  Nomor: " . ($kk->nomor_sertifikat ?? 'null') . "\n";

echo "\n🔧 Next Steps for Manual Testing:\n";
echo "1. Buka browser dan akses: http://localhost/master/karyawan/6/crew-checklist\n";
echo "2. Cari field 'Kartu Keluarga' (biasanya item ID 4)\n";
echo "3. Input nomor sertifikat: KK1234567890\n";
echo "4. Klik di luar field atau tekan Tab untuk trigger blur event\n";
echo "5. Periksa di browser console apakah ada debug messages\n";
echo "6. Submit form dan cek apakah status berubah ke 'ada'\n";

echo "\n💡 Debug Checklist:\n";
echo "- Pastikan JavaScript tidak disabled\n";
echo "- Buka Developer Tools (F12) dan lihat Console tab\n";
echo "- Cari messages yang dimulai dengan 'DEBUG:'\n";
echo "- Jika tidak ada messages, JavaScript mungkin tidak ter-load\n";

echo "\n📝 Expected Debug Messages:\n";
echo "- 'DEBUG: JavaScript execution started'\n";
echo "- 'DEBUG: Found X nomor_sertifikat fields'\n";
echo "- 'DEBUG: Input event for checklist[4][nomor_sertifikat]'\n";
echo "- 'DEBUG: Item 4: nomor=\"KK1234567890\", status=\"ada\"'\n";

echo "\n🎯 If JavaScript doesn't work, try this manual test in browser console:\n";
echo "const field = document.querySelector('input[name=\"checklist[4][nomor_sertifikat]\"]');\n";
echo "if (field) {\n";
echo "    field.value = 'KK1234567890';\n";
echo "    field.dispatchEvent(new Event('blur', { bubbles: true }));\n";
echo "    console.log('Manual trigger executed');\n";
echo "} else {\n";
echo "    console.log('Field not found');\n";
echo "}\n";
