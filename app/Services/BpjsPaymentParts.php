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
        $amountResolver = new BpjsJournalAmounts;
        $pranota->loadMissing('details.karyawan');
        foreach ($pranota->details as $detail) {
            $jkn = round((float) $detail->bpjs_kesehatan + (float) $detail->bpjs_ketenagakerjaan, 2);
            foreach (['jkn' => $jkn, 'jamsostek' => round((float) $detail->total - $jkn, 2)] as $component => $amount) {
                if ($amount <= 0 || isset($used[$detail->id.':'.$component])) {
                    continue;
                }
                $employee = $detail->karyawan_pranota;
                $type = mb_strtoupper(preg_replace('/\s+/u', ' ', trim((string) ($component === 'jkn' ? $employee?->tipe_jkn : $employee?->tipe_bp_jamsostek)))) ?: 'Tanpa tipe';
                $fallback = (string) ($component === 'jkn' ? $employee?->group_jkn : $employee?->group_bp_jamsostek);
                $groupName = trim($fallback) ?: 'Tanpa group';
                $branch = trim((string) ($detail->karyawan?->cabang_bpjs));
                if ($branch === '') {
                    $branch = trim((string) ($employee?->cabang_bpjs));
                }
                $branch = $branch !== '' ? $branch : 'Tanpa cabang';
                // A payment choice represents one type within a group, across all branches.
                $key = $component.'-'.hash('sha256', mb_strtoupper($groupName.'|'.$type));
                $lookup = $type.':'.$fallback;
                if (! array_key_exists($lookup, $creditAccounts)) {
                    $creditAccounts[$lookup] = $resolver->resolve($type === 'Tanpa tipe' ? '' : $type, $fallback);
                }
                $credit = $creditAccounts[$lookup];
                $groups[$key] ??= ['key' => $key, 'komponen' => $component, 'group' => $groupName, 'cabang_bpjs' => $branch,
                    'tipe' => $type, 'amount' => 0, 'parts' => [],
                    'selection_key' => $component.'-'.hash('sha256', mb_strtoupper($groupName)), 'employees' => [],
                    'credit_id' => $credit?->id, 'credit_label' => $credit ? $credit->nomor_akun.' - '.$credit->nama_akun : 'Akun jurnal belum tersedia di COA'];
                if ($groups[$key]['credit_id'] !== $credit?->id) {
                    $groups[$key]['credit_id'] = null;
                    $groups[$key]['credit_label'] = 'Tipe ini memiliki pemetaan akun jurnal berbeda';
                }
                $groups[$key]['amount'] = round($groups[$key]['amount'] + $amount, 2);
                $groups[$key]['parts'][] = ['pranota_bpjs_header_id' => $pranota->id, 'pranota_bpjs_detail_id' => $detail->id,
                    'komponen' => $component, 'tipe' => $type, 'subtotal' => $amount];
                $groups[$key]['employees'][] = [
                    'key' => $detail->id.':'.$component, 'cabang_bpjs' => $branch,
                    'nik' => $employee?->nik ?: '-', 'nama' => $employee?->nama_lengkap ?: 'Karyawan tidak tersedia',
                    'amount' => $amount,
                ];
                [$tunjangan, $biaya, $hutang] = $amountResolver->forDetail($detail, $component);
                $employeeIndex = array_key_last($groups[$key]['employees']);
                $groups[$key]['employees'][$employeeIndex]['tunjangan'] = $tunjangan;
                $groups[$key]['employees'][$employeeIndex]['biaya'] = $biaya;
                $groups[$key]['employees'][$employeeIndex]['hutang'] = $hutang;
            }
        }

        return $groups;
    }
}
