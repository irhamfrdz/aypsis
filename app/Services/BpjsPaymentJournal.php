<?php

namespace App\Services;

use App\Models\Coa;
use Illuminate\Validation\ValidationException;

class BpjsPaymentJournal
{
    public function allocations(array $groups, $details): array
    {
        $amounts = [];
        $resolver = new BpjsJournalAmounts;
        $add = function ($id, $amount) use (&$amounts) {
            $cents = (int) round($amount * 100);
            if ($cents < 0) {
                throw ValidationException::withMessages(['group_keys' => 'Rincian jurnal BPJS tidak boleh negatif.']);
            }
            if ($cents) {
                $amounts[$id] = ($amounts[$id] ?? 0) + $cents;
            }
        };
        $find = function ($number) {
            $matches = Coa::where('nomor_akun', $number)->get();
            if ($matches->count() !== 1) {
                throw ValidationException::withMessages(['group_keys' => 'Akun COA '.$number.' harus tersedia dan unik.']);
            }
            return $matches->first()->id;
        };
        foreach ($groups as $group) {
            foreach ($group['parts'] as $part) {
                $detail = $details->get($part['pranota_bpjs_detail_id']);
                [$allowance, $cost, $debt] = $resolver->forDetail($detail, $part['komponen']);
                if ((int) round(($allowance + $cost + $debt) * 100) !== (int) round($part['subtotal'] * 100)) {
                    throw ValidationException::withMessages(['group_keys' => 'Jumlah tunjangan, biaya, dan hutang tidak sesuai nominal pembayaran. Periksa rincian pranota.']);
                }
                $add($group['credit_id'], $allowance);
                if ($cost > 0) {
                    $source = Coa::findOrFail($group['credit_id']);
                    $number = match (substr($source->nomor_akun, 0, 4)) {
                        '5101', '6101' => '6101.006',
                        '6201' => '6201.006',
                        default => null,
                    };
                    if (! $number) {
                        throw ValidationException::withMessages(['group_keys' => 'Cabang akun biaya tidak dapat ditentukan dari '.$source->nomor_akun.'.']);
                    }
                    $add($find($number), $cost);
                }
                if ($debt > 0) {
                    $add($find('2103.002'), $debt);
                }
            }
        }
        return array_map(fn ($cents) => $cents / 100, $amounts);
    }
}
