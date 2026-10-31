<?php

// Run without arguments to inspect. Add --execute to apply this specific repair.
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PranotaUangRit;
use App\Models\PranotaUangRitKenek;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Services\PranotaUangRitKenekStatus;
use App\Services\RekapUangRitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$execute = in_array('--execute', $argv, true);

try {
    DB::transaction(function () use ($execute): void {
        $pranota = PranotaUangRitKenek::where('no_pranota', 'PURK-10-26-000043')->lockForUpdate()->sole();
        if ($pranota->id !== 48 || $pranota->status !== 'draft'
            || $pranota->created_at->format('Y-m-d H:i:s') !== '2026-10-31 11:07:54') {
            throw new RuntimeException('Pranota no longer matches the investigated draft. No repair applied.');
        }

        $expected = [
            'JB10036317', 'JB10036334', 'JP0014371', 'JP0014376', 'JB10036347',
            'JB10036364', 'JB10036367', 'JP0014387', 'JB10036378', 'JP0014397',
            'JP0014400', 'JP0014301', 'JB10036397', 'JB10036404', 'JB10036411',
            'SS0003769', 'SS0003772', 'SS0003781',
        ];
        $references = (new RekapUangRitService)->references($pranota);
        if ($references->count() !== 18 || $references->pluck('number')->sort()->values()->all() !== collect($expected)->sort()->values()->all()) {
            throw new RuntimeException('The 18 surat jalan references have changed. No repair applied.');
        }

        $plans = [];
        foreach ($references as $reference) {
            $bongkaran = $reference['bongkaran'];
            $model = $bongkaran ? SuratJalanBongkaran::class : SuratJalan::class;
            $numberColumn = $bongkaran ? 'nomor_surat_jalan' : 'no_surat_jalan';
            $suratJalan = $model::where($numberColumn, $reference['number'])->lockForUpdate()->sole();
            $log = DB::table('audit_logs')->where('auditable_type', $model)
                ->where('auditable_id', $suratJalan->id)->whereBetween('id', [386997, 387014])
                ->where('created_at', '2026-10-31 11:07:54')->sole();
            $old = json_decode($log->old_values, true, 512, JSON_THROW_ON_ERROR);
            $new = json_decode($log->new_values, true, 512, JSON_THROW_ON_ERROR);
            $field = 'status_pembayaran_uang_rit';
            if (parse_url($log->url, PHP_URL_PATH) !== '/pranota-uang-rit-kenek'
                || ! array_key_exists($field, $old) || ! array_key_exists($field, $new)
                || $old[$field] !== ($bongkaran ? null : 'belum_dibayar')
                || $new[$field] !== ($bongkaran ? 'lunas' : 'sudah_masuk_pranota')) {
                throw new RuntimeException('Audit evidence differs for '.$reference['number']);
            }
            if (! in_array($suratJalan->$field, [$old[$field], $new[$field]], true)) {
                throw new RuntimeException('Driver status changed since investigation: '.$reference['number']);
            }
            $allowedKenek = $bongkaran ? [null, 'belum_bayar', 'lunas'] : ['belum_dibayar', 'sudah_masuk_pranota'];
            if (! in_array($suratJalan->status_pembayaran_uang_rit_kenek, $allowedKenek, true)) {
                throw new RuntimeException('Kenek status changed since investigation: '.$reference['number']);
            }
            $plans[] = ['model' => $suratJalan, 'reference' => $reference, 'restore' => $old[$field], 'audit_id' => $log->id];
        }

        $driverPranotas = PranotaUangRit::whereNotIn('status', ['cancelled'])->lockForUpdate()->get();
        foreach ($plans as $plan) {
            $reference = $plan['reference'];
            $idField = $reference['bongkaran'] ? 'surat_jalan_bongkaran_id' : 'surat_jalan_id';
            foreach ($driverPranotas as $driverPranota) {
                $byNumber = (new RekapUangRitService)->references($driverPranota)->contains(fn ($item) => $item === $reference);
                if ($byNumber || (string) $driverPranota->$idField === (string) $plan['model']->id) {
                    throw new RuntimeException('A driver pranota now references '.$reference['number'].': '.$driverPranota->no_pranota);
                }
            }
        }

        $snapshot = array_map(fn ($plan) => [
            'table' => $plan['model']->getTable(), 'id' => $plan['model']->id,
            'nomor' => $plan['reference']['number'], 'audit_id' => $plan['audit_id'],
            'status_supir_before' => $plan['model']->status_pembayaran_uang_rit,
            'status_kenek_before' => $plan['model']->status_pembayaran_uang_rit_kenek,
            'restore_supir' => $plan['restore'],
        ], $plans);
        echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
        if (! $execute) {
            echo 'DRY RUN: 18 records checked; no data changed.'.PHP_EOL;

            return;
        }

        $backupPath = storage_path('app/repairs/kenek-PURK-10-26-000043-'.now()->format('Ymd-His').'.json');
        File::ensureDirectoryExists(dirname($backupPath));
        if (File::put($backupPath, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) === false) {
            throw new RuntimeException('Cannot save the status backup. No repair applied.');
        }
        $restored = 0;
        foreach ($plans as $plan) {
            if ($plan['model']->status_pembayaran_uang_rit !== $plan['restore']) {
                $plan['model']->update(['status_pembayaran_uang_rit' => $plan['restore']]);
                $restored++;
            }
        }
        app(PranotaUangRitKenekStatus::class)->sync($pranota, SuratJalan::STATUS_UANG_RIT_SUDAH_MASUK_PRANOTA);
        echo 'Restored driver statuses: '.$restored.PHP_EOL;
        echo 'Kenek statuses synchronized for the existing draft; no amounts changed.'.PHP_EOL;
        echo 'Backup: '.$backupPath.PHP_EOL;
    });
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage().PHP_EOL);
    exit(1);
}
