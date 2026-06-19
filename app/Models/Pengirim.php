<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pengirim extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_pengirim',
        'nickname1',
        'contact_person',
        'alamat',
        'catatan',
        'status',
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
                    $model->nama_pengirim,
                    $model->nickname1,
                    $model->getOriginal('nama_pengirim'),
                    $model->getOriginal('nickname1'),
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
                        $ttlQuery->where('nama_pengirim', 'like', '%'.$firstWord.'%');
                    }
                    $ttlCandidates = $ttlQuery->get();
                    foreach ($ttlCandidates as $tt) {
                        if (self::cleanNameForSync($tt->nama_pengirim) === $cleanModelName) {
                            $tt->alamat_pengirim = $newAddress;
                            $tt->save();
                        }
                    }

                    // 2. Update TandaTerima
                    $ttQuery = TandaTerima::query();
                    if (strlen($firstWord) >= 3) {
                        $ttQuery->where('pengirim', 'like', '%'.$firstWord.'%');
                    }
                    $ttCandidates = $ttQuery->get();
                    foreach ($ttCandidates as $tt) {
                        if (self::cleanNameForSync($tt->pengirim) === $cleanModelName) {
                            $tt->alamat_pengirim = $newAddress;
                            $tt->save();
                        }
                    }
                }
            }
        });
    }
}
