<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class FixOutstandingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fix-outstanding-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix outstanding status for orders that should be active instead of pending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 MEMPERBAIKI STATUS OUTSTANDING ORDERS...');
        $this->info('==============================================');

        try {
            // Get orders with confirmed status but pending outstanding_status
            $ordersToFix = Order::where('status', 'confirmed')
                                ->where('outstanding_status', 'pending')
                                ->where('sisa', '>', 0)
                                ->get();

            $this->info("📊 Ditemukan {$ordersToFix->count()} order yang perlu diperbaiki");

            if ($ordersToFix->count() === 0) {
                $this->info('✅ Tidak ada order yang perlu diperbaiki');
                return 0;
            }

            $fixed = 0;
            foreach ($ordersToFix as $order) {
                // Update outstanding_status based on completion
                if ($order->sisa == $order->units) {
                    // No progress yet, should be pending but ready to process
                    $order->outstanding_status = 'pending';
                    $order->completion_percentage = 0.00;
                } elseif ($order->sisa > 0 && $order->sisa < $order->units) {
                    // Partial progress
                    $order->outstanding_status = 'partial';
                    $order->completion_percentage = round((($order->units - $order->sisa) / $order->units) * 100, 2);
                } elseif ($order->sisa <= 0) {
                    // Completed
                    $order->outstanding_status = 'completed';
                    $order->completion_percentage = 100.00;
                    $order->completed_at = now();
                }

                $order->save();

                $this->line("✅ Fixed Order #{$order->nomor_order} - Status: {$order->outstanding_status} ({$order->completion_percentage}%)");
                $fixed++;
            }

            $this->info('');
            $this->info("🎉 SELESAI! {$fixed} order berhasil diperbaiki.");
            $this->info('📈 Outstanding status sekarang sudah sesuai dengan kondisi sebenarnya.');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }
    }
}
