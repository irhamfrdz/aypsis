<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateSuratJalanTarikKosongBatamKontainer extends Command
{
    protected $signature = 'surat-jalan-tarik-kosong-batam:update-kontainer
        {file? : File berisi pasangan nomor surat jalan dan nomor kontainer, atau teks bulk sembilan kolom}
        {--apply : Simpan perubahan ke database; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Perbarui nomor kontainer surat jalan tarik kosong Batam berdasarkan nomor surat jalan';

    public function handle(): int
    {
        $file = $this->argument('file') ?: base_path('database/data/surat-jalan-tarik-kosong-batam-kontainer-2026-09-25.csv');
        if (! is_file($file)) {
            $file = base_path($file);
        }

        if (! is_file($file) || ! is_readable($file)) {
            $this->error("File tidak dapat dibaca: {$file}");

            return self::FAILURE;
        }

        try {
            $mappings = $this->readMappings($file);
            $apply = (bool) $this->option('apply');

            // Kunci baris selama pemeriksaan dan pembaruan supaya hasilnya tetap konsisten.
            return $apply
                ? DB::transaction(fn () => $this->process($mappings, true))
                : $this->process($mappings, false);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    /** @return array<string, string> */
    private function readMappings(string $file): array
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new RuntimeException("File tidak dapat dibuka: {$file}");
        }

        $mappings = [];
        $line = 0;

        try {
            while (($fields = fgetcsv($handle, 0, ';')) !== false) {
                $line++;
                if ($fields === [null] || $fields === ['']) {
                    continue;
                }

                $suratJalan = trim((string) ($fields[0] ?? ''), " \t\n\r\0\x0B\xEF\xBB\xBF");
                if ($suratJalan === 'no_surat_jalan') {
                    continue;
                }

                // Format ringkas: SJ;kontainer. Format bulk: SJ;tanggal;kontainer;...
                $kontainer = count($fields) === 2 ? $fields[1] : ($fields[2] ?? '');
                $kontainer = strtoupper(trim((string) $kontainer));

                if (! preg_match('/^\d+$/', $suratJalan) || ! preg_match('/^[A-Z]{4}\d{7}$/', $kontainer)) {
                    throw new RuntimeException("Format nomor surat jalan atau kontainer tidak valid pada baris {$line}.");
                }

                if (isset($mappings[$suratJalan])) {
                    throw new RuntimeException("Nomor surat jalan {$suratJalan} muncul lebih dari sekali (baris {$line}).");
                }

                $mappings[$suratJalan] = $kontainer;
            }
        } finally {
            fclose($handle);
        }

        if ($mappings === []) {
            throw new RuntimeException('File tidak berisi pasangan nomor surat jalan dan kontainer.');
        }

        return $mappings;
    }

    /** @param array<string, string> $mappings */
    private function process(array $mappings, bool $apply): int
    {
        $query = DB::table('surat_jalan_tarik_kosong_batams')
            ->whereIn('no_surat_jalan', array_keys($mappings));

        if ($apply) {
            $query->lockForUpdate();
        }

        $existing = $query->get(['id', 'no_surat_jalan', 'no_kontainer'])
            ->keyBy('no_surat_jalan');

        $changes = [];
        $missing = [];
        $unchanged = 0;

        foreach ($mappings as $suratJalan => $kontainer) {
            $record = $existing->get($suratJalan);
            if ($record === null) {
                $missing[] = $suratJalan;
            } elseif ($record->no_kontainer === $kontainer) {
                $unchanged++;
            } else {
                $changes[] = [$suratJalan, $record->no_kontainer ?: '(kosong)', $kontainer, $record->id];
            }
        }

        $this->info('Jumlah data: '.count($mappings).', berubah: '.count($changes).", sudah sesuai: {$unchanged}, tidak ditemukan: ".count($missing));

        if ($changes !== []) {
            $this->table(['No. Surat Jalan', 'Kontainer lama', 'Kontainer baru'],
                array_map(fn ($change) => array_slice($change, 0, 3), $changes));
        }

        if ($missing !== []) {
            $this->warn('Nomor surat jalan tidak ditemukan: '.implode(', ', $missing));
            if ($apply) {
                throw new RuntimeException('Tidak ada data yang diubah karena ada nomor surat jalan yang tidak ditemukan.');
            }
        }

        if (! $apply) {
            $this->comment('Pratinjau saja. Tambahkan --apply untuk menyimpan perubahan.');

            return self::SUCCESS;
        }

        $now = now();
        foreach ($changes as [$suratJalan, , $kontainer, $id]) {
            DB::table('surat_jalan_tarik_kosong_batams')
                ->where('id', $id)
                ->update(['no_kontainer' => $kontainer, 'updated_at' => $now]);
        }

        $this->info('Berhasil memperbarui '.count($changes).' nomor kontainer.');

        return self::SUCCESS;
    }
}
