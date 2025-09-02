<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

class UpdateKontainerPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontainer:update-periods {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update periods for ongoing containers (containers without end date)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting container period update...');

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be saved to database');
        }

        // Ambil kontainer yang masih berjalan (tanggal_akhir null)
        $ongoingContainers = DaftarTagihanKontainerSewa::whereNull('tanggal_akhir')
            ->whereNotNull('tanggal_awal')
            ->get();

        if ($ongoingContainers->isEmpty()) {
            $this->info('✅ No ongoing containers found (all containers have end dates)');
            return Command::SUCCESS;
        }

        $this->info("📦 Found {$ongoingContainers->count()} ongoing containers");
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($ongoingContainers as $container) {
            try {
                $startDate = Carbon::parse($container->tanggal_awal)->startOfDay();
                $currentDate = Carbon::now()->startOfDay();

                // Hitung periode berdasarkan selisih bulan (konversi ke integer)
                $monthsDiff = intval($startDate->diffInMonths($currentDate));
                $calculatedPeriode = $monthsDiff + 1;

                $currentPeriode = $container->periode ?? 1;

                // Update hanya jika periode yang dihitung lebih besar
                if ($calculatedPeriode > $currentPeriode) {
                    $this->line(sprintf(
                        "📊 %s: %s -> %s (Start: %s, %d months)",
                        $container->nomor_kontainer ?? 'Unknown',
                        $currentPeriode,
                        $calculatedPeriode,
                        $startDate->format('Y-m-d'),
                        $monthsDiff
                    ));

                    if (!$isDryRun) {
                        $container->update(['periode' => $calculatedPeriode]);
                    }

                    $updated++;
                } else {
                    $this->comment(sprintf(
                        "⏭️  %s: Period %s unchanged (calculated: %s)",
                        $container->nomor_kontainer ?? 'Unknown',
                        $currentPeriode,
                        $calculatedPeriode
                    ));
                    $skipped++;
                }

            } catch (\Exception $e) {
                $this->error(sprintf(
                    "❌ Error processing %s: %s",
                    $container->nomor_kontainer ?? 'Unknown',
                    $e->getMessage()
                ));
                $errors++;
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info("🔍 DRY RUN SUMMARY:");
            $this->info("  📈 Would update: {$updated} containers");
            $this->info("  ⏭️  Would skip: {$skipped} containers");
            $this->info("  ❌ Errors: {$errors} containers");
            $this->newLine();
            $this->comment("💡 Run without --dry-run to apply changes");
        } else {
            $this->info("✅ UPDATE COMPLETED:");
            $this->info("  📈 Updated: {$updated} containers");
            $this->info("  ⏭️  Skipped: {$skipped} containers");
            $this->info("  ❌ Errors: {$errors} containers");
        }

        return Command::SUCCESS;
    }
}
