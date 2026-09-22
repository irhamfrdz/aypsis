<?php

namespace App\Console\Commands;

use App\Models\Manifest;
use App\Models\Prospek;
use App\Models\TandaTerimaLclKontainerPivot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairLclManifestCommand extends Command
{
    protected $signature = 'manifest:repair-lcl
        {container : Nomor kontainer LCL}
        {seal : Nomor seal}
        {voyage : Nomor voyage}
        {--ship= : Nama kapal, wajib bila ada lebih dari satu kapal}
        {--dry-run : Tampilkan data yang akan dibuat tanpa menyimpan}';

    protected $description = 'Membuat Manifest LCL yang belum ada dari data stuffing secara idempotent';

    public function handle(): int
    {
        $container = trim($this->argument('container'));
        $seal = trim($this->argument('seal'));
        $voyage = trim($this->argument('voyage'));
        $ship = $this->option('ship') ? trim($this->option('ship')) : null;

        $pivots = TandaTerimaLclKontainerPivot::with(['tandaTerima.items', 'tandaTerima.term'])
            ->where('nomor_kontainer', $container)
            ->where('nomor_seal', $seal)
            ->get();

        if ($pivots->isEmpty()) {
            $this->error('Data stuffing tidak ditemukan untuk kontainer/seal tersebut.');

            return self::FAILURE;
        }

        $prospekId = Prospek::where('nomor_kontainer', $container)
            ->where('no_seal', $seal)
            ->value('id');

        $ships = Manifest::where('nomor_kontainer', $container)
            ->where('no_seal', $seal)
            ->where('no_voyage', $voyage)
            ->pluck('nama_kapal')
            ->filter()
            ->unique()
            ->values();

        if (! $ship && $ships->count() === 1) {
            $ship = $ships->first();
        }

        if (! $ship) {
            $this->error('Nama kapal wajib diberikan dengan opsi --ship=... agar Manifest tidak salah voyage.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Memeriksa %d tanda terima untuk %s / seal %s / voyage %s (%s).',
            $pivots->count(), $container, $seal, $voyage, $ship
        ));

        $created = 0;
        $skipped = 0;

        foreach ($pivots as $pivot) {
            $tt = $pivot->tandaTerima;
            if (! $tt || ! $tt->nomor_tanda_terima) {
                $this->warn("Pivot {$pivot->id} dilewati karena tanda terima tidak lengkap.");
                continue;
            }

            $exists = $this->existingManifest($container, $seal, $voyage, $ship, $tt->nomor_tanda_terima);
            if ($exists) {
                $skipped++;
                $this->line("  SKIP  {$tt->nomor_tanda_terima} (Manifest #{$exists->id})");
                continue;
            }

            $this->line("  CREATE {$tt->nomor_tanda_terima}");
            if ($this->option('dry-run')) {
                $created++;
                continue;
            }

            DB::transaction(function () use ($container, $seal, $voyage, $ship, $pivot, $tt, $prospekId) {
                if ($this->existingManifest($container, $seal, $voyage, $ship, $tt->nomor_tanda_terima)) {
                    return;
                }

                $last = Manifest::whereNotNull('nomor_bl')->orderByDesc('id')->lockForUpdate()->first();
                $lastNumber = 0;
                if ($last && preg_match('/\d+/', (string) $last->nomor_bl, $matches)) {
                    $lastNumber = (int) $matches[0];
                }

                $units = $tt->items->pluck('satuan')->filter()->unique();
                $term = $tt->term;
                $termValue = $term instanceof \App\Models\Term ? $term->kode : $term;
                Manifest::create([
                    'nomor_bl' => 'MNF-'.str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT),
                    'nomor_kontainer' => $container,
                    'no_seal' => $seal,
                    'tipe_kontainer' => 'LCL',
                    'size_kontainer' => $pivot->size_kontainer,
                    'nama_kapal' => $ship,
                    'no_voyage' => $voyage,
                    'nomor_tanda_terima' => $tt->nomor_tanda_terima,
                    'pengirim' => $tt->nama_pengirim,
                    'penerima' => $tt->nama_penerima,
                    'alamat_pengirim' => $tt->alamat_pengirim,
                    'alamat_penerima' => $tt->alamat_penerima,
                    'alamat_pengiriman' => $tt->alamat_penerima,
                    'contact_person' => $tt->contact_person,
                    'nama_barang' => $tt->items->pluck('nama_barang')->filter()->unique()->implode(', ') ?: 'LCL',
                    'volume' => $tt->items->sum('meter_kubik'),
                    'tonnage' => $tt->items->sum('tonase'),
                    'kuantitas' => $tt->total_koli,
                    'satuan' => $units->count() === 1 ? $units->first() : ($units->count() > 1 ? 'PKGS' : null),
                    'pelabuhan_bongkar' => $tt->tujuanPengiriman?->nama_tujuan,
                    'penerimaan' => $tt->tanggal_tanda_terima,
                    'term' => $termValue,
                    'prospek_id' => $prospekId,
                ]);
            });

            $created++;
        }

        $this->newLine();
        $this->info("Selesai. Dibuat: {$created}; dilewati karena sudah ada: {$skipped}.");

        return self::SUCCESS;
    }

    private function existingManifest(string $container, string $seal, string $voyage, string $ship, string $ttNumber): ?Manifest
    {
        return Manifest::where('nomor_kontainer', $container)
            ->where('no_seal', $seal)
            ->where('no_voyage', $voyage)
            ->where('nomor_tanda_terima', $ttNumber)
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [strtoupper(trim(str_replace('.', '', $ship)))])
            ->first();
    }
}
