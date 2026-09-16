<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJadwalKapalBerlabuh extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_jadwal_kapal_berlabuhs';

    protected $fillable = [
        'pelabuhan',
        'master_pelabuhan_id',
        'nama_kapal',
        'master_kapal_id',
        'no_voyage',
        'tanggal_closing',
        'tanggal_etd',
        'tanggal_eta',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_closing' => 'date',
        'tanggal_etd' => 'date',
        'tanggal_eta' => 'date',
    ];

    /**
     * Relasi ke Master Kapal
     */
    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'master_kapal_id');
    }

    /**
     * Relasi ke Master Pelabuhan
     */
    public function pelabuhanRelation()
    {
        return $this->belongsTo(MasterPelabuhan::class, 'master_pelabuhan_id');
    }

    /**
     * Relasi ke User pembuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User pengubah
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope filter pencarian
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nama_kapal', 'like', "%{$search}%")
                ->orWhere('pelabuhan', 'like', "%{$search}%")
                ->orWhere('no_voyage', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
        });
    }

    /**
     * Scope filter berdasarkan pelabuhan
     */
    public function scopeByPelabuhan($query, $pelabuhan)
    {
        if (!empty($pelabuhan)) {
            return $query->where('pelabuhan', $pelabuhan);
        }
        return $query;
    }

    /**
     * Scope filter berdasarkan nama kapal
     */
    public function scopeByKapal($query, $kapal)
    {
        if (!empty($kapal)) {
            return $query->where('nama_kapal', $kapal);
        }
        return $query;
    }

    /**
     * Scope filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Status badge HTML helper
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'aktif' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800"><span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-600"></span>Aktif</span>',
            'selesai' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-600"></span>Selesai</span>',
            'batal' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800"><span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-600"></span>Batal</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">' . ucfirst($this->status) . '</span>',
        };
    }
}
