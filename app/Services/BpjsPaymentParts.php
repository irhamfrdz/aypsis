<?php

namespace App\Services;

use App\Models\PranotaBpjsHeader;
use Illuminate\Support\Facades\DB;

class BpjsPaymentParts
{
    public function groups(PranotaBpjsHeader $pranota): array
    {
        $paid = DB::table('pembayaran_pranota_bpjs_parts')->where('pranota_bpjs_header_id', $pranota->id)->get();
        // Payments made before splitting was introduced settled the entire pranota.
        if ($pranota->pembayaran()->exists() && $paid->isEmpty()) {
            return [];
        }
        $used = $paid->mapWithKeys(fn ($part) => [$part->pranota_bpjs_detail_id.':'.$part->komponen => true]);
        $groups = [];
        $pranota->loadMissing('details.karyawan');
        foreach ($pranota->details as $detail) {
            $jkn = round((float) $detail->bpjs_kesehatan + (float) $detail->bpjs_ketenagakerjaan, 2);
            foreach (['jkn' => $jkn, 'jamsostek' => round((float) $detail->total - $jkn, 2)] as $component => $amount) {
                if ($amount <= 0 || isset($used[$detail->id.':'.$component])) {
                    continue;
                }
                $employee = $detail->karyawan_pranota;
                $type = trim((string) ($component === 'jkn' ? $employee?->tipe_jkn : $employee?->tipe_bp_jamsostek)) ?: 'Tanpa tipe';
                $key = $component.'-'.hash('sha256', mb_strtoupper($type));
                $groups[$key] ??= ['key' => $key, 'komponen' => $component, 'tipe' => $type, 'amount' => 0, 'parts' => []];
                $groups[$key]['amount'] = round($groups[$key]['amount'] + $amount, 2);
                $groups[$key]['parts'][] = ['pranota_bpjs_header_id' => $pranota->id, 'pranota_bpjs_detail_id' => $detail->id, 'komponen' => $component, 'tipe' => $type, 'subtotal' => $amount];
            }
        }

        return $groups;
    }
}
