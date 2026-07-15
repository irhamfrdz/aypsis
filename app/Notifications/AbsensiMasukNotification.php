<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AbsensiMasukNotification extends Notification
{
    use Queueable;

    protected $absensi;

    /**
     * Create a new notification instance.
     */
    public function __construct($absensi)
    {
        $this->absensi = $absensi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $karyawanNama = $this->absensi->karyawan 
            ? $this->absensi->karyawan->nama_lengkap 
            : 'Karyawan NIK: ' . $this->absensi->nik;

        $waktuFormatted = $this->absensi->waktu instanceof \Carbon\Carbon 
            ? $this->absensi->waktu->format('H:i:s') 
            : \Carbon\Carbon::parse($this->absensi->waktu)->format('H:i:s');

        return [
            'message' => "Absensi Baru: {$karyawanNama} telah melakukan absen {$this->absensi->tipe}.",
            'notes' => "Waktu Absen: {$waktuFormatted} (Keterangan: " . ($this->absensi->keterangan ?? '-') . ")",
            'url' => route('absensi.index') . '?nik=' . $this->absensi->nik,
        ];
    }
}
