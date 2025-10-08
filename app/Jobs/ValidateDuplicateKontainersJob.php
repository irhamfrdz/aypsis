<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateDuplicateKontainersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting duplicate kontainer validation job');

        try {
            // Find duplicates
            $duplicates = DB::select("
                SELECT sk.nomor_seri_gabungan, sk.status as stock_status, k.status as kontainer_status
                FROM stock_kontainers sk
                INNER JOIN kontainers k ON sk.nomor_seri_gabungan = k.nomor_seri_gabungan
                WHERE sk.nomor_seri_gabungan IS NOT NULL
                AND k.nomor_seri_gabungan IS NOT NULL
                AND sk.status != 'inactive'
            ");

            if (!empty($duplicates)) {
                $fixed = 0;

                foreach ($duplicates as $duplicate) {
                    DB::table('stock_kontainers')
                        ->where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                        ->update(['status' => 'inactive']);

                    $fixed++;
                    Log::info("Auto-fixed duplicate: {$duplicate->nomor_seri_gabungan} - Stock kontainer set to inactive");
                }

                Log::info("Duplicate validation job completed: {$fixed} duplicates fixed");
            } else {
                Log::info('Duplicate validation job completed: No duplicates found');
            }

        } catch (\Exception $e) {
            Log::error('Duplicate validation job failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
