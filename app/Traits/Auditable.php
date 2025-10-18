<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditable()
    {
        // Log ketika model dibuat
        static::created(function (Model $model) {
            $model->logAudit('created', null, $model->getAuditableAttributes());
        });

        // Log ketika model diupdate
        static::updated(function (Model $model) {
            $original = $model->getOriginal();
            $changes = $model->getDirty();

            if (!empty($changes)) {
                $oldValues = array_intersect_key($original, $changes);
                $model->logAudit('updated', $oldValues, $changes);
            }
        });

        // Log ketika model dihapus
        static::deleted(function (Model $model) {
            $model->logAudit('deleted', $model->getAuditableAttributes(), null);
        });
    }

    /**
     * Log audit untuk model ini
     */
    public function logAudit($action, $oldValues = null, $newValues = null, $description = null)
    {
        return AuditLog::createLog($this, $action, $oldValues, $newValues, $description);
    }

    /**
     * Mendapatkan attributes yang akan diaudit
     * Override method ini jika ingin mengecualikan field tertentu
     */
    public function getAuditableAttributes()
    {
        $attributes = $this->getAttributes();

        // Exclude sensitive fields
        $excludeFields = [
            'password',
            'remember_token',
            'email_verified_at',
            'created_at',
            'updated_at'
        ];

        // Exclude fields yang ada di property $auditExclude jika ada
        if (property_exists($this, 'auditExclude') && is_array($this->auditExclude)) {
            $excludeFields = array_merge($excludeFields, $this->auditExclude);
        }

        return array_diff_key($attributes, array_flip($excludeFields));
    }

    /**
     * Relationship ke audit logs
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderBy('created_at', 'desc');
    }

    /**
     * Mendapatkan audit log terbaru
     */
    public function latestAuditLog()
    {
        return $this->morphOne(AuditLog::class, 'auditable')->latest('created_at');
    }

    /**
     * Log custom action (tidak otomatis)
     */
    public function logCustomAction($action, $description = null, $additionalData = null)
    {
        return $this->logAudit($action, null, $additionalData, $description);
    }
}
