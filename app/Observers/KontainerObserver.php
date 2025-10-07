<?php

namespace App\Observers;

use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Support\Facades\Log;

class KontainerObserver
{
    /**
     * Handle the Kontainer "created" event.
     */
    public function created(Kontainer $kontainer): void
    {
        $this->handleDuplicateValidation($kontainer, 'created');
    }

    /**
     * Handle the Kontainer "updated" event.
     */
    public function updated(Kontainer $kontainer): void
    {
        // Only check if nomor_seri_gabungan was changed
        if ($kontainer->wasChanged('nomor_seri_gabungan')) {
            $this->handleDuplicateValidation($kontainer, 'updated');
        }
    }

    /**
     * Handle the Kontainer "deleted" event.
     */
    public function deleted(Kontainer $kontainer): void
    {
        // When kontainer is deleted, reactivate stock kontainer if exists
        $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $kontainer->nomor_seri_gabungan)
                                       ->where('status', 'inactive')
                                       ->first();
        
        if ($stockKontainer) {
            $stockKontainer->update(['status' => 'available']);
            Log::info("Kontainer {$kontainer->nomor_seri_gabungan} deleted, stock kontainer reactivated");
        }
    }

    /**
     * Handle duplicate validation when kontainer is created or updated
     */
    private function handleDuplicateValidation(Kontainer $kontainer, string $action): void
    {
        // Find any stock kontainer with same nomor_seri_gabungan
        $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $kontainer->nomor_seri_gabungan)
                                       ->where('status', '!=', 'inactive')
                                       ->first();
        
        if ($stockKontainer) {
            $stockKontainer->update(['status' => 'inactive']);
            Log::info("Kontainer {$kontainer->nomor_seri_gabungan} {$action}, duplicate stock kontainer set to inactive");
        }
    }
}
