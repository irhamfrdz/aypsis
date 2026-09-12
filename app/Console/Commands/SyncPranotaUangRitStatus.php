<?php

namespace App\Console\Commands;

use App\Models\PranotaUangRit;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPranotaUangRitStatus extends Command
{
    protected $signature = 'pranota:sync-uang-rit-status
                            {--dry-run : Tampilkan perubahan tanpa menyimpan ke database}';

    protected $description = 'Sinkronkan status pembayaran surat jalan berdasarkan pranota uang rit';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun
            ? 'MODE DRY RUN: tidak ada perubahan yang disimpan.'
            : 'MODE LIVE: status surat jalan akan diperbarui.');

        $pranotas = PranotaUangRit::query()
            ->where(function ($query) {
                $query->where('status', '!=', PranotaUangRit::STATUS_CANCELLED)
                    ->orWhereNull('status');
            })
            ->get([
                'status',
                'surat_jalan_id',
                'surat_jalan_bongkaran_id',
                'no_surat_jalan',
            ]);

        $activeRegularIds = [];
        $activeBongkaranIds = [];
        $cancelledRegularIds = [];
        $cancelledBongkaranIds = [];
        $activeRegularNumbers = [];
        $activeBongkaranNumbers = [];
        $cancelledRegularNumbers = [];
        $cancelledBongkaranNumbers = [];

        foreach ($pranotas as $pranota) {
            $isCancelled = $pranota->status === PranotaUangRit::STATUS_CANCELLED;
            $regularIds = $isCancelled ? $cancelledRegularIds : $activeRegularIds;
            $bongkaranIds = $isCancelled ? $cancelledBongkaranIds : $activeBongkaranIds;

            if ($pranota->surat_jalan_id) {
                $regularIds[] = (int) $pranota->surat_jalan_id;
            }

            if ($pranota->surat_jalan_bongkaran_id) {
                $bongkaranIds[] = (int) $pranota->surat_jalan_bongkaran_id;
            }

            $regularNumbers = $isCancelled ? $cancelledRegularNumbers : $activeRegularNumbers;
            $bongkaranNumbers = $isCancelled ? $cancelledBongkaranNumbers : $activeBongkaranNumbers;

            foreach (explode(',', (string) $pranota->no_surat_jalan) as $number) {
                $number = trim($number);
                if ($number === '') {
                    continue;
                }

                if (preg_match('/\s*\(Bongkaran\)\s*$/i', $number)) {
                    $bongkaranNumbers[] = preg_replace('/\s*\(Bongkaran\)\s*$/i', '', $number);
                } else {
                    $regularNumbers[] = $number;
                }
            }

            if ($isCancelled) {
                $cancelledRegularIds = $regularIds;
                $cancelledBongkaranIds = $bongkaranIds;
                $cancelledRegularNumbers = $regularNumbers;
                $cancelledBongkaranNumbers = $bongkaranNumbers;
            } else {
                $activeRegularIds = $regularIds;
                $activeBongkaranIds = $bongkaranIds;
                $activeRegularNumbers = $regularNumbers;
                $activeBongkaranNumbers = $bongkaranNumbers;
            }
        }

        $activeRegularIds = $this->resolveRegularIds($activeRegularIds, $activeRegularNumbers);
        $activeBongkaranIds = $this->resolveBongkaranIds($activeBongkaranIds, $activeBongkaranNumbers);
        $cancelledRegularIds = $this->resolveRegularIds($cancelledRegularIds, $cancelledRegularNumbers);
        $cancelledBongkaranIds = $this->resolveBongkaranIds($cancelledBongkaranIds, $cancelledBongkaranNumbers);

        $activeRegularIds = array_values(array_unique($activeRegularIds));
        $activeBongkaranIds = array_values(array_unique($activeBongkaranIds));
        $cancelledRegularIds = array_values(array_diff(array_unique($cancelledRegularIds), $activeRegularIds));
        $cancelledBongkaranIds = array_values(array_diff(array_unique($cancelledBongkaranIds), $activeBongkaranIds));

        $updates = [
            'surat_jalan_dibayar' => [
                'model' => SuratJalan::class,
                'eligible' => fn ($query) => $query->where('rit', 'menggunakan_rit'),
                'active_ids' => $activeRegularIds,
                'cancelled_ids' => $cancelledRegularIds,
                'active_status' => SuratJalan::STATUS_UANG_RIT_DIBAYAR,
                'cancelled_status' => SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR,
            ],
            'surat_jalan_bongkaran_lunas' => [
                'model' => SuratJalanBongkaran::class,
                'eligible' => fn ($query) => $query->where(function ($q) {
                    $q->where('rit', 'menggunakan_rit')->orWhereNull('rit');
                }),
                'active_ids' => $activeBongkaranIds,
                'cancelled_ids' => $cancelledBongkaranIds,
                'active_status' => 'lunas',
                'cancelled_status' => 'belum_bayar',
            ],
        ];

        $totalUpdated = 0;
        $runner = function () use ($updates, $dryRun, &$totalUpdated): void {
            foreach ($updates as $label => $update) {
                $model = $update['model'];
                $activeQuery = ($model::query())
                    ->tap($update['eligible'])
                    ->whereIn('id', $update['active_ids'])
                    ->where(function ($query) use ($update) {
                        $query->where('status_pembayaran_uang_rit', '!=', $update['active_status'])
                            ->orWhereNull('status_pembayaran_uang_rit');
                    });
                $cancelledQuery = ($model::query())
                    ->tap($update['eligible'])
                    ->whereIn('id', $update['cancelled_ids'])
                    ->where(function ($query) use ($update) {
                        $query->where('status_pembayaran_uang_rit', '!=', $update['cancelled_status'])
                            ->orWhereNull('status_pembayaran_uang_rit');
                    });
                $orphanQuery = ($model::query())
                    ->tap($update['eligible'])
                    ->where(function ($query) use ($update) {
                        $query->where('status_pembayaran_uang_rit', '!=', $update['cancelled_status'])
                            ->orWhereNull('status_pembayaran_uang_rit');
                    })
                    ->when($update['active_ids'] !== [], function ($query) use ($update) {
                        $query->whereNotIn('id', $update['active_ids']);
                    });

                $activeCount = $activeQuery->count();
                $cancelledCount = $cancelledQuery->count();
                $orphanCount = $orphanQuery->count();
                $this->line("{$label}: {$activeCount} aktif, {$cancelledCount} pranota batal, {$orphanCount} tanpa pranota");

                if (! $dryRun) {
                    $activeQuery->update(['status_pembayaran_uang_rit' => $update['active_status']]);
                    $cancelledQuery->update(['status_pembayaran_uang_rit' => $update['cancelled_status']]);
                    $orphanQuery->update(['status_pembayaran_uang_rit' => $update['cancelled_status']]);
                }

                $totalUpdated += $activeCount + $cancelledCount + $orphanCount;
            }
        };

        if ($dryRun) {
            $runner();
        } else {
            DB::transaction($runner);
        }

        $this->info(($dryRun ? 'Perkiraan' : 'Total').' data diperbarui: '.$totalUpdated);

        return self::SUCCESS;
    }

    private function resolveRegularIds(array $ids, array $numbers): array
    {
        if ($numbers !== []) {
            $ids = array_merge($ids, SuratJalan::whereIn('no_surat_jalan', array_unique($numbers))->pluck('id')->all());
        }

        return $ids;
    }

    private function resolveBongkaranIds(array $ids, array $numbers): array
    {
        if ($numbers !== []) {
            $ids = array_merge($ids, SuratJalanBongkaran::whereIn('nomor_surat_jalan', array_unique($numbers))->pluck('id')->all());
        }

        return $ids;
    }
}
