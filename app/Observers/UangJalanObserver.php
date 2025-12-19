<?php

namespace App\Observers;

use App\Models\UangJalan;
use Illuminate\Support\Facades\Log;

class UangJalanObserver
{
    /**
     * Handle the UangJalan "updated" event.
     * Update pranota total when uang jalan is updated
     */
    public function updated(UangJalan $uangJalan)
    {
        // Hanya update pranota jika uang jalan sudah masuk pranota
        if ($uangJalan->status === 'sudah_masuk_pranota') {
            $this->updateRelatedPranotaTotals($uangJalan);
        }
    }

    /**
     * Handle the UangJalan "deleted" event.
     * Update pranota total when uang jalan is deleted
     */
    public function deleted(UangJalan $uangJalan)
    {
        // Update pranota total jika ada
        if ($uangJalan->status === 'sudah_masuk_pranota') {
            $this->updateRelatedPranotaTotals($uangJalan);
        }
    }

    /**
     * Update total amount for all related pranota uang jalan
     */
    protected function updateRelatedPranotaTotals(UangJalan $uangJalan)
    {
        try {
            // Ambil semua pranota yang terkait (bisa multiple karena many-to-many)
            $pranotaUangJalans = $uangJalan->pranotaUangJalan()->get();
            
            foreach ($pranotaUangJalans as $pranotaUangJalan) {
                // Recalculate total pranota berdasarkan semua uang jalan yang terkait
                $totalUangJalan = $pranotaUangJalan->uangJalans()->sum('jumlah_total');
                $jumlahUangJalan = $pranotaUangJalan->uangJalans()->count();
                
                $oldTotal = $pranotaUangJalan->total_amount;
                
                // Update pranota
                $pranotaUangJalan->update([
                    'jumlah_uang_jalan' => $jumlahUangJalan,
                    'total_amount' => $totalUangJalan,
                ]);
                
                Log::info('Pranota total auto-updated via observer', [
                    'pranota_id' => $pranotaUangJalan->id,
                    'pranota_nomor' => $pranotaUangJalan->nomor_pranota,
                    'old_total' => $oldTotal,
                    'new_total' => $totalUangJalan,
                    'jumlah_uang_jalan' => $jumlahUangJalan,
                    'trigger_uang_jalan_id' => $uangJalan->id,
                    'trigger_uang_jalan_nomor' => $uangJalan->nomor_uang_jalan,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating pranota totals in observer', [
                'uang_jalan_id' => $uangJalan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
