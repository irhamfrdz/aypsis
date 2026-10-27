<?php

namespace App\Rules;

use App\Models\MasterRumusBpjs;
use App\Services\KaryawanBpjsGroups;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JamsostekTypeMatchesGroup implements ValidationRule
{
    public function __construct(private mixed $group, private ?string $category = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($this->group) || ! is_string($value)) {
            $fail('Tipe BP Jamsostek harus sesuai dengan Group BP Jamsostek yang dipilih.');

            return;
        }

        $group = KaryawanBpjsGroups::normalize($this->group);
        $tipe = KaryawanBpjsGroups::normalize($value);
        $matches = MasterRumusBpjs::where('jenis', 'jamsostek')->get(['group_name', 'tipe'])
            ->contains(fn ($rumus) => KaryawanBpjsGroups::normalize((string) $rumus->group_name) === $group
                && KaryawanBpjsGroups::normalize((string) $rumus->tipe) === $tipe
                && ($this->category === null || $rumus->jamsostekCategory() === $this->category));

        if (! $matches) {
            if ($this->category !== null) {
                $label = $this->category === 'bpu_crew' ? 'BPU-CREW' : 'Non BPU-CREW';
                $fail('Group dan Tipe BP Jamsostek harus cocok dengan data master kategori '.$label.'.');
            } else {
                $fail('Tipe BP Jamsostek harus sesuai dengan Group BP Jamsostek yang dipilih.');
            }
        }
    }
}
