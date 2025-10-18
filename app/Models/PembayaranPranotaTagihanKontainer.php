<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PembayaranPranotaTagihanKontainer extends Model
{
    use Auditable;

    protected $table = 'pembayaran_pranota_tagihan_kontainer';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kas' => 'date',
    ];

    public function tagihans()
    {
    // The TagihanKontainerSewa model was removed during a refactor.
    // Keep this method safe for now by returning an empty relation-like object.
    // When you reintroduce the invoice model, replace this with a proper belongsToMany.
    return $this->newCollection();
    }
}
