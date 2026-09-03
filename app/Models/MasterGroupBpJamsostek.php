<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterGroupBpJamsostek extends Model
{
    protected $table = 'master_group_bp_jamsosteks';

    protected $fillable = [
        'nama_group',
        'keterangan',
        'status',
        'created_by',
        'updated_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
