<?php

namespace App\Services;

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\UangJalan;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class UangJalanApprovalService
{
    public function tugasBerjalan(?string $supir): Collection
    {
        if (blank($supir)) {
            return collect();
        }

        $hariIni = today();

        return SuratJalan::query()
            ->where('supir', $supir)
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->where('status_pembayaran_uang_jalan', 'dibayar')
            ->whereDate('tanggal_surat_jalan', '<=', $hariIni)
            ->whereDoesntHave('tandaTerima', function ($query) use ($hariIni) {
                $query->where('approval_pelabuhan_status', 'approved')
                    ->whereDate('created_at', '<=', $hariIni);
            })
            ->orderBy('tanggal_surat_jalan')
            ->get(['id', 'no_surat_jalan', 'tanggal_surat_jalan', 'supir', 'no_plat']);
    }

    public function buatUangJalan(SuratJalan|SuratJalanBongkaran $suratJalan, string $jenis, array $data, ?int $createdBy, bool $nomorBaru = false): UangJalan
    {
        $foreignKey = $jenis === 'bongkaran' ? 'surat_jalan_bongkaran_id' : 'surat_jalan_id';
        if (UangJalan::where($foreignKey, $suratJalan->id)->exists()) {
            throw ValidationException::withMessages(['surat_jalan_id' => 'Uang jalan untuk surat jalan ini sudah dibuat.']);
        }

        $nomor = $nomorBaru ? null : ($data['nomor_uang_jalan'] ?? null);
        if (filled($nomor) && UangJalan::where('nomor_uang_jalan', $nomor)->exists()) {
            $nomor = null;
        }

        $uangJalan = UangJalan::create([
            'nomor_uang_jalan' => $nomor ?: UangJalan::generateNomorUangJalan(),
            'tanggal_uang_jalan' => $data['tanggal_uang_jalan'],
            'surat_jalan_id' => $jenis === 'biasa' ? $suratJalan->id : null,
            'surat_jalan_bongkaran_id' => $jenis === 'bongkaran' ? $suratJalan->id : null,
            'kegiatan_bongkar_muat' => $data['kegiatan_bongkar_muat'],
            'jumlah_uang_jalan' => $data['jumlah_uang_jalan'],
            'jumlah_mel' => $data['jumlah_mel'] ?? 0,
            'jumlah_pelancar' => $data['jumlah_pelancar'] ?? 0,
            'jumlah_kawalan' => $data['jumlah_kawalan'] ?? 0,
            'jumlah_parkir' => $data['jumlah_parkir'] ?? 0,
            'subtotal' => $data['subtotal'],
            'alasan_penyesuaian' => $data['alasan_penyesuaian'] ?? null,
            'jumlah_penyesuaian' => $data['jumlah_penyesuaian'] ?? 0,
            'jumlah_total' => $data['jumlah_total'],
            'memo' => $data['memo'] ?? null,
            'status' => 'belum_masuk_pranota',
            'created_by' => $createdBy,
        ]);

        if ($jenis === 'biasa') {
            $suratJalan->update(['status_pembayaran_uang_jalan' => 'sudah_masuk_uang_jalan']);
        }

        return $uangJalan;
    }
}
