<?php

namespace App\Services;

use App\Models\Coa;
use Illuminate\Validation\ValidationException;

class BpjsJournalAccount
{
    private ?array $mapping = null;

    public function resolve(string $type, string $fallback = ''): ?Coa
    {
        $compact = fn ($value) => preg_replace('/[\s_-]+/u', '', mb_strtoupper($value));
        $label = $this->label($type, $fallback);
        if (! $label) {
            return null;
        }
        [$number, $name] = explode(' - ', $label, 2);
        $accounts = Coa::where('nomor_akun', $number)->get();
        if ($accounts->count() === 1) {
            return $accounts->first();
        }
        $matches = $accounts->filter(fn ($account) => $compact($account->nama_akun) === $compact($name));

        return $matches->count() === 1 ? $matches->first() : null;
    }

    public function label(string $type, string $fallback = ''): ?string
    {
        $compact = fn ($value) => preg_replace('/[\s_-]+/u', '', mb_strtoupper($value));
        $key = $compact($type);
        if ($key === 'BPUCREW' || (! $key && $compact($fallback) === 'BPUCREW')) {
            $key = 'CREW';
        }
        $this->mapping ??= json_decode(file_get_contents(public_path('js/bpjs-journal-accounts.json')), true);
        $mapping = collect($this->mapping)->first(fn ($row) => $row[0] === $key);
        return $mapping[2] ?? null;
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

    public function selectedAmounts(array $groups): array
    {
        $cents = [];
        foreach ($groups as $group) {
            $id = $group['credit_id'] ?? null;
            if (! $id || !is_numeric($group['amount'] ?? null) || $group['amount'] <= 0) {
                throw ValidationException::withMessages(['group_keys' => 'Akun jurnal atau nominal tipe yang dipilih belum valid.']);
            }
            $cents[$id] = ($cents[$id] ?? 0) + (int) round($group['amount'] * 100);
        }

        return array_map(fn ($amount) => $amount / 100, $cents);
    }
}
