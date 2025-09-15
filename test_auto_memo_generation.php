<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\Kontainer;
use App\Models\PerbaikanKontainer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== TESTING AUTO-GENERATE NOMOR MEMO PERBAIKAN ON APPROVAL ===\n\n";

// Simulate authentication
$user = User::find(1); // Assuming admin user exists
if (!$user) {
    echo "❌ Admin user not found. Please create admin user first.\n";
    exit(1);
}

Auth::login($user);
echo "✅ Logged in as: {$user->name}\n\n";

// Create test data
echo "🔄 Creating test permohonan with perbaikan kegiatan...\n";

DB::beginTransaction();
try {
    // Create test kontainer
    $kontainer = Kontainer::firstOrCreate(
        ['nomor_kontainer' => 'TEST1234567'],
        [
            'ukuran' => '20ft',
            'status' => 'baik',
            'tanggal_beli' => now()->subYear()
        ]
    );
    echo "✅ Test kontainer created: {$kontainer->nomor_kontainer}\n";

    // Create test permohonan
    $permohonan = Permohonan::create([
        'nomor_memo' => 'TEST/MP/' . date('YmdHis'),
        'kegiatan' => 'PERBAIKAN',
        'vendor_perusahaan' => 'TEST_VENDOR',
        'supir_id' => 1, // Assuming supir exists
        'krani_id' => 1, // Assuming krani exists
        'plat_nomor' => 'TEST123',
        'no_chasis' => 'TESTCHASSIS',
        'ukuran' => '20ft',
        'tujuan' => 'TEST_DESTINATION',
        'jumlah_kontainer' => 1,
        'tanggal_memo' => now(),
        'jumlah_uang_jalan' => 1000000,
        'status' => 'Pending'
    ]);
    echo "✅ Test permohonan created: {$permohonan->nomor_memo}\n";

    // Attach kontainer to permohonan
    $permohonan->kontainers()->attach($kontainer->id);
    echo "✅ Kontainer attached to permohonan\n";

    // Test the createPerbaikanKontainer method
    echo "\n🔄 Testing createPerbaikanKontainer method...\n";

    $controller = new \App\Http\Controllers\PenyelesaianController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('createPerbaikanKontainer');
    $method->setAccessible(true);

    $tanggalPerbaikan = now()->toDateString();
    $result = $method->invoke($controller, $permohonan, $tanggalPerbaikan);

    if ($result > 0) {
        echo "✅ createPerbaikanKontainer succeeded! Created {$result} records\n";

        // Check if nomor_memo_perbaikan was generated
        $perbaikanRecords = PerbaikanKontainer::where('kontainer_id', $kontainer->id)
            ->whereDate('tanggal_perbaikan', $tanggalPerbaikan)
            ->get();

        foreach ($perbaikanRecords as $perbaikan) {
            echo "✅ Perbaikan record created with memo: {$perbaikan->nomor_memo_perbaikan}\n";
            echo "   - Status: {$perbaikan->status_perbaikan}\n";
            echo "   - Deskripsi: {$perbaikan->deskripsi_perbaikan}\n";
        }

        // Verify memo format
        if (preg_match('/^MP\d{13}$/', $perbaikan->nomor_memo_perbaikan)) {
            echo "✅ Nomor memo format is correct: {$perbaikan->nomor_memo_perbaikan}\n";
        } else {
            echo "❌ Nomor memo format is incorrect: {$perbaikan->nomor_memo_perbaikan}\n";
        }

    } else {
        echo "❌ createPerbaikanKontainer failed or returned 0\n";
    }

    DB::rollBack(); // Rollback to avoid creating test data in production
    echo "\n✅ Test completed successfully! All changes rolled back.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Test failed: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
}
