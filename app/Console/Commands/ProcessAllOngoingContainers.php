<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessAllOngoingContainers extends Command
{
    protected $signature = 'container:process-all-ongoing {--dry-run : Show what would be created without actually creating}';
    protected $description = 'Process all ongoing containers that need new periods';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info("=== PROCESSING ALL ONGOING CONTAINERS ===");
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }
        
        // Find containers that might need new periods
        // These are containers where current date > latest end date
        $currentDate = Carbon::now();
        
        $containersToProcess = DB::table('daftar_tagihan_kontainer_sewa as dts1')
            ->select('dts1.nomor_kontainer', 'dts1.vendor', 'dts1.tanggal_awal', 
                     DB::raw('MAX(dts1.periode) as max_periode'),
                     DB::raw('MAX(dts1.tanggal_akhir) as latest_end_date'))
            ->groupBy('dts1.nomor_kontainer', 'dts1.vendor', 'dts1.tanggal_awal')
            ->havingRaw('MAX(dts1.tanggal_akhir) < ?', [$currentDate->format('Y-m-d')])
            ->get();
        
        $this->info("Found {$containersToProcess->count()} containers that may need new periods");
        
        if ($containersToProcess->count() == 0) {
            $this->info("No containers need processing.");
            return 0;
        }
        
        $totalCreated = 0;
        
        foreach ($containersToProcess as $container) {
            $this->info("\n--- Processing {$container->nomor_kontainer} ---");
            $this->line("Latest periode: {$container->max_periode}");
            $this->line("Latest end date: {$container->latest_end_date}");
            
            $created = $this->processContainer($container, $dryRun);
            $totalCreated += $created;
            
            if ($created > 0) {
                $this->info("✅ Created {$created} period(s) for {$container->nomor_kontainer}");
            } else {
                $this->line("→ No periods needed for {$container->nomor_kontainer}");
            }
        }
        
        $this->info("\n🎉 SUMMARY: Created {$totalCreated} total periods across {$containersToProcess->count()} containers");
        
        return 0;
    }
    
    private function processContainer($containerInfo, $dryRun = false)
    {
        // Get the full latest record
        $latestRecord = DaftarTagihanKontainerSewa::where('nomor_kontainer', $containerInfo->nomor_kontainer)
            ->where('vendor', $containerInfo->vendor)
            ->where('tanggal_awal', $containerInfo->tanggal_awal)
            ->orderBy('periode', 'desc')
            ->first();
            
        if (!$latestRecord) {
            return 0;
        }
        
        $currentDate = Carbon::now();
        $lastEndDate = Carbon::parse($latestRecord->tanggal_akhir);
        
        // Calculate periods to create
        $periodsToCreate = [];
        $nextPeriode = $latestRecord->periode + 1;
        
        $periodeStart = $lastEndDate->copy()->addDay();
        $periodeEnd = $periodeStart->copy()->addMonthsNoOverflow(1)->subDay();
        
        // Create periods until we cover the current date
        while ($periodeStart->lessThanOrEqualTo($currentDate)) {
            $periodsToCreate[] = [
                'periode' => $nextPeriode,
                'start' => $periodeStart->copy(),
                'end' => $periodeEnd->copy()
            ];
            
            $this->line("  Will create periode {$nextPeriode}: {$periodeStart->format('Y-m-d')} to {$periodeEnd->format('Y-m-d')}");
            
            $nextPeriode++;
            $periodeStart = $periodeEnd->copy()->addDay();
            $periodeEnd = $periodeStart->copy()->addMonthsNoOverflow(1)->subDay();
        }
        
        if (empty($periodsToCreate)) {
            return 0;
        }
        
        if ($dryRun) {
            $this->warn("  [DRY RUN] Would create " . count($periodsToCreate) . " period(s)");
            return count($periodsToCreate);
        }
        
        // Create the periods
        DB::beginTransaction();
        
        try {
            foreach ($periodsToCreate as $period) {
                $this->createPeriode($latestRecord, $period['periode'], $period['start'], $period['end']);
            }
            
            DB::commit();
            return count($periodsToCreate);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("  ❌ Error creating periods for {$containerInfo->nomor_kontainer}: " . $e->getMessage());
            return 0;
        }
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
        $newRecord->tanggal_akhir = $endDate->format('Y-m-d');
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