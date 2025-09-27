<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestPembayaranValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-pembayaran-validation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing pembayaran validation and storage...');

        // Get available pranota
        $pranota = \App\Models\PranotaPerbaikanKontainer::where('status', 'belum_dibayar')->first();

        if (!$pranota) {
            $this->error('No available pranota for testing');
            return;
        }

        $this->info("Found pranota ID: {$pranota->id} with total biaya: " . ($pranota->total_biaya ?? 0));

        // Test validation data
        $testData = [
            'pranota_perbaikan_kontainer_ids' => [$pranota->id],
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'nomor_pembayaran' => 'PPK-241001-000001',
            'nomor_cetakan' => 1,
            'bank' => 'Bank BCA',
            'jenis_transaksi' => 'Debit',
        ];

        // Test validation
        $rules = [
            'pranota_perbaikan_kontainer_ids' => 'required|array|min:1',
            'pranota_perbaikan_kontainer_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
            'tanggal_pembayaran' => 'required|date',
            'nomor_pembayaran' => 'required|string',
            'nomor_cetakan' => 'required|integer|min:1|max:9',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        $this->info('Validation passed!');

        // Test storage simulation
        try {
            $this->info('Testing storage simulation...');

            $pranotaIds = $testData['pranota_perbaikan_kontainer_ids'];
            $totalPembayaran = 0;

            // Calculate total payment
            foreach ($pranotaIds as $pranotaId) {
                $pranotaItem = \App\Models\PranotaPerbaikanKontainer::findOrFail($pranotaId);
                $totalPembayaran += $pranotaItem->total_biaya ?? 0;
                $this->info("Pranota {$pranotaId}: Rp " . number_format($pranotaItem->total_biaya ?? 0, 0, ',', '.'));
            }

            $this->info("Total pembayaran: Rp " . number_format($totalPembayaran, 0, ',', '.'));

            // Test creating payment record (without actually saving)
            $paymentData = [
                'pranota_perbaikan_kontainer_id' => $pranota->id,
                'tanggal_pembayaran' => $testData['tanggal_pembayaran'],
                'nominal_pembayaran' => $pranota->total_biaya ?? 0,
                'nomor_invoice' => $testData['nomor_pembayaran'],
                'metode_pembayaran' => 'transfer', // Use valid enum value
                'keterangan' => "Pembayaran pranota {$pranota->nomor_pranota} - {$testData['jenis_transaksi']} via {$testData['bank']}",
                'status_pembayaran' => 'paid',
                'created_by' => 1, // Assuming user ID 1 exists
                'updated_by' => 1,
            ];

            $this->info('Payment data to be created:');
            foreach ($paymentData as $key => $value) {
                $this->line("  {$key}: {$value}");
            }

            // Test if we can create the model (without saving)
            $payment = new \App\Models\PembayaranPranotaPerbaikanKontainer($paymentData);
            $this->info('Model created successfully (not saved)');

        } catch (\Exception $e) {
            $this->error('Error during storage test: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return 1;
        }

        $this->info('All tests passed!');
        return 0;
    }
}
