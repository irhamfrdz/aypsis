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
        $creditAccounts = [];
        $resolver = new BpjsJournalAccount;
        $pranota->loadMissing('details.karyawan');
        foreach ($pranota->details as $detail) {
            $jkn = round((float) $detail->bpjs_kesehatan + (float) $detail->bpjs_ketenagakerjaan, 2);
            foreach (['jkn' => $jkn, 'jamsostek' => round((float) $detail->total - $jkn, 2)] as $component => $amount) {
                if ($amount <= 0 || isset($used[$detail->id.':'.$component])) {
                    continue;
                }
                $employee = $detail->karyawan_pranota;
                $type = trim((string) ($component === 'jkn' ? $employee?->tipe_jkn : $employee?->tipe_bp_jamsostek)) ?: 'Tanpa tipe';
                $fallback = (string) ($component === 'jkn' ? $employee?->group_jkn : $employee?->group_bp_jamsostek);
                $groupName = trim($fallback) ?: 'Tanpa group';
                $branch = trim((string) ($employee?->cabang_bpjs)) ?: 'Tanpa cabang';
                $key = $component.'-'.hash('sha256', mb_strtoupper($groupName.'|'.$type.'|'.$branch));
                $lookup = $type.':'.$fallback;
                if (! array_key_exists($lookup, $creditAccounts)) {
                    $creditAccounts[$lookup] = $resolver->resolve($type === 'Tanpa tipe' ? '' : $type, $fallback);
                }
                $credit = $creditAccounts[$lookup];
                $groups[$key] ??= ['key' => $key, 'komponen' => $component, 'group' => $groupName, 'cabang_bpjs' => $branch,
                    'tipe' => $type, 'amount' => 0, 'parts' => [],
                    'credit_id' => $credit?->id, 'credit_label' => $credit ? $credit->nomor_akun.' - '.$credit->nama_akun : 'Akun jurnal belum tersedia di COA'];
                if ($groups[$key]['credit_id'] !== $credit?->id) {
                    $groups[$key]['credit_id'] = null;
                    $groups[$key]['credit_label'] = 'Tipe ini memiliki pemetaan akun jurnal berbeda';
                }
                $groups[$key]['amount'] = round($groups[$key]['amount'] + $amount, 2);
                $groups[$key]['parts'][] = ['pranota_bpjs_header_id' => $pranota->id, 'pranota_bpjs_detail_id' => $detail->id,
                    'komponen' => $component, 'tipe' => $type, 'subtotal' => $amount];
            }
        }

        return $groups;
    }
}
