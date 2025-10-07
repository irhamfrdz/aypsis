<?php

namespace App\Observers;

use App\Models\StockKontainer;
use App\Models\Kontainer;
use Illuminate\Support\Facades\Log;

class StockKontainerObserver
{
    /**
     * Handle the StockKontainer "creating" event.
     */
    public function creating(StockKontainer $stockKontainer): void
    {
        $this->validateDuplicate($stockKontainer);
    }

    /**
     * Handle the StockKontainer "updating" event.
     */
    public function updating(StockKontainer $stockKontainer): void
    {
        // Only check if nomor_seri_gabungan is being changed
        if ($stockKontainer->isDirty('nomor_seri_gabungan')) {
            $this->validateDuplicate($stockKontainer);
        }
    }

    /**
     * Validate if nomor kontainer exists in kontainers table
     */
    private function validateDuplicate(StockKontainer $stockKontainer): void
    {
        if (!$stockKontainer->nomor_seri_gabungan) {
            return;
        }

        $existingKontainer = Kontainer::where('nomor_seri_gabungan', $stockKontainer->nomor_seri_gabungan)->first();
        
        if ($existingKontainer && $stockKontainer->status !== 'inactive') {
            $stockKontainer->status = 'inactive';
            
            Log::info("Auto-set stock kontainer {$stockKontainer->nomor_seri_gabungan} to inactive due to duplicate in kontainers table", [
                'stock_kontainer_id' => $stockKontainer->id,
                'existing_kontainer_id' => $existingKontainer->id
            ]);
        }
    }
}
