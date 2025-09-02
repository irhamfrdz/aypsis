<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncTagihanPeriode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:sync-periode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize periode for tagihan_kontainer_sewa based on tanggal_harga_awal (increments each full month)';

    public function handle()
    {
        $this->info('Starting periode sync...');
        $now = Carbon::now();

        $rows = DB::table('tagihan_kontainer_sewa')
            ->whereNotNull('tanggal_harga_awal')
            ->get(['id', 'tanggal_harga_awal', 'periode']);

        $updated = 0;
        foreach ($rows as $r) {
            // Normalize any stored float-like periode values (e.g. '1.0210...') to integer floor
            if (!is_null($r->periode) && $r->periode !== '' && is_numeric($r->periode)) {
                $pFloat = (float) $r->periode;
                $pInt = (int) floor($pFloat);
                if ($pFloat !== (float) $pInt) {
                    DB::table('tagihan_kontainer_sewa')->where('id', $r->id)->update(['periode' => (string)$pInt, 'updated_at' => now()]);
                    $updated++;
                    Log::info('Normalized tagihan periode stored as float', ['id' => $r->id, 'from' => $r->periode, 'to' => $pInt]);
                    // update local variable so further logic uses normalized value
                    $r->periode = (string)$pInt;
                }
            }
            try {
                $start = Carbon::parse($r->tanggal_harga_awal);
            } catch (\Exception $e) {
                continue;
            }

            // diffInMonths may return fractional months; use floor to count only full months
            $months = (int) floor($start->diffInMonths($now));
            $desired = 1 + $months; // periode starts at 1 and increments per full month

            $current = is_null($r->periode) || $r->periode === '' ? 0 : (int)$r->periode;
            if ($current < $desired) {
                DB::table('tagihan_kontainer_sewa')->where('id', $r->id)->update(['periode' => (string)((int)$desired), 'updated_at' => now()]);
                $updated++;
                Log::info('Updated tagihan periode', ['id' => $r->id, 'from' => $current, 'to' => (int)$desired]);
            }
        }

        $this->info("Periode sync completed. Rows updated: {$updated}");
        return 0;
    }
}
