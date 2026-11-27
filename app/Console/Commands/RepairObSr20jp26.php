<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class RepairObSr20jp26 extends Command
{
    protected $signature = 'ob:repair-sr20jp26 {--apply : Terapkan koreksi; tanpa opsi ini hanya memeriksa}';

    protected $description = 'Samakan OB bongkar SR20JP26 dengan 126 baris OB muat, dengan backup otomatis';

    private function voyage(string $table)
    {
        return DB::table($table)->where('no_voyage', 'SR20JP26')
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), ' ', '')) = ?", ['KMSRIWIJAYARAYA']);
    }

    private function key(object $row): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($row->nomor_kontainer ?? '')).'|'.($row->prospek_id ?? '');
    }

    private function signature($rows): array
    {
        $counts = [];
        foreach ($rows as $row) {
            $key = $this->key($row);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        ksort($counts);

        return $counts;
    }

    public function handle(): int
    {
        DB::beginTransaction();
        try {
            $muat = $this->voyage('naik_kapal')->orderBy('id')->lockForUpdate()->get();
            $bongkar = $this->voyage('bls')->orderBy('id')->lockForUpdate()->get();
            if ($muat->count() !== 126) {
                throw new RuntimeException('Jumlah muat bukan 126. Koreksi dibatalkan.');
            }
            if ($this->signature($muat) === $this->signature($bongkar)) {
                DB::rollBack();
                $this->info('Data sudah sesuai: muat 126, bongkar 126. Tidak ada perubahan.');

                return self::SUCCESS;
            }

            $native = $bongkar->whereNotNull('prospek_id');
            $imports = $bongkar->whereNull('prospek_id');
            $used = [];
            $missing = [];
            foreach ($muat as $row) {
                $match = $native->first(fn ($bl) => ! isset($used[$bl->id]) && $this->key($bl) === $this->key($row));
                if ($match) {
                    $used[$match->id] = true;
                } else {
                    $missing[] = $row;
                }
            }
            if ($bongkar->count() !== 286 || $native->count() !== 115 || $imports->count() !== 171 || count($used) !== 115 || count($missing) !== 11) {
                throw new RuntimeException('Pola data berbeda dari audit (286 bongkar, 115 cocok, 171 impor, 11 belum ada). Koreksi dibatalkan.');
            }
            $ids = $imports->pluck('id')->all();
            foreach (['surat_jalan_bongkarans', 'tagihan_ob'] as $table) {
                if (DB::table($table)->whereIn('bl_id', $ids)->exists()) {
                    throw new RuntimeException("Data impor sudah direferensikan oleh {$table}. Koreksi dibatalkan.");
                }
            }
            foreach ($imports as $row) {
                if ($row->sudah_ob || $row->supir_id || $row->tanggal_ob || $row->status_bongkar !== 'Belum Bongkar') {
                    throw new RuntimeException('Data impor memiliki aktivitas bongkar/OB. Koreksi dibatalkan.');
                }
            }
            $this->info('Muat: 126; bongkar: 286. Rencana: hapus 171 impor, tambah 11 dari muat.');
            if (! $this->option('apply')) {
                DB::rollBack();
                $this->comment('Pemeriksaan saja. Jalankan dengan --apply untuk menerapkan.');

                return self::SUCCESS;
            }

            $directory = storage_path('app/backups');
            File::ensureDirectoryExists($directory);
            $backup = $directory.'/ob-sr20jp26-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json';
            $written = File::put($backup, json_encode([
                'voyage' => 'SR20JP26', 'muat' => $muat, 'bongkar' => $bongkar,
                'remove_ids' => $ids, 'missing_muat_ids' => array_column($missing, 'id'),
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            if ($written === false) {
                throw new RuntimeException('Backup gagal ditulis.');
            }
            $this->info('Backup: '.$backup);
            $this->voyage('bls')->whereIn('id', $ids)->delete();
            foreach ($missing as $row) {
                $prospek = DB::table('prospek')->where('id', $row->prospek_id)->first();
                $data = [];
                foreach (['prospek_id', 'nomor_kontainer', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'no_voyage', 'nama_kapal', 'pelabuhan_asal', 'pelabuhan_tujuan', 'asal_kontainer', 'ke', 'created_by'] as $field) {
                    $data[$field] = $row->$field ?? null;
                }
                $data['size_kontainer'] = $row->size_kontainer ?: ($row->ukuran_kontainer ?? null);
                $data += [
                    'nama_barang' => $row->jenis_barang ?: ($prospek->barang ?? null),
                    'tonnage' => $row->total_tonase ?: ($prospek->total_ton ?? null),
                    'volume' => $row->total_volume ?: ($prospek->total_volume ?? null),
                    'kuantitas' => $row->kuantitas ?: ($prospek->kuantitas ?? null),
                    'pengirim' => $prospek->pt_pengirim ?? null,
                    'status_bongkar' => 'Belum Bongkar', 'sudah_ob' => false,
                    'created_at' => now(), 'updated_at' => now(),
                ];
                $data['max_tv'] = max((float) $data['tonnage'], (float) $data['volume']);
                $existing = $native->first(fn ($bl) => $this->key($bl) === $this->key($row));
                if ($existing) {
                    foreach (['pengirim', 'penerima', 'alamat_pengiriman', 'contact_person', 'satuan', 'term'] as $field) {
                        $data[$field] = $existing->$field;
                    }
                }
                DB::table('bls')->insert($data);
            }
            $after = $this->voyage('bls')->get();
            if ($after->count() !== 126 || $this->signature($after) !== $this->signature($muat)) {
                throw new RuntimeException('Verifikasi akhir gagal.');
            }
            DB::commit();
            $this->info('Berhasil. Muat 126 dan bongkar 126; nomor kontainer, prospek, dan jumlah baris cocok.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->error('Tidak ada perubahan disimpan: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
