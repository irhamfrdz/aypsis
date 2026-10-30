<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRumusBpjs extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'master_rumus_bpjs';

    protected $fillable = [
        'jenis',
        'group_name',
        'tipe',
        'cabang_bpjs',
        'tipe_rumus',
        'nilai',
        'tunjangan_persen',
        'hutang_persen',
        'hutang_tiers',
        'biaya_persen',
        'keterangan_custom',
        'created_by',
        'updated_by',
        'jht_biaya',
        'jht_hutang',
        'jkk_tunjangan',
        'jkm_tunjangan',
        'jp_biaya',
        'jp_hutang',
        'jp_max_dpp',
        'jp_max_age',
        'diskon_status',
        'diskon_tipe',
        'diskon_nilai',
        'diskon_pengecualian',
    ];

    protected $casts = [
        'diskon_pengecualian' => 'array',
        'hutang_tiers' => 'array',
        'jp_max_dpp' => 'decimal:2',
        'jp_max_age' => 'integer',
    ];

    public function jamsostekCategory(): string
    {
        $groupName = strtoupper($this->group_name ?? '');
        $tipe = strtoupper(trim($this->tipe ?? ''));

        if (str_contains($groupName, 'PPU')) {
            return 'ppu';
        }

        $isCrew = in_array($tipe, ['CREW', 'BPU-CREW'], true)
            || ($tipe === '' && str_contains($groupName, 'BPU-CREW'));

        return $isCrew ? 'bpu_crew' : 'bpu_non_crew';
    }
}
