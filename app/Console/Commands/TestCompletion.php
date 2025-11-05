<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;

class TestCompletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:completion {order_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test completion logic for orders and surat jalan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 TESTING COMPLETION LOGIC...');
        $this->info('==============================');

        $orderId = $this->argument('order_id');

        if ($orderId) {
            $this->testSpecificOrder($orderId);
        } else {
            $this->testAllOrdersWithSuratJalan();
        }

        return 0;
    }

    private function testSpecificOrder($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            $this->error("Order #{$orderId} tidak ditemukan!");
            return;
        }

        $this->info("📋 Order: {$order->nomor_order}");
        $this->info("   Units: {$order->units}");
        $this->info("   Sisa: {$order->sisa}");
        $this->info("   Status: {$order->outstanding_status}");
        $this->info("   Completion: {$order->completion_percentage}%");
        
        $suratJalans = SuratJalan::where('order_id', $orderId)->get();
        $this->info("   Surat Jalan: {$suratJalans->count()} dokumen");
        
        foreach ($suratJalans as $sj) {
            $this->line("     - {$sj->no_surat_jalan}: {$sj->jumlah_kontainer} kontainer");
        }
    }

    private function testAllOrdersWithSuratJalan()
    {
        $orders = Order::whereHas('suratJalans')->with('suratJalans')->get();
        
        $this->info("📊 Ditemukan {$orders->count()} order dengan surat jalan");
        
        foreach ($orders as $order) {
            $totalKontainer = $order->suratJalans->sum('jumlah_kontainer');
            $expectedSisa = $order->units - $totalKontainer;
            $expectedCompletion = $totalKontainer > 0 ? round(($totalKontainer / $order->units) * 100, 2) : 0;
            
            $this->line("");
            $this->line("📋 Order: {$order->nomor_order}");
            $this->line("   Units: {$order->units} | Sisa: {$order->sisa} | Completion: {$order->completion_percentage}%");
            $this->line("   Total Kontainer SJ: {$totalKontainer}");
            $this->line("   Expected Sisa: {$expectedSisa} | Expected Completion: {$expectedCompletion}%");
            
            if ($order->sisa != $expectedSisa || $order->completion_percentage != $expectedCompletion) {
                $this->error("   ❌ MISMATCH! Perlu diperbaiki");
                
                // Fix it
                $order->sisa = max(0, $expectedSisa);
                $order->updateOutstandingStatus();
                $order->save();
                
                $this->info("   ✅ Fixed! New Sisa: {$order->sisa}, New Completion: {$order->completion_percentage}%");
            } else {
                $this->info("   ✅ OK");
            }
        }
    }
}
