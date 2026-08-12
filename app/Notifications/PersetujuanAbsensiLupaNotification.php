<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersetujuanAbsensiLupaNotification extends Notification
{
    use Queueable;

    protected $pengajuan;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengajuan)
    {
        $this->pengajuan = $pengajuan;
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
        $karyawanNama = $this->pengajuan->karyawan 
            ? $this->pengajuan->karyawan->nama_lengkap 
            : 'Karyawan NIK: ' . $this->pengajuan->karyawan_id;

        $tanggalFormatted = \Carbon\Carbon::parse($this->pengajuan->tanggal)->format('d M Y');
        $waktuFormatted = \Carbon\Carbon::parse($this->pengajuan->waktu)->format('H:i');

        return [
            'title' => "Pengajuan Lupa Absen Baru",
            'message' => "{$karyawanNama} mengajukan lupa absen {$this->pengajuan->tipe_absen} pada tanggal {$tanggalFormatted} pukul {$waktuFormatted}.",
            'notes' => "Alasan: " . $this->pengajuan->alasan,
            'url' => route('master.persetujuan-absensi-lupa.index', [], false),
        ];
    }
}
