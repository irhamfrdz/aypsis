<?php

use App\Models\Prospek;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('prospek as prospek')
            ->join('bls as imported_bl', function ($join) {
                $join->on('imported_bl.nomor_kontainer', '=', 'prospek.nomor_kontainer')
                    ->whereNull('imported_bl.prospek_id')
                    ->on('imported_bl.created_at', '=', 'prospek.updated_at');
            })
            ->where('prospek.status', Prospek::STATUS_SUDAH_MUAT)
            ->where(function ($query) {
                $query->whereNull('prospek.no_voyage')->orWhere('prospek.no_voyage', '');
            })
            ->where(function ($query) {
                $query->whereNull('prospek.nama_kapal')->orWhere('prospek.nama_kapal', '');
            })
            ->whereNull('prospek.tanggal_muat')
            ->select('prospek.id', 'prospek.updated_at', 'imported_bl.id as imported_bl_id')
            ->orderBy('prospek.id')
            ->each(function ($candidate) {
                DB::transaction(function () use ($candidate) {
                    $prospek = DB::table('prospek')->where('id', $candidate->id)->lockForUpdate()->first();

                    if (! $prospek || $prospek->status !== Prospek::STATUS_SUDAH_MUAT || $prospek->updated_at != $candidate->updated_at ||
                        $prospek->no_voyage || $prospek->nama_kapal || $prospek->tanggal_muat ||
                        $this->hasRelatedLoadingDocument($prospek)) {
                        return;
                    }

                    $importedBlExists = DB::table('bls')
                        ->where('id', $candidate->imported_bl_id)
                        ->whereNull('prospek_id')
                        ->where('nomor_kontainer', $prospek->nomor_kontainer)
                        ->where('created_at', $prospek->updated_at)
                        ->exists();

                    if (! $importedBlExists) {
                        return;
                    }

                    $now = now();
                    DB::table('prospek')->where('id', $prospek->id)->update([
                        'status' => Prospek::STATUS_AKTIF,
                        'updated_at' => $now,
                    ]);

                    DB::table('audit_logs')->insert([
                        'auditable_type' => Prospek::class,
                        'auditable_id' => $prospek->id,
                        'action' => 'updated',
                        'module' => 'Prospek',
                        'user_name' => 'System',
                        'description' => 'Koreksi status: impor BL tanpa relasi prospek mengubah status berdasarkan nomor kontainer yang digunakan kembali. Tidak ada dokumen muat/OB untuk pasangan kontainer dan seal ini.',
                        'old_values' => json_encode(['status' => Prospek::STATUS_SUDAH_MUAT]),
                        'new_values' => json_encode(['status' => Prospek::STATUS_AKTIF]),
                        'created_at' => $now,
                    ]);
                });
            });
    }

    private function hasRelatedLoadingDocument(object $prospek): bool
    {
        $matchesProspekOrContainerSeal = function ($query) use ($prospek) {
            $query->where('prospek_id', $prospek->id)
                ->orWhere(function ($query) use ($prospek) {
                    $query->where('nomor_kontainer', $prospek->nomor_kontainer)
                        ->where('no_seal', $prospek->no_seal);
                });
        };

        return DB::table('naik_kapal')->where($matchesProspekOrContainerSeal)->exists()
            || DB::table('bls')->where($matchesProspekOrContainerSeal)->exists()
            || DB::table('manifests')->where($matchesProspekOrContainerSeal)->exists();
    }

    public function down(): void
    {
        // A status may have changed again after this correction, so it is not safe to revert automatically.
    }
};
