<?php

namespace App\Services;

use App\Models\SkSewa;
use App\Models\SkTagihanBulan;
use Carbon\Carbon;

class SewaKontainerBillingService
{
    /**
     * Generate billing periods for a sewa transaction.
     * Ported from generateBillingPeriodsForSewa() in utils.ts
     */
    public function generateBillingPeriods(SkSewa $sewa, ?string $currentDate = null): array
    {
        $periods = [];
        $startDate = Carbon::parse($sewa->tanggal_sewa);
        $today = $currentDate ? Carbon::parse($currentDate) : Carbon::today();

        // End limit: return date if completed, or today if still active
        $limitDate = $sewa->tanggal_kembali
            ? Carbon::parse($sewa->tanggal_kembali)
            : $today;

        $containerPart = str_replace(' ', '', trim($sewa->no_kontainer));
        $serialPart = $this->dateToExcelSerial($sewa->tanggal_sewa);

        $currStart = $startDate->copy();
        $index = 1;

        while ($currStart->lte($limitDate) || $index === 1) {
            $nextStart = $this->getNextCycleStart($currStart);
            $normalEndDate = $nextStart->copy()->subDay();

            $monthSuffix = str_pad($index, 2, '0', STR_PAD_LEFT);
            $kodeTagihan = "{$containerPart}{$serialPart}{$monthSuffix}";

            if ($normalEndDate->lte($limitDate)) {
                // Full billing cycle
                $days = $currStart->diffInDays($normalEndDate) + 1;
                $amount = $sewa->jenis_tarif === 'Bulanan'
                    ? $sewa->tarif_bulanan
                    : $days * $sewa->tarif_harian;

                $periods[] = [
                    'kode_tagihan' => $kodeTagihan,
                    'sewa_id' => $sewa->id,
                    'bulan_ke' => $index,
                    'tanggal_awal' => $currStart->format('Y-m-d'),
                    'tanggal_akhir' => $normalEndDate->format('Y-m-d'),
                    'jumlah_hari' => $days,
                    'tipe_tarif' => $sewa->jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN',
                    'jumlah_tagihan_estimasi' => (int) $amount,
                ];

                $currStart = $nextStart->copy();
                $index++;
            } else {
                // Prorate / partial period (last period)
                $endDate = $limitDate->lt($currStart) ? $currStart->copy() : $limitDate->copy();
                $days = $currStart->diffInDays($endDate) + 1;

                $amount = 0;
                $tipeTarif = 'PRORATE';

                if ($sewa->jenis_tarif === 'Harian') {
                    $amount = $days * $sewa->tarif_harian;
                    $tipeTarif = 'HARIAN';
                } else {
                    $month = $currStart->month;
                    $baseDays = 30;
                    if ($month === 2) {
                        $baseDays = $currStart->isLeapYear() ? 29 : 28;
                    }

                    if ($days === $baseDays) {
                        $amount = $sewa->tarif_bulanan;
                        $tipeTarif = 'BULANAN';
                    } else {
                        $dailyRate = $sewa->tarif_bulanan / $baseDays;
                        $amount = (int) round($days * $dailyRate);
                        $tipeTarif = 'PRORATE';
                    }
                }

                $periods[] = [
                    'kode_tagihan' => $kodeTagihan,
                    'sewa_id' => $sewa->id,
                    'bulan_ke' => $index,
                    'tanggal_awal' => $currStart->format('Y-m-d'),
                    'tanggal_akhir' => $endDate->format('Y-m-d'),
                    'jumlah_hari' => $days,
                    'tipe_tarif' => $tipeTarif,
                    'jumlah_tagihan_estimasi' => (int) $amount,
                ];

                break;
            }
        }

        return $periods;
    }

    /**
     * Sync billing periods to database for a sewa.
     * Creates new periods, updates existing ones, preserves user-modified fields.
     */
    public function syncBillingPeriods(SkSewa $sewa, ?string $currentDate = null): void
    {
        $generatedPeriods = $this->generateBillingPeriods($sewa, $currentDate);

        foreach ($generatedPeriods as $periodData) {
            $existing = SkTagihanBulan::where('kode_tagihan', $periodData['kode_tagihan'])->first();

            if ($existing) {
                // Only update system-generated fields, preserve user overrides
                $existing->update([
                    'tanggal_awal' => $periodData['tanggal_awal'],
                    'tanggal_akhir' => $periodData['tanggal_akhir'],
                    'jumlah_hari' => $periodData['jumlah_hari'],
                    'tipe_tarif' => $periodData['tipe_tarif'],
                    'jumlah_tagihan_estimasi' => $periodData['jumlah_tagihan_estimasi'],
                ]);
            } else {
                SkTagihanBulan::create($periodData);
            }
        }

        // Remove any periods that no longer exist (e.g., if return date was changed)
        $validCodes = collect($generatedPeriods)->pluck('kode_tagihan')->toArray();
        SkTagihanBulan::where('sewa_id', $sewa->id)
            ->whereNotIn('kode_tagihan', $validCodes)
            ->where('status_bayar', 'Belum Ditagih') // Only delete unbilled ones
            ->delete();
    }

