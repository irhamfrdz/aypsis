<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Testing Family Members Creation\n";

try {
    // Find a test karyawan
    $karyawan = App\Models\Karyawan::first();

    if (!$karyawan) {
        echo "❌ No karyawan found for testing\n";
        exit;
    }

    echo "✅ Testing with karyawan: {$karyawan->nama_lengkap}\n";

    // Test creating a family member
    $familyData = [
        'hubungan' => 'ISTRI',
        'nama' => 'TEST FAMILY MEMBER',
        'tanggal_lahir' => '1990-01-01',
        'alamat' => 'TEST ADDRESS',
        'no_telepon' => '081234567890',
        'nik_ktp' => '1234567890123456',
        'no_bpjs_kesehatan' => 'TEST123456',
        'faskes' => 'TEST PUSKESMAS'
    ];

    $familyMember = $karyawan->familyMembers()->create($familyData);
    echo "✅ Created family member: {$familyMember->nama}\n";

    // Test retrieving family members
    $members = $karyawan->familyMembers;
    echo "👨‍👩‍👧‍👦 Total family members: " . $members->count() . "\n";

    foreach ($members as $member) {
        echo "  - {$member->hubungan}: {$member->nama}\n";
    }

    // Clean up test data
    $familyMember->delete();
    echo "🗑️  Cleaned up test data\n";

    echo "🎯 Family members functionality is working!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
