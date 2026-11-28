<?php

namespace App\Console\Commands;

use App\Http\Controllers\BlController;
use App\Models\Manifest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class RepairManifestSr20jp26 extends Command
{
    protected $signature = 'manifest:repair-sr20jp26 {--apply : Simpan koreksi setelah backup dan verifikasi}';

    protected $description = 'Bersihkan 9 kontainer pindah AS22JP26 yang masih tercatat di SR20JP26';

    private const CONTAINERS = [
        10237 => 'AYPU2063007', 10321 => 'GLDU5759330',
        10326 => 'AYPU3250475', 10375 => 'MAGU5226741',
        10395 => 'AYPU3480644', 10413 => 'HJMU1531909',
        10414 => 'AYPU6524065', 10415 => 'AYPU6223171',
        10433 => 'AYPU3231406',
    ];

    private function voyage(string $table, string $voyage, string $ship)
    {
        return DB::table($table)->where('no_voyage', $voyage)
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), ' ', '')) = ?", [$ship]);
    }

    private function counts(array $exclude = []): array
    {
        $rows = Manifest::hydrate($this->voyage('manifests', 'SR20JP26', 'KMSRIWIJAYARAYA')->whereNotIn('id', $exclude)->get()->map(fn ($r) => (array) $r)->all());
        $method = new ReflectionMethod(BlController::class, 'buildRekapBongkaranPerincianItems');
        $items = $method->invoke(app(BlController::class), $rows)->keyBy('nama_barang');

        return [
            $items->get('Container Full 20 feet')['kuantitas'] ?? 0,
            $items->get('Container Full 40 feet')['kuantitas'] ?? 0,
        ];
    }

    public function handle(): int
    {
        try {
            DB::beginTransaction();
            $before = $this->counts();
            $remove = [];
            $destination = [];
            foreach (['naik_kapal', 'bls', 'manifests'] as $table) {
                $source = $this->voyage($table, 'SR20JP26', 'KMSRIWIJAYARAYA')
                    ->whereIn('prospek_id', array_keys(self::CONTAINERS))->lockForUpdate()->get();
                $target = $this->voyage($table, 'AS22JP26', 'KMALKENPESONA')
                    ->whereIn('prospek_id', array_keys(self::CONTAINERS))->lockForUpdate()->get();
                foreach (self::CONTAINERS as $prospekId => $container) {
                    $prospek = DB::table('prospek')->where('id', $prospekId)->lockForUpdate()->first();
                    if (! $prospek || $prospek->no_voyage !== 'AS22JP26'
                        || preg_replace('/[^A-Z0-9]/', '', strtoupper($prospek->nama_kapal)) !== 'KMALKENPESONA'
                        || $prospek->nomor_kontainer !== $container) {
                        throw new RuntimeException("Prospek {$prospekId} belum cocok dengan perpindahan AS22JP26.");
                    }
                    $matches = $target->where('prospek_id', $prospekId)->where('nomor_kontainer', $container);
                    if ($matches->count() !== 1) {
                        throw new RuntimeException("{$table}: tujuan {$container} harus memiliki tepat satu baris.");
                    }
                    $old = $source->where('prospek_id', $prospekId);
                    if ($old->count() > 1 || $old->contains(fn ($r) => $r->nomor_kontainer !== $container)) {
                        throw new RuntimeException("{$table}: sumber {$container} ambigu.");
                    }
                }
                foreach ($table === 'manifests' ? $source : [] as $row) {
                    foreach (['sudah_ob', 'supir_id', 'tanggal_ob', 'is_tl', 'ob_mobil_panjang', 'ob_pasangan_id'] as $field) {
                        if (! empty($row->$field)) {
                            throw new RuntimeException("{$table} ID {$row->id} memiliki aktivitas {$field}.");
                        }
                    }
                    if (isset($row->status_bongkar) && $row->status_bongkar !== 'Belum Bongkar') {
                        throw new RuntimeException("{$table} ID {$row->id} sudah diproses bongkar.");
                    }
                }
                // OB/BL can contain completed work and financial history. They are
                // evidence for this audit, not deletion targets for this report repair.
                if ($table !== 'manifests') {
                    $this->line("{$table}: {$source->count()} catatan lama dipertahankan sebagai riwayat OB/BL.");

                    continue;
                }
                $remove[$table] = $source;
                $destination[$table] = $target;
            }

            $this->table(['Tabel', 'Baris lama dihapus', 'Baris AS22JP26 dipertahankan'], collect($remove)
                ->map(fn ($rows, $table) => [$table, $rows->count(), $destination[$table]->count()])->values()->all());
            $this->table(['Prospek', 'Kontainer'], collect(self::CONTAINERS)->map(fn ($number, $id) => [$id, $number])->values()->all());

            // Check even non-FK references before deleting. Unknown polymorphic types
            // sharing these IDs are also blocked rather than guessing their meaning.
            $columns = ['manifest_id' => 'manifests'];
            foreach (Schema::getTableListing() as $table) {
                $names = Schema::getColumnListing($table);
                foreach ($columns as $column => $source) {
                    if (in_array($column, $names, true) && DB::table($table)->whereIn($column, $remove[$source]->pluck('id'))->exists()) {
                        throw new RuntimeException("Referensi {$table}.{$column} masih memakai baris lama; periksa sebelum koreksi.");
                    }
                }
                if (in_array('item_type', $names, true) && in_array('item_id', $names, true)
                    && DB::table($table)->whereIn('item_id', collect($remove)->flatMap->pluck('id')->all())->exists()) {
                    throw new RuntimeException("Referensi item pada {$table} perlu diperiksa sebelum koreksi.");
                }
            }

            if (collect($remove)->sum->count() === 0) {
                if ($before !== [94, 13]) {
                    throw new RuntimeException('Baris lama tidak ada tetapi jumlah laporan bukan 94/13. Perlu audit ulang.');
                }
                DB::rollBack();
                $this->info('Data sudah benar: full 20ft 94, full 40ft 13.');

                return self::SUCCESS;
            }

            // Verify the proposed report without writing anything during preview.
            $after = $this->counts($remove['manifests']->pluck('id')->all());
            if ($after !== [94, 13]) {
                throw new RuntimeException("Hasil {$after[0]}/{$after[1]} bukan 94/13. Perubahan dibatalkan.");
            }
            $backupPath = null;
            if ($this->option('apply')) {
                File::ensureDirectoryExists(storage_path('app/backups'));
                $backupPath = storage_path('app/backups/manifest-sr20jp26-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
                if (File::put($backupPath, json_encode(['before' => $before, 'remove' => $remove, 'destination' => $destination], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)) === false) {
                    throw new RuntimeException('Backup gagal ditulis.');
                }
            }
            if ($this->option('apply')) {
                foreach ($remove as $table => $rows) {
                    DB::table($table)->whereIn('id', $rows->pluck('id'))->delete();
                }
                if ($this->counts() !== [94, 13]) {
                    throw new RuntimeException('Verifikasi setelah koreksi gagal.');
                }
            }
            $this->info("Full 20ft: {$before[0]} -> {$after[0]}; full 40ft: {$before[1]} -> {$after[1]}.");
            if ($this->option('apply')) {
                DB::commit();
                $this->info('Koreksi disimpan. Backup: '.$backupPath);
            } else {
                DB::rollBack();
                $this->info('PREVIEW: tidak ada perubahan disimpan. Gunakan --apply untuk menerapkan.');
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->error('Koreksi dibatalkan: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