    /**
     * Check if a sewa has any billed/paid tagihans
     */
    public function sewaHasInvoices(SkSewa $sewa): bool
    {
        return $sewa->tagihans()
            ->where('status_bayar', '!=', 'Belum Ditagih')
            ->exists();
    }

    /**
     * Convert date to Excel serial number (for billing code generation)
     */
    public function dateToExcelSerial($dateStr): int
    {
        try {
            $date = Carbon::parse($dateStr);
            $baseDate = Carbon::create(1899, 12, 30);

            return (int) $baseDate->diffInDays($date);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the next cycle start date (calendar month shift)
     */
    public function getNextCycleStart(Carbon $curr): Carbon
    {
        $year = $curr->year;
        $month = $curr->month;
        $day = $curr->day;

        $nextMonthSameDay = Carbon::create($year, $month, 1)->addMonth();

        try {
            $nextMonthSameDay->day($day);
        } catch (\Exception $e) {
            // Day doesn't exist in next month (e.g., Jan 31 -> Feb)
            // Move to 1st of the month after next
            return Carbon::create($year, $month, 1)->addMonths(2)->startOfMonth();
        }

        if ($nextMonthSameDay->day !== $day) {
            return Carbon::create($year, $month, 1)->addMonths(2)->startOfMonth();
        }

        return $nextMonthSameDay;
    }

    /**
     * Format number as Rupiah
     */
    public static function formatRupiah(int $num): string
    {
        $isNegative = $num < 0;
        $formatted = number_format(abs($num), 0, ',', '.');

        return ($isNegative ? '-' : '') . 'Rp ' . $formatted;
    }

    /**
     * Format date to Indonesian short format (dd Mmm yy)
     */
    public static function formatIndoDate(?string $dateStr): string
    {
        if (! $dateStr) {
            return '-';
        }

        try {
            $date = Carbon::parse($dateStr);
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            return $date->format('d') . ' ' . $months[$date->month - 1] . ' ' . $date->format('y');
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    /**
     * Parse flexible Indonesian date input to Y-m-d
     * Supports: ddmmyyyy, ddmmyy, dd/mm/yyyy, dd-mm-yyyy, "21 Mei 25", Excel serial
     */
    public static function parseInputDate(?string $input): ?string
    {
        if (! $input) {
            return null;
        }

        $trim = trim($input);
        if (! $trim) {
            return null;
        }

        // 1. Pure 8-digit: ddmmyyyy
        if (preg_match('/^\d{8}$/', $trim)) {
            $d = (int) substr($trim, 0, 2);
            $m = (int) substr($trim, 2, 2);
            $y = (int) substr($trim, 4, 4);
            if ($d > 0 && $d <= 31 && $m > 0 && $m <= 12 && $y >= 1900 && $y <= 2100) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }

        // 2. Pure 6-digit: ddmmyy
        if (preg_match('/^\d{6}$/', $trim)) {
            $d = (int) substr($trim, 0, 2);
            $m = (int) substr($trim, 2, 2);
            $y = (int) substr($trim, 4, 2);
            $y += ($y < 50 ? 2000 : 1900);
            if ($d > 0 && $d <= 31 && $m > 0 && $m <= 12) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }

        // 3. Excel serial number
        if (preg_match('/^\d+(\.\d+)?$/', $trim)) {
            $serial = (float) $trim;
            if ($serial > 0 && $serial < 100000) {
                $baseDate = Carbon::create(1899, 12, 30);
                $date = $baseDate->addDays((int) $serial);

                return $date->format('Y-m-d');
            }
        }

        // 4. Textual date: "21 Mei 25" or "21 May 2025"
        if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})$/', $trim, $matches)) {
            $d = (int) $matches[1];
            $monthWord = strtolower($matches[2]);
            $y = (int) $matches[3];
            if ($y < 100) {
                $y += ($y < 50 ? 2000 : 1900);
            }

            $monthMap = [
                'jan' => 1, 'januari' => 1, 'january' => 1,
                'feb' => 2, 'februari' => 2, 'february' => 2,
                'mar' => 3, 'maret' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'mei' => 5, 'may' => 5,
                'jun' => 6, 'juni' => 6, 'june' => 6,
                'jul' => 7, 'juli' => 7, 'july' => 7,
                'agt' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
                'sep' => 9, 'september' => 9,
                'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
                'nov' => 11, 'november' => 11,
                'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
            ];

            $prefix = strlen($monthWord) >= 3 ? substr($monthWord, 0, 3) : $monthWord;
            $m = $monthMap[$prefix] ?? $monthMap[$monthWord] ?? null;

            if ($m && $d > 0 && $d <= 31) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }

        // 5. dd/mm/yy or dd/mm/yyyy with slashes or dashes
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $trim, $matches)) {
            $d = (int) $matches[1];
            $m = (int) $matches[2];
            $y = (int) $matches[3];
            if ($y < 100) {
                $y += ($y < 50 ? 2000 : 1900);
            }
            if ($d > 0 && $d <= 31 && $m > 0 && $m <= 12) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }

        // 6. ISO format: yyyy-mm-dd
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $trim, $matches)) {
            $y = (int) $matches[1];
            $m = (int) $matches[2];
            $d = (int) $matches[3];

            return sprintf('%04d-%02d-%02d', $y, $m, $d);
        }

        return null;
    }
}
