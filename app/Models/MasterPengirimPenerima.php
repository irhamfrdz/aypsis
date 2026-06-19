<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPengirimPenerima extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_pengirim_penerima';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'npwp',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship to User who created this record
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relationship to User who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope untuk filter data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate kode otomatis
     */
    public static function generateKode()
    {
        $lastRecord = self::withTrashed()->orderBy('id', 'desc')->first();
        $lastNumber = $lastRecord ? intval(substr($lastRecord->kode, 3)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return 'PP-'.$newNumber;
    }

    private static function cleanNameForSync($name)
    {
        if (empty($name)) return '';
        $name = strtoupper($name);
        $name = preg_replace('/\b(PT|CV|UD|TB|TOKO|Tbk)\b\.?/i', '', $name);
        $name = preg_replace('/[^A-Z0-9]/', '', $name);
        return trim($name);
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            if ($model->isDirty('alamat')) {
                $names = array_filter(array_unique([
                    $model->nama,
                    $model->getOriginal('nama'),
                ]));

                $newAddress = $model->alamat;

                foreach ($names as $modelName) {
                    $cleanModelName = self::cleanNameForSync($modelName);
                    if (empty($cleanModelName)) continue;

                    // Get first word for SQL query optimization
                    $words = array_filter(explode(' ', preg_replace('/[^A-Z0-9 ]/', '', strtoupper(preg_replace('/\b(PT|CV|UD|TB|TOKO|Tbk)\b\.?/i', '', $modelName)))));
                    $firstWord = !empty($words) ? reset($words) : '';

                    // 1. Update TandaTerimaLcl
                    $ttlQuery = TandaTerimaLcl::query();
                    if (strlen($firstWord) >= 3) {
                        $ttlQuery->where(function($q) use ($firstWord) {
                            $q->where('nama_pengirim', 'like', '%' . $firstWord . '%')
                              ->orWhere('nama_penerima', 'like', '%' . $firstWord . '%')
                              ->orWhere('notify_party', 'like', '%' . $firstWord . '%');
                        });
                    }
                    $ttlCandidates = $ttlQuery->get();
                    foreach ($ttlCandidates as $tt) {
                        $updated = false;
                        if (self::cleanNameForSync($tt->nama_pengirim) === $cleanModelName) {
                            $tt->alamat_pengirim = $newAddress;
                            $updated = true;
                        }
                        if (self::cleanNameForSync($tt->nama_penerima) === $cleanModelName) {
                            $tt->alamat_penerima = $newAddress;
                            $updated = true;
                        }
                        if (self::cleanNameForSync($tt->notify_party) === $cleanModelName) {
                            $tt->alamat_notify_party = $newAddress;
                            $updated = true;
                        }
                        if ($updated) {
                            $tt->save();
                        }
                    }

                    // 2. Update TandaTerima
                    $ttQuery = TandaTerima::query();
                    if (strlen($firstWord) >= 3) {
                        $ttQuery->where(function($q) use ($firstWord) {
                            $q->where('pengirim', 'like', '%' . $firstWord . '%')
                              ->orWhere('penerima', 'like', '%' . $firstWord . '%')
                              ->orWhere('notify_party', 'like', '%' . $firstWord . '%');
                        });
                    }
                    $ttCandidates = $ttQuery->get();
                    foreach ($ttCandidates as $tt) {
                        $updated = false;
                        if (self::cleanNameForSync($tt->pengirim) === $cleanModelName) {
                            $tt->alamat_pengirim = $newAddress;
                            $updated = true;
                        }
                        if (self::cleanNameForSync($tt->penerima) === $cleanModelName) {
                            $tt->alamat_penerima = $newAddress;
                            $updated = true;
                        }
                        if (self::cleanNameForSync($tt->notify_party) === $cleanModelName) {
                            $tt->alamat_notify_party = $newAddress;
                            $updated = true;
                        }
                        if ($updated) {
                            $tt->save();
                        }
                    }
                }
            }
        });
    }
}
