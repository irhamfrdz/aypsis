<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    use Auditable;

    protected $fillable = [
        'nama_penerima',
        'nickname',
        'pic',
        'telepon',
        'alamat',
        'npwp',
        'nitku',
        'catatan',
        'status',
        'iu_bp_kawasan',
    ];

    private static function cleanNameForSync($name)
    {
        if (empty($name)) {
            return '';
        }
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
                    $model->nama_penerima,
                    $model->getOriginal('nama_penerima'),
                ]));

                $newAddress = $model->alamat;

                foreach ($names as $modelName) {
                    $cleanModelName = self::cleanNameForSync($modelName);
                    if (empty($cleanModelName)) {
                        continue;
                    }

                    // Get first word for SQL query optimization
                    $words = array_filter(explode(' ', preg_replace('/[^A-Z0-9 ]/', '', strtoupper(preg_replace('/\b(PT|CV|UD|TB|TOKO|Tbk)\b\.?/i', '', $modelName)))));
                    $firstWord = ! empty($words) ? reset($words) : '';

                    // 1. Update TandaTerimaLcl
                    $ttlQuery = TandaTerimaLcl::query();
                    if (strlen($firstWord) >= 3) {
                        $ttlQuery->where(function ($q) use ($firstWord) {
                            $q->where('nama_penerima', 'like', '%'.$firstWord.'%')
                                ->orWhere('notify_party', 'like', '%'.$firstWord.'%');
                        });
                    }
                    $ttlCandidates = $ttlQuery->get();
                    foreach ($ttlCandidates as $tt) {
                        $updated = false;
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
                        $ttQuery->where(function ($q) use ($firstWord) {
                            $q->where('penerima', 'like', '%'.$firstWord.'%')
                                ->orWhere('notify_party', 'like', '%'.$firstWord.'%');
                        });
                    }
                    $ttCandidates = $ttQuery->get();
                    foreach ($ttCandidates as $tt) {
                        $updated = false;
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
