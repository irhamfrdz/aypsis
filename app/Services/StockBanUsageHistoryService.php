<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\StockBan;
use App\Models\StockBanLuarBatam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockBanUsageHistoryService
{
    public function record(Model $ban): void
    {
        $this->insertSnapshot($ban->getTable(), $ban->getKey(), $ban->getAttributes());
    }

    public function backfill(): void
    {
        if (Schema::hasTable('audit_logs')) {
            $states = [];
            AuditLog::query()
                ->whereIn('auditable_type', [StockBan::class, StockBanLuarBatam::class])
                ->orderBy('id')
                ->chunkById(500, function ($logs) use (&$states) {
                    foreach ($logs as $log) {
                        $sourceType = $log->auditable_type === StockBan::class ? 'stock_bans' : 'stock_ban_luar_batams';
                        $key = $sourceType.':'.$log->auditable_id;
                        $previous = $states[$key] ?? [];
                        $old = $log->old_values ?? [];
                        $new = $log->new_values ?? [];
                        if (! is_array($new)) {
                            continue;
                        }
                        $current = $log->action === 'created' ? $new : array_replace($previous, $new);
                        $oldStatus = $old['status'] ?? ($previous['status'] ?? null);
                        if (($new['status'] ?? null) === 'Terpakai' && $oldStatus !== 'Terpakai') {
                            $this->insertSnapshot($sourceType, (int) $log->auditable_id, $current, $log->id);
                        }
                        $states[$key] = $current;
                    }
                });
        }

        foreach (['stock_bans', 'stock_ban_luar_batams'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            DB::table($table)->where('status', 'Terpakai')->orderBy('id')->chunkById(500, function ($bans) use ($table) {
                foreach ($bans as $ban) {
                    $date = $ban->tanggal_digunakan ?? $ban->tanggal_keluar ?? null;
                    if ($date && ! DB::table('stock_ban_usage_histories')
                        ->where('source_type', $table)->where('source_id', $ban->id)
                        ->where('tanggal_digunakan', $date)->exists()) {
                        $this->insertSnapshot($table, $ban->id, (array) $ban);
                    }
                }
            });
        }
    }

    private function insertSnapshot(string $sourceType, int $sourceId, array $values, ?int $auditLogId = null): void
    {
        if ($auditLogId && DB::table('stock_ban_usage_histories')->where('audit_log_id', $auditLogId)->exists()) {
            return;
        }
        $date = $values['tanggal_digunakan'] ?? $values['tanggal_keluar'] ?? null;
        if (! $date || (empty($values['mobil_id']) && empty($values['alat_berat_id']))) {
            return;
        }

        $mobilId = $values['mobil_id'] ?? null;
        $alatBeratId = $values['alat_berat_id'] ?? null;
        $mobil = $mobilId ? DB::table('mobils')->where('id', $mobilId)->first(['nomor_polisi', 'no_kir']) : null;
        $alatBerat = $alatBeratId ? DB::table('alat_berats')->where('id', $alatBeratId)->first(['nama']) : null;
        $penerima = ! empty($values['penerima_id'])
            ? DB::table('karyawans')->where('id', $values['penerima_id'])->value('nama_lengkap') : null;
        $namaBan = ! empty($values['nama_stock_ban_id'])
            ? DB::table('nama_stock_bans')->where('id', $values['nama_stock_ban_id'])->value('nama') : null;

        DB::table('stock_ban_usage_histories')->insert([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'audit_log_id' => $auditLogId,
            'mobil_id' => $mobilId,
            'alat_berat_id' => $alatBeratId,
            'tanggal_digunakan' => substr((string) $date, 0, 10),
            'unit_name' => $mobil?->nomor_polisi ?: ($mobil?->no_kir ?: ($alatBerat?->nama ?: null)),
            'nama_ban' => $namaBan,
            'merk' => $values['merk'] ?? null,
            'ukuran' => $values['ukuran'] ?? null,
            'nomor_seri' => $values['nomor_seri'] ?? null,
            'nomor_bukti_pakai' => $values['nomor_bukti_pakai'] ?? null,
            'penerima_id' => $values['penerima_id'] ?? null,
            'penerima_name' => $penerima ?: ($values['penerima_manual'] ?? null),
            'keterangan' => $values['keterangan'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
