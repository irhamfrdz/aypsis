<?php

namespace App\Services;

use App\Models\PranotaBpjsDetail;
use App\Models\PranotaBpjsHeader;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BpjsPartialPranotaEditor
{
    public function save(PranotaBpjsHeader $header, array $rows): void
    {
        $existing = $header->details()->get()->keyBy(fn ($row) => $row->tipe_karyawan.':'.$row->karyawan_id);
        $parts = DB::table('pembayaran_pranota_bpjs_parts')->where('pranota_bpjs_header_id', $header->id)->get()->groupBy('pranota_bpjs_detail_id');
        if ($parts->isEmpty()) {
            throw ValidationException::withMessages(['details' => 'Rincian pembayaran pranota Partial tidak tersedia. Periksa pembayaran sebelum mengubah rincian.']);
        }
        $retained = [];
        $seen = [];
        $newRows = [];
        foreach ($rows as $row) {
            $key = $row['tipe_karyawan'].':'.$row['karyawan_id'];
            if (isset($seen[$key])) {
                throw ValidationException::withMessages(['details' => 'Karyawan yang sama tidak boleh dicatat dua kali.']);
            }
            $seen[$key] = true;
            $old = $existing->get($key);
            if (! $old || ! $parts->has($old->id)) {
                $newRows[] = $row;
                continue;
            }
            $candidate = (new PranotaBpjsDetail)->newFromBuilder($row);
            foreach ($parts[$old->id] as $part) {
                $fields = $part->komponen === 'jkn' ? ['tipe_jkn', 'group_jkn'] : ['tipe_bp_jamsostek', 'group_bp_jamsostek'];
                foreach ($fields as $field) {
                    if ($old->karyawan_pranota?->$field !== $candidate->karyawan_pranota?->$field) {
                        throw ValidationException::withMessages(['details' => 'Tipe dan group komponen yang sudah dibayar tidak dapat diubah.']);
                    }
                }
                $resolver = new BpjsJournalAmounts;
                if ($resolver->forDetail($old, $part->komponen) !== $resolver->forDetail($candidate, $part->komponen)) {
                    throw ValidationException::withMessages(['details' => 'Nominal komponen yang sudah dibayar tidak dapat diubah. Ubah hanya komponen yang belum dibayar.']);
                }
            }
            unset($row['created_at']);
            DB::table('pranota_bpjs_details')->where('id', $old->id)->update($row);
            $retained[] = $old->id;
        }
        if ($parts->keys()->diff($retained)->isNotEmpty()) {
            throw ValidationException::withMessages(['details' => 'Karyawan yang sudah dibayar tidak dapat dihapus dari pranota.']);
        }
        $header->details()->whereNotIn('id', $retained)->delete();
        foreach (array_chunk($newRows, 40) as $chunk) {
            PranotaBpjsDetail::query()->insert($chunk);
        }
        $paid = (float) $parts->flatten(1)->sum('subtotal');
        $total = array_sum(array_column($rows, 'total'));
        $header->update(['status' => $paid >= round($total, 2) ? 'paid' : 'partial']);
    }
}
