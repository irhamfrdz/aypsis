<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewEquipment extends Model
{
    protected $table = 'crew_checklists';

    protected $fillable = [
        'karyawan_id',
        'item_name',
        'status',
        'nomor_sertifikat',
        'issued_date',
        'expired_date',
        'catatan'
    ];

    protected $dates = [
        'issued_date',
        'expired_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public static function getDefaultItems()
    {
        return [
            'Formulir Data Karyawan',
            'CV',
            'E-KTP',
            'Kartu Keluarga',
            'BPJS Kesehatan',
            'BPJS Ketenagakerjaan',
            'NPWP',
            'PAS PHOTO 4X6',
            'REKENING BCA',
            'BUKU PELAUT',
            'IJAZAH',
            'ENDORS',
            'BST (Basic Safety Training)',
            'SCRB (Survival Craft and Rescue Boat)',
            'AFF (Advanced Fire Fighting)',
            'MFA (Medical First Aid)',
            'SAT (Security Awareness Training)',
            'SDSD (Seafarer with Designated Security Duties)',
            'ERM (Engine Room Resource Management)',
            'BRM (Bridge Resource Management)',
            'MC (Medical Care)'
        ];
    }

    public static function createDefaultChecklistForKaryawan($karyawanId)
    {
        $defaultItems = self::getDefaultItems();

        foreach ($defaultItems as $item) {
            self::create([
                'karyawan_id' => $karyawanId,
                'item_name' => $item,
                'status' => 'tidak',
                'nomor_sertifikat' => null,
                'issued_date' => null,
                'expired_date' => null,
                'catatan' => null
            ]);
        }
    }
}
