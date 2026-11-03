<?php

namespace App\Services;

use App\Models\Coa;
use Illuminate\Validation\ValidationException;

class BpjsJournalAccount
{
    public function resolve(string $type, string $fallback = ''): ?Coa
    {
        $compact = fn ($value) => preg_replace('/[\s_-]+/u', '', mb_strtoupper($value));
        $key = $compact($type);
        if ($key === 'BPUCREW' || (! $key && $compact($fallback) === 'BPUCREW')) {
            $key = 'CREW';
        }
        $mapping = collect(json_decode(file_get_contents(public_path('js/bpjs-journal-accounts.json')), true))->first(fn ($row) => $row[0] === $key);
        if (! $mapping) {
            return null;
        }
        [$number, $name] = explode(' - ', $mapping[2], 2);
        $accounts = Coa::where('nomor_akun', $number)->get();
        if ($accounts->count() === 1) {
            return $accounts->first();
        }
        $matches = $accounts->filter(fn ($account) => $compact($account->nama_akun) === $compact($name));

        return $matches->count() === 1 ? $matches->first() : null;
    }

    public function selectedId(array $groups): int
    {
        $ids = array_column($groups, 'credit_id');
        if (in_array(null, $ids, true) || count($ids) !== count($groups)) {
            throw ValidationException::withMessages(['group_keys' => 'Akun jurnal untuk tipe yang dipilih belum tersedia atau belum sesuai di COA.']);
        }
        $ids = array_unique($ids);
        if (count($ids) !== 1) {
            throw ValidationException::withMessages(['group_keys' => 'Tipe yang dipilih memiliki akun jurnal berbeda. Simpan pembayarannya secara terpisah.']);
        }

        return (int) reset($ids);
    }
}
