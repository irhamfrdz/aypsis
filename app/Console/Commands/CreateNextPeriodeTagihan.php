<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

class CreateNextPeriodeTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:create-next-periode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create next periode tagihan based on container duration (like CSV logic)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating periode tagihan with CSV-like logic...');

        // Get distinct containers with their start dates and end dates
        $containers = DaftarTagihanKontainerSewa::select('vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir')
            ->groupBy('vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir')
            ->whereNotNull('tanggal_awal')
            ->get();

        $this->info('Found '.$containers->count().' unique containers.');

        $created = 0;

        foreach ($containers as $container) {
            try {
                $startDate = Carbon::parse($container->tanggal_awal);
                $currentDate = Carbon::now();

                // Calculate total periods needed like in CSV
                if ($container->tanggal_akhir) {
                    // Container with end date - calculate periods until end date
                    $endDate = Carbon::parse($container->tanggal_akhir);
                    $totalMonths = intval($startDate->diffInMonths($endDate));
                    $maxPeriode = $totalMonths + 1;
                } else {
                    // Ongoing container - calculate periods until now
                    $totalMonths = intval($startDate->diffInMonths($currentDate));
                    $maxPeriode = $totalMonths + 1;
                }

                // Ensure at least periode 1
                $maxPeriode = max(1, $maxPeriode);

                // Get existing max periode for this container
                $existingMaxPeriode = DaftarTagihanKontainerSewa::where('vendor', $container->vendor)
                    ->where('nomor_kontainer', $container->nomor_kontainer)
                    ->where('tanggal_awal', $container->tanggal_awal)
                    ->max('periode') ?? 0;

                // Create missing periods
                for ($periode = $existingMaxPeriode + 1; $periode <= $maxPeriode; $periode++) {
                    // Calculate period dates
                    $periodStart = $startDate->copy()->addMonthsNoOverflow($periode - 1);
                    $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();

                    // Cap by container end date if exists
                    if ($container->tanggal_akhir) {
                        $containerEnd = Carbon::parse($container->tanggal_akhir);
                        if ($periodEnd->gt($containerEnd)) {
                            $periodEnd = $containerEnd;
                        }
                    }

                    // Get base data from periode 1
                    $baseRecord = DaftarTagihanKontainerSewa::where('vendor', $container->vendor)
                        ->where('nomor_kontainer', $container->nomor_kontainer)
                        ->where('tanggal_awal', $container->tanggal_awal)
                        ->where('periode', 1)
                        ->first();

                    if (!$baseRecord) {
                        continue; // Skip if no base record
                    }

                    $attributes = [
                        'vendor' => $container->vendor,
                        'nomor_kontainer' => $container->nomor_kontainer,
                        'tanggal_awal' => $container->tanggal_awal,
                        'periode' => $periode,
                    ];

                    // Calculate period-specific values
                    $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
                    $daysInFullMonth = $periodStart->daysInMonth;
                    $isFullMonth = $daysInPeriod >= $daysInFullMonth;

                    $baseDpp = (float) ($baseRecord->dpp ?? 0);
                    $periodDpp = $isFullMonth ? $baseDpp : round($baseDpp * ($daysInPeriod / $daysInFullMonth), 2);

                    $values = [
                        'size' => $baseRecord->size,
                        'group' => $baseRecord->group,
                        'tanggal_akhir' => $container->tanggal_akhir,
                        'masa' => $this->generateMasaString($periodStart, $periodEnd),
                        'tarif' => $isFullMonth ? 'Bulanan' : 'Harian',
                        'dpp' => $periodDpp,
                        'dpp_nilai_lain' => round($periodDpp * 11 / 12, 2),
                        'ppn' => round(($periodDpp * 11 / 12) * 0.12, 2),
                        'pph' => round($periodDpp * 0.02, 2),
                        'grand_total' => round($periodDpp + (($periodDpp * 11 / 12) * 0.12) - ($periodDpp * 0.02), 2),
                        'status' => $baseRecord->status,
                    ];

                    $row = DaftarTagihanKontainerSewa::firstOrCreate($attributes, $values);

                    if ($row->wasRecentlyCreated) {
                        $created++;
                        $this->line("Created periode={$periode} for container {$container->nomor_kontainer} (vendor {$container->vendor})");
                    }
                }

            } catch (\Exception $e) {
                $this->error("Error processing container {$container->nomor_kontainer}: " . $e->getMessage());
                continue;
            }
        }

        $this->info('Created '.$created.' new periode tagihan.');
        return 0;
    }

    /**
     * Generate masa string in Indonesian format
     */
    private function generateMasaString($start, $end)
    {
        $months = [
            1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
            5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
            9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
        ];

        $startStr = $start->format('j') . ' ' . $months[(int)$start->format('n')] . ' ' . $start->format('Y');
        $endStr = $end->format('j') . ' ' . $months[(int)$end->format('n')] . ' ' . $end->format('Y');

        return $startStr . ' - ' . $endStr;
    }
}
