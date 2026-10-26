<?php

namespace App\Services;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KaryawanBpjsTextUpdater
{
    public function update(string $text): array
    {
        $result = ['updated' => 0, 'unchanged' => 0, 'errors' => [], 'failed_text' => []];
        $groups = app(KaryawanBpjsGroups::class)->options();

        foreach (preg_split('/\R/u', $text) as $index => $line) {
            if (trim($line) === '') {
                continue;
            }

            try {
                $data = $this->parse($line, $groups);
                $changed = DB::transaction(function () use ($data) {
                    $employees = Karyawan::where('nik', $data['nik'])->lockForUpdate()->get();
                    if ($employees->count() !== 1) {
                        $this->invalid('NIK tidak ditemukan atau terdaftar lebih dari sekali.');
                    }
                    $employee = $employees->first();
                    if ($this->normalizeName($data['nama']) !== $this->normalizeName($employee->nama_lengkap)) {
                        $this->invalid('Nama tidak cocok dengan karyawan pada NIK tersebut.');
                    }

                    unset($data['nik'], $data['nama']);
                    $employee->fill($data);
                    if (! $employee->isDirty()) {
                        return false;
                    }
                    $employee->save();

                    return true;
                });
                $result[$changed ? 'updated' : 'unchanged']++;
            } catch (ValidationException $exception) {
                $result['errors'][] = 'Baris '.($index + 1).': '.$exception->validator->errors()->first();
                $result['failed_text'][] = $line;
            } catch (\Throwable $exception) {
                report($exception);
                $result['errors'][] = 'Baris '.($index + 1).': Gagal menyimpan data. Silakan coba lagi.';
                $result['failed_text'][] = $line;
            }
        }

        return $result;
    }

    private function parse(string $line, array $groups): array
    {
        $fields = ['nik', 'nama', 'dpp_jkn', 'group_jkn', 'dpp_bp_jamsostek', 'group_bp_jamsostek', 'cabang_bpjs'];
        $data = [];
        // Split between labelled fields, preserving Indonesian decimal commas.
        foreach (preg_split('/,\s*(?=[\p{L}_][\p{L}\s_]*;)/u', trim($line)) as $pair) {
            $parts = explode(';', $pair);
            if (count($parts) !== 2) {
                $this->invalid('Gunakan format kolom;nilai, kolom;nilai. Satu karyawan per baris.');
            }
            $key = str_replace(' ', '_', mb_strtolower(preg_replace('/\s+/u', ' ', trim($parts[0]))));
            $value = trim($parts[1]);
            if (! in_array($key, $fields, true)) {
                $this->invalid('Kolom "'.$parts[0].'" tidak dikenal.');
            }
            if (array_key_exists($key, $data)) {
                $this->invalid('Kolom "'.$parts[0].'" ditulis lebih dari sekali.');
            }
            if (mb_strlen($value) > 255) {
                $this->invalid('Nilai kolom "'.$parts[0].'" maksimal 255 karakter.');
            }
            $data[$key] = $value;
        }

        if (empty($data['nik'])) {
            $this->invalid('NIK wajib diisi.');
        }
        if (! isset($data['nama']) || $data['nama'] === '') {
            $this->invalid('Nama karyawan wajib diisi.');
        }
        $data = array_filter($data, fn ($value) => $value !== '');
        foreach ($groups as $key => $allowed) {
            if (! isset($data[$key])) {
                continue;
            }
            $data[$key] = mb_strtoupper(preg_replace('/\s+/u', ' ', $data[$key]));
            if (! in_array($data[$key], $allowed, true)) {
                $this->invalid(str_replace('_', ' ', ucfirst($key)).' "'.$data[$key].'" tidak tersedia. Pilihan: '.implode(', ', $allowed).'.');
            }
        }
        foreach (['dpp_jkn', 'dpp_bp_jamsostek'] as $key) {
            if (! isset($data[$key])) {
                continue;
            }
            if (! preg_match('/^(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d{1,2})?$/D', $data[$key])) {
                $this->invalid(strtoupper(str_replace('_', ' ', $key)).' harus nominal, contoh 3.200.000. Gunakan kolom Group untuk PPU-KANTOR.');
            }
            $data[$key] = str_replace(',', '.', str_replace('.', '', $data[$key]));
        }
        if (count(array_diff(array_keys($data), ['nik', 'nama'])) === 0) {
            $this->invalid('Isi minimal satu kolom DPP, Group, atau Cabang BPJS.');
        }

        return $data;
    }

    private function normalizeName(?string $name): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($name ?? '')));
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['bpjs_text' => $message]);
    }
}
