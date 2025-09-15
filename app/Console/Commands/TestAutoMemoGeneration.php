<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permohonan;
use App\Models\Kontainer;
use App\Models\PerbaikanKontainer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestAutoMemoGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auto-memo-generation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test auto-generation of nomor memo perbaikan on approval';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TESTING AUTO-GENERATE NOMOR MEMO PERBAIKAN ON APPROVAL ===');

        // Simulate authentication
        $user = User::find(1); // Assuming admin user exists
        if (!$user) {
            $this->error('Admin user not found. Please create admin user first.');
            return 1;
        }

        Auth::login($user);
        $this->info("✅ Logged in as: {$user->name}");

        // Create test data
        $this->info('🔄 Creating test permohonan with perbaikan kegiatan...');

        DB::beginTransaction();
        try {
            // Create test kontainer
            $kontainer = Kontainer::firstOrCreate(
                ['nomor_seri_gabungan' => 'TEST1234567'],
                [
                    'awalan_kontainer' => 'TEST',
                    'nomor_seri_kontainer' => '123456',
                    'akhiran_kontainer' => '7',
                    'ukuran' => '20ft',
                    'tipe_kontainer' => 'Standard',
                    'status' => 'baik',
                    'tanggal_beli' => now()->subYear()
                ]
            );
            $this->info("✅ Test kontainer created: {$kontainer->nomor_kontainer}");

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
            $this->info("✅ Test permohonan created: {$permohonan->nomor_memo}");

            // Attach kontainer to permohonan
            $permohonan->kontainers()->attach($kontainer->id);
            $this->info('✅ Kontainer attached to permohonan');

            // Test the createPerbaikanKontainer method
            $this->info('🔄 Testing createPerbaikanKontainer method...');

            $controller = new \App\Http\Controllers\PenyelesaianController();
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('createPerbaikanKontainer');
            $method->setAccessible(true);

            $tanggalPerbaikan = now()->toDateString();
            $result = $method->invoke($controller, $permohonan, $tanggalPerbaikan);

            if ($result > 0) {
                $this->info("✅ createPerbaikanKontainer succeeded! Created {$result} records");

                // Check if nomor_memo_perbaikan was generated
                $perbaikanRecords = PerbaikanKontainer::where('kontainer_id', $kontainer->id)
                    ->whereDate('tanggal_perbaikan', $tanggalPerbaikan)
                    ->get();

                foreach ($perbaikanRecords as $perbaikan) {
                    $this->info("✅ Perbaikan record created with memo: {$perbaikan->nomor_memo_perbaikan}");
                    $this->info("   - Status: {$perbaikan->status_perbaikan}");
                    $this->info("   - Deskripsi: {$perbaikan->deskripsi_perbaikan}");
                }

                // Verify memo format
                if (preg_match('/^MP\d{13}$/', $perbaikan->nomor_memo_perbaikan)) {
                    $this->info("✅ Nomor memo format is correct: {$perbaikan->nomor_memo_perbaikan}");
                } else {
                    $this->error("❌ Nomor memo format is incorrect: {$perbaikan->nomor_memo_perbaikan}");
                }

            } else {
                $this->error('❌ createPerbaikanKontainer failed or returned 0');
            }

            DB::rollBack(); // Rollback to avoid creating test data in production
            $this->info('✅ Test completed successfully! All changes rolled back.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Test failed: {$e->getMessage()}");
            $this->error("Stack trace:\n{$e->getTraceAsString()}");
            return 1;
        }

        return 0;
    }
}
