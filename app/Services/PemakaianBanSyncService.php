<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class PemakaianBanSyncService
{
    public function run(bool $dryRun = false, ?string $source = null, ?int $id = null): array
    {
        $counts = [];
        $inserted = 0;
        $existing = 0;

        DB::transaction(function () use (&$counts, &$inserted, &$existing, $dryRun, $source, $id) {
            DB::table('stock_ban_usage_histories')->when($source !== null, fn ($q) => $q->whereRaw($source === 'stock_ban_usage_histories' ? '1=1' : '1=0')->where('id', $id))->orderBy('id')->chunkById(500, function ($rows) use (&$counts, &$inserted, &$existing, $dryRun, $source, $id) {
                foreach ($rows as $row) {
                    $this->copy([
                        'source_table' => 'stock_ban_usage_histories',
                        'source_id' => $row->id,
                        'jenis_komponen' => 'ban',
                        'stock_table' => $row->source_type,
                        'stock_id' => $row->source_id,
                        'audit_log_id' => $row->audit_log_id,
                        'tanggal_digunakan' => $row->tanggal_digunakan,
                        'qty' => 1,
                        'mobil_id' => $row->mobil_id,
                        'alat_berat_id' => $row->alat_berat_id,
                        'unit_name' => $row->unit_name,
                        'nama_barang' => $row->nama_ban ?: 'Ban',
                        'merk' => $row->merk,
                        'ukuran' => $row->ukuran,
                        'nomor_seri' => $row->nomor_seri,
                        'nomor_bukti_pakai' => $row->nomor_bukti_pakai,
                        'penerima_id' => $row->penerima_id,
                        'penerima_name' => $row->penerima_name,
                        'keterangan' => $row->keterangan,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ], $counts, $inserted, $existing, $dryRun, $source !== null);
                }
            });

            // Query Builder intentionally includes soft-deleted stock/master rows.
            DB::table('stock_ban_dalam_usages')->when($source !== null, fn ($q) => $q->whereRaw($source === 'stock_ban_dalam_usages' ? '1=1' : '1=0')->where('id', $id))->orderBy('id')->chunkById(500, function ($rows) use (&$counts, &$inserted, &$existing, $dryRun, $source, $id) {
                foreach ($rows as $row) {
                    $stockTable = 'stock_ban_dalams';
                    $stockId = $row->stock_ban_dalam_id;
                    $kind = null;
                    if (! $stockId) {
                        if (! preg_match('/^\[(Ring Velg|Velg) ID: (\d+)\]/', $row->keterangan ?? '', $matches)) {
                            throw new RuntimeException("Unknown stock reference in usage #{$row->id}");
                        }
                        $kind = $matches[1] === 'Velg' ? 'velg' : 'ring_velg';
                        $stockTable = $kind === 'velg' ? 'stock_velgs' : 'stock_ring_velgs';
                        $stockId = (int) $matches[2];
                    }
                    $stock = DB::table($stockTable)->where('id', $stockId)->first();
                    $name = $stock ? DB::table('nama_stock_bans')->where('id', $stock->nama_stock_ban_id)->value('nama') : null;
                    if (! $kind) {
                        $normalized = strtolower($name ?? '');
                        $kind = str_contains($normalized, 'ban dalam') ? 'ban_dalam'
                            : (str_contains($normalized, 'ban perut') ? 'ban_perut' : null);
                        if (! $stock || ! $name) {
                            throw new RuntimeException("Missing stock/master for usage #{$row->id}");
                        }
                        // Paint, rags, locks, etc. share this legacy table but are not tire components.
                        if (! $kind) {
                            continue;
                        }
                    }
                    $mobil = $row->mobil_id ? DB::table('mobils')->where('id', $row->mobil_id)->first() : null;
                    $receiver = $row->penerima_id ? DB::table('karyawans')->where('id', $row->penerima_id)->value('nama_lengkap') : null;
                    $this->copy([
                        'source_table' => 'stock_ban_dalam_usages',
                        'source_id' => $row->id,
                        'jenis_komponen' => $kind,
                        'stock_table' => $stockTable,
                        'stock_id' => $stockId,
                        // Preserve the date used by the current report.
                        'tanggal_digunakan' => $row->tanggal_keluar ?? $row->tanggal_digunakan,
                        'tanggal_keluar' => $row->tanggal_keluar,
                        'qty' => $row->qty,
                        'mobil_id' => $row->mobil_id,
                        'kapal_id' => $row->kapal_id,
                        'gudang_id' => $row->gudang_id,
                        'unit_name' => $mobil?->nomor_polisi ?: ($mobil?->no_kir ?: null),
                        'nama_barang' => $name ?: ($kind === 'velg' ? 'Velg' : 'Ring Velg'),
                        'ukuran' => $stock->ukuran ?? null,
                        'penerima_id' => $row->penerima_id,
                        'penerima_name' => $receiver ?: $row->penerima_manual,
                        'keterangan' => $row->keterangan,
                        'created_by' => $row->created_by ?? null,
                        'updated_by' => $row->updated_by ?? null,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ], $counts, $inserted, $existing, $dryRun, $source !== null);
                }
            });
        });

        return compact('counts', 'inserted', 'existing');
    }

    private function copy(array $data, array &$counts, int &$inserted, int &$existing, bool $dryRun, bool $sync): void
    {
        if (empty($data['tanggal_digunakan']) || $data['qty'] < 1) {
            throw new RuntimeException("Invalid date/quantity: {$data['source_table']} #{$data['source_id']}");
        }
        $kind = $data['jenis_komponen'];
        $counts[$kind] = ($counts[$kind] ?? 0) + 1;
        $key = ['source_table' => $data['source_table'], 'source_id' => $data['source_id']];
        $stored = DB::table('pemakaian_ban')->where($key)->first();
        if ($stored && $sync) {
            DB::table('pemakaian_ban')->where($key)->update($data + ['deleted_at' => null]);
            $existing++;
            return;
        }
        if ($stored) {
            foreach ($data as $field => $value) {
                if ((string) $stored->$field !== (string) $value) {
                    throw new RuntimeException("Destination differs: {$data['source_table']} #{$data['source_id']} ({$field})");
                }
            }
            $existing++;

            return;
        }
        if (! $dryRun) {
            DB::table('pemakaian_ban')->insert($data);
        }
        $inserted++;
    }
}
