<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StowagePlan extends Model
{
    protected $fillable = [
        'manifest_id',
        'bay',
        'row',
        'tier',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function manifest()
    {
        return $this->belongsTo(Manifest::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
