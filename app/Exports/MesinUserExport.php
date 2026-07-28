<?php

namespace App\Exports;

use App\Models\MesinUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MesinUserExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sn;

    public function __construct($sn = null)
    {
        $this->sn = $sn;
    }

    public function collection()
    {
        $query = MesinUser::query();
        if ($this->sn) {
            $query->where('sn', $this->sn);
        }
        return $query->orderBy('sn')->orderBy('pin')->get();
    }

    public function headings(): array
    {
        return [
            'ID Database',
            'Serial Number (SN)',
            'PIN / NIK',
            'Nama di Mesin',
            'Privilege (Hak Akses)',
            'Password',
            'Group',
            'Ditarik Pada'
        ];
    }

    public function map($user): array
    {
        $privilegeMap = [
            '0' => 'User Biasa',
            '2' => 'Enroller',
            '14' => 'Admin'
        ];

        return [
            $user->id,
            $user->sn,
            $user->pin,
            $user->name ?: '-',
            isset($privilegeMap[$user->privilege]) ? $privilegeMap[$user->privilege] : $user->privilege,
            $user->password ?: '-',
            $user->group ?: '-',
            $user->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
