<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'auditable_type',
        'auditable_id',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // Tidak perlu updated_at karena audit log tidak diupdate
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    /**
     * Polymorphic relationship ke model yang diaudit
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship ke User yang melakukan aksi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan modul
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope untuk filter berdasarkan aksi
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Helper method untuk membuat log audit
     */
    public static function createLog($model, $action, $oldValues = null, $newValues = null, $description = null)
    {
        $user = Auth::user();

        return static::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->username : 'System',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'module' => static::getModuleName($model),
            'description' => $description ?: static::getDefaultDescription($action, $model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Mendapatkan nama modul berdasarkan model
     */
    protected static function getModuleName($model)
    {
        $className = class_basename($model);

        // Mapping nama model ke nama modul
        $moduleMap = [
            'Karyawan' => 'karyawan',
            'User' => 'user',
            'Divisi' => 'divisi',
            'PricelistGateIn' => 'pricelist-gate-in',
            'PricelistCat' => 'pricelist-cat',
            'PricelistSewaKontainer' => 'pricelist-sewa-kontainer',
            'Tujuan' => 'tujuan',
            'TujuanKirim' => 'tujuan-kirim',
            'Pajak' => 'pajak',
            'MasterBank' => 'bank',
            'Cabang' => 'cabang',
            'Pekerjaan' => 'pekerjaan',
            'JenisBarang' => 'jenis-barang',
            'Term' => 'term',
            'Pengirim' => 'pengirim',
        ];

        return $moduleMap[$className] ?? strtolower($className);
    }

    /**
     * Mendapatkan deskripsi default berdasarkan aksi
     */
    protected static function getDefaultDescription($action, $model)
    {
        $modelName = class_basename($model);
        $moduleName = static::getModuleName($model);

        $descriptions = [
            'created' => "Menambah data {$moduleName} baru",
            'updated' => "Mengubah data {$moduleName}",
            'deleted' => "Menghapus data {$moduleName}",
            'viewed' => "Melihat detail {$moduleName}",
            'imported' => "Mengimpor data {$moduleName}",
            'exported' => "Mengekspor data {$moduleName}",
        ];

        return $descriptions[$action] ?? "Melakukan aksi {$action} pada {$moduleName}";
    }

    /**
     * Format perubahan data untuk ditampilkan
     */
    public function getFormattedChanges()
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        $oldValues = $this->old_values ?: [];
        $newValues = $this->new_values ?: [];

        foreach ($newValues as $field => $newValue) {
            $oldValue = $oldValues[$field] ?? null;

            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $field,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Get user name untuk ditampilkan
     */
    public function getUserDisplayName()
    {
        return $this->user ? $this->user->username : ($this->user_name ?? 'Unknown User');
    }
}
