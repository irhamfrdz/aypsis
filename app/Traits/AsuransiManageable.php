<?php

namespace App\Traits;

use App\Models\User;

trait AsuransiManageable
{
    /**
     * Get the user who uploaded the asuransi document.
     */
    public function asuransiUploader()
    {
        return $this->belongsTo(User::class, 'asuransi_uploaded_by');
    }

    /**
     * Get the user who approved the asuransi document.
     */
    public function asuransiApprover()
    {
        return $this->belongsTo(User::class, 'asuransi_approved_by');
    }

    /**
     * Scope a query to only include items with/without asuransi documents.
     */
    public function scopeHasAsuransi($query, $hasAsuransi = true)
    {
        if ($hasAsuransi) {
            return $query->whereNotNull('asuransi_path');
        }
        return $query->whereNull('asuransi_path');
    }

    /**
     * Scope a query to only include approved/unapproved asuransi.
     */
    public function scopeAsuransiApproved($query, $approved = true)
    {
        return $query->where('is_asuransi_approved', $approved);
    }
}
