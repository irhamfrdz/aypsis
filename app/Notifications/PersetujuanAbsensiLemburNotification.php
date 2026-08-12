<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersetujuanAbsensiLemburNotification extends Notification
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
            : 'Karyawan ID: ' . $this->pengajuan->karyawan_id;

        $tanggalFormatted = \Carbon\Carbon::parse($this->pengajuan->tanggal)->format('d M Y');
        $jamMulai = \Carbon\Carbon::parse($this->pengajuan->jam_mulai)->format('H:i');
        $jamSelesai = \Carbon\Carbon::parse($this->pengajuan->jam_selesai)->format('H:i');

        return [
            'title' => "Pengajuan Lembur Baru",
            'message' => "{$karyawanNama} mengajukan lembur pada tanggal {$tanggalFormatted} dari pukul {$jamMulai} s/d {$jamSelesai}.",
            'notes' => "Kegiatan: " . $this->pengajuan->keterangan,
            'url' => route('master.persetujuan-absensi-lembur.index', [], false),
        ];
    }
}
