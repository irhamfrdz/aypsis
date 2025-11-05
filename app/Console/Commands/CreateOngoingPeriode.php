<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateOngoingPeriode extends Command
{
    protected $signature = 'container:create-ongoing {container=RXTU4540180}';
    protected $description = 'Create ongoing periode for container that should continue';

    public function handle()
    {
        $containerNo = $this->argument('container');
        
        $this->info("=== CREATING ONGOING PERIODE FOR: $containerNo ===");
        
        // Get the latest record for this container
        $latestRecord = DaftarTagihanKontainerSewa::where('nomor_kontainer', $containerNo)
            ->orderBy('periode', 'desc')
            ->first();
            
        if (!$latestRecord) {
            $this->error("Container $containerNo not found!");
            return 1;
        }
        
        $this->info("Found latest periode: {$latestRecord->periode}");
        $this->info("Latest end date: {$latestRecord->tanggal_akhir}");
        
        // Check if we need to create next periode
        $currentDate = Carbon::now();
        $lastEndDate = Carbon::parse($latestRecord->tanggal_akhir);
        
        if ($currentDate->lessThanOrEqualTo($lastEndDate)) {
            $this->warn("Current date ({$currentDate->format('Y-m-d')}) is not past the last end date ({$lastEndDate->format('Y-m-d')})");
            $this->warn("No new periode needed yet.");
            return 0;
        }
        
        // Calculate how many periods we need to create
        $periodsToCreate = [];
        $nextPeriode = $latestRecord->periode + 1;
        $startDate = Carbon::parse($latestRecord->tanggal_awal);
        
        $this->info("Current date: {$currentDate->format('Y-m-d')}");
        $this->info("Last end date: {$lastEndDate->format('Y-m-d')}");
        $this->info("Next periode to create: $nextPeriode");
        
        // We need to create periode starting from the next one
        $periodeStart = $lastEndDate->copy()->addDay(); // Start day after last end
        $periodeEnd = $periodeStart->copy()->addMonthsNoOverflow(1)->subDay();
        
        $this->info("Next periode 8 would be: {$periodeStart->format('Y-m-d')} to {$periodeEnd->format('Y-m-d')}");
        
        // Create periods until we cover the current date
        while ($periodeStart->lessThanOrEqualTo($currentDate)) {
            $periodsToCreate[] = [
                'periode' => $nextPeriode,
                'start' => $periodeStart->copy(),
                'end' => $periodeEnd->copy()
            ];
            
            $this->line("Will create periode $nextPeriode: {$periodeStart->format('Y-m-d')} to {$periodeEnd->format('Y-m-d')}");
            
            // Move to next period
            $nextPeriode++;
            $periodeStart = $periodeEnd->copy()->addDay();
            $periodeEnd = $periodeStart->copy()->addMonthsNoOverflow(1)->subDay();
        }
        
        if (empty($periodsToCreate)) {
            $this->info("No periods need to be created.");
            return 0;
        }
        
        $this->info("Will create " . count($periodsToCreate) . " periode(s):");
        foreach ($periodsToCreate as $period) {
            $this->line("  Periode {$period['periode']}: {$period['start']->format('Y-m-d')} to {$period['end']->format('Y-m-d')}");
        }
        
        if (!$this->confirm('Continue creating these periods?')) {
            $this->info('Cancelled.');
            return 0;
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($periodsToCreate as $period) {
                $this->createPeriode($latestRecord, $period['periode'], $period['start'], $period['end']);
                $this->info("✅ Created periode {$period['periode']}");
            }
            
            DB::commit();
            $this->info("🎉 Successfully created " . count($periodsToCreate) . " periode(s) for container $containerNo");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error creating periods: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createPeriode($baseRecord, $periode, $startDate, $endDate)
    {
        // Calculate period-specific values
        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        $daysInFullMonth = $startDate->daysInMonth;
        $isFullMonth = $daysInPeriod >= $daysInFullMonth;
        
        $baseDpp = (float) ($baseRecord->dpp ?? 0);
        $periodDpp = $isFullMonth ? $baseDpp : round($baseDpp * ($daysInPeriod / $daysInFullMonth), 2);
        
        $newRecord = new DaftarTagihanKontainerSewa();
        $newRecord->vendor = $baseRecord->vendor;
        $newRecord->nomor_kontainer = $baseRecord->nomor_kontainer;
        $newRecord->size = $baseRecord->size;
        $newRecord->group = $baseRecord->group;
        $newRecord->tanggal_awal = $baseRecord->tanggal_awal;
        $newRecord->tanggal_akhir = $endDate->format('Y-m-d'); // Update end date to this periode
        $newRecord->periode = $periode;
        $newRecord->masa = $this->generateMasaString($startDate, $endDate);
        $newRecord->tarif = $isFullMonth ? 'Bulanan' : 'Harian';
        $newRecord->dpp = $periodDpp;
        $newRecord->dpp_nilai_lain = round($periodDpp * 11 / 12, 2);
        $newRecord->ppn = round(($periodDpp * 11 / 12) * 0.12, 2);
        $newRecord->pph = round($periodDpp * 0.02, 2);
        $newRecord->grand_total = round($periodDpp + (($periodDpp * 11 / 12) * 0.12) - ($periodDpp * 0.02), 2);
        
        $newRecord->save();
        
        return $newRecord;
    }
    
    private function generateMasaString($startDate, $endDate)
    {
        return $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
    }
}