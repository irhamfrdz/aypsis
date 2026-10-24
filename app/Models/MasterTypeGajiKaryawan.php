<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterTypeGajiKaryawan extends Model
{
    use Auditable;

    protected $table = 'type_gaji_karyawan';

    protected $fillable = ['type_gaji_id', 'nama', 'keterangan', 'status'];

    protected $casts = ['type_gaji_id' => 'integer'];

    public function getFormattedTypeGajiIdAttribute(): ?string
    {
        return $this->type_gaji_id === null ? null : '00'.$this->type_gaji_id;
    }

    /** Lock the shared allocator inside the caller's transaction. */
    public static function lockIdSequence(): object
    {
        DB::table('nomor_terakhir')->insertOrIgnore([
            'modul' => 'type_gaji_karyawan', 'nomor_terakhir' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return DB::table('nomor_terakhir')->where('modul', 'type_gaji_karyawan')->lockForUpdate()->first();
    }
}
