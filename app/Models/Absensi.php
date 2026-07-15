<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use Auditable, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'karyawan_id',
        'nik',
        'waktu',
        'tipe',
        'mesin_id',
        'keterangan',
        'latitude',
        'longitude',
        'device',
        'detail_lokasi',
        'foto',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * Get the employee that owns the attendance log.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Get the machine that generated the attendance log.
     */
    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }

    /**
     * Booted function to trigger notifications on created event.
     */
    protected static function booted()
    {
        static::created(function ($absensi) {
            // Kirim notifikasi hanya ke user 'adit' dan 'kiky'
            $users = \App\Models\User::whereIn('username', ['adit', 'kiky'])->get();
            
            $karyawanNama = $absensi->karyawan 
                ? $absensi->karyawan->nama_lengkap 
                : 'Karyawan NIK: ' . $absensi->nik;
            $waktuFormatted = $absensi->waktu instanceof \Carbon\Carbon 
                ? $absensi->waktu->format('H:i:s') 
                : \Carbon\Carbon::parse($absensi->waktu)->format('H:i:s');
                
            $title = "Absensi Baru: {$absensi->tipe}";
            $body = "{$karyawanNama} telah melakukan absen {$absensi->tipe} pukul {$waktuFormatted}.";

            foreach ($users as $user) {
                // Notifikasi web/database
                $user->notify(new \App\Notifications\AbsensiMasukNotification($absensi));
                
                // Notifikasi HP/Expo Push
                if ($user->expo_push_token) {
                    \App\Services\ExpoNotificationService::send(
                        $user->expo_push_token,
                        $title,
                        $body,
                        ['absensi_id' => $absensi->id, 'nik' => $absensi->nik]
                    );
                }
            }
        });
    }
}
