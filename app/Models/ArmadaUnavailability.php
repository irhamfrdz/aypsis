<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ArmadaUnavailability extends Model
{
    use Auditable;

    protected $fillable = ['mobil_id', 'start_date', 'end_date', 'reason', 'created_by'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
