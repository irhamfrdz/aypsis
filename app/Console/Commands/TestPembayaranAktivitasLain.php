<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestPembayaranAktivitasLain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-pembayaran-aktivitas-lain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test penyimpanan data pembayaran aktivitas lain dengan tipe penyesuaian array';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing penyimpanan data pembayaran aktivitas lain...');

        $data = [
            'nomor' => 'TEST'.time(),
            'tanggal' => '2025-12-09',
            'jenis_aktivitas' => 'Pembayaran Adjusment Uang Jalan',
            'jenis_penyesuaian' => 'pengurangan',
            'tipe_penyesuaian' => ['mel', 'parkir'],
            'no_surat_jalan' => 'SJ001',
            'penerima' => 'Test User',
            'keterangan' => 'Test pembayaran adjusment uang jalan',
            'jumlah' => 100000,
            'debit_kredit' => 'debit',
            'akun_coa_id' => 3,
            'akun_bank_id' => 3,
            'created_by' => 1,
        ];

        try {
            $pembayaran = \App\Models\PembayaranAktivitasLain::create($data);
            $this->info('✅ Data berhasil disimpan dengan ID: '.$pembayaran->id);
            $this->info('📋 Jenis aktivitas: '.$pembayaran->jenis_aktivitas);
            $this->info('📋 Jenis penyesuaian: '.$pembayaran->jenis_penyesuaian);
            $this->info('📋 Tipe penyesuaian: '.json_encode($pembayaran->tipe_penyesuaian));
            $this->info('📋 Jumlah: '.$pembayaran->jumlah);

            // Test retrieval
            $retrieved = \App\Models\PembayaranAktivitasLain::find($pembayaran->id);
            $this->info('🔄 Data berhasil diambil kembali:');
            $this->info('📋 Tipe penyesuaian (retrieved): '.json_encode($retrieved->tipe_penyesuaian));

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());
        }
    }
}
