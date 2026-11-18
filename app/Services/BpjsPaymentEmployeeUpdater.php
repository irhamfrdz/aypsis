<?php

namespace App\Services;

use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BpjsPaymentEmployeeUpdater
{
    public function update(PembayaranPranotaBpjs $payment): int
    {
        return DB::transaction(function () use ($payment) {
            $payment = PembayaranPranotaBpjs::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $parts = DB::table('pembayaran_pranota_bpjs_parts')->where('pembayaran_pranota_bpjs_id', $payment->id)->lockForUpdate()->get();
            if ($parts->isEmpty()) {
                throw ValidationException::withMessages(['employees' => 'Rincian karyawan pembayaran ini tidak tersedia.']);
            }
            $details = PranotaBpjsDetail::with('karyawan')->whereIn('id', $parts->pluck('pranota_bpjs_detail_id'))->get()->keyBy('id');
            $snapshots = $payment->employee_snapshots ?? [];
            $changed = [];
            foreach ($parts as $part) {
                $detail = $details->get($part->pranota_bpjs_detail_id);
                $employee = $detail?->karyawan;
                if (! $employee || ! in_array($part->komponen, ['jkn', 'jamsostek'], true)) {
                    throw ValidationException::withMessages(['employees' => 'Data master karyawan tidak lengkap. Tidak ada perubahan disimpan.']);
                }
                $fields = ['nik', 'nama_lengkap', 'group_jkn', 'tipe_jkn', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'cabang_bpjs'];
                $snapshot = $employee->only($fields);
                $type = $part->komponen === 'jkn' ? $employee->tipe_jkn : $employee->tipe_bp_jamsostek;
                $type = mb_strtoupper(preg_replace('/\s+/u', ' ', trim((string) $type))) ?: 'Tanpa tipe';
                $previous = $snapshots[$detail->id] ?? $detail->karyawan_pranota?->only($fields);
                if ($previous !== $snapshot || $part->tipe !== $type) {
                    $changed[$detail->id] = true;
                }
                $snapshots[$detail->id] = $snapshot;
                DB::table('pembayaran_pranota_bpjs_parts')->where('id', $part->id)->update(['tipe' => $type]);
            }
            $payment->update(['employee_snapshots' => $snapshots]);
            return count($changed);
        });
    }
}
